<?php
session_start();
include 'connection.php';

$user_id = $_SESSION['user_id'];
$daily = $_POST['daily_goal'];
$weekly = $_POST['weekly_goal'];

// Silip yeniden ekle
mysqli_query($conn, "DELETE FROM calorie_goals WHERE user_id = $user_id");

$query = "INSERT INTO calorie_goals (user_id, daily_goal, weekly_goal) VALUES ($user_id, $daily, $weekly)";
mysqli_query($conn, $query);

header("Location: home.php");
