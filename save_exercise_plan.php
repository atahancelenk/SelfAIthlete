<?php
include 'auth.php';
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Yetkisiz erişim";
    exit;
}

$user_id = $_SESSION['user_id'];
$plan_name = $_POST['plan_name'] ?? '';
$exercises = $_POST['exercises'] ?? [];

if (!$plan_name || empty($exercises)) {
    http_response_code(400);
    echo "Plan adı ve egzersizler gerekli";
    exit;
}

// Egzersiz planını ekle
$stmt = mysqli_prepare($conn, "INSERT INTO exercise_plans (user_id, name, created_at) VALUES (?, ?, NOW())");
mysqli_stmt_bind_param($stmt, "is", $user_id, $plan_name);
mysqli_stmt_execute($stmt);
$plan_id = mysqli_insert_id($conn);

// Egzersizleri ekle
$stmt = mysqli_prepare($conn, "INSERT INTO exercise_plan_items (exercise_plan_id, exercise_id) VALUES (?, ?)");
foreach ($exercises as $exercise_id) {
    mysqli_stmt_bind_param($stmt, "ii", $plan_id, $exercise_id);
    mysqli_stmt_execute($stmt);
}

echo "Egzersiz planı başarıyla kaydedildi.";
