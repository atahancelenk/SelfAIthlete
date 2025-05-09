<?php
include 'auth.php';
include 'connection.php';

$user_id = $_SESSION['user_id'];
$plan_id = intval($_GET['id']);

// Plan sahibi kontrolü
$check = mysqli_prepare($conn, "SELECT * FROM diet_plans WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($check, "ii", $plan_id, $user_id);
mysqli_stmt_execute($check);
$result = mysqli_stmt_get_result($check);
if (mysqli_num_rows($result) === 0) {
    die("Bu plana erişiminiz yok.");
}
$plan = mysqli_fetch_assoc($result);

// Plan yemeklerini çek
$query = "SELECT f.name, f.calories, f.protein, f.fat, f.carbs, f.default_unit, dpi.quantity FROM diet_plan_items dpi
              JOIN foods f ON dpi.food_id = f.id
              WHERE dpi.diet_plan_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $plan_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);
$items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);

function simplifyUnit($unit)
{
    if ($unit === '100g') return 'g';
    if ($unit === '100ml') return 'ml';
    return $unit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Plan Detayı</title>
    <style>
        body {
            font-family: Arial;
            padding: 30px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #888;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        a.button {
            padding: 6px 12px;
            background: #555;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <h1><?= htmlspecialchars($plan['name']) ?> - Detayları</h1>
    <p><strong>Oluşturulma:</strong> <?= date('d.m.Y H:i', strtotime($plan['created_at'])) ?></p>
    <p><strong>Toplam:</strong> <?= $plan['total_calories'] ?> kcal | <?= $plan['total_protein'] ?>g protein | <?= $plan['total_fat'] ?>g yağ | <?= $plan['total_carbs'] ?>g karbonhidrat</p>

    <h2>İçerdiği Yemekler</h2>
    <table>
        <tr>
            <th>Yemek</th>
            <th>Kalori</th>
            <th>Protein</th>
            <th>Yağ</th>
            <th>Karbonhidrat</th>
        </tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] . ' ' . simplifyUnit($item['default_unit']) ?>)</td>
                <td><?= $item['calories'] ?> kcal</td>
                <td><?= $item['protein'] ?> g</td>
                <td><?= $item['fat'] ?> g</td>
                <td><?= $item['carbs'] ?> g</td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="diet.php" class="button">← Geri Dön</a>
</body>

</html>