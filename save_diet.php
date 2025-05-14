<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Yetkisiz erişim";
    exit;
}

$user_id = $_SESSION['user_id'];
$plan_name = $_POST['plan_name'] ?? '';
$foods = $_POST['foods'] ?? [];
$amounts = $_POST['amounts'] ?? [];

if (!$plan_name || empty($foods) || empty($amounts)) {
    http_response_code(400);
    echo "Plan adı, yemekler ve miktarlar gerekli.";
    exit;
}

$total_calories = $total_protein = $total_fat = $total_carbs = 0;

// Her yemeğin değerini hesapla
$placeholders = implode(',', array_fill(0, count($foods), '?'));
$types = str_repeat('i', count($foods));
$stmt = mysqli_prepare($conn, "SELECT id, calories, protein, fat, carbs FROM foods WHERE id IN ($placeholders)");
mysqli_stmt_bind_param($stmt, $types, ...$foods);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ID -> değer eşle
$nutrition_map = [];
while ($row = mysqli_fetch_assoc($result)) {
    $nutrition_map[$row['id']] = $row;
}

$units = $_POST['default_units'] ?? [];

// Besinleri işleyip toplamları hesapla
for ($i = 0; $i < count($foods); $i++) {
    $id = (int)$foods[$i];
    $amount = (float)$amounts[$i];
    $unit = $units[$i] ?? '100g';

    // Birime göre multiplier hesapla
    $multiplier = ($unit === 'adet') ? $amount : ($amount / 100.0);

    if (isset($nutrition_map[$id])) {
        $total_calories += $nutrition_map[$id]['calories'] * $multiplier;
        $total_protein  += $nutrition_map[$id]['protein'] * $multiplier;
        $total_fat      += $nutrition_map[$id]['fat'] * $multiplier;
        $total_carbs    += $nutrition_map[$id]['carbs'] * $multiplier;
    }
}

// Planı kaydet
$stmt = $conn->prepare("INSERT INTO diet_plans (user_id, name, total_calories, total_protein, total_fat, total_carbs, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("isdddd", $user_id, $plan_name, $total_calories, $total_protein, $total_fat, $total_carbs);
$stmt->execute();
$plan_id = $stmt->insert_id;
$stmt->close();

// Plan detaylarını kaydet
$stmt = $conn->prepare("INSERT INTO diet_plan_items (diet_plan_id, food_id, quantity) VALUES (?, ?, ?)");
for ($i = 0; $i < count($foods); $i++) {
    $stmt->bind_param("iii", $plan_id, $foods[$i], $amounts[$i]);
    $stmt->execute();
}

echo "Plan başarıyla kaydedildi.";
