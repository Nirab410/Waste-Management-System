<?php 
    session_start();
    
    $page_title = "EcoWaste | Waste Requests";
    include '../../includes/navbar.php'; 
    include '../../dbConnection.php';

    // Check if user is logged in
    if(!isset($_SESSION['user_id'])){
       header("Location: /Auth/login.php");
       exit();
    }

    // Check if user is CENTER_CONTROLLER
    $stmt = $pdo->query("
        SELECT role
        FROM users
        WHERE id = '{$_SESSION['user_id']}'
    ");
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    if($role['role'] != 'CENTER_CONTROLLER'){
        echo "<script>alert('Access denied. CENTER_CONTROLLER role required.');</script>";
        header("Location: /Auth/login.php");
        exit();
    }
    
    // Get center_id for this controller
    $centerStmt = $pdo->prepare("
        SELECT id, name 
        FROM recycling_centers 
        WHERE controller_id = ? 
        LIMIT 1
    ");
    $centerStmt->execute([$_SESSION['user_id']]);
    $center = $centerStmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$center){
        echo "<script>alert('No recycling center assigned to you.');</script>";
        exit();
    }
    
    // Get all waste requests for this center with priority sorting
    $requestsStmt = $pdo->prepare("
        SELECT 
            wr.id,
            wr.request_type,
            wr.frequency,
            wr.pickup_date,
            wr.collection_location,
            wr.estimated_weight,
            wr.status,
            wr.created_at,
            u.full_name as resident_name,
            u.phone as resident_phone,
            z.zone_name
        FROM waste_requests wr
        JOIN users u ON wr.resident_id = u.id
        LEFT JOIN recycling_centers rc ON wr.center_id = rc.id
        LEFT JOIN zones z ON rc.zone_id = z.id
        WHERE wr.center_id = ?
        ORDER BY 
            CASE WHEN wr.status = 'PENDING' THEN 0 ELSE 1 END,
            CASE WHEN wr.request_type = 'EMERGENCY' THEN 0 ELSE 1 END,
            wr.created_at DESC
    ");
    $requestsStmt->execute([$center['id']]);
    $requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Tahoma, sans-serif;
}

:root {
    --primary: #3fa34d;
    --secondary: #7bcf9b;
    --light: #f4f6f5;
    --dark: #1f2933;
    --card: #ffffff;
}

body {
    background: var(--light);
    color: #333;
}

/* ================= TOP NAV ================= */
.top-nav {
    height: 60px;
    background: linear-gradient(to right, var(--primary), var(--secondary));
    padding: 0 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.top-nav h2 {
    color: #fff;
    font-weight: 600;
}

.nav-center a {
    margin: 0 15px;
    text-decoration: none;
    font-weight: 600;
    color: #ffffff;
    opacity: 0.9;
}

.nav-center a:hover {
    opacity: 1;
    text-decoration: underline;
}

/* ================= LAYOUT ================= */
.layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    min-height: calc(100vh - 60px);
}

/* ================= SIDEBAR ================= */
.sidebar {
    background: var(--dark);
    padding: 20px;
}

.sidebar a {
    display: block;
    text-decoration: none;
    color: #cfd8dc;
    padding: 12px 14px;
    margin-bottom: 12px;
    border-radius: 6px;
    font-size: 15px;
    transition: 0.3s;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}

.sidebar a.active {
    background: var(--secondary);
    color: #fff;
}

/* ================= MAIN CONTENT ================= */
.main {
    padding: 30px;
}

.main h1 {
    margin-bottom: 10px;
    color: var(--primary);
}

.main p {
    margin-bottom: 25px;
    color: #555;
}

/* ================= TABLE ================= */
.table-wrapper {
    overflow-x: auto;
    margin-top: 20px;
}

table {
    width: 100%;
    min-width: 1000px;
    border-collapse: collapse;
    background: var(--card);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

th, td {
    padding: 12px;
    text-align: left;
    font-size: 14px;
}

th {
    background: var(--primary);
    color: #fff;
    font-weight: 600;
}

tr:nth-child(even) {
    background: #f1f5f3;
}

/* ================= STATUS BADGE ================= */
.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
    white-space: nowrap;
}

.badge-pending {
    background: #fef3c7;
    color: #92400e;
}

.badge-assigned {
    background: #dbeafe;
    color: #1e40af;
}

.badge-collected {
    background: #d1fae5;
    color: #065f46;
}

.badge-completed {
    background: #e0e7ff;
    color: #3730a3;
}

.badge-emergency {
    background: #fee2e2;
    color: #991b1b;
}

.badge-normal {
    background: #e5e7eb;
    color: #374151;
}
</style>

<div class="top-nav">
    <h2>Recycling Center - <?= htmlspecialchars($center['name']) ?></h2>
    <div class="nav-center">
        <a href="WasteRequests.php">Waste Requests</a>
        <a href="DailyIntake.php">Daily Intake</a>
    </div>
</div>

<div class="layout">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="WasteRequests.php" class="active">Waste Requests</a>
        <a href="DailyIntake.php">Daily Intake</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <h1>Waste Requests</h1>
        <p>View all waste collection requests assigned to your center.</p>

        <!-- REQUESTS TABLE -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Resident</th>
                        <th>Zone</th>
                        <th>Location</th>
                        <th>Pickup Date</th>
                        <th>Weight</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($requests)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #999; padding: 30px;">
                                No waste requests for your center yet
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($requests as $request): ?>
                        <tr>
                            <td>#<?= $request['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($request['resident_name']) ?></strong><br>
                                <small style="color: #666;"><?= htmlspecialchars($request['resident_phone']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($request['zone_name'] ?? 'N/A') ?></td>
                            <td style="max-width: 200px;"><?= htmlspecialchars($request['collection_location']) ?></td>
                            <td><?= date('M d, Y', strtotime($request['pickup_date'])) ?></td>
                            <td><?= $request['estimated_weight'] ? htmlspecialchars($request['estimated_weight']) . ' kg' : '<em style="color: #999;">Not specified</em>' ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($request['request_type']) ?>">
                                    <?= htmlspecialchars($request['request_type']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= strtolower($request['status']) ?>">
                                    <?= htmlspecialchars($request['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>