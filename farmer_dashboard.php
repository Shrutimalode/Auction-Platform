<?php

session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .dashboard-header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .card {
            margin: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .btn-custom {
            background-color: #4CAF50;
            color: white;
        }
        .btn-custom:hover {
            background-color: #45a049;
        }

        footer {
    background-color: transparent; /* Make footer background transparent */
    font-size: 14px;
    padding: 20px 0;
    text-align: center;
    margin-top: 17%; /* Pushes footer to the bottom */
}

footer p {
    margin: 0;
    color:dark-grey; /* Optional: Adjust text color if needed */
}
    </style>
</head>
<body>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <h1>Welcome, Farmer!</h1>
    <p>Manage your products and track customer orders</p>
    <a href="farmer_profile.php" style="position: absolute; top: 20px; right: 20px;">
        <img src="profile.jpg" alt="Profile" style="width: 40px; height: 40px;">
    </a>
    <a href="logout.php">
            <button>Logout</button>
        </a>
</div>


<!-- Main Content Section -->
<div class="container">
    <div class="row">
        <!-- Manage Products Card -->
        <div class="col-md-4">
            <div class="card">
                <h3 class="card-title">Manage Your Products</h3>
                <p class="card-text">Update your available products, prices, and quantities.</p>
                <a href="manage_products.php" class="btn btn-custom">Update Products</a>
            </div>
        </div>

        <!-- Add Product Card -->
        <div class="col-md-4">
            <div class="card">
                <h3 class="card-title">Add New Product</h3>
                <p class="card-text">Add new products to the system to start selling.</p>
                <a href="add_product.php" class="btn btn-custom">Add Product</a>
            </div>
        </div>

        <!-- Notifications/Orders Card -->
        <div class="col-md-4">
            <div class="card">
                <h3 class="card-title">Customer Orders</h3>
                <p class="card-text">View and respond to customer orders. Notifications of new orders will be listed here.</p>
                <a href="view_order.php" class="btn btn-custom">View Orders</a>
            </div>
        </div>
    </div>
</div>


<!-- Footer Section -->
<footer>
    <div class="container">
        <p>&copy; 2024 eAuction. All Rights Reserved.</p>
        <p>Contact us: 📞 9699040876 | ✉️ smaauction22@gmail.com</p>
    </div>
</footer>

<!-- Bootstrap JS for functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
