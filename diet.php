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
    <title>Diyet Plan</title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }

        input[type="text"] {
            padding: 5px;
            width: 300px;
        }

        button {
            padding: 6px 12px;
            margin-left: 5px;
        }

        #message {
            color: green;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <h1>Diyet Planlayıcı</h1>

    <form method="get">
        <input type="text" name="search" placeholder="Yemek ara..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Ara</button>
    </form>

    <h2>Yemek Listesi</h2>
    <p style="font-style: italic; color: gray; margin-top: -10px;">
        Gösterilen değerler 100g / 100ml / 1 adet veya porsiyon üzerinden hesaplanmıştır.
    </p>

    <table>
        <tr>
            <th>İsim</th>
            <th>Kalori</th>
            <th>Protein</th>
            <th>Yağ</th>
            <th>Karbonhidrat</th>
            <th></th>
        </tr>
        <?php foreach ($foods as $food): ?>
            <tr>
                <td><?= htmlspecialchars($food['name']) ?></td>
                <td><?= $food['calories'] ?></td>
                <td><?= $food['protein'] ?></td>
                <td><?= $food['fat'] ?></td>
                <td><?= $food['carbs'] ?></td>
                <td><?= htmlspecialchars($food['default_unit']) ?></td>
                <td>
                    <input type="number" id="amount_<?= $food['id'] ?>" placeholder="Miktar" style="width:60px">
                    <button type="button" onclick="addFood(<?= $food['id'] ?>, '<?= htmlspecialchars($food['name']) ?>', <?= $food['calories'] ?>, <?= $food['protein'] ?>, <?= $food['fat'] ?>, <?= $food['carbs'] ?>, '<?= $food['default_unit'] ?>')">Ekle</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Seçilen Yemekler</h2>
    <form id="dietForm">
        <table id="selected-foods">
            <tr>
                <th>İsim</th>
                <th>Kalori</th>
                <th>Protein</th>
                <th>Yağ</th>
                <th>Karbonhidrat</th>
                <th>Birim</th>
            </tr>
        </table>

        <p id="totals">Toplam: 0 kcal | 0g protein | 0g yağ | 0g karbonhidrat</p>

        <label for="plan_name">Plan İsmi:</label>
        <input type="text" name="plan_name" required>
        <button type="submit">Planı Kaydet</button>
        <p id="message"></p>
    </form>

    <script>
        function addFood(id, name, calories, protein, fat, carbs, defaultUnit) {
            const amountInput = document.getElementById(`amount_${id}`);
            const amount = parseFloat(amountInput.value);
            if (isNaN(amount) || amount <= 0) {
                alert("Lütfen geçerli bir miktar girin.");
                return;
            }

            const multiplier = (defaultUnit === 'adet') ? amount : amount / 100.0;

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

            const table = document.getElementById("selected-foods");
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
        <td><button onclick="this.parentElement.parentElement.remove(); updateTotals();">Sil</button></td>`;

            updateTotals();
        }

        function updateTotals() {
            let calories = 0,
                protein = 0,
                fat = 0,
                carbs = 0;
            const rows = document.querySelectorAll("#selected-foods tr:not(:first-child)");
            rows.forEach(row => {
                const cells = row.querySelectorAll("td");
                calories += parseFloat(cells[1].innerText);
                protein += parseFloat(cells[2].innerText);
                fat += parseFloat(cells[3].innerText);
                carbs += parseFloat(cells[4].innerText);
            });
            document.getElementById("totals").innerText =
                `Toplam: ${calories.toFixed(1)} kcal | ${protein.toFixed(1)}g protein | ${fat.toFixed(1)}g yağ | ${carbs.toFixed(1)}g karbonhidrat`;
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
                    document.getElementById("message").innerText = "Plan başarıyla kaydedildi.";
                })
                .catch(err => {
                    document.getElementById("message").innerText = "Hata oluştu: " + err;
                });
        });
    </script>
    <?php include 'my_diets.php'; ?>
</body>

</html>