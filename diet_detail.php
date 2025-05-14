<?php
include 'auth.php';
include 'connection.php';

$user_id = $_SESSION['user_id'];
$plan_id = intval($_GET['id']);

// Check if the user owns this plan
$check = mysqli_prepare($conn, "SELECT * FROM diet_plans WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($check, "ii", $plan_id, $user_id);
mysqli_stmt_execute($check);
$result = mysqli_stmt_get_result($check);
if (mysqli_num_rows($result) === 0) {
    die("You do not have access to this plan.");
}
$plan = mysqli_fetch_assoc($result);

// Get plan items
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
    <title>Plan Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 30px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        table th {
            background-color: #343a40;
            color: white;
            text-align: center;
        }

        table td {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-body">
                <h2 class="text-primary fw-bold"><?= htmlspecialchars($plan['name']) ?> - Details</h2>
                <p><strong>📅 Created on:</strong> <?= date('d.m.Y H:i', strtotime($plan['created_at'])) ?></p>
                <p class="fw-bold">🔥 Total: <?= $plan['total_calories'] ?> kcal | 🥩 <?= $plan['total_protein'] ?>g protein | 🧈 <?= $plan['total_fat'] ?>g fat | 🍞 <?= $plan['total_carbs'] ?>g carbs</p>

                <h4 class="mt-4">🍽️ Included Foods</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Food</th>
                                <th>Calories</th>
                                <th>Protein</th>
                                <th>Fat</th>
                                <th>Carbohydrates</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] . ' ' . simplifyUnit($item['default_unit']) ?>)</td>
                                    <td><?= $item['calories'] ?> kcal</td>
                                    <td><?= $item['protein'] ?> g</td>
                                    <td><?= $item['fat'] ?> g</td>
                                    <td><?= $item['carbs'] ?> g</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="diet.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>

</body>

</html>