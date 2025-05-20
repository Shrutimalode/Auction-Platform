<?php
include 'db.php'; 

session_start();


// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

// Fetch the user's wallet balance
$user_id = $_SESSION['user_id'];
$balanceQuery = "SELECT wallet_balance, username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($balanceQuery);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($wallet_balance, $username);
$stmt->fetch();
$stmt->close();

// Fetch the user's orders
$orderQuery = "SELECT o.order_id, o.bid_amount AS order_amount, o.order_date, p.product AS product
               FROM orders o
               JOIN listings p ON o.listing_id = p.listing_id
               WHERE o.customer_id = ?";
$orderStmt = $conn->prepare($orderQuery);
if (!$orderStmt) {
    die("Error preparing order statement: " . $conn->error);
}
$orderStmt->bind_param("i", $user_id);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
$orderStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
        }
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

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .container h2 {
            color: #4CAF50;
            margin-bottom: 20px;
            text-align: center;
        }
        .balance {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #4CAF50;
            margin: 10px 0;
        }
        .profile-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }
        .profile-info div {
            flex: 1;
            text-align: center;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }

        /* Badge Styles */
        .badge {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 14px;
            position: absolute;
            top: 10px;
            left: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
    </style>
</head>
<body>
<header>
    <h1>My Profile</h1>
    <div>


      <a href="customer_dashboard.php">        <button>Go to dashboard</button></a>
      
        <a href="add_money.php">
            <button>Wallet</button>
        </a>
        <a href="logout.php">
            <button>Logout</button>
        </a>
    </div>
</header>


    <div class="container">
        <span class="badge">My Profile</span>
        <h2>Your Fund Balance</h2>
        <p class="balance">₹<?= htmlspecialchars(number_format($wallet_balance, 2)) ?></p>
        
        <div class="profile-info">
            <div>
                <i class="fas fa-user-circle" style="font-size: 40px; color: #4CAF50;"></i>
                <p><strong>Username</strong></p>
                <p><?= htmlspecialchars($username) ?></p> 
            </div>
        </div>

        <h3>Your Orders</h3>
        <?php if ($orderResult && $orderResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $orderResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_id']) ?></td>
                            <td><?= htmlspecialchars($row['product']) ?></td>
                            <td>₹<?= htmlspecialchars(number_format($row['order_amount'], 2)) ?></td>
                            <td><?= htmlspecialchars($row['order_date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no orders yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
