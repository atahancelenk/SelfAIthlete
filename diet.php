<?php
include 'auth.php';
include 'connection.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM foods WHERE name LIKE ?";
$stmt = mysqli_prepare($conn, $query);
$searchTerm = "%$search%";
mysqli_stmt_bind_param($stmt, "s", $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$foods = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diet Plan</title>
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
                <h1 class="display-5 fw-bold text-center text-primary">
                    <i class="bi bi-egg-fried"></i> Diet Planner
                </h1>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" placeholder="Search for food..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Food List</h5>
            </div>
            <div class="card-body">
                <p style="font-style: italic; color: gray;">
                    The values shown are calculated per 100g / 100ml / 1 piece or serving.
                </p>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Calories</th>
                                <th>Protein</th>
                                <th>Fat</th>
                                <th>Carbs</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($foods as $food): ?>
                                <tr>
                                    <td><?= htmlspecialchars($food['name']) ?></td>
                                    <td><?= $food['calories'] ?></td>
                                    <td><?= $food['protein'] ?></td>
                                    <td><?= $food['fat'] ?></td>
                                    <td><?= $food['carbs'] ?></td>
                                    <td>
                                        <input type="number" id="amount_<?= $food['id'] ?>" placeholder="Amount" class="form-control form-control-sm">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addFood(<?= $food['id'] ?>, '<?= htmlspecialchars($food['name']) ?>', <?= $food['calories'] ?>, <?= $food['protein'] ?>, <?= $food['fat'] ?>, <?= $food['carbs'] ?>, '<?= $food['default_unit'] ?>')">
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
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle"></i> Selected Foods
            </div>
            <div class="card-body">
                <form id="dietForm">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="selected-foods">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Calories</th>
                                    <th>Protein</th>
                                    <th>Fat</th>
                                    <th>Carbs</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <p id="totals" class="fw-bold">Total: 0 kcal | 0g protein | 0g fat | 0g carbs</p>
                    <div class="mb-3">
                        <label for="plan_name" class="form-label">Plan Name:</label>
                        <input type="text" name="plan_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Plan</button>
                    <p id="message" class="mt-2"></p>
                </form>
            </div>
        </div>

        <?php include 'my_diets.php'; ?>
    </div>

    <script>
        function addFood(id, name, calories, protein, fat, carbs, defaultUnit) {
            const amountInput = document.getElementById(`amount_${id}`);
            const amount = parseFloat(amountInput.value);
            if (isNaN(amount) || amount <= 0) {
                alert("Please enter a valid amount.");
                return;
            }

            const multiplier = (defaultUnit === 'piece') ? amount : amount / 100.0;

            const adjCalories = (calories * multiplier).toFixed(1);
            const adjProtein = (protein * multiplier).toFixed(1);
            const adjFat = (fat * multiplier).toFixed(1);
            const adjCarbs = (carbs * multiplier).toFixed(1);

            function simplifyUnit(Unit) {
                if (Unit === '100g') return 'g';
                if (Unit === '100ml') return 'ml';
                return Unit;
            }

            const displayUnit = simplifyUnit(defaultUnit);

            const table = document.getElementById("selected-foods").querySelector("tbody");
            const row = table.insertRow();
            row.innerHTML = `
            <td><input type='hidden' name='foods[]' value='${id}'>
            <input type='hidden' name='amounts[]' value='${amount}'>
            <input type='hidden' name='default_units[]' value='${defaultUnit}'>
            ${name} (${amount} ${displayUnit})</td>
            <td>${adjCalories}</td>
            <td>${adjProtein}</td>
            <td>${adjFat}</td>
            <td>${adjCarbs}</td>
            <td><button onclick="this.parentElement.parentElement.remove(); updateTotals();" class="btn btn-danger btn-sm"><i class='bi bi-x'></i></button></td>`;

            updateTotals();
        }

        function updateTotals() {
            let calories = 0,
                protein = 0,
                fat = 0,
                carbs = 0;
            const rows = document.querySelectorAll("#selected-foods tbody tr");
            rows.forEach(row => {
                const cells = row.querySelectorAll("td");
                calories += parseFloat(cells[1].innerText);
                protein += parseFloat(cells[2].innerText);
                fat += parseFloat(cells[3].innerText);
                carbs += parseFloat(cells[4].innerText);
            });
            document.getElementById("totals").innerText =
                `Total: ${calories.toFixed(1)} kcal | ${protein.toFixed(1)}g protein | ${fat.toFixed(1)}g fat | ${carbs.toFixed(1)}g carbs`;
        }

        document.getElementById("dietForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch("save_diet.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById("message").innerText = "Plan saved successfully.";
                })
                .catch(err => {
                    document.getElementById("message").innerText = "Error occurred: " + err;
                });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>