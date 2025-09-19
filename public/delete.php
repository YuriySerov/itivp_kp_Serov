<?php
require __DIR__ . '/../config.php';

if (!isset($_GET['id'])) die("Не указан ID растения.");

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("DELETE FROM plants WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
