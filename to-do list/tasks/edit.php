<?php
require_once '../init.php';
require_once '../config/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$task = $stmt->fetch();
if (!$task) {
    echo "Tugas tidak ditemukan.";
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $detail = trim($_POST['detail']);
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];

    if (strlen($title) > 50) $errors[] = "Judul maksimal 50 karakter.";
    if (strlen($detail) > 250) $errors[] = "Detail maksimal 250 karakter.";

    if (empty($title) || empty($detail) || empty($deadline) || empty($priority)) {
        $errors[] = "Semua field harus diisi.";
    }

    if ($deadline < date('Y-m-d')) {
        $errors[] = "Deadline harus setelah hari ini.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE tasks SET title=?, detail=?, deadline=?, priority=? WHERE id=? AND user_id=?");
        $stmt->execute([$title, $detail, $deadline, $priority, $id, $_SESSION['user_id']]);
        header("Location: index.php");
        exit;
    }
} else {
    $title = $task['title'];
    $detail = $task['detail'];
    $deadline = $task['deadline'];
    $priority = $task['priority'];
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Tugas</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
    <h2>Edit Tugas</h2>
    <?php foreach ($errors as $e) echo "<p style='color:red;'>$e</p>"; ?>
    <form method="post">
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required><br>
        <textarea name="detail" required><?= htmlspecialchars($detail) ?></textarea><br>
        <input type="date" name="deadline" value="<?= $deadline ?>" required><br>
        <label>Prioritas:</label>
        <select name="priority" required>
            <option value="1" <?= $priority == 1 ? 'selected' : '' ?>>Merah</option>
            <option value="2" <?= $priority == 2 ? 'selected' : '' ?>>Oranye</option>
            <option value="3" <?= $priority == 3 ? 'selected' : '' ?>>Kuning</option>
            <option value="4" <?= $priority == 4 ? 'selected' : '' ?>>Hijau</option>
        </select><br>
        <button type="submit">Simpan Perubahan</button>
    </form>
    <a href="index.php">Kembali</a>
</body>
</html>
