<?php
require 'db.php';  // Database connection


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
    $role = $_POST['role'];  // Farmer or Customer
    $phone = $_POST['phone'];
    $account_holder_name = $_POST['account_holder_name'];
    $account_number = $_POST['account_number'];
    $ifsc_code = $_POST['ifsc_code'];
    $bank_name = $_POST['bank_name'];

    // Insert the new user into the database
    $sql = "INSERT INTO users (username, password, role, phone, account_holder_name, account_number, ifsc_code, bank_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param('ssssssss', $username, $password, $role, $phone, $account_holder_name, $account_number, $ifsc_code, $bank_name);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error executing query: " . $stmt->error; // Show specific error
        }

        $stmt->close(); // Close the statement
    } else {
        echo "Error preparing statement: " . $conn->error; // Show specific error
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
        }
        .registration-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .registration-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .btn-custom {
            background-color:#091057;
            color: white;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="registerModalLabel">Register</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="registerForm">
          <!-- Username Field -->
          <div class="mb-3">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
          </div>

          <!-- Password Field -->
          <div class="mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
          </div>

          <!-- Role Selection (Farmer or Customer) -->
          <div class="mb-3">
            <select class="form-select" id="role" name="role" required>
              <option value="farmer">Farmer</option>
              <option value="customer">Customer</option>
            </select>
          </div>

          <!-- Phone Number Field -->
          <div class="mb-3">
            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
          </div>

          <!-- Account Holder Name Field -->
          <div class="mb-3">
            <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" placeholder="Account Holder Name" required>
          </div>

          <!-- Account Number Field -->
          <div class="mb-3">
            <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account Number" required>
          </div>

          <!-- Bank Name Field -->
          <div class="mb-3">
            <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name" required>
          </div>

          <!-- IFSC Code Field -->
          <div class="mb-3">
            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" placeholder="IFSC Code" required>
          </div>

          <!-- Register Button -->
          <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <div id="registerMessage"></div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript for handling the form submission via AJAX -->
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault(); // Prevent form from submitting the traditional way

  // Get form data
  const formData = new FormData(this);
  
  // Send form data via AJAX
  fetch('register.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    // Show the response message in the modal
    document.getElementById('registerMessage').innerHTML = data;
    
    // Optionally, reset the form
    this.reset();
  })
  .catch(error => {
    // Show error message in the modal
    document.getElementById('registerMessage').innerHTML = 'An error occurred during registration.';
  });
});
</script>

<!-- Bootstrap JS for functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
