<?php 
    session_start();
    
    $page_title = "EcoWaste | Resident Portal";
    include '../../includes/navbar.php'; 
    include '../../dbConnection.php';

    // Check if user is logged in
    if(!isset($_SESSION['user_id'])){
       header("Location: /Auth/login.php");
       exit();
    }

    // Check if user is RESIDENT
    $stmt = $pdo->query("
        SELECT role
        FROM users
        WHERE id = '{$_SESSION['user_id']}'
    ");
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    if($role['role'] != 'RESIDENT'){
        echo "<script>alert('Access denied. RESIDENT role required.');</script>";
        header("Location: /Auth/login.php");
        exit();
    }
    
    // Get resident info
    $residentStmt = $pdo->prepare("SELECT full_name, phone FROM users WHERE id = ?");
    $residentStmt->execute([$_SESSION['user_id']]);
    $resident = $residentStmt->fetch(PDO::FETCH_ASSOC);
    
    // Get all zones
    $zonesStmt = $pdo->query("SELECT zone_name FROM zones ORDER BY zone_name");
    $zones = $zonesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Handle Pickup Request Submission
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_request'])){
        $request_type = $_POST['request_type'];
        $frequency = $_POST['frequency'];
        $pickup_date = $_POST['pickup_date'];
        $zone_name = $_POST['zone_name'];
        $collection_location = $_POST['collection_location'];
        $estimated_weight = $_POST['estimated_weight'];
        
        try {
            // Get zone_id from zone_name
            $zoneStmt = $pdo->prepare("SELECT id FROM zones WHERE zone_name = ?");
            $zoneStmt->execute([$zone_name]);
            $zone = $zoneStmt->fetch(PDO::FETCH_ASSOC);
            
            if($zone){
                $zone_id = $zone['id'];
                
                // Get recycling center id from zone_id
                $centerStmt = $pdo->prepare("SELECT id FROM recycling_centers WHERE zone_id = ? LIMIT 1");
                $centerStmt->execute([$zone_id]);
                $center = $centerStmt->fetch(PDO::FETCH_ASSOC);
                
                if($center){
                    $center_id = $center['id'];
                    
                    $insertRequest = $pdo->prepare("
                        INSERT INTO waste_requests (resident_id, request_type, frequency, pickup_date, collection_location, center_id, estimated_weight, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING', NOW())
                    ");
                    
                    if($insertRequest->execute([$_SESSION['user_id'], $request_type, $frequency, $pickup_date, $collection_location, $center_id, $estimated_weight])){
                        echo "<script>alert('Pickup request submitted successfully!');</script>";
                    } else {
                        echo "<script>alert('Failed to submit request.');</script>";
                    }
                } else {
                    echo "<script>alert('No recycling center found for selected zone.');</script>";
                }
            } else {
                echo "<script>alert('Invalid zone selected.');</script>";
            }
        } catch(Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    
    // Get latest waste request status
    $statusStmt = $pdo->prepare("
        SELECT status, pickup_date, collection_location 
        FROM waste_requests 
        WHERE resident_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $statusStmt->execute([$_SESSION['user_id']]);
    $latestRequest = $statusStmt->fetch(PDO::FETCH_ASSOC);
    
    // Get allocated collector info
    $collectorStmt = $pdo->prepare("
        SELECT u.full_name, u.phone 
        FROM collector_assignments ca
        JOIN users u ON ca.collector_id = u.id
        WHERE ca.request_id = (
            SELECT id FROM waste_requests 
            WHERE resident_id = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        )
        LIMIT 1
    ");
    $collectorStmt->execute([$_SESSION['user_id']]);
    $collector = $collectorStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resident Portal | EcoWaste</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Tahoma, sans-serif;
}

body {
    background: #f5f7f6;
    color: #333;
    min-height: 100vh;
}

/* ================= NAVBAR ================= */
header {
    background: #ffffff;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

nav a {
    margin-right: 25px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
}

/* Profile dropdown */
.profile {
    position: relative;
}

.profile input{
    display: none;
}

.profile img {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    cursor: pointer;
}

.profile-box {
    display: none;
    position: absolute;
    right: 0;
    top: 55px;
    background: #fff;
    padding: 15px;
    width: 200px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.profile input:checked ~ .profile-box {
    display: block;
}

.profile-box p {
    font-size: 14px;
    margin-bottom: 8px;
}

.logout {
    display: block;
    margin-top: 10px;
    background: #2fb463;
    color: #000;
    text-align: center;
    padding: 8px;
    border-radius: 6px;
    text-decoration: none;
}

/* ================= LAYOUT ================= */
.container {
    padding: 30px 40px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}

/* ================= CARD ================= */
.card {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
}

.card h3 {
    color: #2fb463;
    margin-bottom: 15px;
}

/* ================= INPUT ================= */
input, select, textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    background: #2fb463;
    border: none;
    padding: 10px;
    width: 100%;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

button:hover {
    opacity: 0.9;
}

/* ================= STATUS ================= */
.status-step {
    margin-bottom: 10px;
    padding-left: 10px;
    border-left: 4px solid #ccc;
}

.active {
    border-color: #2fb463;
    color: #2fb463;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="container">

    <!-- Pickup Location -->
    <div class="card">
        <h3>Pickup Location</h3>
        <label>Select Zone</label>
        <select name="zone_name" id="zoneSelect" required>
            <option value="">-- Select Zone --</option>
            <?php foreach($zones as $zone): ?>
                <option value="<?= htmlspecialchars($zone['zone_name']) ?>"><?= htmlspecialchars($zone['zone_name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label>Detailed Address</label>
        <input type="text" name="collection_location" id="locationInput" placeholder="Enter detailed pickup address" required>
    </div>

    <!-- Pickup Request -->
    <div class="card">
        <h3>Pickup Request</h3>
        <form method="POST">
            <label>Request Type</label>
            <select name="request_type" required>
                <option value="NORMAL">Normal</option>
                <option value="EMERGENCY">Emergency</option>
            </select>

            <label>Frequency</label>
            <select name="frequency" required>
                <option value="ONCE">Once</option>
                <option value="DAILY">Daily</option>
                <option value="WEEKLY">Weekly</option>
            </select>

            <label>Pickup Date</label>
            <input type="date" name="pickup_date" required>

            <label>Estimated Weight (kg)</label>
            <input type="number" name="estimated_weight" placeholder="Enter weight in kg" step="0.1" min="0" required>

            <input type="hidden" name="zone_name" id="hiddenZoneName" value="">
            <input type="hidden" name="collection_location" id="hiddenLocation" value="">

            <button type="submit" name="submit_request">Request Pickup</button>
        </form>
    </div>

    <!-- Live Status -->
    <div class="card">
        <h3>Live Status</h3>
        <?php if($latestRequest): ?>
            <p><strong>Pickup Date:</strong> <?= date('M d, Y', strtotime($latestRequest['pickup_date'])) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($latestRequest['collection_location']) ?></p>
            <br>
            
            <div class="status-step <?= $latestRequest['status'] == 'PENDING' ? 'active' : '' ?>">
                Pending
            </div>
            <div class="status-step <?= $latestRequest['status'] == 'ASSIGNED' ? 'active' : '' ?>">
                Assigned
            </div>
            <div class="status-step <?= $latestRequest['status'] == 'COLLECTED' ? 'active' : '' ?>">
                Collected
            </div>
            <div class="status-step <?= $latestRequest['status'] == 'COMPLETED' ? 'active' : '' ?>">
                Completed
            </div>
        <?php else: ?>
            <p>No active requests</p>
        <?php endif; ?>
    </div>

    <!-- Collector Info -->
    <div class="card">
        <h3>Allocated Collector</h3>
        <?php if($collector): ?>
            <p><strong>Status:</strong> Allocated</p>
            <p><strong>Name:</strong> <?= htmlspecialchars($collector['full_name']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($collector['phone']) ?></p>
        <?php else: ?>
            <p><strong>Status:</strong> Not Allocated Yet</p>
            <p>Waiting for collector assignment...</p>
        <?php endif; ?>
    </div>

</div>

<script>
// Sync zone and location from first card to second card
document.getElementById('zoneSelect').addEventListener('change', function() {
    document.getElementById('hiddenZoneName').value = this.value;
});

document.getElementById('locationInput').addEventListener('input', function() {
    document.getElementById('hiddenLocation').value = this.value;
});
</script>

<?php include '../../includes/footer.php'; ?>

</body>
</html>