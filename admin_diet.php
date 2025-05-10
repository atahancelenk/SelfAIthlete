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
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Yemek Yönetimi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #888;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"],
        input[type="number"] {
            padding: 5px;
            width: 100px;
        }

        button {
            padding: 6px 12px;
        }
    </style>
</head>

<body>
    <?php include 'admin_header.php'; ?>
    <h1>Yemek Yönetimi (Admin Panel)</h1>

    <form method="POST">
        <input type="text" name="name" placeholder="Besin Adı" required>

        <label for="default_unit">Birim:</label>
        <select name="default_unit" id="default_unit">
            <option value="100g">100g</option>
            <option value="100ml">100ml</option>
            <option value="porsiyon">Porsiyon</option>
            <option value="dilim">Dilim</option>
            <option value="adet">Adet</option>
        </select>

        <input type="number" name="calories" placeholder="Kalori (100 birimde)" step="0.1" required>
        <input type="number" name="protein" placeholder="Protein (g)" step="0.1" required>
        <input type="number" name="fat" placeholder="Yağ (g)" step="0.1" required>
        <input type="number" name="carbs" placeholder="Karbonhidrat (g)" step="0.1" required>
        <button type="submit" name="add_food">Ekle</button>
    </form>


    <h2>Mevcut Yemekler</h2>
    <table>
        <tr>
            <th>İsim</th>
            <th>Kalori</th>
            <th>Protein</th>
            <th>Yağ</th>
            <th>Karbonhidrat</th>
            <th>Birim</th>
            <th>İşlemler</th>
        </tr>
        <?php while ($food = mysqli_fetch_assoc($foods)): ?>
            <tr>
                <form method="post">
                    <input type="hidden" name="id" value="<?= $food['id'] ?>">
                    <td><input type="text" name="name" value="<?= htmlspecialchars($food['name']) ?>"></td>
                    <td><input type="number" name="calories" value="<?= $food['calories'] ?>" step="0.01"></td>
                    <td><input type="number" name="protein" value="<?= $food['protein'] ?>" step="0.01"></td>
                    <td><input type="number" name="fat" value="<?= $food['fat'] ?>" step="0.01"></td>
                    <td><input type="number" name="carbs" value="<?= $food['carbs'] ?>" step="0.01"></td>
                    <td>
                        <select name="default_unit">
                            <option value="100g" <?= $food['default_unit'] == '100g' ? 'selected' : '' ?>>g</option>
                            <option value="100ml" <?= $food['default_unit'] == '100ml' ? 'selected' : '' ?>>ml</option>
                            <option value="porsiyon" <?= $food['default_unit'] == 'porsiyon' ? 'selected' : '' ?>>Porsiyon</option>
                            <option value="dilim" <?= $food['default_unit'] == 'dilim' ? 'selected' : '' ?>>Dilim</option>
                            <option value="adet" <?= $food['default_unit'] == 'adet' ? 'selected' : '' ?>>Adet</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="id" value="<?= $food['id'] ?>">
                        <button type="submit" name="update_food">Güncelle</button>
                        <a href="?delete=<?= $food['id'] ?>" onclick="return confirm('Silmek istediğine emin misin?')">Sil</a>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>