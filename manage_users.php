<?php
include 'admin_auth.php';
include 'connection.php';

// Kullanıcı silme işlemi
if(isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('Sorgu hatası!');
    header('location:manage_users.php');
    exit();
}

// Kullanıcı bilgilerini güncelleme işlemi
if(isset($_POST['update_user'])) {
    $update_id = $_POST['user_id'];
    $update_name = $_POST['name'];
    $update_email = $_POST['email'];
    $update_type = $_POST['user_type'];
    
    mysqli_query($conn, "UPDATE `users` SET name = '$update_name', email = '$update_email', user_type = '$update_type' WHERE id = '$update_id'") or die('Sorgu hatası!');
    header('location:manage_users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <style>
        .action-btns { white-space: nowrap; }
        .table-responsive { overflow-x: auto; }
        .search-box { max-width: 300px; }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4"><i class="bi bi-people-fill"></i> Kullanıcı Yönetimi</h2>
                
                <!-- Kullanıcı Arama -->
                <div class="mb-3">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control search-box" placeholder="Kullanıcı ara..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button type="submit" class="btn btn-primary ms-2"><i class="bi bi-search"></i> Ara</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>İsim</th>
                                    <th>Email</th>
                                    <th>Kullanıcı Türü</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Arama sorgusu
                                $search = isset($_GET['search']) ? $_GET['search'] : '';
                                $query = "SELECT * FROM `users`";
                                
                                if(!empty($search)) {
                                    $query .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
                                }
                                
                                $query .= " ORDER BY created_at DESC";
                                $select_users = mysqli_query($conn, $query) or die('Sorgu hatası!');
                                
                                if(mysqli_num_rows($select_users) > 0) {
                                    while($fetch_users = mysqli_fetch_assoc($select_users)) {
                                ?>
                                <tr>
                                    <td><?php echo $fetch_users['id']; ?></td>
                                    <td><?php echo htmlspecialchars($fetch_users['name']); ?></td>
                                    <td><?php echo htmlspecialchars($fetch_users['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $fetch_users['user_type'] == 'admin' ? 'bg-success' : 'bg-primary'; ?>">
                                            <?php echo $fetch_users['user_type']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($fetch_users['created_at'])); ?></td>
                                    <td class="action-btns">
                                        <!-- Düzenle Butonu (Modal Tetikleyici) -->
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                data-bs-target="#editUser<?php echo $fetch_users['id']; ?>">
                                            <i class="bi bi-pencil-square"></i> Düzenle
                                        </button>
                                        
                                        <!-- Sil Butonu -->
                                        <a href="manage_users.php?delete=<?php echo $fetch_users['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                            <i class="bi bi-trash"></i> Sil
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- Düzenleme Modalı -->
                                <div class="modal fade" id="editUser<?php echo $fetch_users['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Kullanıcı Düzenle</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="user_id" value="<?php echo $fetch_users['id']; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">İsim</label>
                                                        <input type="text" name="name" class="form-control" 
                                                               value="<?php echo htmlspecialchars($fetch_users['name']); ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" 
                                                               value="<?php echo htmlspecialchars($fetch_users['email']); ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Kullanıcı Türü</label>
                                                        <select name="user_type" class="form-select" required>
                                                            <option value="user" <?php echo $fetch_users['user_type'] == 'user' ? 'selected' : ''; ?>>Normal Kullanıcı</option>
                                                            <option value="admin" <?php echo $fetch_users['user_type'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                    <button type="submit" name="update_user" class="btn btn-primary">Güncelle</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">Kullanıcı bulunamadı</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Silme işlemi onayı
        function confirmDelete() {
            return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');
        }
    </script>
</body>
</html>