<?php
    include 'auth.php';
    include 'connection.php';

    $user_id = $_SESSION['user_id'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM diet_plans WHERE user_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $plans = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Planlarım</title>
    <style>
        body { font-family: Arial; padding: 30px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table, th, td { border: 1px solid #888; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        a.button { padding: 6px 12px; background: #2e8b57; color: #fff; text-decoration: none; border-radius: 4px; }
        a.button:hover { background: #226644; }
    </style>
</head>
<body>
    <h1>Diyet Planlarım</h1>

    <table>
        <tr>
            <th>Plan İsmi</th>
            <th>Kalori</th>
            <th>Protein</th>
            <th>Yağ</th>
            <th>Karbonhidrat</th>
            <th>Tarih</th>
            <th>Detay</th>
        </tr>
        <?php foreach($plans as $plan): ?>
        <tr>
            <td><?= htmlspecialchars($plan['name']) ?></td>
            <td><?= $plan['total_calories'] ?> kcal</td>
            <td><?= $plan['total_protein'] ?> g</td>
            <td><?= $plan['total_fat'] ?> g</td>
            <td><?= $plan['total_carbs'] ?> g</td>
            <td><?= date('d.m.Y', strtotime($plan['created_at'])) ?></td>
            <td><a class="button" href="diet_detail.php?id=<?= $plan['id'] ?>">Detaylar</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
