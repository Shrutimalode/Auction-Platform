<?php
session_start();
include 'db.php'; // Make sure this file defines $conn properly

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

// Initialize variables
$wallet_balance = 0;

// Fetch the user's wallet balance
if (isset($conn)) {
    $user_id = $_SESSION['user_id'];
    $balanceQuery = "SELECT wallet_balance FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($balanceQuery);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($wallet_balance);
        $stmt->fetch();
        $stmt->close();

        // Save wallet balance in session
        $_SESSION['wallet_balance'] = $wallet_balance;
    } else {
        die("Error in statement preparation: " . $conn->error);
    }

    // Fetch products and auction details
    $sql = "SELECT p.listing_id, p.product AS product, u.username AS farmer_username, p.description, 
                   p.starting_price, p.auction_start_time, p.pickup_location
            FROM listings p
            JOIN users u ON p.farmer_id = u.user_id
            WHERE u.role = 'farmer'";

    $result = $conn->query($sql);

    if ($conn->error) {
        die("Error: " . $conn->error);
    }
} else {
    die("Database connection failed.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }
        header {
            background-color: #4CAF50;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 15px 30px;
        }
        header h1 {
            margin: 0;
        }
        header a button {
            background-color: #fff;
            color: #4CAF50;
            border: 1px solid #4CAF50;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .navbar-icons {
            display: flex;
            align-items: center;
        }
        .navbar-icons a {
            margin-left: 15px;
        }
        .navbar-icons img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        .listing-card {
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .listing-card button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
        }
    </style>
    <script>
        function checkBalance(requiredAmount, userBalance) {
            if (userBalance < requiredAmount) {
                alert("Insufficient balance. Please add more funds to your wallet.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<header>
    <h1>Farmers Auction Platform</h1>
    <div class="navbar-icons">
        <a href="add_money.php"><button>Wallet</button></a>
        <a href="logout.php"><button>Logout</button></a>
        <a href="customer_profile.php">
            <img src="profile.jpg" alt="Profile">
        </a>
    </div>
</header>

<div class="container">
    <h1>Browse Products</h1>
    <p>Your Wallet Balance: ₹<?= htmlspecialchars(number_format($wallet_balance, 2)) ?></p>

    <?php if ($result->num_rows > 0): ?>
        <div class="listings row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="listing-card col-md-6">
                    <h3><?= htmlspecialchars($row['product']) ?></h3>
                    <p><strong>Farmer:</strong> <?= htmlspecialchars($row['farmer_username']) ?></p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($row['description']) ?></p>
                    <p><strong>Starting Price:</strong> ₹<?= htmlspecialchars(number_format($row['starting_price'], 2)) ?></p>
                    <p><strong>Auction Start:</strong> <?= htmlspecialchars($row['auction_start_time']) ?></p>
                    <p><strong>Pickup Location:</strong> <?= htmlspecialchars($row['pickup_location']) ?></p>
                    <a href="order.php?listing_id=<?= htmlspecialchars($row['listing_id']) ?>" 
                       onclick="return checkBalance(<?= htmlspecialchars($row['starting_price']) ?>, <?= $_SESSION['wallet_balance'] ?>)">
                        <button>Go For Auction</button>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No listings available at the moment.</p>
    <?php endif; ?>
</div>

</body>
</html>
