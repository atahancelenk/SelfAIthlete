<?php
include 'admin_auth.php';
include 'connection.php';

// Yeni egzersiz ekleme işlemi
if (isset($_POST['add_exercise'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $muscles = implode(",", $_POST['muscles']); // çoklu seçim
    $gif_path = $_POST['gif_path'];

    $stmt = mysqli_prepare($conn, "INSERT INTO exercises (name, type, muscles, gif_path) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $type, $muscles, $gif_path);
    mysqli_stmt_execute($stmt);
}

// Silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM exercises WHERE id = $id");
}

// Güncelleme işlemi
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

// Egzersizleri çek
$exercises = mysqli_query($conn, "SELECT * FROM exercises");

// Kas grubu filtreleri için liste
$muscleGroups = ["Göğüs", "Sırt", "Bacak", "Omuz", "Karın", "Kol"];
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Egzersiz Yönetimi</title>
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
        input[type="number"],
        select,
        .checkboxes {
            padding: 5px;
            margin: 2px;
        }

        button {
            padding: 6px 12px;
        }
    </style>
</head>

<body>
    <?php include 'admin_header.php'; ?>
    <h1>Egzersiz Yönetimi (Admin Panel)</h1>

    <form method="post">
        <h2>Yeni Egzersiz Ekle</h2>
        <input type="text" name="name" placeholder="İsim" required>
        <input type="text" name="type" placeholder="Tür" required>
        <div class="checkboxes">
            <?php foreach ($muscleGroups as $muscle): ?>
                <label><input type="checkbox" name="muscles[]" value="<?= $muscle ?>"> <?= $muscle ?></label>
            <?php endforeach; ?>
        </div>
        <input type="text" name="gif_path" placeholder="Örn: gifs/push_up.gif" required>
        <button type="submit" name="add_exercise">Ekle</button>
    </form>

    <h2>Mevcut Egzersizler</h2>
    <table>
        <tr>
            <th>İsim</th>
            <th>Tür</th>
            <th>Kaslar</th>
            <th>Gif</th>
            <th>İşlemler</th>
        </tr>
        <?php while ($exercise = mysqli_fetch_assoc($exercises)): ?>
            <tr>
                <form method="post">
                    <td><input type="text" name="name" value="<?= htmlspecialchars($exercise['name']) ?>"></td>
                    <td><input type="text" name="type" value="<?= $exercise['type'] ?>"></td>
                    <td>
                        <?php
                        $selectedMuscles = explode(",", $exercise['muscles']);
                        foreach ($muscleGroups as $muscle): ?>
                            <label><input type="checkbox" name="muscles[]" value="<?= $muscle ?>" <?= in_array($muscle, $selectedMuscles) ? 'checked' : '' ?>> <?= $muscle ?></label><br>
                        <?php endforeach; ?>
                    </td>
                    <td><input type="text" name="gif_path" value="<?= htmlspecialchars($exercise['gif_path']) ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?= $exercise['id'] ?>">
                        <button type="submit" name="update_exercise">Güncelle</button>
                        <a href="?delete=<?= $exercise['id'] ?>" onclick="return confirm('Silmek istediğine emin misin?')">Sil</a>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>