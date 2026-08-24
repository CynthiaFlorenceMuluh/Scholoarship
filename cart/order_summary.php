<?php
session_start();
include("config/db.php");

$user_id = $_SESSION['user_id'] ?? 1;

$cart = mysqli_query($conn,
    "SELECT cart.*, products.*
     FROM cart
     JOIN products ON cart.product_id = products.product_id
     WHERE cart.user_id='$user_id'"
);

$shipping = mysqli_query($conn,
    "SELECT shipping_info.*, addresses.*
     FROM shipping_info
     JOIN addresses ON shipping_info.address_id = addresses.address_id
     WHERE shipping_info.user_id='$user_id'
     ORDER BY shipping_info.shipping_id DESC
     LIMIT 1"
);

$address = mysqli_fetch_assoc($shipping);
$subtotal = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Summary</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<h1>Order Summary</h1>

<?php if ($address && mysqli_num_rows($cart) > 0) { ?>

<section>

    <h2>Products</h2>

    <table>

        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($cart)) {

            $total = $row['price'] * $row['quantity'];
            $subtotal += $total;
        ?>

        <tr>
            <td><?php echo $row['product_name']; ?></td>

            <td>
                <?php echo $row['price']; ?> FCFA
            </td>

            <td>
                <?php echo $row['quantity']; ?>
            </td>

            <td>
                <?php echo $total; ?> FCFA
            </td>
        </tr>

        <?php } ?>

    </table>

</section>

<?php
$delivery_fee = $address['delivery_fee'];
$final_total = $subtotal + $delivery_fee;
?>

<section>

    <h2>Delivery Information</h2>

    <p>
        Name: <?php echo $address['full_name']; ?>
    </p>

    <p>
        Phone: <?php echo $address['phone']; ?>
    </p>

    <p>
        City: <?php echo $address['city']; ?>
    </p>

    <p>
        Address: <?php echo $address['street_address']; ?>
    </p>

    <p>
        Delivery: <?php echo $address['delivery_option']; ?>
    </p>

</section>

<section>

    <h2>Payment Summary</h2>

    <p>
        Subtotal: <?php echo $subtotal; ?> FCFA
    </p>

    <p>
        Delivery Fee: <?php echo $delivery_fee; ?> FCFA
    </p>

    <h3>
        Final Total: <?php echo $final_total; ?> FCFA
    </h3>

    <form action="checkout.php" method="POST">

        <select name="payment_method">

            <option value="Mobile Money">
                Mobile Money
            </option>

            <option value="Cash on Delivery">
                Cash on Delivery
            </option>

            <option value="Bank Transfer">
                Bank Transfer
            </option>

        </select>

        <input type="hidden"
               name="total"
               value="<?php echo $final_total; ?>">

        <button type="submit">
            Continue to Payment
        </button>

    </form>

</section>

<?php } else { ?>

<section>

    <h2>Order Not Ready</h2>

    <p>
        Please add products to your cart and enter your shipping information.
    </p>

    <a class="button" href="cart.php">
        Go to Cart
    </a>

</section>

<?php } ?>

</body>
</html>