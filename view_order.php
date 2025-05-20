<?php
include 'db.php';

session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit;
}

$farmer_id = $_SESSION['user_id'];
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

        header {
            background-color: #4CAF50;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }


        header a button {
            background-color: #fff;
            color: #4CAF50;
            border: 1px solid #4CAF50;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, color 0.3s;
        }
        header a button:hover {
            background-color: #4CAF50;
            color: #fff;
        }
    </style>
</head>
<body>
<header>
        <h1>Farmers Auction Platform</h1>
        <div class="navbar-icons">
      
           
            <a href="farmer_dashboard.php">
            <button>Go to dashboard</button>
        </a>
           
           
        </div>
    </header>
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
</html>

</body>