<?php
require_once '../init.php';
require_once '../config/config.php';

$errors = [];
$title = $detail = $deadline = $priority = '';

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
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, detail, deadline, priority) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $detail, $deadline, $priority]);
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Tambah Tugas</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
    <h2>Tambah Tugas</h2>
    <?php foreach ($errors as $e) echo "<p style='color:red;'>$e</p>"; ?>
    <form method="post">
        <input type="text" name="title" placeholder="Judul" value="<?= htmlspecialchars($title) ?>" required><br>
        <textarea name="detail" placeholder="Detail" required><?= htmlspecialchars($detail) ?></textarea><br>
        <input type="date" name="deadline" required><br>
        <label>Prioritas:</label>
        <select name="priority" required>
            <option value="1">Merah</option>
            <option value="2">Oranye</option>
            <option value="3">Kuning</option>
            <option value="4">Hijau</option>
        </select><br>
        <button type="submit">Simpan</button>
    </form>
    <a href="index.php">Kembali</a>
</body>
</html>
