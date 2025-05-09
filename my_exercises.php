<?php
include 'auth.php';
include 'connection.php';

$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM exercise_plans WHERE user_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$plans = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Egzersiz Planlarım</title>
</head>

<body>
    <h1>Egzersiz Planlarım</h1>
    <table border="1" cellpadding="8">
        <tr>
            <th>Plan İsmi</th>
            <th>Tarih</th>
            <th>Detay</th>
        </tr>
        <?php foreach ($plans as $plan): ?>
            <tr>
                <td><?= htmlspecialchars($plan['name']) ?></td>
                <td><?= date('d.m.Y', strtotime($plan['created_at'])) ?></td>
                <td><a href="exercise_detail.php?id=<?= $plan['id'] ?>">Detay</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>