<?php
include 'db.php'; 

session_start();


// Check if the farmer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit;
}

$farmer_id = $_SESSION['user_id'];

if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $new_price = $_POST['new_price'];
    $new_quantity = $_POST['new_quantity'];
    $status = $_POST['status'];

    $sql = "UPDATE Products 
            SET price = ?, quantity = ?, status = ? 
            WHERE product_id = ? AND farmer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dssi", $new_price, $new_quantity, $status, $product_id, $farmer_id);

    if ($stmt->execute()) {
        echo "Product updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Get product details to prefill the form
$product_id = $_GET['product_id'];
$sql = "SELECT * FROM Products WHERE product_id = ? AND farmer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $product_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
</head>
<body>
    <h1>Update Product</h1>
    <form method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
        
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?php echo $product['product_name']; ?>" disabled><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" disabled><?php echo $product['description']; ?></textarea><br>

        <label for="new_price">New Price:</label>
        <input type="number" id="new_price" name="new_price" value="<?php echo $product['price']; ?>" required><br>

        <label for="new_quantity">New Quantity:</label>
        <input type="number" id="new_quantity" name="new_quantity" value="<?php echo $product['quantity']; ?>" required><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Available" <?php if ($product['status'] === 'Available') echo 'selected'; ?>>Available</option>
            <option value="Sold" <?php if ($product['status'] === 'Sold') echo 'selected'; ?>>Sold</option>
        </select><br>

        <input type="submit" name="update_product" value="Update Product">
    </form>
</body>
</html>
