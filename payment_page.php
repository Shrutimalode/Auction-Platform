<?php
include 'db.php';
session_start();


// Ensure order_id is set
if (!isset($_GET['order_id'])) {
    die("Error: Order ID is missing.");
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Retrieve order details, including bid amount and product name, and get farmer's user_id
$orderQuery = $conn->prepare("SELECT orders.bid_amount, listings.product, listings.farmer_id 
                              FROM orders 
                              JOIN listings ON orders.listing_id = listings.listing_id 
                              WHERE orders.order_id = ? AND orders.customer_id = ?");
if (!$orderQuery) {
    die("Prepare failed for order retrieval: " . $conn->error);
}
$orderQuery->bind_param("ii", $order_id, $user_id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();
$order = $orderResult->fetch_assoc();
$orderQuery->close();

// Retrieve wallet balance of the customer
$walletQuery = $conn->prepare("SELECT wallet_balance FROM users WHERE user_id = ?");
if (!$walletQuery) {
    die("Prepare failed for wallet balance retrieval: " . $conn->error);
}
$walletQuery->bind_param("i", $user_id);
$walletQuery->execute();
$walletQuery->bind_result($wallet_balance);
$walletQuery->fetch();
$walletQuery->close();

// Initialize success message and payment error
$successMessage = "";
$paymentError = "";

// Process payment when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($wallet_balance >= $order['bid_amount']) {
        // Deduct amount from customer's wallet
        $new_balance = $wallet_balance - $order['bid_amount'];
        $updateWallet = $conn->prepare("UPDATE users SET wallet_balance = ? WHERE user_id = ?");
        if (!$updateWallet) {
            die("Prepare failed for wallet update: " . $conn->error);
        }
        $updateWallet->bind_param("di", $new_balance, $user_id);
        $updateWallet->execute();
        $updateWallet->close();

        // Add the bid amount to the farmer's wallet balance
        $farmer_id = $order['farmer_id'];
        $updateFarmerWallet = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE user_id = ?");
        if (!$updateFarmerWallet) {
            die("Prepare failed for farmer wallet update: " . $conn->error);
        }
        $updateFarmerWallet->bind_param("di", $order['bid_amount'], $farmer_id);
        $updateFarmerWallet->execute();
        $updateFarmerWallet->close();

        // Update order status to "Paid"
        $updateOrder = $conn->prepare("UPDATE orders SET status = 'Paid' WHERE order_id = ?");
        if (!$updateOrder) {
            die("Prepare failed for order status update: " . $conn->error);
        }
        $updateOrder->bind_param("i", $order_id);
        $updateOrder->execute();
        $updateOrder->close();

        // Redirect to receipt page
        header("Location: generate_receipt.php?order_id=$order_id");
        exit();
    } else {
        $paymentError = "Insufficient wallet balance. Please add funds.";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Page</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; color: #333; }
        .container { max-width: 600px; margin: auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        h1 { color: #4CAF50; }
        .info { margin: 20px 0; }
        .button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .button:hover { background-color: #45a049; }
        .error { color: #ff0000; }
        .success { color: #4CAF50; font-weight: bold; margin-top: 20px; }
        .download-receipt { display: none; margin-top: 20px; }
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
    <script>
        function confirmPayment() {
            return confirm("Are you sure you want to proceed with the payment?");
        }

        function downloadReceipt() {
            const content = `Receipt\n\nProduct: <?= htmlspecialchars($order['product']) ?>\nAmount Paid: ₹<?= number_format($order['bid_amount'], 2) ?>\nOrder ID: <?= htmlspecialchars($order_id) ?>\n\nThank you for your purchase!`;
            const blob = new Blob([content], { type: 'text/plain' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'receipt.txt';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</head>
<body>
 <!-- Navbar -->
 <div class="navbar">
        <a href="customer_dashboard.php">Go to Dashboard</a>
    </div>

    <div class="container">
        <h1>Confirm Your Order</h1>
        <p class="info"><strong>Product:</strong> <?= htmlspecialchars($order['product']) ?></p>
        <p class="info"><strong>Amount to Pay:</strong> ₹<?= number_format($order['bid_amount'], 2) ?></p>
        <p class="info"><strong>Your Wallet Balance:</strong> ₹<?= number_format($wallet_balance, 2) ?></p>

        <?php if ($paymentError): ?>
            <p class="error"><?= $paymentError ?></p>
        <?php elseif ($successMessage): ?>
            <p class="success"><?= $successMessage ?></p>
            <button class="button download-receipt" onclick="downloadReceipt()">Download Receipt</button>
        <?php else: ?>
            <form method="POST" onsubmit="return confirmPayment();">
                <button type="submit" class="button">Confirm and Pay</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
