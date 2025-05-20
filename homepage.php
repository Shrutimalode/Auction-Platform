

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Website - Home</title>
     <link rel="stylesheet" href="responsive.css"> 

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f7f7f7;
        }

        /* Navbar */
        /* Navbar */
        .navbar {
            background: #43a047;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }

        .nav-link {
            color: white;
            font-size: 1rem;
            margin-right: 15px;
            text-decoration: none;
            padding: 8px 18px;
            transition: 0.4s;
            border-radius: 25px;
        }

        .nav-link:hover {
            background-color: white;
            color: #43a047;
        }

        .contact-info {
            color: white;
            font-size: 14px;
            padding: 10px 0;
        }

        .navbar-toggler {
            border-color: white;
        }

        .navbar-toggler-icon {
            background-color: white;
            width: 30px;
            height: 30px;
            border-radius: 4px;
        }

        .contact-info {
            color: white;
            font-size: 14px;
            padding-top: 10px;
            padding-right: 10px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(97, 132, 110, 0.7), rgba(0, 77, 64, 0.7)), url('try.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease-in-out;
        }

        .hero-section p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease-in-out;
        }

        .hero-section .btn-primary {
            background-color:#43a047;
            color: white;
            font-size: 1.3rem;
            padding: 12px 40px;
            transition: all 0.4s ease;
            border-radius: 50px;
            border-color:white;
            animation: fadeInUp 1.2s ease-in-out;
        }

        .hero-section .btn-primary:hover {
            background-color:white;
            transform: scale(1.05);
            color:#43a047;
            border-color:#43a047;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Features Section */
        .features-section {
            padding: 60px 0;
            background-color: #fff;
        }

        .features-section h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #43a047;
            font-weight: bold;
        }

        .card {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            border-radius: 15px 15px 0 0;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            text-align: center;
            padding: 20px;
        }

        .card-body h5 {
            color: #333;
            font-size: 1.3rem;
        }

        .card-body p {
            color: #777;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* Registration Modal */
        .modal-header {
            background-color: #43a047;
            color: white;
        }

        .form-control, .form-select {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #43a047;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #1b5e20;
            box-shadow: 0 0 0 .25rem rgba(45, 106, 79, 0.25);
        }

        .btn-primary {
            background-color: #00c853;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 1.1rem;
        }

        .btn-primary:hover {
            background-color: #00e676;
            border-color: #00e676;
        }
  
        footer {
    background-color: transparent; /* Make footer background transparent */
    font-size: 14px;
    padding: 20px 0;
    text-align: center;
    margin-top: 5%; /* Pushes footer to the bottom */
}

footer p {
    margin: 0;
    color:dark-grey; /* Optional: Adjust text color if needed */
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
                <li class="nav-item d-lg-none text-center">
                    <div class="contact-info">
                        📞 9769040876 | ✉️ smaauction22@gmail.com
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about_us.php">About Us</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Hero Section -->

<section class="hero-section">
    <div class="text-box text-center">
        <h1>Welcome to Farm Auction Platform</h1>
        <p>Where farmers and customers connect directly!</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">Get Started</button>
    </div>
</section>


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




<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <h2 class="text-center mb-5">Key Features</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="f1.jpg" alt="Live Auction Image" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Live Auctions</h5>
                        <p class="card-text">Join live auctions and place your bids in real-time, directly connecting farmers with buyers.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="f2.jpg" alt="Farm Fresh Produce Image" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Farm Fresh Produce</h5>
                        <p class="card-text">Access organic, fresh produce directly from the farm to your home with no middlemen.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="f3.jpg" alt="Secure Payments Image" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Secure Payments</h5>
                        <p class="card-text">Make payments through our secure platform with fast processing and low fees.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Footer Section -->
<footer>
    <div>
        <p>&copy; 2024 eAuction. All Rights Reserved.</p>
        <p>Contact us: 📞 9699040876 | ✉️ smaauction22@gmail.com</p>
    </div>
</footer>






<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript for handling AJAX form submission -->
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault(); // Prevent the form from submitting in the traditional way

  // Get form data
  const formData = new FormData(this);
  
  // Send the form data using AJAX
  fetch('register.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    // Display the response message inside the modal
    document.getElementById('registerMessage').innerHTML = data;

    // Optionally, reset the form
    this.reset();
  })
  .catch(error => {
    // Show error message inside the modal
    document.getElementById('registerMessage').innerHTML = '<div class="alert alert-danger">An error occurred during registration.</div>';
  });
});
</script>

</body>
</html>
