<?php
session_start();
include '../../dbConnection.php';
include '../../includes/navbar.php';
$page_title="Collector Management | EcoWaste";

/* ----------------- AUTH CHECK ----------------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: /Auth/login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
if ($stmt->fetchColumn() !== 'ADMIN') {
    die("Access denied");
}

/* ----------------- HANDLE ACTIONS ----------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* TOGGLE STATUS */
    if (isset($_POST['toggle_status'])) {
        $pdo->prepare("
            UPDATE users 
            SET is_active = ? 
            WHERE id = ?
        ")->execute([$_POST['new_status'], $_POST['collector_id']]);
    }

    /* DELETE COLLECTOR */
    if (isset($_POST['delete_collector'])) {
        $pdo->prepare("
            DELETE FROM users 
            WHERE id = ? AND role = 'COLLECTOR'
        ")->execute([$_POST['collector_id']]);
    }

    /* UPDATE COLLECTOR */
    if (isset($_POST['update_collector'])) {
        $pdo->prepare("
            UPDATE users 
            SET full_name = ?, phone = ?, address = ?
            WHERE id = ?
        ")->execute([
            $_POST['full_name'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['collector_id']
        ]);
    }
}

/* ----------------- FETCH COLLECTORS ----------------- */
$collectors = $pdo->query("
    SELECT id, full_name, phone, address, is_active
    FROM users
    WHERE role = 'COLLECTOR'
")->fetchAll(PDO::FETCH_ASSOC);
?>



<link rel="stylesheet" href="CollectorManagement.css">


<div class="container">

<a href="AdminDashboard.php" class="back-admin-btn">← Back to Admin Dashboard</a>

<div class="title">Collectors List</div>
<div class="subtitle">Manage and monitor waste collectors</div>

<div class="table-wrapper">
<table>
<thead>
<tr>
    <th>COLLECTOR</th>
    <th>PHONE</th>
    <th>ACTION</th>
    <th>STATUS</th>
</tr>
</thead>

<tbody>
<?php foreach ($collectors as $c): ?>
<tr>
<td>
    <div class="collector">
        <div class="avatar blue">
            <?= strtoupper(substr($c['full_name'],0,2)) ?>
        </div>
        <div>
            <div class="name"><?= htmlspecialchars($c['full_name']) ?></div>
            <div class="id">ID : <?= $c['id'] ?></div>
        </div>
    </div>
</td>

<td><?= htmlspecialchars($c['phone']) ?></td>

<td>
    <!-- UPDATE -->
    <button class="action-btn update"
        onclick="openModal(
            '<?= $c['id'] ?>',
            '<?= htmlspecialchars($c['full_name'], ENT_QUOTES) ?>',
            '<?= htmlspecialchars($c['phone'], ENT_QUOTES) ?>',
            '<?= htmlspecialchars($c['address'], ENT_QUOTES) ?>'
        )">
        Update
    </button>

    <!-- DELETE -->
    <form method="POST" style="display:inline"
          onsubmit="return confirm('Delete this collector?')">
        <input type="hidden" name="collector_id" value="<?= $c['id'] ?>">
        <button class="action-btn delete" name="delete_collector">Delete</button>
    </form>
</td>

<td>
    <!-- STATUS -->
    <form method="POST" style="display:inline"
          onsubmit="return confirm('Change status?')">
        <input type="hidden" name="collector_id" value="<?= $c['id'] ?>">
        <input type="hidden" name="new_status" value="<?= $c['is_active'] ? 0 : 1 ?>">
        <button class="status <?= $c['is_active'] ? 'active' : 'inactive' ?>"
                name="toggle_status">
            <?= $c['is_active'] ? 'Active' : 'Inactive' ?>
        </button>
    </form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

<!-- UPDATE MODAL -->
<div id="updateModal" class="modal">
<div class="modal-box">

<a href="#" class="modal-close" onclick="closeModal()">✕</a>
<div class="modal-title">Update Collector</div>

<form method="POST">
<input type="hidden" name="collector_id" id="cid">

<div class="form-group">
<label>Full Name</label>
<input type="text" name="full_name" id="cname" required>
</div>

<div class="form-group">
<label>Phone</label>
<input type="text" name="phone" id="cphone" required>
</div>

<div class="form-group">
<label>Address</label>
<input type="text" name="address" id="caddress">
</div>

<div class="modal-actions">
<a href="#" class="modal-cancel" onclick="closeModal()">Cancel</a>
<button class="modal-confirm" name="update_collector">Update</button>
</div>
</form>

</div>
</div>
<?php include "../../includes/footer.php"; ?>

<script>
function openModal(id, name, phone, address) {
    document.getElementById('cid').value = id;
    document.getElementById('cname').value = name;
    document.getElementById('cphone').value = phone;
    document.getElementById('caddress').value = address;
    document.getElementById('updateModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('updateModal').style.display = 'none';
}
</script>


