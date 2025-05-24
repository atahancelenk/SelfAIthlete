<?php
include 'auth.php';
include 'connection.php';
$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <?php include 'header.php'; ?>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .profile-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .form-divider {
            margin: 25px 0;
            border-top: 1px solid #eee;
        }

        .body-fat-result {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
        }

        .body-fat-value {
            font-weight: bold;
            color: #2c3e50;
        }

        .calculate-btn {
            background-color: #2ecc71;
            margin-top: 10px;
        }

        .calculate-btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <h2>Profile Information</h2>
        <form action="update_profile.php" method="POST">
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?= htmlspecialchars($user['age']) ?>">
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender">
                    <option value="Male" <?= $user['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $user['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $user['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div class="form-group">
                <label for="password">New Password (leave blank to keep current):</label>
                <input type="password" id="password" name="password" placeholder="Enter new password">
            </div>

            <div class="form-divider"></div>

            <div class="form-group">
                <label for="height">Height (cm):</label>
                <input type="number" step="0.01" id="height" name="height" value="<?= htmlspecialchars($user['height']) ?>">
            </div>

            <div class="form-group">
                <label for="weight">Weight (kg):</label>
                <input type="number" step="0.01" id="weight" name="weight" value="<?= htmlspecialchars($user['weight']) ?>">
            </div>

            <div class="form-group">
                <label for="waist">Waist (cm):</label>
                <input type="number" step="0.01" id="waist" name="waist" value="<?= htmlspecialchars($user['waist']) ?>">
            </div>

            <div class="form-group">
                <label for="neck">Neck (cm):</label>
                <input type="number" step="0.01" id="neck" name="neck" value="<?= htmlspecialchars($user['neck']) ?>">
            </div>

            <?php if ($user['gender'] == 'Female') : ?>
                <div class="form-group">
                    <label for="hip">Hip (cm):</label>
                    <input type="number" step="0.01" id="hip" name="hip" value="<?= htmlspecialchars($user['hip']) ?>">
                </div>
            <?php endif; ?>

            <!-- Body Fat Calculation Section -->
            <div class="form-group">
                <button type="button" class="calculate-btn" onclick="calculateBodyFat()">Calculate Body Fat Percentage</button>
                <div class="body-fat-result" id="bodyFatResult" style="<?= ($user['body_fat_percentage'] == 0) ? 'display:none;' : '' ?>">
                    Current Body Fat Percentage: <span class="body-fat-value"><?= htmlspecialchars($user['body_fat_percentage']) ?>%</span>
                </div>
            </div>

            <button type="submit">Update Profile</button>
        </form>
    </div>

    <script>
        function calculateBodyFat() {
            const gender = document.getElementById('gender').value;
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);
            const waist = parseFloat(document.getElementById('waist').value);
            const neck = parseFloat(document.getElementById('neck').value);
            const hip = document.getElementById('hip') ? parseFloat(document.getElementById('hip').value) : null;

            // Validate inputs
            if (!height || !weight || !waist || !neck || (gender === 'Female' && !hip)) {
                alert('Please fill in all required measurements to calculate body fat percentage.');
                return;
            }

            // US Navy Method calculation
            let bodyFat;
            if (gender === 'Male') {
                bodyFat = 495 / (1.0324 - 0.19077 * Math.log10(waist - neck) + 0.15456 * Math.log10(height)) - 450;
            } else if (gender === 'Female') {
                bodyFat = 495 / (1.29579 - 0.35004 * Math.log10(waist + hip - neck) + 0.22100 * Math.log10(height)) - 450;
            } else {
                alert('Body fat calculation is only available for Male and Female genders.');
                return;
            }

            // Round to 2 decimal places
            bodyFat = Math.round(bodyFat * 100) / 100;

            // Display result
            const resultDiv = document.getElementById('bodyFatResult');
            resultDiv.style.display = 'block';
            resultDiv.querySelector('.body-fat-value').textContent = bodyFat + '%';

            // Update the hidden field if it exists (or you can add one to the form)
            if (document.getElementById('body_fat_percentage')) {
                document.getElementById('body_fat_percentage').value = bodyFat;
            }
        }
    </script>
</body>

</html>