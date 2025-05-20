<?php
include 'db.php';

session_start();


// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit;
}

$farmer_id = $_SESSION['user_id'];

// Fetch Farmer Details
$farmerQuery = $conn->prepare("SELECT username,  phone, wallet_balance FROM users WHERE user_id = ?");
$farmerQuery->bind_param("i", $farmer_id);
$farmerQuery->execute();
$farmerResult = $farmerQuery->get_result();
$farmer = $farmerResult->fetch_assoc();
$farmerQuery->close();

// Fetch Farmer Products
$productsQuery = $conn->prepare("SELECT listing_id, product, starting_price, auction_start_time FROM listings WHERE farmer_id = ?");
$productsQuery->bind_param("i", $farmer_id);
$productsQuery->execute();
$productsResult = $productsQuery->get_result();
$productsQuery->close();

// Fetch Farmer Orders
$ordersQuery = $conn->prepare("
    SELECT o.order_id, o.order_date, o.bid_amount, l.pickup_location, o.status, c.username AS customer_name
    FROM orders o
    JOIN listings l ON o.listing_id = l.listing_id
    JOIN users c ON o.customer_id = c.user_id
    WHERE l.farmer_id = ?
    ORDER BY o.order_date DESC
");
$ordersQuery->bind_param("i", $farmer_id);
$ordersQuery->execute();
$ordersResult = $ordersQuery->get_result();
$ordersQuery->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f7f7f7; }
        .profile-header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
        .profile-container { padding: 20px; }
        .card { margin: 10px 0; }
    </style>
</head>
<body>
<div class="profile-header">
    <h1><?= htmlspecialchars($farmer['username']) ?>'s Profile</h1>
</div>
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #4CAF50;">
    <div class="container-fluid">
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                    <a class="nav-link text-white" href="farmer_dashboard.php">Go to Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container profile-container">
    <!-- Farmer Details -->
    <h2>Profile Information</h2>
    <p><strong>username:</strong> <?= htmlspecialchars($farmer['username']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($farmer['phone']) ?></p>
    <p><strong>Wallet Balance:</strong> <?= htmlspecialchars($farmer['wallet_balance']) ?></p>

    <!-- Products -->
    <h2>Your Products</h2>
    <?php if ($productsResult->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Auction Start Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $productsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product']) ?></td>
                        <td>₹<?= number_format($product['starting_price'], 2) ?></td>
                        <td><?= htmlspecialchars($product['auction_start_time']) ?></td>
                        <td>
                            <a href="edit_product.php?product_id=<?= $product['listing_id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_product.php?product_id=<?= $product['listing_id'] ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>

    <!-- Orders -->
    <h2>Customer Orders</h2>
    <?php if ($ordersResult->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Bid Amount</th>
                    <th>Pickup Location</th>
                    <th>Status</th>
                    <th>Customer</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $ordersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_date']) ?></td>
                        <td>₹<?= number_format($order['bid_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($order['pickup_location']) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

</html>
