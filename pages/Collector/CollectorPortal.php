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
    
    // Delete assignments where accepted_at is not today
    $deleteOldAssignments = $pdo->prepare("
        DELETE FROM collector_assignments 
        WHERE collector_id = ? 
        AND DATE(accepted_at) != CURDATE()
    ");
    $deleteOldAssignments->execute([$_SESSION['user_id']]);

    // Handle "Mark As Picked" button click
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_picked']) && isset($_POST['request_id'])){
        $request_id = $_POST['request_id'];
        
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Update waste_requests status to COLLECTED
            $updateStmt = $pdo->prepare("
                UPDATE waste_requests 
                SET status = 'COLLECTED' 
                WHERE id = ? AND status = 'ASSIGNED'
            ");
            $updateStmt->execute([$request_id]);
            
            // Update collector_assignments accepted_at
            $updateCollectorAssignment = $pdo->prepare(
                "UPDATE collector_assignments
                SET accepted_at = NOW()
                WHERE request_id = ? AND collector_id = ?"
            );
            $updateCollectorAssignment->execute([$request_id, $_SESSION['user_id']]);
            
            // Commit transaction
            $pdo->commit();
            
            echo "<script>alert('Request marked as collected successfully!'); window.location.href=window.location.href;</script>";
            
        } catch(Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            echo "<script>alert('Error updating status: " . addslashes($e->getMessage()) . "');</script>";
        }
    }

    // Handle mark as completed
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_completed']) && isset($_POST['request_id'])){
        $request_id = $_POST['request_id'];
        
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Update waste_requests status to COMPLETED
            $updateStmt = $pdo->prepare("
                UPDATE waste_requests 
                SET status = 'COMPLETED' 
                WHERE id = ? AND status = 'COLLECTED'
            ");
            $updateStmt->execute([$request_id]);
            
            // Delete from collector_assignments
            $deleteStmt = $pdo->prepare("
                DELETE FROM collector_assignments 
                WHERE request_id = ? AND collector_id = ?
            ");
            $deleteStmt->execute([$request_id, $_SESSION['user_id']]);
            
            // Commit transaction
            $pdo->commit();
            
            echo "<script>alert('Task marked as completed successfully!'); window.location.href=window.location.href;</script>";
            
        } catch(Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }

    // Handle send message and upload photo together
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message']) && isset($_POST['request_id'])){
        $request_id = $_POST['request_id'];
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        $file = isset($_FILES['proof_image']) ? $_FILES['proof_image'] : null;
        
        $message_sent = false;
        $photo_uploaded = false;
        
        try {
            // Handle message if provided
            if(!empty($message)){
                $insertMessage = $pdo->prepare("
                    INSERT INTO chat_messages (request_id, sender_id, message, sent_at) 
                    VALUES (?, ?, ?, NOW())
                ");
                
                if($insertMessage->execute([$request_id, $_SESSION['user_id'], $message])){
                    $message_sent = true;
                }
            }
            
            // Handle photo upload if provided
            if($file && $file['error'] == 0){
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                if(in_array($file['type'], $allowed_types) && $file['size'] <= $max_size){
                    // Create upload directory if it doesn't exist
                    $upload_dir = '../../uploads/collection_proofs/';
                    if(!is_dir($upload_dir)){
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_filename = 'proof_' . $request_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    // Move uploaded file
                    if(move_uploaded_file($file['tmp_name'], $upload_path)){
                        // Store in database
                        $insert_stmt = $pdo->prepare("
                            INSERT INTO collection_proofs (request_id, image_path, uploaded_at) 
                            VALUES (?, ?, NOW())
                        ");
                        
                        if($insert_stmt->execute([$request_id, $upload_path])){
                            $photo_uploaded = true;
                        }
                    }
                }
            }
            
            // Show appropriate success message
            if($message_sent && $photo_uploaded){
                echo "<script>alert('Message and photo sent successfully!'); window.location.href=window.location.href;</script>";
            } elseif($message_sent){
                echo "<script>alert('Message sent successfully!'); window.location.href=window.location.href;</script>";
            } elseif($photo_uploaded){
                echo "<script>alert('Photo uploaded successfully!'); window.location.href=window.location.href;</script>";
            } else {
                echo "<script>alert('Please enter a message or upload a photo.');</script>";
            }
            
        } catch(Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    
    $assignmentStmt = $pdo->query("
        SELECT 
            wr.id AS request_id,
            (SELECT full_name FROM users WHERE id = wr.resident_id) AS full_name,
            wr.collection_location AS location,
            wr.status
        FROM waste_requests wr
        WHERE wr.status IN ('ASSIGNED', 'COLLECTED', 'COMPLETED') AND wr.id IN (
            SELECT request_id 
            FROM collector_assignments 
            WHERE collector_id = {$_SESSION['user_id']}
        )
    ");

    $row = $assignmentStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="style.css">

<div class="container">
    <a href="CollectorDashboard.html" class="back-link">← Back To Dashboard</a>

    <div class="card">
        <h1 class="main-title">Task Details</h1>

        <div class="pickup-section">
            <h2 class="section-title">Pickup Request</h2>

            <?php foreach($row as $request): ?>
            <div class="request-card">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                    
                    <div class="request-header">
                        <div>
                            <p><span class="label">Resident:</span> <?= htmlspecialchars($request['full_name']) ?></p>
                            <p><span class="label">Location:</span> <?= htmlspecialchars($request['location']) ?></p>
                        </div>
                        
                        <?php if($request['status'] == 'ASSIGNED'): ?>
                            <button type="submit" name="mark_picked" class="mark-button">Mark As Picked</button>
                        <?php elseif($request['status'] == 'COLLECTED'): ?>
                            <button type="submit" name="mark_completed" class="mark-button collected">Collected</button>

                        <?php endif; ?>
                    </div>

                    <div class="upload-section">
                        <label class="label">Upload Photo:</label><br>
                        <input type="file" name="proof_image" accept="image/jpeg,image/png,image/jpg">
                    </div>

                    <div class="chat-section">
                        <input type="text" name="message" placeholder="Type a message...">
                        <button type="submit" name="send_message">Send</button>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>  
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>