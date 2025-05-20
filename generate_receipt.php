<?php
include 'db.php';

session_start();


// Ensure order_id is set
if (!isset($_GET['order_id'])) {
    die("Error: Order ID is missing.");
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

$orderQuery = $conn->prepare("SELECT orders.bid_amount, listings.product, listings.pickup_location, users.username AS farmer_name 
                              FROM orders 
                              JOIN listings ON orders.listing_id = listings.listing_id 
                              JOIN users ON listings.farmer_id = users.user_id 
                              WHERE orders.order_id = ? AND orders.customer_id = ?");
if (!$orderQuery) {
    die("Prepare failed for order retrieval: " . $conn->error);
}
$orderQuery->bind_param("ii", $order_id, $user_id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();
$order = $orderResult->fetch_assoc();
$orderQuery->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f7f7f7; 
            color: #333; 
            margin: 0; 
            padding: 0;
        }
        .container { 
            width: 80%; 
            max-width: 700px; 
            margin: 40px auto; 
            padding: 20px; 
            background-color: #ffffff; 
            border-radius: 10px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
        }
        h1 { 
            text-align: center; 
            color: #4CAF50; 
            margin-bottom: 20px; 
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            color: #4CAF50;
        }
        .info { 
            margin: 10px 0; 
            font-size: 16px; 
            line-height: 1.5; 
        }
        .info strong { 
            color: #555; 
        }
        .receipt-section {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .receipt-footer {
            text-align: center;
            font-size: 14px;
            color: #888;
            margin-top: 20px;
        }
        .button { 
            background-color: #4CAF50; 
            color: white; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 16px; 
        }
        .button:hover { 
            background-color: #45a049; 
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
        function downloadReceiptAsPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Receipt details
            const receiptTitle = "Payment Receipt";
            const product = "<?= htmlspecialchars($order['product']) ?>";
            const farmer = "<?= htmlspecialchars($order['farmer_name']) ?>";
            const amountPaid = "₹<?= number_format($order['bid_amount'], 2) ?>";
            const pickupLocation = "<?= htmlspecialchars($order['pickup_location']) ?>";
            const orderId = "<?= htmlspecialchars($order_id) ?>";
            const date = new Date().toLocaleString();

            // Set font and alignment
            doc.setFont("Helvetica", "bold");
            doc.setFontSize(16);
            doc.text(receiptTitle, 105, 20, { align: "center" });

            doc.setFontSize(12);
            doc.setFont("Helvetica", "normal");
            doc.text(`Date: ${date}`, 15, 40);
            doc.text(`Order ID: ${orderId}`, 15, 50);
            doc.text(`Product: ${product}`, 15, 60);
            doc.text(`Farmer: ${farmer}`, 15, 70);
            doc.text(`Amount Paid: ${amountPaid}`, 15, 80);
            doc.text(`Pickup Location: ${pickupLocation}`, 15, 90);

            // Footer
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text("Thank you for your purchase!", 105, 120, { align: "center" });

            // Save PDF
            doc.save(`Receipt_${orderId}.pdf`);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Your Receipt</h1>

        <div class="receipt-header">
            <p>Thank you for your purchase! Below are the details of your order.</p>
        </div>

        <div class="receipt-section">
            <p class="info"><strong>Product:</strong> <?= htmlspecialchars($order['product']) ?></p>
            <p class="info"><strong>Farmer:</strong> <?= htmlspecialchars($order['farmer_name']) ?></p>
            <p class="info"><strong>Amount Paid:</strong> ₹<?= number_format($order['bid_amount'], 2) ?></p>
            <p class="info"><strong>Pickup Location:</strong> <?= htmlspecialchars($order['pickup_location']) ?></p>
            <p class="info"><strong>Order ID:</strong> <?= htmlspecialchars($order_id) ?></p>
        </div>

        <button class="button" onclick="downloadReceiptAsPDF()">Download PDF Receipt</button>

        <div class="receipt-footer">
            <p>If you have any questions, feel free to contact us.</p>
        </div>
    </div>
</body>
</html>
