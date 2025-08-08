<?php
require_once '../init.php';
require_once '../config/config.php';

// Ambil user_id
$user_id = $_SESSION['user_id'];

// Cek dan update status tugas hangus
$today = date('Y-m-d');
$pdo->prepare("UPDATE tasks SET is_expired = 1 WHERE deadline < ? AND is_done = 0 AND user_id = ?")
    ->execute([$today, $user_id]);

// Filter
$filter_priority = $_GET['priority'] ?? '';
$sort_deadline = isset($_GET['sort']) && $_GET['sort'] === 'deadline';

// Query dasar
$query = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$user_id];

if ($filter_priority !== '') {
    $query .= " AND priority = ?";
    $params[] = $filter_priority;
}

$query .= " ORDER BY ";
$query .= $sort_deadline ? "deadline ASC" : "priority ASC, deadline ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>To-Do List</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
    <a href="../auth/logout.php">Logout</a>
    <h3 class="tdl2">To-Do List</h3>
    <div class="add77">
         <a href="add.php">+ Tambah Tugas</a>
    </div>

   

    <form class="tdl89" method="get">
        <label>Filter Prioritas:</label>
        <select name="priority" onchange="this.form.submit()">
            <option value="">Semua</option>
            <option value="1" <?= $filter_priority == 1 ? 'selected' : '' ?>>Merah</option>
            <option value="2" <?= $filter_priority == 2 ? 'selected' : '' ?>>Oranye</option>
            <option value="3" <?= $filter_priority == 3 ? 'selected' : '' ?>>Kuning</option>
            <option value="4" <?= $filter_priority == 4 ? 'selected' : '' ?>>Hijau</option>
            
        </select>
        <label><input type="checkbox" name="sort" value="deadline" <?= $sort_deadline ? 'checked' : '' ?> onchange="this.form.submit()"> Urut Deadline Terdekat</label>
    </form>

    <div class="task-list">
        <?php if (count($tasks) === 0): ?>
            <p>Tidak ada tugas.</p>
        <?php endif; ?>

        <?php foreach ($tasks as $task): ?>
            <div class="task-box priority-<?= $task['priority'] ?>">
                <h4><?= htmlspecialchars($task['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($task['detail'])) ?></p>
                <p><strong>Deadline:</strong> <?= date('d M Y H:i', strtotime($task['deadline'])) ?></p>
                <p><strong>Dibuat:</strong> <?= date('d M Y H:i', strtotime($task['created_at'])) ?></p>

                <div class="task-status">
                    <?php if ($task['is_done']): ?>
                        <p class="status-done">✅ Selesai</p>
                    <?php elseif ($task['is_expired']): ?>
                        <p class="status-expired">❌ Hangus</p>
                    <?php else: ?>
                        <p class="status-progress">⏳ Sedang Berjalan</p>
                    <?php endif; ?>
                </div>

                <div class="task-actions">
                    <?php if (!$task['is_done'] && !$task['is_expired']): ?>
                        <a href="done.php?id=<?= $task['id'] ?>">Tandai Selesai</a>
                    <?php endif; ?>
                    <a href="edit.php?id=<?= $task['id'] ?>">Edit</a>
                    <a href="delete.php?id=<?= $task['id'] ?>" onclick="return confirm('Hapus tugas ini?')">Hapus</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>