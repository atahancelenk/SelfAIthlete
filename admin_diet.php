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

        $stmt = mysqli_prepare($conn, "INSERT INTO foods (name, calories, protein, fat, carbs) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sdddd", $name, $calories, $protein, $fat, $carbs);
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

        $stmt = mysqli_prepare($conn, "UPDATE foods SET name=?, calories=?, protein=?, fat=?, carbs=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sddddi", $name, $calories, $protein, $fat, $carbs, $id);
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
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #888; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        form { margin-top: 20px; }
        input[type="text"], input[type="number"] { padding: 5px; width: 100px; }
        button { padding: 6px 12px; }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    <h1>Yemek Yönetimi (Admin Panel)</h1>

    <form method="post">
        <h2>Yeni Yemek Ekle</h2>
        <input type="text" name="name" placeholder="İsim" required>
        <input type="number" name="calories" placeholder="Kalori" step="0.01" required>
        <input type="number" name="protein" placeholder="Protein" step="0.01" required>
        <input type="number" name="fat" placeholder="Yağ" step="0.01" required>
        <input type="number" name="carbs" placeholder="Karbonhidrat" step="0.01" required>
        <button type="submit" name="add_food">Ekle</button>
    </form>

    <h2>Mevcut Yemekler</h2>
    <table>
        <tr>
            <th>İsim</th><th>Kalori</th><th>Protein</th><th>Yağ</th><th>Karbonhidrat</th><th>İşlemler</th>
        </tr>
        <?php while($food = mysqli_fetch_assoc($foods)): ?>
        <tr>
            <form method="post">
                <td><input type="text" name="name" value="<?=htmlspecialchars($food['name'])?>"></td>
                <td><input type="number" name="calories" value="<?=$food['calories']?>" step="0.01"></td>
                <td><input type="number" name="protein" value="<?=$food['protein']?>" step="0.01"></td>
                <td><input type="number" name="fat" value="<?=$food['fat']?>" step="0.01"></td>
                <td><input type="number" name="carbs" value="<?=$food['carbs']?>" step="0.01"></td>
                <td>
                    <input type="hidden" name="id" value="<?=$food['id']?>">
                    <button type="submit" name="update_food">Güncelle</button>
                    <a href="?delete=<?=$food['id']?>" onclick="return confirm('Silmek istediğine emin misin?')">Sil</a>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
    <script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>
