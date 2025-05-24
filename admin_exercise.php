<?php
include 'admin_auth.php';
include 'connection.php';

// Add new exercise
if (isset($_POST['add_exercise'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $muscles = implode(",", $_POST['muscles']); // multiple selection
    $gif_path = $_POST['gif_path'];

    $stmt = mysqli_prepare($conn, "INSERT INTO exercises (name, type, muscles, gif_path) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $type, $muscles, $gif_path);
    mysqli_stmt_execute($stmt);
}

// Delete exercise
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM exercises WHERE id = $id");
}

// Update exercise
if (isset($_POST['update_exercise'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $muscles = implode(",", $_POST['muscles']);
    $gif_path = $_POST['gif_path'];

    $stmt = mysqli_prepare($conn, "UPDATE exercises SET name=?, type=?, muscles=?, gif_path=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $name, $type, $muscles, $gif_path, $id);
    mysqli_stmt_execute($stmt);
}

// Get exercises
$exercises = mysqli_query($conn, "SELECT * FROM exercises");

// Muscle group filters
$muscleGroups = ["Chest", "Back", "Leg", "Shoulder", "Abs", "Arm"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercise Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background-color: #343a40;
            color: white;
            text-align: center;
            vertical-align: middle;
        }
        .table td {
            vertical-align: middle;
        }
        .form-control, .form-select {
            border-radius: 5px;
        }
        .btn-action {
            padding: 5px 10px;
            margin: 0 3px;
        }
        .exercise-input {
            min-width: 150px;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .muscle-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .muscle-checkbox {
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .gif-preview {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-5 fw-bold text-center text-primary">
                    <i class="bi bi-activity"></i> Exercise Management Panel
                </h1>
            </div>
        </div>

        <!-- Add New Exercise Form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Exercise</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Exercise Name</label>
                        <input type="text" class="form-control exercise-input" name="name" placeholder="e.g. Push Up" required>
                    </div>
                    <div class="col-md-4">
                        <label for="type" class="form-label">Exercise Type</label>
                        <input type="text" class="form-control exercise-input" name="type" placeholder="e.g. Strength, Cardio" required>
                    </div>
                    <div class="col-md-4">
                        <label for="gif_path" class="form-label">GIF Path</label>
                        <input type="text" class="form-control exercise-input" name="gif_path" placeholder="e.g. gifs/push_up.gif" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Target Muscles</label>
                        <div class="muscle-checkboxes">
                            <?php foreach ($muscleGroups as $muscle): ?>
                                <div class="muscle-checkbox form-check">
                                    <input class="form-check-input" type="checkbox" name="muscles[]" value="<?= $muscle ?>" id="muscle-<?= strtolower($muscle) ?>">
                                    <label class="form-check-label" for="muscle-<?= strtolower($muscle) ?>"><?= $muscle ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_exercise" class="btn btn-success">
                            <i class="bi bi-save"></i> Add Exercise
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Exercises Table -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Current Exercises</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Exercise Name</th>
                                <th>Type</th>
                                <th>Target Muscles</th>
                                <th>GIF Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($exercise = mysqli_fetch_assoc($exercises)): 
                                $selectedMuscles = explode(",", $exercise['muscles']);
                            ?>
                                <tr>
                                    <form method="post">
                                        <input type="hidden" name="id" value="<?= $exercise['id'] ?>">
                                        <td>
                                            <input type="text" class="form-control exercise-input" name="name" value="<?= htmlspecialchars($exercise['name']) ?>">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control exercise-input" name="type" value="<?= htmlspecialchars($exercise['type']) ?>">
                                        </td>
                                        <td>
                                            <div class="muscle-checkboxes">
                                                <?php foreach ($muscleGroups as $muscle): ?>
                                                    <div class="muscle-checkbox form-check">
                                                        <input class="form-check-input" type="checkbox" name="muscles[]" 
                                                               value="<?= $muscle ?>" id="muscle-<?= $exercise['id'] ?>-<?= strtolower($muscle) ?>"
                                                               <?= in_array($muscle, $selectedMuscles) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="muscle-<?= $exercise['id'] ?>-<?= strtolower($muscle) ?>"><?= $muscle ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control exercise-input" name="gif_path" value="<?= htmlspecialchars($exercise['gif_path']) ?>">
                                            <?php if (!empty($exercise['gif_path'])): ?>
                                                <img src="<?= htmlspecialchars($exercise['gif_path']) ?>" class="gif-preview mt-2" alt="Exercise GIF">
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-buttons">
                                            <button type="submit" name="update_exercise" class="btn btn-primary btn-action" title="Update">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <a href="?delete=<?= $exercise['id'] ?>" class="btn btn-danger btn-action" title="Delete" 
                                               onclick="return confirm('Are you sure you want to delete this exercise?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[name="name"]');
            if (firstInput) firstInput.focus();
        });
    </script>
</body>
</html>