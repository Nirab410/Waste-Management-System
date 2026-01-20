<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Auth/login.php");
    exit();
}

include '../../dbConnection.php';
$page_title = "EcoWaste | Collector Dashboard";
include '../../includes/navbar.php';

/*
  Remaining tasks logic:
  - assigned to this collector
  - request status = 'ASSIGNED'
*/
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS remaining_tasks
    FROM collector_assignments ca
    JOIN waste_requests wr ON wr.id = ca.request_id
    WHERE ca.collector_id = :collector_id
      AND wr.status = 'ASSIGNED'
");

$stmt->execute([
    ':collector_id' => $_SESSION['user_id']
]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);
$remainingTasks = $result['remaining_tasks'];
?>

<link rel="stylesheet" href="collectorDashboard.css">
<body>
<div class="container">

    <h1 class="page-title">Waste Collector Dashboard</h1>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <h2 class="welcome-text">
            Welcome, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Collector') ?>
        </h2>

        <div class="task-count">
            <?= $remainingTasks ?>
        </div>

        <p class="task-label">Total Remaining Tasks</p>
    </div>

    <!-- Go to Daily Tasks -->
    <a href="CollectorPortal.php">
        <button class="daily-tasks-button">
            <span>Go to Daily Tasks</span>
            <svg class="arrow-icon" fill="currentColor" viewBox="0 0 24 24">
                <path d="M5 12h14M13 5l7 7-7 7"/>
            </svg>
        </button>
    </a>

</div>
</body>
</html>

<?php include '../../includes/footer.php'; ?>
