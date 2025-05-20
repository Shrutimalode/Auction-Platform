<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(to right, #e8f5e9, #f0f2f5);
        }

        /* Header Styling */
        header {
            background-color: #43a047;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        header a button {
            background-color: #fff;
            color: #43a047;
            border: 1px solid #43a047;
            padding: 8px 15px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s;
        }

        header a button:hover {
            background-color: #43a047;
            color: #fff;
        }

        /* Main Container */
        .container {
            max-width: 900px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadeIn 1s ease;
        }

        h1, h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        p {
            color: #555;
            line-height: 1.6;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        /* Rate Us Section */
        .rate-us {
            margin-top: 30px;
            background: #e8f5e9;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            animation: slideIn 1s ease-out;
        }

        .rate-us h2 {
            margin-bottom: 10px;
            color: #388e3c;
        }

        .stars {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .stars input {
            display: none;
        }

        .stars label {
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.3s, transform 0.3s;
        }

        .stars label:hover,
        .stars input:checked ~ label {
            color: #FFD700;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        

            header h1 {
                font-size: 1.5rem;
            }

            header a button {
                font-size: 0.9rem;
                padding: 5px 10px;
            }

            p {
                font-size: 0.9rem;
            }

            .rate-us h2 {
                font-size: 1.5rem;
            }
        
    </style>
</head>
<body>
<header>
    <h1>Farmers Auction Platform</h1>
    <a href="logout.php">
        <button>Logout</button>
    </a>
</header>

<div class="container">
    <h1>About Us</h1>
    <p>Welcome to AgriDirect! We are committed to connecting farmers directly with customers, eliminating the middleman and ensuring farmers get the best value for their products. Our platform offers a secure auction system where customers can bid on fresh produce and other farm products.</p>
    <p>At AgriDirect, we strive to make farming sustainable and profitable by leveraging technology to empower both farmers and consumers. Join us in building a fair and transparent agricultural marketplace.</p>

    <div class="rate-us">
        <h2>Rate Us</h2>
        <label for="rating">How would you rate your experience?</label>
        <div class="stars">
            <input type="radio" id="star5" name="rating" value="5">
            <label for="star5">&#9733;</label>
            <input type="radio" id="star4" name="rating" value="4">
            <label for="star4">&#9733;</label>
            <input type="radio" id="star3" name="rating" value="3">
            <label for="star3">&#9733;</label>
            <input type="radio" id="star2" name="rating" value="2">
            <label for="star2">&#9733;</label>
            <input type="radio" id="star1" name="rating" value="1">
            <label for="star1">&#9733;</label>
        </div>
    </div>
</div>
</body>
</html>
