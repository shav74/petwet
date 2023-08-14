<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}
;

if (isset($_POST['add_product'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);

   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/' . $image_01;

   $image_02 = $_FILES['image_02']['name'];
   $image_02 = filter_var($image_02, FILTER_SANITIZE_STRING);
   $image_size_02 = $_FILES['image_02']['size'];
   $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
   $image_folder_02 = '../uploaded_img/' . $image_02;

   $image_03 = $_FILES['image_03']['name'];
   $image_03 = filter_var($image_03, FILTER_SANITIZE_STRING);
   $image_size_03 = $_FILES['image_03']['size'];
   $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
   $image_folder_03 = '../uploaded_img/' . $image_03;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if ($select_products->rowCount() > 0) {
      $message[] = 'product name already exist!';
   } else {

      $insert_products = $conn->prepare("INSERT INTO `products`(name, details, price, image_01, image_02, image_03) VALUES(?,?,?,?,?,?)");
      $insert_products->execute([$name, $details, $price, $image_01, $image_02, $image_03]);

      if ($insert_products) {
         if ($image_size_01 > 2000000 or $image_size_02 > 2000000 or $image_size_03 > 2000000) {
            $message[] = 'image size is too large!';
         } else {
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            move_uploaded_file($image_tmp_name_02, $image_folder_02);
            move_uploaded_file($image_tmp_name_03, $image_folder_03);
            $message[] = 'new product added!';
         }

      }

   }

}
;

if (isset($_GET['delete'])) {

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/' . $fetch_delete_image['image_01']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_02']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_03']);
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   header('location:manage-products.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="add-products">

      <h1 class="heading">List Your Property</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <div class="flex">

            <div class="detailBox" style="margin-top:0vh;">
               <span>Essencials</span>
            </div>

            <div class="inputBox-large">
               <span>Property Name</span>
               <input type="text" class="box" required maxlength="100" placeholder="enter property name" name="name">
            </div>

            <div class="inputBox-large">
               <span>property description</span>
               <textarea name="details" placeholder="enter property description" class="box" required maxlength="500"
                  cols="30" rows="10"></textarea>
            </div>

            <div class="inputBox">
               <span>Property Type</span>
               <select name="type" id="type" class="box">
                  <option value="hotel" selected>Hotel</option>
                  <option value="apartment">Apartment</option>
                  <option value="other">other</option>
               </select>
            </div>

            <div class="inputBox">
               <span>Property Category</span>
               <select name="type" id="type" class="box">
                  <option value="" disabled>selecty category</option>
                  <option value="#">Guest House</option>
                  <option value="hotel">Hotel</option>
                  <option value="#">Bed and breakfast</option>
                  <option value="#">Hostel</option>
                  <option value="#">Inn</option>
                  <option value="#">Motel</option>
                  <option value="#">Resort</option>
                  <option value="#">Lodge</option>
                  <option value="#">Other</option>
               </select>
            </div>

            <div class="detailBox">
               <span>Location Details</span>
            </div>

            <div class="inputBox-large">
               <span>Address</span>
               <input type="text" class="box" placeholder="enter address of the property" name="price">
            </div>

            <div class="inputBox">
               <span>Zip-Code</span>
               <input type="text" class="box" placeholder="Postal Code">
            </div>

            <div class="inputBox">
               <span>City</span>
               <input type="text" class="box" placeholder="city name">
            </div>

            <div class="detailBox">
               <span>Extra Features</span>
            </div>

            <div class="inputBox-large">
               <span>Select facilities you offer</span>
               <div class="box">
                  <input type="checkbox" name="#" id="#"> Swimming pool <br>
                  <input type="checkbox" name="#" id="#"> Spa <br>
                  <input type="checkbox" name="#" id="#"> Air conditioning <br>
                  <input type="checkbox" name="#" id="#"> Family rooms <br>
                  <input type="checkbox" name="#" id="#"> Fitness center <br>
                  <input type="checkbox" name="#" id="#"> Bar <br>
                  <input type="checkbox" name="#" id="#"> Restaurant <br>
               </div>
            </div>

            <div class="inputBox">
               <span>Is breakfast available in your place?</span>
               <div class="box">
                  <input type="radio" name="#" id="#"> Yes <br>
                  <input type="radio" name="#" id="#"> No<br>
               </div>
            </div>

            <div class="inputBox">
               <span>Is parking available?</span>
               <div class="box">
                  <input type="radio" name="#" id="#"> Yes <br>
                  <input type="radio" name="#" id="#"> No<br>
               </div>
            </div>

            <div class="inputBox-large">
               <span>Select Languages</span>
               <div class="box">
                  <input type="checkbox" name="#" id="#"> English <br>
                  <input type="checkbox" name="#" id="#"> Sinhala <br>
                  <input type="checkbox" name="#" id="#"> Tamil <br>
                  <input type="checkbox" name="#" id="#"> Hindi <br>
                  <input type="checkbox" name="#" id="#"> French <br>
                  <input type="checkbox" name="#" id="#"> Add others <br>
               </div>
            </div>

            <div class="detailBox">
               <span>Check In and Check Out</span>
            </div>

            <div class="detailBox" style="margin-bottom: 0;">
               <span>Check In</span>
            </div>

            <div class="inputBox">
               <span>From</span>
               <select name="type" id="type" class="box">
                  <option value="#">12 PM</option>
                  <option value="#">1 PM</option>
                  <option value="#">2 PM</option>
                  <option value="#">3 PM</option>
                  <option value="#">4 PM</option>
                  <option value="#">5 PM</option>
                  <option value="#">6 PM</option>
                  <option value="#">7 PM</option>
                  <option value="#">6 PM</option>
               </select>
            </div>

            <div class="inputBox">
               <span>To</span>
               <select name="type" id="type" class="box">
                  <option value="#">12 PM</option>
                  <option value="#">1 PM</option>
                  <option value="#">2 PM</option>
                  <option value="#">3 PM</option>
                  <option value="#">4 PM</option>
                  <option value="#">5 PM</option>
                  <option value="#">6 PM</option>
                  <option value="#">7 PM</option>
                  <option value="#">6 PM</option>
               </select>
            </div>

            <div class="detailBox" style="margin-bottom: 0;">
               <span>Check Out</span>
            </div>

            <div class="inputBox">
               <span>From</span>
               <select name="type" id="type" class="box">
                  <option value="#">12 PM</option>
                  <option value="#">1 PM</option>
                  <option value="#">2 PM</option>
                  <option value="#">3 PM</option>
                  <option value="#">4 PM</option>
                  <option value="#">5 PM</option>
                  <option value="#">6 PM</option>
                  <option value="#">7 PM</option>
                  <option value="#">6 PM</option>
               </select>
            </div>

            <div class="inputBox">
               <span>To</span>
               <select name="type" id="type" class="box">
                  <option value="#">12 PM</option>
                  <option value="#">1 PM</option>
                  <option value="#">2 PM</option>
                  <option value="#">3 PM</option>
                  <option value="#">4 PM</option>
                  <option value="#">5 PM</option>
                  <option value="#">6 PM</option>
                  <option value="#">7 PM</option>
                  <option value="#">6 PM</option>
               </select>
            </div>

            <div class="inputBox">
               <span>Are children allowed?</span>
               <div class="box">
                  <input type="radio" name="#" id="#"> Yes <br>
                  <input type="radio" name="#" id="#"> No<br>
               </div>
            </div>

            <div class="inputBox">
               <span>Are pets allowed?</span>
               <div class="box">
                  <input type="radio" name="#" id="#"> Yes <br>
                  <input type="radio" name="#" id="#"> No<br>
               </div>
            </div>

            <!-- room details -->

            <div class="detailBox">
               <span>Add room details</span>
            </div>

            <div class="inputBox">
               <span>Unit type</span>
               <select name="type" id="type" class="box">
                  <option value="#">Single</option>
                  <option value="#">Doube</option>
                  <option value="#">Twin</option>
                  <option value="#">Tripe</option>
                  <option value="#">Quad</option>
                  <option value="#">Family</option>
                  <option value="#">Suite</option>
               </select>
            </div>

            <div class="inputBox">
               <span>How many beds available in this room?</span>
               <input type="number" max="10" class="box" placeholder="enter bed count">
            </div>

            <div class="inputBox">
               <span>How many guests can stay in this room?</span>
               <input type="number" max="10" class="box" placeholder="enter number of people">
            </div>

            <div class="inputBox">
               <span>How big is this room?</span>
               <input type="number" max="10o" class="box" placeholder="square meters">
            </div>

            <div class="detailBox">
               <span>Price</span>
            </div>

            <div class="inputBox">
               <span>Room price (required)</span>
               <input type="number" min="0" class="box" required max="9999999999" placeholder="enter room price"
                  onkeypress="if(this.value.length == 10) return false;" name="price">
            </div>

            <div class="detailBox">
               <span>Policies</span>
            </div>

            <div class="detailBox">
               <span>Upload Photos</span>
            </div>

            <div class="inputBox-large">
               <span>Upload cover image</span>
               <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
            </div>

            <div class="inputBox">
               <span>additional Images</span>
               <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
            </div>
            <div class="inputBox">
               <span>image 3(required)</span>
               <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
            </div>

            <div class="detailBox">
               <span>I have read and I accept all terms n conditions</span>
               <div class="box">
                  <input type="radio" name="#" id="#"> Yes <br>
                  <input type="radio" name="#" id="#"> No<br>
               </div>
            </div>

         </div>

         <input type="submit" value="List Property" class="btn" name="add_product">
      </form>

   </section>
   <script src="../js/admin_script.js"></script>

</body>

</html>