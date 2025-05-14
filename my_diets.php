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
    <title>My Plans</title>
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
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-journal-text"></i> My Diet Plans</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Plan Name</th>
                                <th>Calories</th>
                                <th>Protein</th>
                                <th>Fat</th>
                                <th>Carbohydrates</th>
                                <th>Date</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plans as $plan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($plan['name']) ?></td>
                                    <td><?= $plan['total_calories'] ?> kcal</td>
                                    <td><?= $plan['total_protein'] ?> g</td>
                                    <td><?= $plan['total_fat'] ?> g</td>
                                    <td><?= $plan['total_carbs'] ?> g</td>
                                    <td><?= date('d.m.Y', strtotime($plan['created_at'])) ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="diet_detail.php?id=<?= $plan['id'] ?>"><i class="bi bi-eye"></i> Details</a>
                                        <a class="btn btn-sm btn-danger" href="delete_diet.php?id=<?= $plan['id'] ?>" onclick="return confirm('Are you sure you want to delete this plan?')"><i class="bi bi-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</html>