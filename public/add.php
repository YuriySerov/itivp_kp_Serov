<?php
require __DIR__ . '/../config.php';


$errors = [];
$name = '';
$description = '';
$watering_schedule = '';
$last_watered = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $watering_schedule = trim($_POST['watering_schedule']);
    $last_watered = $_POST['last_watered'] ?: null;

    if (!$name) {
        $errors[] = "Название обязательно.";
    } elseif (mb_strlen($name) > 255) {
        $errors[] = "Название не должно превышать 255 символов.";
    }

    if (mb_strlen($description) > 1000) {
        $errors[] = "Описание слишком длинное (максимум 1000 символов).";
    }

    $watering_schedule_lower = mb_strtolower(trim($watering_schedule));

    if (!$watering_schedule_lower) {
        $errors[] = "График полива обязателен.";
    } elseif (!preg_match('/^(раз в 1 день|раз в \d+ дн(я|ей)?|ежедневно)$/u', $watering_schedule_lower)) {
        $errors[] = "График полива должен быть в формате 'раз в 3 дня' или 'ежедневно'.";
    }



    if ($last_watered && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $last_watered)) {
        $errors[] = "Дата последнего полива некорректна.";
    }

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO plants (name, description, watering_schedule, last_watered, status) VALUES (?, ?, ?, ?, 'посажен')");
        $stmt->execute([$name, $description, $watering_schedule, $last_watered]);
        header("Location: index.php");
        exit;
    }
}

include __DIR__ . '/../templates/header.php';
?>

<h1 class="mb-4">➕ Добавить растение</h1>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="plant-form">
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Описание</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">График полива</label>
        <input type="text" name="watering_schedule" class="form-control"
            placeholder="например: раз в 3 дня или ежедневно"
            value="<?= htmlspecialchars($watering_schedule) ?>" required>

    </div>

    <div class="mb-3">
        <label class="form-label">Дата последнего полива</label>
        <input type="date" name="last_watered" class="form-control" value="<?= htmlspecialchars($last_watered) ?>">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="index.php" class="btn btn-secondary">Назад</a>
    </div>
</form>

<?php include __DIR__ . '/../templates/footer.php'; ?>