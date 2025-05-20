<?php
include 'db.php';

session_start();


// Check if the user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit;
}

// Get the product ID from the query string
$product_id = $_GET['product_id'];

// Fetch the product details for the given product ID
$farmer_id = $_SESSION['user_id'];
$query = "SELECT * FROM listings WHERE listing_id = ? AND farmer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $product_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// If the form is submitted, update the product details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product'];
    $description = $_POST['description'];

    // Ensure starting_price is treated as a float
    $starting_price = (float) $_POST['starting_price'];

    $auction_start_time = $_POST['auction_start_time'];
    $pickup_location = $_POST['pickup_location'];
    
    // Get the auction duration (days, hours, minutes)
    $days = $_POST['days'];
    $hours = $_POST['hours'];
    $minutes = $_POST['minutes'];

    // Calculate the auction end time by adding the selected duration (in days, hours, minutes) to the start time
    $auction_start_timestamp = strtotime($auction_start_time);
    $auction_end_timestamp = $auction_start_timestamp + ($days * 86400) + ($hours * 3600) + ($minutes * 60); // converting days, hours, minutes to seconds
    $auction_end_time = date('Y-m-d H:i:s', $auction_end_timestamp);

    // Update the product in the database
    // Ensure the bind_param string matches the number of parameters
    $stmt = $conn->prepare("UPDATE listings SET product = ?, description = ?, starting_price = ?, auction_start_time = ?, auction_end_time = ?, pickup_location = ? WHERE listing_id = ? AND farmer_id = ?");
    $stmt->bind_param("ssdssssi", $product_name, $description, $starting_price, $auction_start_time, $auction_end_time, $pickup_location, $product_id, $farmer_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo "Product updated successfully!";
        header("Location: manage_products.php"); // Redirect back to the manage products page
        exit;
    } else {
        echo "Error updating product: " . $stmt->error;
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(rgba(97, 132, 110, 0.7), rgba(0, 77, 64, 0.7)), url('try.jpg') no-repeat center center/cover;
            background-size: cover;
        }
        .navbar {
            width: 100%;
            background-color: #4CAF50;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
        }

        .navlist {
            display: flex;
            align-items: center;
            list-style: none;
        }

        .navlist a {
            color: white;
            font-size: 1rem;
            margin-right: 15px;
            text-decoration: none;
            padding: 8px 18px;
            transition: 0.4s;
            border-radius: 25px;
        }

        .navlist a:hover {
            background-color: white;
            color: #43a047;
        }

        .container {
            margin: 100px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            width: 70%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        .success-message {
            color: black;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Style for the duration picker */
        .duration-picker {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .duration-picker input {
            width: 30%;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="navlist">
            <a href="farmer_dashboard.php">Go to dashboard</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Edit Product</h1>

        <!-- Success Message -->
        <?php if (!empty($success_message)) : ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="product">Product Name:</label>
                <input type="text" id="product" name="product" value="<?= htmlspecialchars($product['product']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="starting_price">Starting Price:</label>
                <input type="number" id="starting_price" name="starting_price" value="<?= htmlspecialchars($product['starting_price']) ?>" required>
            </div>

            <div class="form-group">
                <label for="auction_start_time">Auction Start Time:</label>
                <input type="datetime-local" id="auction_start_time" name="auction_start_time" value="<?= htmlspecialchars($product['auction_start_time']) ?>" required>
            </div>

            <div class="form-group">
                <label for="auction_duration">Auction Duration:</label>
                <div class="duration-picker">
                    <input type="number" id="days" name="days" placeholder="Days" min="0" value="0" required> 
                    <input type="number" id="hours" name="hours" placeholder="Hours" min="0" max="23" value="0" required> 
                    <input type="number" id="minutes" name="minutes" placeholder="Minutes" min="0" max="59" value="0" required>
                </div>
            </div>

            <div class="form-group">
                <label for="pickup_location">Pickup Location:</label>
                <select id="pickup_location" name="pickup_location" required>
                    <option value="">Select a location</option>
                    <option value="Location 1" <?= $product['pickup_location'] == 'Location 1' ? 'selected' : '' ?>>Location 1</option>
                    <option value="Location 2" <?= $product['pickup_location'] == 'Location 2' ? 'selected' : '' ?>>Location 2</option>
                    <option value="Location 3" <?= $product['pickup_location'] == 'Location 3' ? 'selected' : '' ?>>Location 3</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit">Update Product</button>
            </div>
        </form>
    </div>
</body>
</html>
