<?php 
    session_start();
    
    $page_title = "EcoWaste | Task Management";
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
    
    // Handle collector assignment
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_collector'])){
        $request_id = $_POST['request_id'];
        $collector_id = $_POST['collector_id'];
        
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Update waste_requests status to ASSIGNED
            $updateStmt = $pdo->prepare("
                UPDATE waste_requests 
                SET status = 'ASSIGNED' 
                WHERE id = ? AND status = 'PENDING'
            ");
            $updateStmt->execute([$request_id]);
            
            // Insert into collector_assignments
            $assignStmt = $pdo->prepare("
                INSERT INTO collector_assignments (collector_id, request_id, assigned_at) 
                VALUES (?, ?, NOW())
            ");
            $assignStmt->execute([$collector_id, $request_id]);
            
            // Commit transaction
            $pdo->commit();
            
            echo "<script>alert('Collector assigned successfully!'); window.location.href='TaskManagement.php';</script>";
            
        } catch(Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    
    // Get all waste requests ordered by priority (EMERGENCY first) then newest first
    $requestsStmt = $pdo->query("
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
        ORDER BY 
            CASE WHEN wr.status = 'PENDING' THEN 0 ELSE 1 END,
            CASE WHEN wr.request_type = 'EMERGENCY' THEN 0 ELSE 1 END,
            wr.created_at DESC
    ");
    $requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get active collectors with their task count
    
    // $collectorsStmt = $pdo->query("
    //     SELECT 
    //         u.id,
    //         u.full_name,
    //         u.phone,
    //         COUNT(ca.id) as assigned_tasks
    //     FROM users u
    //     LEFT JOIN collector_assignments ca ON u.id = ca.collector_id
    //     WHERE u.role = 'COLLECTOR'
    //     GROUP BY u.id, u.full_name, u.phone
    //     ORDER BY assigned_tasks ASC, u.full_name ASC
    // ");
    $collectorsStmt = $pdo->query("
    SELECT 
        u.id,
        u.full_name,
        u.phone,
        COUNT(ca.id) as assigned_tasks
    FROM users u
    LEFT JOIN collector_assignments ca ON u.id = ca.collector_id
    WHERE u.role = 'COLLECTOR' AND u.is_active = 1 -- Added status check
    GROUP BY u.id, u.full_name, u.phone
    ORDER BY assigned_tasks ASC, u.full_name ASC
    ");
    $collectors = $collectorsStmt->fetchAll(PDO::FETCH_ASSOC);
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
    overflow-x: auto;
}

.main h1 {
    margin-bottom: 10px;
    color: var(--primary);
}

.main p {
    margin-bottom: 25px;
    color: #555;
}

/* ---------- TABLE ---------- */
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

/* ---------- STATUS BADGE ---------- */
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

/* ---------- BUTTONS ---------- */
.btn {
    padding: 6px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    white-space: nowrap;
}

.btn-assign {
    background: var(--primary);
    color: white;
}

.btn-assign:hover {
    opacity: 0.9;
}

.btn-assigned {
    background: #94a3b8;
    color: white;
    cursor: not-allowed;
}

/* ---------- MODAL ---------- */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    overflow-y: auto;
}

.modal-content {
    background: white;
    margin: 80px auto;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h2 {
    color: var(--primary);
    font-size: 22px;
}

.close {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #999;
    line-height: 1;
}

.close:hover {
    color: #333;
}

.collector-list {
    max-height: 400px;
    overflow-y: auto;
}

.collector-item {
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
}

.collector-item:hover {
    border-color: var(--primary);
    background: #f0fdf4;
}

.collector-item input[type="radio"] {
    margin-right: 15px;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.collector-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex: 1;
}

.task-count {
    background: #e0e7ff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #3730a3;
}

.submit-btn {
    background: var(--primary);
    color: white;
    padding: 12px;
    width: 100%;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    margin-top: 20px;
}

.submit-btn:hover {
    opacity: 0.9;
}
</style>

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="AdminDashboard.php">Dashboard</a>
        <a href="CollectorManagement.php">Collector Management</a>
        <a class="active" href="TaskManagement.php">Task Management</a>
        <a href="AddRecyclingCenter.php">Add Recycling Center</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <h1>Task Management</h1>
        <p>View and assign waste collection requests to collectors.</p>

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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
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
                        <td>
                            <?php if($request['status'] == 'PENDING'): ?>
                                <button class="btn btn-assign" onclick="openModal(<?= $request['id'] ?>)">
                                    Assign
                                </button>
                            <?php else: ?>
                                <button class="btn btn-assigned" disabled>Assigned</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL -->
<div id="assignModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Assign Collector</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        
        <form method="POST" id="assignForm">
            <input type="hidden" name="request_id" id="requestId">
            
            <h3 style="margin-bottom: 15px; color: #333; font-size: 16px;">Select Collector:</h3>
            
            <div class="collector-list">
                <?php foreach($collectors as $collector): ?>
                <label class="collector-item">
                    <input type="radio" name="collector_id" value="<?= $collector['id'] ?>" required>
                    <div class="collector-info">
                        <div>
                            <strong><?= htmlspecialchars($collector['full_name']) ?></strong><br>
                            <small style="color: #666;"><?= htmlspecialchars($collector['phone']) ?></small>
                        </div>
                        <span class="task-count"><?= $collector['assigned_tasks'] ?> tasks</span>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" name="assign_collector" class="submit-btn">
                Confirm Assignment
            </button>
        </form>
    </div>
</div>

<script>
function openModal(requestId) {
    document.getElementById('requestId').value = requestId;
    document.getElementById('assignModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('assignModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('assignModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Make entire collector item clickable
document.addEventListener('DOMContentLoaded', function() {
    const collectorItems = document.querySelectorAll('.collector-item');
    collectorItems.forEach(item => {
        item.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
        });
    });
});
</script>

<?php include '../../includes/footer.php'; ?>