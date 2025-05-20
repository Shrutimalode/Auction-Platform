<?php
include 'db.php'; 

session_start();


// Check if the user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit;
}

// Fetch products for the logged-in farmer
$farmer_id = $_SESSION['user_id']; 
$sql = "SELECT * FROM listings WHERE farmer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
  
    <style>
        /* General reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: linear-gradient(rgba(97, 132, 110, 0.7), rgba(0, 77, 64, 0.7)), url('try.jpg') no-repeat center center/cover;
            color: #333;
            padding: 0;
            margin: 0;
        }

        /* Navbar styles */
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

        /* Main content styling */
        h1 {
            margin: 20px 0;
            font-size: 2em;
            color:white;
        }

        h2 {
            margin-top: 10px;
            font-size: 1.2em;
            color:white;
            font-style: italic;
        }

        .listings {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            justify-content: center;
            padding: 20px;
        }

        .listing-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 340px; /* Increase width */
            padding: 20px;
            transition: transform 0.2s;
            text-align: center;
        }

        .listing-card:hover {
            transform: scale(1.02);
        }

        .listing-card h3 {
            color: #4CAF50;
            margin-bottom: 10px;
            font-size: 1.5em; /* Increase font size */
        }

        .listing-card p {
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .listing-card button {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .listing-card button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <ul class="navlist">
            <a href="farmer_dashboard.php">Go to dashboard</a>
         
        </ul>
    </div>

    <h1>Your Products</h1>
    <h2>Efficiently manage your listings, edit details, and make updates anytime!</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="listings">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="listing-card">
                    <h3><?= htmlspecialchars($row['product']) ?></h3>
                    <p><strong>Description:</strong> <?= htmlspecialchars($row['description']) ?></p>
                    <p><strong>Starting Price:</strong> ₹<?= htmlspecialchars($row['starting_price']) ?></p>
                    <p><strong>Auction Start:</strong> <?= htmlspecialchars($row['auction_start_time']) ?></p>
                    
                    <!-- Update and Delete Options -->
                    <a href="edit_product.php?product_id=<?= $row['listing_id'] ?>"><button>Update Product</button></a>
                    <a href="delete_product.php?product_id=<?= $row['listing_id'] ?>"><button>Delete Product</button></a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</body>
</html>
