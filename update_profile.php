<?php
session_start();
include 'connection.php';
$user_id = $_SESSION['user_id'];

$age = intval($_POST['age']);
$gender = $_POST['gender'];
$email = $_POST['email'];
$password = $_POST['password'];

$height = floatval($_POST['height']);
$weight = floatval($_POST['weight']);
$waist = floatval($_POST['waist']);
$neck = floatval($_POST['neck']);
$hip = isset($_POST['hip']) ? floatval($_POST['hip']) : null;

// Calculate body fat % using US Navy Method
function log10_safe($val)
{
    return ($val > 0) ? log10($val) : 0;
}

if ($gender === 'Male') {
    $bodyFat = 495 / (1.0324 - 0.19077 * log10_safe($waist - $neck) + 0.15456 * log10_safe($height)) - 450;
} elseif ($gender === 'Female' && $hip !== null) {
    $bodyFat = 495 / (1.29579 - 0.35004 * log10_safe($waist + $hip - $neck) + 0.22100 * log10_safe($height)) - 450;
} else {
    $bodyFat = null;
}

if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET age=?, gender=?, email=?, password=?, height=?, weight=?, waist=?, neck=?, hip=?, body_fat_percentage=? WHERE id=?");
    $stmt->bind_param("isssddddddi", $age, $gender, $email, $hashed_password, $height, $weight, $waist, $neck, $hip, $bodyFat, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET age=?, gender=?, email=?, height=?, weight=?, waist=?, neck=?, hip=?, body_fat_percentage=? WHERE id=?");
    $stmt->bind_param("issddddddi", $age, $gender, $email, $height, $weight, $waist, $neck, $hip, $bodyFat, $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Status</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .message-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }

        .success {
            color: #27ae60;
        }

        .error {
            color: #e74c3c;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>
    <div class="message-box">
        <?php
        if ($stmt->execute()) {
            echo '<h2 class="success">Profile Updated Successfully</h2>';
            echo '<p>Your changes have been saved.</p>';
        } else {
            echo '<h2 class="error">Update Failed</h2>';
            echo '<p>' . htmlspecialchars($stmt->error) . '</p>';
        }
        ?>
        <a href="profile.php" class="btn">Back to Profile</a>
    </div>
</body>

</html>