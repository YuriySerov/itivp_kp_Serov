<?php
require __DIR__ . '/../config.php';

if (!isset($_GET['id'], $_GET['status'])) die("Не указан ID или статус растения.");

$id = (int)$_GET['id'];
$status = ($_GET['status'] === 'взошёл') ? 'взошёл' : 'посажен';

$stmt = $pdo->prepare("UPDATE plants SET status=? WHERE id=?");
$stmt->execute([$status, $id]);

header("Location: index.php");
exit;
