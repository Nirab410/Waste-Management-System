<?php 
    session_start();
    if(!isset($_SESSION['user_id'])){
       header("Location: /Auth/login.php");
       exit();
    }

    $page_title = "EcoWaste | Collector Portal";
    include '../../includes/navbar.php'; 
    include '../../dbConnection.php';
    
    $stmt = $pdo->query("
        SELECT role
        FROM users
        WHERE id = '{$_SESSION['user_id']}'
    ");
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    if($role['role'] != 'COLLECTOR'){
        echo "<script>alert('Access denied. Collector role required.');</script>";
        exit();
    }
    
    $assignmentStmt = $pdo->query("
        SELECT 
            (SELECT full_name FROM users WHERE id = wr.resident_id ) AS full_name,
            wr.collection_location AS location
        FROM waste_requests wr
        WHERE wr.status='ASSIGNED' AND wr.id IN (
            SELECT request_id 
            FROM collector_assignments 
            WHERE collector_id = {$_SESSION['user_id']}
        )
    ");

    $row = $assignmentStmt->fetchAll(PDO::FETCH_ASSOC);
    // $residentName = $row['full_name'];
    // $location = $row['location'];

?>

<link rel="stylesheet" href="style.css">

<div class="container">
    <a href="CollectorDashborad.html" class="back-link">← Back To Dashboard</a>

    <div class="card">
        <h1 class="main-title">Task Details</h1>

        <div class="pickup-section">
            <h2 class="section-title">Pickup Request</h2>

            <?php foreach($row as $request): ?>
                 <div class="request-card">
                <div class="request-header">
                    <div>
                        <p><span class="label">Resident:</span> <?= htmlspecialchars($request['full_name']) ?></p>
                        <p><span class="label">Location:</span> <?= htmlspecialchars($request['location']) ?></p>
                    </div>
                    <button class="mark-button">Mark As Picked</button>
                </div>

                <div class="upload-section">
                    <label class="label">Upload Photo:</label><br>
                    <input type="file">
                </div>

                <div class="chat-section">
                    <input type="text" placeholder="Type a message...">
                    <button>Send</button>
                </div>
            </div>
            <?php endforeach; ?>  

    </div>
</div>

<?php include '../../includes/footer.php'; ?>