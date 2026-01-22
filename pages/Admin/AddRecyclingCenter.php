<?php 
    session_start();
    
    $page_title = "EcoWaste | Add Recycling Center";
    include '../../includes/navbar.php'; 
    include '../../dbConnection.php';

    // Check if user is logged in
    if(!isset($_SESSION['user_id'])){
       header("Location: /Auth/login.php");
       exit();
    }

    // Check if user is ADMIN
    $stmt = $pdo->query("
        SELECT role
        FROM users
        WHERE id = '{$_SESSION['user_id']}'
    ");
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    if($role['role'] != 'ADMIN'){
        echo "<script>alert('Access denied. ADMIN role required.');</script>";
        header("Location: /Auth/login.php");
        exit();
    }
    
    // Get all zones
    $zonesStmt = $pdo->query("SELECT id, zone_name FROM zones ORDER BY zone_name");
    $zones = $zonesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get available center controllers (not already assigned)
    $controllersStmt = $pdo->query("
        SELECT u.id, u.full_name, u.phone
        FROM users u
        WHERE u.role = 'CENTER_CONTROLLER'
        AND u.id NOT IN (
            SELECT DISTINCT controller_id 
            FROM recycling_centers 
            WHERE controller_id IS NOT NULL
        )
        ORDER BY u.full_name
    ");
    $availableControllers = $controllersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Handle form submission
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_center'])){
        $center_name = trim($_POST['center_name']);
        $address = trim($_POST['address']);
        $max_capacity = $_POST['max_capacity'];
        $controller_id = $_POST['controller_id'];
        $selected_zones = isset($_POST['zones']) ? $_POST['zones'] : [];
        
        if(empty($selected_zones)){
            echo "<script>alert('Please select at least one zone.');</script>";
        } elseif(empty($controller_id)){
            echo "<script>alert('Please select a center controller.');</script>";
        } else {
            try {
                // Start transaction
                $pdo->beginTransaction();
                
                // Insert recycling center for each selected zone
                $insertStmt = $pdo->prepare("
                    INSERT INTO recycling_centers (name, address, max_capacity, zone_id, controller_id, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                
                foreach($selected_zones as $zone_id){
                    $insertStmt->execute([$center_name, $address, $max_capacity, $zone_id, $controller_id]);
                }
                
                // Commit transaction
                $pdo->commit();
                
                echo "<script>alert('Recycling center added successfully for " . count($selected_zones) . " zone(s)!'); window.location.href='AddRecyclingCenter.php';</script>";
                
            } catch(Exception $e) {
                // Rollback on error
                $pdo->rollBack();
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
            }
        }
    }
    
    // Get all recycling centers with their zones and controllers
    $centersStmt = $pdo->query("
        SELECT 
            rc.id,
            rc.name,
            rc.address,
            rc.max_capacity,
            rc.created_at,
            z.zone_name,
            u.full_name as controller_name,
            u.phone as controller_phone
        FROM recycling_centers rc
        LEFT JOIN zones z ON rc.zone_id = z.id
        LEFT JOIN users u ON rc.controller_id = u.id
        ORDER BY rc.created_at DESC
    ");
    $centers = $centersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group centers by name to show multiple zones
    $groupedCenters = [];
    foreach($centers as $center){
        $name = $center['name'];
        if(!isset($groupedCenters[$name])){
            $groupedCenters[$name] = [
                'id' => $center['id'],
                'name' => $center['name'],
                'address' => $center['address'],
                'max_capacity' => $center['max_capacity'],
                'created_at' => $center['created_at'],
                'controller_name' => $center['controller_name'],
                'controller_phone' => $center['controller_phone'],
                'zones' => []
            ];
        }
        $groupedCenters[$name]['zones'][] = $center['zone_name'];
    }
?>

<style>
/* ---------- RESET ---------- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Tahoma, sans-serif;
}

/* ---------- COLORS ---------- */
:root {
    --primary: #3fa34d;
    --secondary: #7bcf9b;
    --light: #f4f7f6;
    --dark: #1b1b1b;
    --card: #ffffff;
}

/* ---------- BODY ---------- */
body {
    background: var(--light);
    color: #333;
}

/* ---------- LAYOUT ---------- */
.container {
    display: flex;
}

/* ---------- SIDEBAR ---------- */
.sidebar {
    width: 240px;
    background: #1f2933;
    min-height: calc(100vh - 60px);
    padding-top: 20px;
}

.sidebar a {
    display: block;
    padding: 14px 22px;
    color: #cfd8dc;
    text-decoration: none;
    transition: 0.3s;
    font-size: 15px;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}

.sidebar a.active {
    background: var(--secondary);
    color: #fff;
}

/* ---------- MAIN CONTENT ---------- */
.main {
    flex: 1;
    padding: 30px;
    max-width: 100%;
}

.main h1 {
    margin-bottom: 10px;
    color: var(--primary);
}

.main p {
    margin-bottom: 25px;
    color: #555;
}

/* ---------- FORM CARD ---------- */
.form-card {
    background: var(--card);
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    margin-bottom: 30px;
    max-width: 800px;
}

.form-card h2 {
    color: var(--primary);
    margin-bottom: 20px;
    font-size: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    transition: 0.3s;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

/* ---------- ZONE CHECKBOXES ---------- */
.zones-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
    margin-top: 10px;
}

.zone-checkbox {
    display: flex;
    align-items: center;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

.zone-checkbox:hover {
    border-color: var(--primary);
    background: #f0fdf4;
}

.zone-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 10px;
    cursor: pointer;
}

.zone-checkbox label {
    margin: 0;
    cursor: pointer;
    font-weight: normal;
}

/* ---------- BUTTON ---------- */
.btn-submit {
    background: var(--primary);
    color: white;
    padding: 14px 30px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    margin-top: 10px;
}

.btn-submit:hover {
    opacity: 0.9;
}

/* ---------- TABLE ---------- */
.table-section {
    margin-top: 40px;
}

.table-section h2 {
    color: var(--primary);
    margin-bottom: 20px;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    background: var(--card);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

th, td {
    padding: 14px;
    text-align: left;
}

th {
    background: var(--primary);
    color: #fff;
    font-weight: 600;
}

tr:nth-child(even) {
    background: #f1f5f3;
}

.zone-tag {
    display: inline-block;
    background: #e0e7ff;
    color: #3730a3;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 12px;
    margin: 2px;
    font-weight: 600;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    border-left: 4px solid #f59e0b;
}
</style>

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="AdminDashboard.php">Dashboard</a>
        <a href="CollectorManagement.php">Collector Management</a>
        <a href="TaskManagement.php">Task Management</a>
        <a class="active" href="AddRecyclingCenter.php">Add Recycling Center</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <h1>Add Recycling Center</h1>
        <p>Register new recycling centers and assign zones.</p>

        <!-- FORM CARD -->
        <div class="form-card">
            <h2>Recycling Center Details</h2>
            
            <?php if(empty($availableControllers)): ?>
                <div class="alert-warning">
                    <strong>Warning:</strong> No available center controllers. All controllers are already assigned to centers.
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="center_name">Center Name *</label>
                    <input type="text" id="center_name" name="center_name" placeholder="Enter center name" required>
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea id="address" name="address" placeholder="Enter full address" required></textarea>
                </div>

                <div class="form-group">
                    <label for="max_capacity">Maximum Capacity (tons) *</label>
                    <input type="number" id="max_capacity" name="max_capacity" placeholder="Enter maximum capacity" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="controller_id">Assign Center Controller *</label>
                    <select id="controller_id" name="controller_id" required>
                        <option value="">-- Select Controller --</option>
                        <?php foreach($availableControllers as $controller): ?>
                            <option value="<?= $controller['id'] ?>">
                                <?= htmlspecialchars($controller['full_name']) ?> - <?= htmlspecialchars($controller['phone']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Select Zones * (Select one or more zones)</label>
                    <div class="zones-container">
                        <?php foreach($zones as $zone): ?>
                        <div class="zone-checkbox">
                            <input type="checkbox" name="zones[]" value="<?= $zone['id'] ?>" id="zone_<?= $zone['id'] ?>">
                            <label for="zone_<?= $zone['id'] ?>"><?= htmlspecialchars($zone['zone_name']) ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" name="add_center" class="btn-submit" <?= empty($availableControllers) ? 'disabled' : '' ?>>
                    Add Recycling Center
                </button>
            </form>
        </div>

        <!-- EXISTING CENTERS TABLE -->
        <div class="table-section">
            <h2>Existing Recycling Centers</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Center Name</th>
                            <th>Address</th>
                            <th>Capacity (tons)</th>
                            <th>Controller</th>
                            <th>Zones</th>
                            <th>Added On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($groupedCenters)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #999;">No recycling centers added yet</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($groupedCenters as $center): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($center['name']) ?></strong></td>
                                <td><?= htmlspecialchars($center['address']) ?></td>
                                <td><?= htmlspecialchars($center['max_capacity']) ?></td>
                                <td>
                                    <?php if($center['controller_name']): ?>
                                        <strong><?= htmlspecialchars($center['controller_name']) ?></strong><br>
                                        <small style="color: #666;"><?= htmlspecialchars($center['controller_phone']) ?></small>
                                    <?php else: ?>
                                        <em style="color: #999;">Not assigned</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php foreach($center['zones'] as $zone): ?>
                                        <span class="zone-tag"><?= htmlspecialchars($zone) ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($center['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>