<?php
include 'db.php';

session_start();

// Check if the user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit;
}

// Handle form submission
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $farmer_id = $_SESSION['user_id'];
    $product = $_POST['product'];
    $description = $_POST['description'];
    $starting_price = $_POST['starting_price'];
    $auction_start_time = $_POST['auction_start_time'];
    $pickup_location = $_POST['pickup_location'];

    // Retrieve farmer's username from the users table
    $query = "SELECT username FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $farmer_id);
    $stmt->execute();
    $stmt->bind_result($farmer_username);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    if (!$farmer_username) {
        echo "Error: Farmer username not found.";
        exit;
    }

    // Insert product into listings table
    $stmt = $conn->prepare("INSERT INTO listings (farmer_id, farmer_name, product, description, starting_price, auction_start_time, pickup_location) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssiss", $farmer_id, $farmer_username, $product, $description, $starting_price, $auction_start_time, $pickup_location);
    $stmt->execute();
    $stmt->close();

    $success_message = "Product added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
         body {
            background: linear-gradient(rgba(97, 132, 110, 0.7), rgba(0, 77, 64, 0.7)), url('try.jpg') no-repeat center center/cover;            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
       /* Reset body and html padding/margin */
html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    overflow-x: hidden; /* Prevent horizontal scroll */
}

/* Navbar styling */
.navbar {
    width: 100vw; /* Ensures full viewport width */
    background-color: #4CAF50;
    padding: 10px 0px 8px 0px; /* Remove any padding on the navbar */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: center;
}

.navlist {
    display: flex;
    align-items: center;
    list-style: none;
    padding: 0;
}

/* Navigation links */
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

        /* Form Container */
        .container {
            margin: 100px auto; /* Centered vertically and horizontally */
            padding: 25px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            width: 70%; /* Smaller width for a centered look */
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Success message styling */
        .success-message {
            color: black;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
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
        .form-group textarea {
            width: 100%;
            padding: 10px;
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
        footer {
    background-color: transparent; /* Make footer background transparent */
    font-size: 14px;
    padding: 20px 0;
    text-align: center;
    margin-top: auto; /* Pushes footer to the bottom */
}

footer p {
    margin: 0;
    color:white; /* Optional: Adjust text color if needed */
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
        <h1>Add Product</h1>

        <!-- Success Message inside container -->
        <?php if (!empty($success_message)) : ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="product">Product Name:</label>
                <input type="text" id="product" name="product" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="starting_price">Starting Price:</label>
                <input type="number" id="starting_price" name="starting_price" required>
            </div>

            <div class="form-group">
                <label for="auction_start_time">Auction Start Time:</label>
                <input type="datetime-local" id="auction_start_time" name="auction_start_time" required>
            </div>

            <div class="form-group">
                <label for="pickup_location">Pickup Location:</label>
                <select id="pickup_location" name="pickup_location" required>
                    <option value="">Select a location</option>
                    <option value="Location 1">Location 1</option>
                    <option value="Location 2">Location 2</option>
                    <option value="Location 3">Location 3</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit">Add Product</button>
            </div>
        </form>
    </div>

<!-- Footer Section -->
<footer>
    <div >
        <p>&copy; 2024 eAuction. All Rights Reserved.</p>
        <p>Contact us: 📞 9699040876 | ✉️ smaauction22@gmail.com</p>
    </div>
</footer>
</body>
</html>
