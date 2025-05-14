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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Exercise Plans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 30px;
        }

        .card {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        table th {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2 class="text-primary">My Exercise Plans</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Plan Name</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plans as $plan): ?>
                            <tr>
                                <td><?= htmlspecialchars($plan['name']) ?></td>
                                <td><?= date('d.m.Y', strtotime($plan['created_at'])) ?></td>
                                <td><a class="btn btn-sm btn-primary" href="plan_detail.php?id=<?= $plan['id'] ?>"><i class="bi bi-eye"></i> Details</a></td>
                                <td><a class="btn btn-sm btn-danger" href="delete_exercise.php?id=<?= $plan['id'] ?>" onclick="return confirm('Are you sure you want to delete this exercise plan?')"><i class="bi bi-trash"></i> Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>