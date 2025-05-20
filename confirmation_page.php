<?php
session_start();
include 'db.php';

if (!isset($_GET['order_id'])) {
    die("Error: Order ID is missing.");
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Fetch order details
$orderQuery = $conn->prepare("
    SELECT orders.listing_id, listings.product, orders.bid_amount, orders.customer_id, orders.status 
    FROM orders 
    JOIN listings ON orders.listing_id = listings.listing_id 
    WHERE orders.order_id = ?
");
$orderQuery->bind_param("i", $order_id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();
$order = $orderResult->fetch_assoc();
$orderQuery->close();

if (!$order) {
    die("Order not found.");
}

$isWinner = ($order['customer_id'] == $user_id);
$hasPaid = ($order['status'] == 'Paid');

// Fetch winner name
function getUserNameById($conn, $userId) {
    $query = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $query->bind_result($username);
    $query->fetch();
    $query->close();
    return $username;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        h1 {
            color: #4CAF50;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            text-decoration: none;
            color: #fff;
            background-color: #4CAF50;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        a:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        a:active {
            background-color: #3e8e41;
            transform: translateY(2px);
        }

        .disabled-link {
            color: #aaa;
            pointer-events: none;
        }

        p {
            margin: 10px 0;
        }

        strong {
            color: #333;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            a {
                padding: 8px 16px;
            }
        }
        .navbar {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #4CAF50;
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
 <div class="navbar">
        <a href="customer_dashboard.php">Go to Dashboard</a>
    </div>
    <div class="container">
        <h1>Thank You!</h1>
        <p>Your order for <strong><?= htmlspecialchars($order['product']) ?></strong> has been confirmed.</p>
        <p>Order ID: #<?= htmlspecialchars($order_id) ?></p>

        <?php if ($isWinner && !$hasPaid): ?>
        <form action="process_payment.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <button type="submit">Confirm Order</button>
        </form>
    <?php else: ?>
        <p>Winner: <?php echo getUserNameById($conn, $order['customer_id']); ?></p>
    <?php endif; ?>
    </div>
</body>
</html>
