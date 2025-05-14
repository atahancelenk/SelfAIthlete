<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Yetkisiz erişim.");
}

$user_id = $_SESSION['user_id'];
$plan_id = intval($_GET['id'] ?? 0);

// Plan gerçekten bu kullanıcıya mı ait?
$check = $conn->prepare("SELECT id FROM diet_plans WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $plan_id, $user_id);
$check->execute();
$result = $check->get_result();
if ($result->num_rows === 0) {
    die("Bu plana erişiminiz yok.");
}

// İlişkili verileri sil
$del_items = $conn->prepare("DELETE FROM diet_plan_items WHERE diet_plan_id = ?");
$del_items->bind_param("i", $plan_id);
$del_items->execute();

// Ana planı sil
$del_plan = $conn->prepare("DELETE FROM diet_plans WHERE id = ? AND user_id = ?");
$del_plan->bind_param("ii", $plan_id, $user_id);
$del_plan->execute();

header("Location: diet.php");
exit;
