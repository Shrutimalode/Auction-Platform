<?php

session_start();

require 'db.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to find the user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists and the password matches
    if ($user && password_verify($password, $user['password'])) {
        // Store user information in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];  // 'farmer' or 'customer'

        // Redirect to the appropriate dashboard based on role
        if ($user['role'] == 'farmer') {
            header("Location: farmer_dashboard.php");
        } elseif ($user['role'] == 'customer') {
            header("Location: customer_dashboard.php");
        }
        exit;
    } else {
        echo "<script>alert('Invalid username or password.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - eAuction</title>
    <link rel="stylesheet" href="responsive.css">
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        /* Navbar styling */

        body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}




        .navbar {
            background: #43a047;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding-top: 5px;  /* Reduced padding */
    padding-bottom: 5px; /* Reduced padding */
        }

        .navbar-brand {
            color: white;
            font-size: 1.5rem;
           
        }

        .navlist a {
            color: white;
            font-size: 1rem;
            margin-right: 15px;
            text-decoration: none;
            padding: 5px 10px; 
            transition: 0.4s;
          border-radius :25px;
        }

        .navlist a:hover {
            background-color: white;
            color: #43a047;
        }
        .contact-info {
            color: white;
            font-size: 12px;
            padding: 5px;
            text-align: center;
        }

        /* Container for login form and info box */
        .login-container {
    display: flex;
    flex-wrap: wrap; /* Enable wrapping for smaller screens */
    justify-content: space-between;
    align-items: center;
    min-height: 100vh; /* Full viewport height */
    background: linear-gradient(rgba(97, 132, 110, 0.7), rgba(0, 77, 64, 0.7)), url('try.jpg') no-repeat center center/cover;
    background-size: cover; /* Ensure it covers the entire area */
    padding: 30px;
    gap: 20px; /* Add spacing between items */
}


        /* Styling for the login form box */
        .login-form-box {
    flex: 1;
    max-width: 600px;  /* Ensure a consistent width */
    background-color: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
.login-info-box {
    flex: 1;
    max-width: 600px;  /* Ensure a consistent width */
    background-color: transperant;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

        .login-form-box h2 {
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 2rem;
            text-align: center;
        }

        .login-form-box input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .login-form-box input:focus {
            outline: none;
            border-color: #43a047;
        }

        .login-form-box .btn-login {
            background-color: #43a047;
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-form-box .btn-login:hover {
            background-color: #357a38;
        }

        /* Info box styling */
        /* .login-info-box {
            flex: 1;
            max-width: 500px; 
            padding: 20px;
        } */

        .info-text-box {
            background-color: white;
            padding: 30px; /* Increased padding for a spacious look */
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Darker shadow for more depth */
            height: 100%; /* Fill height */
            transition: transform 0.3s ease; /* Smooth transition */
        }

        /* Add hover effect for info text box */
        .info-text-box:hover {
            transform: scale(1.02); /* Slightly enlarge on hover */
        }

        .info-text-box h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
            font-size: 1.8rem; /* Larger font size */
        }

        .info-text-box p {
            font-size: 1.1rem; /* Increased font size */
            color: #333;
            text-align: center;
            line-height: 1.5;
        }
        /* footer {
    background-color: transparent; 
    font-size: 14px;
    padding: 20px 0;
    text-align: center;
    margin-top: 30%; 
} */

footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 10px;
    margin-top: auto; /* Push footer to the bottom */
    width: 100%;
}

footer p {
    margin: 0;
    color: white; /* Optional: Adjust text color if needed */
}

    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">eAuction</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="contact-info">📞 9699040876 | ✉️ smaauction22@gmail.com</span>
                </li>
             
                <li class="nav-item navlist">
                    <a class="nav-link" href="about_us.php">About Us</a>
                </li>
                <li class="nav-item navlist">
                    <a class="nav-link" href="homepage.php">Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>



    <!-- Login Form -->
    <div class="login-container">
    <!-- Login Form -->
    <div class="login-form-box">
        <h2>Login to Your Account</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn btn-success w-100 btn-lg">Login</button>
        </form>
    </div>

    <!-- Info Box -->
    <div class="login-info-box">
        <div class="info-text-box">
            
            <p>
                Our platform connects farmers and buyers, allowing you to auction your produce easily and efficiently. 
                Join us to access a wider market, make great deals, and ensure your hard work gets the recognition it deserves.
            </p>
        </div>
    </div>
</div>

    <!-- Footer Section -->
<footer >
    <div>
        <p>&copy; 2024 eAuction. All Rights Reserved.</p>
        <p>Contact us: 📞 9699040876 | ✉️ smaauction22@gmail.com</p>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    
   
</body>
</html>
