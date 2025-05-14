<?php
include 'auth.php';
include 'connection.php';

if (!isset($_GET['id'])) {
    echo "Plan ID not found.";
    exit;
}

$plan_id = intval($_GET['id']);

// Plan bilgisi
$plan_query = $conn->prepare("SELECT name FROM exercise_plans WHERE id = ?");
$plan_query->bind_param("i", $plan_id);
$plan_query->execute();
$plan_result = $plan_query->get_result();
$plan = $plan_result->fetch_assoc();

// Egzersizleri getir
$stmt = $conn->prepare("SELECT exercises.* FROM exercise_plan_items 
                        JOIN exercises ON exercise_plan_items.exercise_id = exercises.id 
                        WHERE exercise_plan_items.exercise_plan_id = ?");
$stmt->bind_param("i", $plan_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($plan['name']) ?> - Plan Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 30px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            padding: 20px;
            background-color: white;
            margin-bottom: 20px;
        }
        .btn-back {
            margin-bottom: 25px;
        }
        img {
            max-width: 300px;
            height: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="exercises.php" class="btn btn-secondary btn-back"><i class="bi bi-arrow-left"></i> Back to My Plans</a>

    <h2 class="text-primary mb-4"><?= htmlspecialchars($plan['name']) ?> - Exercises</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <h4><?= htmlspecialchars($row['name']) ?></h4>
            <p><strong>Type:</strong> <?= htmlspecialchars($row['type']) ?></p>
            <p><strong>Target Muscles:</strong> <?= htmlspecialchars($row['muscles']) ?></p>
            <?php if (!empty($row['gif_path'])): ?>
                <img src="<?= htmlspecialchars($row['gif_path']) ?>" alt="Exercise GIF">
            <?php else: ?>
                <p class="text-muted">No instructional GIF available.</p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
