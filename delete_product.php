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
$farmer_id = $_SESSION['user_id'];

// Delete the product from the database
$stmt = $conn->prepare("DELETE FROM listings WHERE listing_id = ? AND farmer_id = ?");
$stmt->bind_param("ii", $product_id, $farmer_id);
$stmt->execute();

echo "Product deleted successfully!";
header("Location: manage_products.php"); // Redirect back to the manage products page
exit;
?>
