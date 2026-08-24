<?php
session_start();


$user_id = $_SESSION['user_id'] ?? 1;

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];

    mysqli_query($conn,
        "UPDATE cart SET quantity='$quantity' 
         WHERE cart_id='$id' AND user_id='$user_id'"
    );

    header("Location: cart.php");
    exit();
}

if (isset($_GET['remove'])) {
    $id = $_GET['remove'];

    mysqli_query($conn,
        "DELETE FROM cart 
         WHERE cart_id='$id' AND user_id='$user_id'"
    );

    header("Location: cart.php");
    exit();
}

$cart = mysqli_query($conn,
    "SELECT cart.*, products.*
     FROM cart
     JOIN products ON cart.product_id = products.product_id
     WHERE cart.user_id='$user_id'"
);

$wishlist = mysqli_query($conn,
    "SELECT wishlist.*, products.*
     FROM wishlist
     JOIN products ON wishlist.product_id = products.product_id
     WHERE wishlist.user_id='$user_id'"
);

$compare = mysqli_query($conn,
    "SELECT comparison.*, products.*
     FROM comparison
     JOIN products ON comparison.product_id = products.product_id
     WHERE comparison.user_id='$user_id'"
);

$subtotal = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Shopping</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<h1>My Shopping</h1>

<nav>
    <a href="#cart">Cart</a>
    <a href="#wishlist">Wishlist</a>
    <a href="#compare">Compare</a>
</nav>

<section id="cart">

    <h2>Shopping Cart</h2>

    <?php if (mysqli_num_rows($cart) > 0) { ?>

        <table>

            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
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
                    <form method="POST">

                        <input type="hidden"
                               name="id"
                               value="<?php echo $row['cart_id']; ?>">

                        <input type="number"
                               name="quantity"
                               value="<?php echo $row['quantity']; ?>"
                               min="1">

                        <button name="update">Update</button>

                    </form>
                </td>

                <td>
                    <?php echo $total; ?> FCFA
                </td>

                <td>
                    <a href="cart.php?remove=<?php echo $row['cart_id']; ?>">
                        Remove
                    </a>
                </td>
            </tr>

            <?php } ?>

        </table>

        <h3>
            Subtotal: <?php echo $subtotal; ?> FCFA
        </h3>

        <a class="button" href="shipping.php">
            Proceed to Shipping
        </a>

    <?php } else { ?>

        <p>Your cart is empty.</p>

    <?php } ?>

</section>


<section id="wishlist">

    <h2>Wishlist</h2>

    <?php if (mysqli_num_rows($wishlist) > 0) { ?>

        <?php while ($row = mysqli_fetch_assoc($wishlist)) { ?>

            <div class="card">

                <h3>
                    <?php echo $row['product_name']; ?>
                </h3>

                <p>
                    Price: <?php echo $row['price']; ?> FCFA
                </p>

                <a href="actions/add_to_cart.php?product_id=<?php echo $row['product_id']; ?>">
                    Add to Cart
                </a>

            </div>

        <?php } ?>

    <?php } else { ?>

        <p>Your wishlist is empty.</p>

    <?php } ?>

</section>


<section id="compare">

    <h2>Compare Products</h2>

    <?php if (mysqli_num_rows($compare) > 0) { ?>

        <?php while ($row = mysqli_fetch_assoc($compare)) { ?>

            <div class="card">

                <h3>
                    <?php echo $row['product_name']; ?>
                </h3>

                <p>
                    Price: <?php echo $row['price']; ?> FCFA
                </p>

                <p>
                    Rating: <?php echo $row['rating'] ?? "No rating"; ?>
                </p>

                <a href="actions/add_to_cart.php?product_id=<?php echo $row['product_id']; ?>">
                    Add to Cart
                </a>

            </div>

        <?php } ?>

    <?php } else { ?>

        <p>No products to compare.</p>

    <?php } ?>

</section>

</body>
</html>