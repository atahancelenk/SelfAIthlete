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
    <title>Egzersiz Planlayıcı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
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

        input[type="text"] {
            padding: 5px;
            width: 250px;
        }

        button {
            padding: 6px 12px;
            background-color: #2e8b57;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #226644;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <h1>Egzersiz Planlayıcı</h1>

    <form method="get">
        <input type="text" name="search" placeholder="Egzersiz ara..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Ara</button>
    </form>

    <h2>Egzersiz Listesi</h2>
    <table>
        <tr>
            <th>İsim</th>
            <th>Tür</th>
            <th>Çalışan Kaslar</th>
            <th>Süre (dk)</th>
            <th></th>
        </tr>
        <?php foreach ($exercises as $exercise): ?>
            <tr>
                <td><?= htmlspecialchars($exercise['name']) ?></td>
                <td><?= $exercise['type'] ?></td>
                <td><?= $exercise['muscles'] ?></td>
                <td><button type="button" onclick="addExercise(<?= $exercise['id'] ?>, '<?= htmlspecialchars($exercise['name']) ?>', '<?= $exercise['type'] ?>', '<?= htmlspecialchars($exercise['muscles']) ?>')">Ekle</button></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Seçilen Egzersizler</h2>
    <form id="exerciseForm">
        <table id="selected-exercises">
            <tr>
                <th>İsim</th>
                <th>Tür</th>
                <th>Süre (dk)</th>
                <th></th>
            </tr>
        </table>

        <label for="plan_name">Plan İsmi:</label>
        <input type="text" name="plan_name" required>
        <button type="submit">Planı Kaydet</button>
        <p id="message"></p>
    </form>

    <script>
        function addExercise(id, name, type) {
            const table = document.getElementById("selected-exercises");
            const row = table.insertRow();
            row.innerHTML = `
            <td><input type='hidden' name='exercises[]' value='${id}'>${name}</td>
            <td>${type}</td>
            <td><button onclick="this.parentElement.parentElement.remove();">Sil</button></td>
        `;
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
                    document.getElementById("message").innerText = "Plan başarıyla kaydedildi.";
                })
                .catch(err => {
                    document.getElementById("message").innerText = "Hata oluştu: " + err;
                });
        });
    </script>
    <?php include 'my_exercises.php'; ?>
</body>

</html>