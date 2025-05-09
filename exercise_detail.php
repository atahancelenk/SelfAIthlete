<?php
include 'auth.php';
include 'connection.php';

if (!isset($_GET['id'])) {
    echo "Egzersiz ID bulunamadı.";
    exit;
}

$exercise_id = intval($_GET['id']);

$query = "SELECT * FROM exercises WHERE id = $exercise_id";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo "<h1>" . htmlspecialchars($row['name']) . "</h1>";
    echo "<p><strong>Türü:</strong> " . htmlspecialchars($row['type']) . "</p>";
    echo "<p><strong>Çalışan Kaslar:</strong> " . htmlspecialchars($row['muscles']) . "</p>";

    // GIF gösterimi
    if (!empty($row['gif_path'])) {
        echo "<div style='margin-top:20px;'><strong>Nasıl Yapılır:</strong><br>";
        echo "<img src='" . htmlspecialchars($row['gif_path']) . "' alt='Egzersiz Gif' style='max-width:100%; height:auto;'></div>";
    } else {
        echo "<p>Bu egzersiz için açıklayıcı bir GIF bulunmamaktadır.</p>";
    }
} else {
    echo "Egzersiz bulunamadı.";
}
