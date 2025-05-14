<?php
include 'auth.php';
include 'connection.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM exercises WHERE name LIKE ?";
$stmt = mysqli_prepare($conn, $query);
$searchTerm = "%$search%";
mysqli_stmt_bind_param($stmt, "s", $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$exercises = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercise Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <?php include 'header.php'; ?>
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

        .table th {
            background-color: #343a40;
            color: white;
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-5 fw-bold text-center text-success">
                    <i class="bi bi-heart-pulse"></i> Exercise Planner
                </h1>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" placeholder="Search for an exercise..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Exercise List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Target Muscles</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exercises as $exercise): ?>
                                <tr>
                                    <td><?= htmlspecialchars($exercise['name']) ?></td>
                                    <td><?= $exercise['type'] ?></td>
                                    <td><?= htmlspecialchars($exercise['muscles']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addExercise(<?= $exercise['id'] ?>, '<?= htmlspecialchars($exercise['name']) ?>', '<?= $exercise['type'] ?>', '<?= htmlspecialchars($exercise['muscles']) ?>')">
                                            <i class="bi bi-plus-circle"></i> Add
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle"></i> Selected Exercises</h5>
            </div>
            <div class="card-body">
                <form id="exerciseForm">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="selected-exercises">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label for="plan_name" class="form-label">Plan Name:</label>
                        <input type="text" name="plan_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Save Plan</button>
                    <p id="message" class="mt-2"></p>
                </form>
            </div>
        </div>

        <?php include 'my_exercises.php'; ?>
    </div>

    <script>
        function addExercise(id, name, type, muscles) {
            const table = document.getElementById("selected-exercises").querySelector("tbody");
            const row = table.insertRow();
            row.innerHTML = `
            <td><input type='hidden' name='exercises[]' value='${id}'>${name}</td>
            <td>${type}</td>
            <td><button onclick="this.parentElement.parentElement.remove();" class="btn btn-danger btn-sm"><i class='bi bi-x'></i></button></td>`;
        }

        document.getElementById("exerciseForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch("save_exercise_plan.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById("message").innerText = "Plan saved successfully.";
                })
                .catch(err => {
                    document.getElementById("message").innerText = "An error occurred: " + err;
                });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>