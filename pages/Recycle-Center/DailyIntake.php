<?php 
    session_start();
    
    $page_title = "EcoWaste | Daily Intake";
    include '../../includes/navbar.php'; 
    include '../../dbConnection.php';

    // Check if user is logged in
    if(!isset($_SESSION['user_id'])){
       header("Location: /Auth/login.php");
       exit();
    }

    // Check if user is CENTER_CONTROLLER - FIXED: Using prepared statement
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($role['role'] != 'CENTER_CONTROLLER'){
        echo "<script>alert('Access denied. CENTER_CONTROLLER role required.');</script>";
        header("Location: /Auth/login.php");
        exit();
    }
    
    // Get center info
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
    
    // Handle form submission
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_intake'])){
        $food_waste = $_POST['food_waste'] ?? 0;
        $plastic = $_POST['plastic'] ?? 0;
        $paper = $_POST['paper'] ?? 0;
        $e_waste = $_POST['e_waste'] ?? 0;
        $metal = $_POST['metal'] ?? 0;
        
        try {
            // Check if record exists for this controller
            $checkStmt = $pdo->prepare("
                SELECT controller_id, food_waste, plastic, paper, e_waste, metal 
                FROM center_waste_information 
                WHERE controller_id = ?
            ");
            $checkStmt->execute([$_SESSION['user_id']]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if($existing){
                // Update existing record by adding new values
                $updateStmt = $pdo->prepare("
                    UPDATE center_waste_information 
                    SET 
                        food_waste = food_waste + ?,
                        plastic = plastic + ?,
                        paper = paper + ?,
                        e_waste = e_waste + ?,
                        metal = metal + ?
                    WHERE controller_id = ?
                ");
                $updateStmt->execute([$food_waste, $plastic, $paper, $e_waste, $metal, $_SESSION['user_id']]);
                echo "<script>alert('Intake record updated successfully!'); window.location.href='DailyIntake.php';</script>";
            } else {
                // Insert new record
                $insertStmt = $pdo->prepare("
                    INSERT INTO center_waste_information 
                    (controller_id, food_waste, plastic, paper, e_waste, metal) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $insertStmt->execute([$_SESSION['user_id'], $food_waste, $plastic, $paper, $e_waste, $metal]);
                echo "<script>alert('Intake record saved successfully!'); window.location.href='DailyIntake.php';</script>";
            }
        } catch(Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    
    // Get total inventory for this controller
    $todayStmt = $pdo->prepare("
        SELECT food_waste, plastic, paper, e_waste, metal 
        FROM center_waste_information 
        WHERE controller_id = ?
    ");
    $todayStmt->execute([$_SESSION['user_id']]);
    $todayInventory = $todayStmt->fetch(PDO::FETCH_ASSOC);
    
    // Set weekly inventory same as total (since we don't have date tracking)
    $weeklyInventory = $todayInventory;
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
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

/* ================= CARD ================= */
.card {
    background: var(--card);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.card h3 {
    margin-bottom: 20px;
    color: var(--primary);
}

/* ================= FORM ================= */
label {
    font-size: 14px;
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
}

select, input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 2px solid #e5e7eb;
    font-size: 14px;
}

select:focus, input:focus {
    outline: none;
    border-color: var(--primary);
}

button {
    width: 100%;
    padding: 12px;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    font-size: 15px;
}

button:hover {
    opacity: 0.9;
}

/* ================= INVENTORY ================= */
.inventory-item {
    display: flex;
    justify-content: space-between;
    padding: 14px;
    margin-bottom: 15px;
    border-radius: 8px;
    font-weight: 600;
}

.plastic { background: #e1bee7; }
.paper { background: #bbdefb; }
.metal { background: #dcedc8; }
.ewaste { background: #b2dfdb; }
.organic { background: #ffe0b2; }

.info-text {
    font-size: 13px;
    color: #666;
    margin-bottom: 20px;
    padding: 10px;
    background: #f0f9ff;
    border-left: 4px solid var(--primary);
    border-radius: 4px;
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
        <a href="WasteRequests.php">Waste Requests</a>
        <a href="DailyIntake.php" class="active">Daily Intake</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <!-- RECORD INTAKE -->
        <div class="card">
            <h3>Record Intake</h3>
            
            <div class="info-text">
                <strong>Note:</strong> Values will be added to existing totals.
            </div>

            <form method="POST">
                <label>Food Waste (kg)</label>
                <input type="number" name="food_waste" placeholder="Enter weight" step="0.01" min="0" value="0">

                <label>Plastic (kg)</label>
                <input type="number" name="plastic" placeholder="Enter weight" step="0.01" min="0" value="0">

                <label>Paper (kg)</label>
                <input type="number" name="paper" placeholder="Enter weight" step="0.01" min="0" value="0">

                <label>E-waste (kg)</label>
                <input type="number" name="e_waste" placeholder="Enter weight" step="0.01" min="0" value="0">

                <label>Metal (kg)</label>
                <input type="number" name="metal" placeholder="Enter weight" step="0.01" min="0" value="0">

                <button type="submit" name="save_intake">Save Record</button>
            </form>
        </div>

        <!-- WEEKLY INVENTORY -->
        <div class="card">
            <h3>Total Inventory By Type</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Current storage</p>

            <div class="inventory-item plastic">
                <span>Plastic</span>
                <span><?= $weeklyInventory['plastic'] ?? 0 ?> kg</span>
            </div>

            <div class="inventory-item paper">
                <span>Paper</span>
                <span><?= $weeklyInventory['paper'] ?? 0 ?> kg</span>
            </div>

            <div class="inventory-item metal">
                <span>Metal</span>
                <span><?= $weeklyInventory['metal'] ?? 0 ?> kg</span>
            </div>

            <div class="inventory-item ewaste">
                <span>E-waste</span>
                <span><?= $weeklyInventory['e_waste'] ?? 0 ?> kg</span>
            </div>

            <div class="inventory-item organic">
                <span>Food Waste</span>
                <span><?= $weeklyInventory['food_waste'] ?? 0 ?> kg</span>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>