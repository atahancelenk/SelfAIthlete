<?php
include 'admin_auth.php';
include 'connection.php';

// Yeni yemek ekleme işlemi
if (isset($_POST['add_food'])) {
    $name = $_POST['name'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $fat = $_POST['fat'];
    $carbs = $_POST['carbs'];
    $unit = $_POST['default_unit'];

    $stmt = mysqli_prepare($conn, "INSERT INTO foods (name, calories, protein, fat, carbs, default_unit) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sdddds", $name, $calories, $protein, $fat, $carbs, $unit);
    mysqli_stmt_execute($stmt);
}

// Silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM foods WHERE id = $id");
}

// Güncelleme işlemi
if (isset($_POST['update_food'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $fat = $_POST['fat'];
    $carbs = $_POST['carbs'];
    $unit = isset($_POST['default_unit']) ? $_POST['default_unit'] : '';

    $stmt = mysqli_prepare($conn, "UPDATE foods SET name=?, calories=?, protein=?, fat=?, carbs=?, default_unit=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sddddsi", $name, $calories, $protein, $fat, $carbs, $unit, $id);
    mysqli_stmt_execute($stmt);
}

// Yemekleri çek
$foods = mysqli_query($conn, "SELECT * FROM foods");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <?php include 'admin_header.php'; ?>
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

        .form-control,
        .form-select {
            border-radius: 5px;
        }

        .btn-action {
            padding: 5px 10px;
            margin: 0 3px;
        }

        .nutrient-input {
            max-width: 130px;
        }

        .food-name-input {
            min-width: 150px;
        }

        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-5 fw-bold text-center text-primary">
                    <i class="bi bi-egg-fried"></i> Food Management Pannel
                </h1>
            </div>
        </div>

        <!-- Yeni Yemek Ekleme Formu -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Food</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-2">
                        <label for="name" class="form-label">Food Name</label>
                        <input type="text" class="form-control food-name-input" name="name" placeholder="Ex: Chicken Breast" required>
                    </div>
                    <div class="col-md-2">
                        <label for="default_unit" class="form-label">Birim</label>
                        <select class="form-select" name="default_unit" required>
                            <option value="100g">100g</option>
                            <option value="100ml">100ml</option>
                            <option value="porsiyon">Porsiyon</option>
                            <option value="dilim">Dilim</option>
                            <option value="adet">Adet</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="calories" class="form-label">Kalori (kcal)</label>
                        <input type="number" class="form-control nutrient-input" name="calories" placeholder="Kalori" step="0.1" required>
                    </div>
                    <div class="col-md-2">
                        <label for="protein" class="form-label">Protein (g)</label>
                        <input type="number" class="form-control nutrient-input" name="protein" placeholder="Protein" step="0.1" required>
                    </div>
                    <div class="col-md-2">
                        <label for="fat" class="form-label">Fat (g)</label>
                        <input type="number" class="form-control nutrient-input" name="fat" placeholder="Yağ" step="0.1" required>
                    </div>
                    <div class="col-md-2">
                        <label for="carbs" class="form-label">Carbs (g)</label>
                        <input type="number" class="form-control nutrient-input" name="carbs" placeholder="Karbonhidrat" step="0.1" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_food" class="btn btn-success">
                            <i class="bi bi-save"></i> Add Food
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mevcut Yemekler Tablosu -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Mevcut Besinler</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Besin Adi</th>
                                <th>Kalori (kcal)</th>
                                <th>Protein (g)</th>
                                <th>Yağ (g)</th>
                                <th>Karbonhidrat (g)</th>
                                <th>Birim</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($food = mysqli_fetch_assoc($foods)): ?>
                                <tr>
                                    <form method="post">
                                        <input type="hidden" name="id" value="<?= $food['id'] ?>">
                                        <td>
                                            <input type="text" class="form-control food-name-input" name="name" value="<?= htmlspecialchars($food['name']) ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control nutrient-input" name="calories" value="<?= $food['calories'] ?>" step="0.01">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control nutrient-input" name="protein" value="<?= $food['protein'] ?>" step="0.01">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control nutrient-input" name="fat" value="<?= $food['fat'] ?>" step="0.01">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control nutrient-input" name="carbs" value="<?= $food['carbs'] ?>" step="0.01">
                                        </td>
                                        <td>
                                            <select class="form-select" name="default_unit">
                                                <option value="100g" <?= $food['default_unit'] == '100g' ? 'selected' : '' ?>>100g</option>
                                                <option value="100ml" <?= $food['default_unit'] == '100ml' ? 'selected' : '' ?>>100ml</option>
                                                <option value="porsiyon" <?= $food['default_unit'] == 'porsiyon' ? 'selected' : '' ?>>Porsiyon</option>
                                                <option value="dilim" <?= $food['default_unit'] == 'dilim' ? 'selected' : '' ?>>Dilim</option>
                                                <option value="adet" <?= $food['default_unit'] == 'adet' ? 'selected' : '' ?>>Adet</option>
                                            </select>
                                        </td>
                                        <td class="action-buttons">
                                            <button type="submit" name="update_food" class="btn btn-primary btn-action" title="Güncelle">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <a href="?delete=<?= $food['id'] ?>" class="btn btn-danger btn-action" title="Sil" onclick="return confirm('Bu besini silmek istediğinize emin misiniz?')">
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
        // Input alanlarına otomatik odaklanma
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[name="name"]');
            if (firstInput) firstInput.focus();
        });
    </script>
</body>

</html>