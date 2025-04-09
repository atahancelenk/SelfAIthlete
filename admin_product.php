<?php
    include 'connection.php';
    session_start();
    $admin_id = $_SESSION['admin_name'];

    if (!isset($admin_id)) {
        header('location:login.php');
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header('location:login.php');
    }

    //Adding products to database
    if (isset($_POST['add_product'])) {
        $product_name = mysqli_real_escape_string($conn, $_POST['name']);
        $product_price = mysqli_real_escape_string($conn, $_POST['price']);
        $product_detail = mysqli_real_escape_string($conn, $_POST['detail']);
        $image = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'img/' . $image;

        $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$product_name'") or die('query failed');
        
        if (mysqli_num_rows($select_product_name) > 0) {
            $message[] = 'Product name already exists!';
        } else {
            if ($image_size > 2000000) {
                $message[] = 'Image size is too large!';
            } else {
                $add_product_query = mysqli_query($conn, "INSERT INTO `products`(name, price, product_detail, image) VALUES('$product_name', '$product_price', '$product_detail', '$image')") or die('query failed');
                if ($add_product_query) {
                    move_uploaded_file($image_tmp_name, $image_folder);
                    $message[] = 'Product added successfully!';
                } else {
                    $message[] = 'Could not add product!';
                }
            }
        }
    }

    //Delete products to database
    if (isset($_GET['delete'])) {
        $delete_id = $_GET['delete'];
        $delete_query = mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
        if ($delete_query) {
            header('location:admin_product.php');
            $message[] = 'Product deleted successfully!';
        } else {
            header('location:admin_product.php');
            $message[] = 'Could not delete product!';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Admin Pannel</title>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    <?php
        if (isset($message)) {
            foreach ($message as $message) {
                echo 
                    '<div class="message">
                        <span>' . $message . '</span>
                        <i class="bi bi-x-circle" onclick="this.parentElement.remove();"></i>
                    </div>
                ';
            }
        }
    ?>
    <div class="line2"></div>
    <section class="add-products form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="input-field">
                <label>Product Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="input-field">
                <label>Product Price</label>
                <input type="text" name="price" required>
            </div>
            <div class="input-field">
                <label>Product Detail</label>
                <textarea name="detail" name="price" required></textarea>
            </div>
            <div class="input-field">
                <label>Product Image</label>
                <input type="file" name="image" accept="img/jpg, img/jpeg, img/png, img/webp" required>
            </div>
            <input type="submit" name="add_product" value="Add Product" class="btn">
        </form>
    </section>

    <div class="line3"></div>
    <div class="line4"></div>

    <div class="show-products">
        <div class="box-container">
            <?php
                $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
                if (mysqli_num_rows($select_products) > 0) {
                    while ($fetch_products = mysqli_fetch_assoc($select_products)) {
            ?>
            <div class="box">
                <img src="img/<?php echo $fetch_products['image']; ?>">
                <h4><?php echo $fetch_products['name']; ?></h4>
                <p>Price : $<?php echo $fetch_products['price']; ?>.00</p>
                <details><?php echo $fetch_products['product_detail']; ?></details>
                <a href="admin_product.php?edit=<?php echo $fetch_products['id']; ?>" class="edit">Edit</a>
                <a href="admin_product.php?delete=<?php echo $fetch_products['id']; ?>" class="delete" onclick="return confirm('Want to delete this product');">Delete</a>

            </div>
            <?php
                    }
                } else {
                    echo '<p class="empty">No products added yet!</p>';
                }
            ?>
        </div>
    </div>

    <script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>
