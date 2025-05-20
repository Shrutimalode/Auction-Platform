<?php
include 'db.php';


session_start();


// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle form submission to add funds
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST['amount'];
    $user_id = $_SESSION['user_id'];

    // Validate that the amount is a positive number
    if ($amount > 0) {
        // Update the user's wallet balance
        $sql = "UPDATE users SET wallet_balance = wallet_balance + ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $amount, $user_id);

        if ($stmt->execute()) {
            $message = "Funds added successfully.";
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Please enter a valid amount.";
    }
}

// Fetch the user's current wallet balance
$balanceQuery = "SELECT wallet_balance FROM users WHERE user_id = ?";
$stmt = $conn->prepare($balanceQuery);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($wallet_balance);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Funds to Wallet</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .navbar {
            display: flex;
            justify-content: center;
            background-color: #333;
        }
        .navbar a {
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            text-align: center;
        }
        .navbar a:hover {
            background-color: #575757;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        form {
            margin-top: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="number"] {
            padding: 10px;
            width: calc(100% - 22px);
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #45a049;
        }
        .payment-methods {
            margin-top: 20px;
        }
        .payment-methods img {
            width: 300px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Add Funds to Wallet</h1>
    </header>

    <div class="navbar">
        <a href="customer_dashboard.php">Home</a>
    </div>

    <div class="container">
        <p>Your Current Wallet Balance: ₹<?= htmlspecialchars(number_format($wallet_balance, 2)) ?></p>
        
        <form method="post" action="">
            <label for="amount">Amount to Add (in ₹):</label>
            <input type="number" id="amount" name="amount" min="1" required>
            <button type="submit">Add Funds</button>
        </form>
        <p><?= htmlspecialchars($message) ?></p>

        <div class="payment-methods">
            <h2>Select a Payment Method:</h2>
            <img src="payment.png" alt="Dummy Payment Method 1">
        </div>
    </div>
</body>
</html>
