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
        WHERE user_id = '{$_SESSION['user_id']}'
    ");
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    if($role['role'] != 'COLLECTOR'){
        echo "<script>alert('Access denied. Collector role required.');</script>";
        exit();
    }
    
    $assignmentsStmt = $pdo->query("
        SELECT full_name
         ( SELECT resident_id
        
            (select request_id from collector_assignments where collector_id = '{$_SESSION['user_id']}')
            from collector_assignments
         )
        FROM users
       
    ");
    

?>

<link rel="stylesheet" href="style.css">

<div class="container">
    <a href="CollectorDashborad.html" class="back-link">← Back To Dashboard</a>

    <div class="card">
        <h1 class="main-title">Task Details</h1>

        <div class="pickup-section">
            <h2 class="section-title">Pickup Request</h2>

            <div class="request-card">
                <div class="request-header">
                    <div>
                        <p><span class="label">Resident:</span> Rahim</p>
                        <p><span class="label">Location:</span> Sector 5, Road 13, Uttara</p>
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

            <div class="request-card">
                <div class="request-header">
                    <div>
                        <p><span class="label">Resident:</span> Karim</p>
                        <p><span class="label">Location:</span> Mirpur 10</p>
                    </div>
                    <button class="mark-button green">Marked As Picked</button>
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
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>