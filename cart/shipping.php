<?php
session_start();
include("config/db.php");

$user_id = $_SESSION['user_id'] ?? 1;

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $delivery = $_POST['delivery'];

    if ($delivery == "Express") {
        $fee = 2500;
    } else {
        $fee = 1000;
    }

    mysqli_query($conn,
        "INSERT INTO addresses
        (user_id, full_name, phone, city, street_address)
        VALUES
        ('$user_id','$name','$phone','$city','$address')"
    );

    $address_id = mysqli_insert_id($conn);

    mysqli_query($conn,
        "INSERT INTO shipping_info
        (user_id, address_id, delivery_option, delivery_fee)
        VALUES
        ('$user_id','$address_id','$delivery','$fee')"
    );

    header("Location: order_summary.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shipping</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<h1>Shipping Information</h1>

<form method="POST">

    <input type="text"
           name="name"
           placeholder="Full Name"
           required>

    <input type="text"
           name="phone"
           placeholder="Phone Number"
           required>

    <input type="text"
           name="city"
           placeholder="City"
           required>

    <textarea name="address"
              placeholder="Street Address"
              required></textarea>

    <select name="delivery">

        <option value="Standard">
            Standard Delivery - 1000 FCFA
        </option>

        <option value="Express">
            Express Delivery - 2500 FCFA
        </option>

    </select>

    <button type="submit" name="submit">
        Continue
    </button>

</form>

</body>
</html>