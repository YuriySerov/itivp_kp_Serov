<?php
require __DIR__ . '/../config.php';

// Фильтры
$statusFilter = $_GET['status'] ?? '';
$scheduleFilter = $_GET['schedule'] ?? '';

// Формируем запрос
$sql = "SELECT * FROM plants WHERE 1";
$params = [];

if ($statusFilter) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

if ($scheduleFilter) {
    $sql .= " AND watering_schedule LIKE ?";
    $params[] = "%$scheduleFilter%";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$plants = $stmt->fetchAll();

include __DIR__ . '/../templates/header.php';
?>

<h1 class="mb-4">🌱 Учёт растений в саду</h1>

<div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="add.php" class="btn btn-success">Добавить растение</a>

    <form method="get" class="d-flex gap-2">
        <select name="status" class="form-select">
            <option value="">Все статусы</option>
            <option value="посажен" <?= $statusFilter === 'посажен' ? 'selected' : '' ?>>Посажен</option>
            <option value="взошёл" <?= $statusFilter === 'взошёл' ? 'selected' : '' ?>>Взошёл</option>
        </select>
        <input type="text" name="schedule" placeholder="График полива" value="<?= htmlspecialchars($scheduleFilter) ?>" class="form-control">
        <button type="submit" class="btn btn-primary">Фильтровать</button>
    </form>
</div>

<div class="table-responsive plant-table">
    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>Название</th>
                <th>Описание</th>
                <th>График полива</th>
                <th>Последний полив</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plants as $plant): ?>
                <tr>
                    <td><?= htmlspecialchars($plant['name']) ?></td>
                    <td><?= htmlspecialchars($plant['description']) ?></td>
                    <td><?= htmlspecialchars($plant['watering_schedule']) ?></td>
                    <td><?= htmlspecialchars($plant['last_watered']) ?></td>
                    <td>
                        <?php if ($plant['status'] === 'взошёл'): ?>
                            <span class="badge bg-success">🌱 Взошёл</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">🌱 Посажен</span>
                        <?php endif; ?>
                    </td>
                    <td class="d-flex gap-1 flex-wrap">
                        <a href="edit.php?id=<?= $plant['id'] ?>" class="btn btn-warning btn-sm" title="Редактировать">Редактировать</a>
                        <a href="delete.php?id=<?= $plant['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить растение?')" title="Удалить">Удалить</a>
                        <?php if ($plant['status'] === 'посажен'): ?>
                            <a href="update_status.php?id=<?= $plant['id'] ?>&status=взошёл" class="btn btn-success btn-sm" title="Отметить взошедшим">✅</a>
                        <?php else: ?>
                            <a href="update_status.php?id=<?= $plant['id'] ?>&status=посажен" class="btn btn-secondary btn-sm" title="Отметить посаженым">↩️</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>