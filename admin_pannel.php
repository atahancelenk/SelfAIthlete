<?php
include 'admin_auth.php';
include 'connection.php';

// Hata raporlama (sadece development ortamında)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <?php include 'admin_header.php'; ?>
    <title>Admin Panel - Dashboard</title>
    <style>
        .stat-card {
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="line4"></div>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Dashboard Overview</h2>
                
                <!-- Sistem Durum Göstergeleri -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <?php
                                $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
                                $num_of_users = mysqli_num_rows($select_users);
                                ?>
                                <h3 class="card-title"><?php echo $num_of_users; ?></h3>
                                <p class="card-text">Normal Users</p>
                                <i class="bi bi-people-fill float-end" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <?php
                                $select_admins = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'admin'") or die('query failed');
                                $num_of_admins = mysqli_num_rows($select_admins);
                                ?>
                                <h3 class="card-title"><?php echo $num_of_admins; ?></h3>
                                <p class="card-text">Admin Users</p>
                                <i class="bi bi-shield-lock float-end" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <?php
                                $select_total = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
                                $num_of_total = mysqli_num_rows($select_total);
                                ?>
                                <h3 class="card-title"><?php echo $num_of_total; ?></h3>
                                <p class="card-text">Total Users</p>
                                <i class="bi bi-person-badge float-end" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Son Aktiviteler ve Hızlı Erişim -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0">Recent Activities</h5>
                            </div>
                            <div class="card-body recient-activity">
                                <ul class="list-group">
                                    <?php
                                    // Son 5 kullanıcıyı göster
                                    $recent_users = mysqli_query($conn, "SELECT * FROM `users` ORDER BY id DESC LIMIT 5");
                                    while($user = mysqli_fetch_assoc($recent_users)){
                                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>New user registered: <strong>'.$user['name'].'</strong></span>
                                                <span class="badge bg-secondary">'.date('d M Y', strtotime($user['created_at'])).'</span>
                                              </li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="manage_users.php" class="btn btn-outline-primary btn-block">
                                        <i class="bi bi-people"></i> Manage Users
                                    </a>
                                    <a href="add_admin.php" class="btn btn-outline-success btn-block">
                                        <i class="bi bi-person-plus"></i> Add Admin
                                    </a>
                                    <a href="settings.php" class="btn btn-outline-secondary btn-block">
                                        <i class="bi bi-gear"></i> System Settings
                                    </a>
                                    <a href="backup.php" class="btn btn-outline-warning btn-block">
                                        <i class="bi bi-database"></i> Backup Database
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Basit bir grafik örneği (isteğe bağlı)
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('userChart').getContext('2d');
            const userChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Normal Users', 'Admin Users', 'Total Users'],
                    datasets: [{
                        label: 'User Statistics',
                        data: [<?php echo $num_of_users; ?>, <?php echo $num_of_admins; ?>, <?php echo $num_of_total; ?>],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>