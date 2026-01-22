<?php 
    session_start();
    
    $page_title = "EcoWaste | Admin Dashboard";
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
    
    // Get total collectors
    $collectorStmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'COLLECTOR'");
    $totalCollectors = $collectorStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get today's active tasks (not pending)
    $todayTasksStmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM waste_requests 
        WHERE DATE(pickup_date) = CURDATE() 
        AND status != 'PENDING'
    ");
    $todayTasks = $todayTasksStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total recycling centers
    $centersStmt = $pdo->query("SELECT COUNT(*) as total FROM recycling_centers");
    $totalCenters = $centersStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get pending requests
    $pendingStmt = $pdo->query("SELECT COUNT(*) as total FROM waste_requests WHERE status = 'PENDING'");
    $pendingRequests = $pendingStmt->fetch(PDO::FETCH_ASSOC)['total'];
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
}

/* ---------- DASHBOARD HEADER ---------- */
.main h1 {
    margin-bottom: 10px;
    color: var(--primary);
}

.main p {
    margin-bottom: 25px;
    color: #555;
}

/* ---------- CARDS ---------- */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.card {
    background: var(--card);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.card h3 {
    font-size: 16px;
    color: #555;
}

.card h2 {
    margin-top: 10px;
    font-size: 28px;
    color: var(--primary);
}
</style>

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <a class="active" href="AdminDashboard.php">Dashboard</a>
        <a href="/pages/Admin/CollectorManagement.php">Collector Management</a>
        <a href="/pages/Admin/TaskManagement.php">Task Management</a>
        <a href="AddRecyclingCenter.php">Add Recycling Center</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <h1>Admin Dashboard</h1>
        <p>Monitor waste collection, manage collectors, and oversee recycling centers.</p>

        <!-- STAT CARDS -->
        <div class="cards">
            <div class="card">
                <h3>Total Collectors</h3>
                <h2><?= $totalCollectors ?></h2>
            </div>
            <div class="card">
                <h3>Active Tasks (Today)</h3>
                <h2><?= $todayTasks ?></h2>
            </div>
            <div class="card">
                <h3>Recycling Centers</h3>
                <h2><?= $totalCenters ?></h2>
            </div>
            <div class="card">
                <h3>Pending Requests</h3>
                <h2><?= $pendingRequests ?></h2>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>