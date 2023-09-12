<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
    $select_admin->execute([$name]);

    if ($select_admin->rowCount() > 0) {
        $message[] = 'username already exist!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'confirm password not matched!';
        } else {
            $insert_admin = $conn->prepare("INSERT INTO `admins`(name, password) VALUES(?,?)");
            $insert_admin->execute([$name, $cpass]);
            $message[] = 'new admin registered successfully!';
        }
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="show-products">

        <h1 class="heading">Manage Properties</h1>

        <div class="box-container">

            <?php
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="box">
                        <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
                        <div class="name">
                            <?= $fetch_products['name']; ?>
                        </div>
                        <div class="price">$<span>
                                <?= $fetch_products['price']; ?>
                            </span>/-</div>
                        <div class="details"><span>
                                <?= $fetch_products['details']; ?>
                            </span></div>
                        <div class="flex-btn">
                            <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
                            <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn"
                                onclick="return confirm('delete this product?');">delete</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="empty">no products added yet!</p>';
            }
            ?>

        </div>

    </section>

    <script src="../js/admin_script.js"></script>

</body>

</html>