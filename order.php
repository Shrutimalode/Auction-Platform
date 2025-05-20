<?php
include 'db.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata'); 

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$listing_id = $_GET['listing_id'] ?? null;
$customer_id = $_SESSION['user_id'] ?? null;

if (!$listing_id || !$customer_id) {
    die("Missing listing ID or user ID.");
}

// Fetch product details
$productQuery = $conn->prepare("SELECT product, starting_price, auction_start_time, auction_end_time, auction_round FROM listings WHERE listing_id = ?");
$productQuery->bind_param("i", $listing_id);
$productQuery->execute();
$productResult = $productQuery->get_result();
$product = $productResult->fetch_assoc();
$productQuery->close();

if (!$product) {
    die("Product not found.");
}

// Calculate auction times
$auction_start_time = strtotime($product['auction_start_time']);
$auction_end_time = strtotime($product['auction_end_time']);
$current_time = time();

$time_left = $auction_end_time - $current_time;
$auction_active = $time_left > 0 && $current_time >= $auction_start_time;

// Handle AJAX request
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    // Get current bids
    $bidsQuery = $conn->prepare("SELECT users.username, bids.bid_amount, bids.bid_time 
                                FROM bids 
                                JOIN users ON bids.customer_id = users.user_id 
                                WHERE bids.listing_id = ? AND bids.auction_round = ? 
                                ORDER BY bids.bid_amount DESC, bids.bid_time ASC");
    $bidsQuery->bind_param("ii", $listing_id, $product['auction_round']);
    $bidsQuery->execute();
    $allBids = $bidsQuery->get_result();
    $bidsQuery->close();
    
    $bids = [];
    while ($bid = $allBids->fetch_assoc()) {
        $bids[] = $bid;
    }
    
    echo json_encode([
        'bids' => $bids,
        'time_left' => $time_left,
        'auction_active' => $auction_active
    ]);
    exit;
}

// Handle auction end logic
if (!$auction_active && $current_time >= $auction_start_time) {
    // Fetch winner
    $winnerQuery = $conn->prepare("SELECT users.user_id, users.username, bids.bid_amount 
                                  FROM bids 
                                  JOIN users ON bids.customer_id = users.user_id 
                                  WHERE bids.listing_id = ? AND bids.auction_round = ? 
                                  ORDER BY bids.bid_amount DESC, bids.bid_time ASC LIMIT 1");
    $winnerQuery->bind_param("ii", $listing_id, $product['auction_round']);
    $winnerQuery->execute();
    $winnerResult = $winnerQuery->get_result();
    $winner = $winnerResult->fetch_assoc();
    $winnerQuery->close();

    if ($winner) {
        // Create order
        $createOrder = $conn->prepare("INSERT INTO orders (customer_id, listing_id, bid_amount, order_date) VALUES (?, ?, ?, NOW())");
        $createOrder->bind_param("iid", $winner['user_id'], $listing_id, $winner['bid_amount']);
        $createOrder->execute();
        $order_id = $conn->insert_id;
        $createOrder->close();

        // Archive bids
        $archiveBidsQuery = $conn->prepare("INSERT INTO archived_bids (customer_id, listing_id, bid_amount, bid_time, auction_round) 
                                          SELECT customer_id, listing_id, bid_amount, bid_time, auction_round 
                                          FROM bids WHERE listing_id = ? AND auction_round = ?");
        $archiveBidsQuery->bind_param("ii", $listing_id, $product['auction_round']);
        $archiveBidsQuery->execute();
        $archiveBidsQuery->close();

        // Clear current bids
        $clearBidsQuery = $conn->prepare("DELETE FROM bids WHERE listing_id = ? AND auction_round = ?");
        $clearBidsQuery->bind_param("ii", $listing_id, $product['auction_round']);
        $clearBidsQuery->execute();
        $clearBidsQuery->close();
    }

    // Increment round
    $updateRoundQuery = $conn->prepare("UPDATE listings SET auction_round = auction_round + 1 WHERE listing_id = ?");
    $updateRoundQuery->bind_param("i", $listing_id);
    $updateRoundQuery->execute();
    $updateRoundQuery->close();
}

// Get current bids for display
$bidsQuery = $conn->prepare("SELECT users.username, bids.bid_amount, bids.bid_time 
                            FROM bids 
                            JOIN users ON bids.customer_id = users.user_id 
                            WHERE bids.listing_id = ? AND bids.auction_round = ? 
                            ORDER BY bids.bid_amount DESC, bids.bid_time ASC");
$bidsQuery->bind_param("ii", $listing_id, $product['auction_round']);
$bidsQuery->execute();
$allBids = $bidsQuery->get_result();
$bidsQuery->close();

// Determine minimum bid amount
$minBid = $product['starting_price'];
if ($allBids->num_rows > 0) {
    $allBids->data_seek(0);
    $highestBid = $allBids->fetch_assoc();
    $minBid = $highestBid['bid_amount'] + 1;
    $allBids->data_seek(0); // Reset pointer
}

// Handle bid submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $auction_active) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $bid_amount = filter_input(INPUT_POST, 'bid_amount', FILTER_VALIDATE_FLOAT);
    
    if ($bid_amount === false || $bid_amount < $minBid) {
        $error = "Invalid bid amount. Minimum bid is ₹" . number_format($minBid, 2);
    } else {
        // Check wallet balance
        $balanceQuery = $conn->prepare("SELECT wallet_balance FROM users WHERE user_id = ?");
        $balanceQuery->bind_param("i", $customer_id);
        $balanceQuery->execute();
        $balanceQuery->bind_result($wallet_balance);
        $balanceQuery->fetch();
        $balanceQuery->close();

        if ($bid_amount > $wallet_balance) {
            $error = "Insufficient balance for this bid.";
        } else {
            // Place bid
            $stmt = $conn->prepare("INSERT INTO bids (customer_id, listing_id, bid_amount, bid_time, auction_round) VALUES (?, ?, ?, NOW(), ?)");
            $stmt->bind_param("iidi", $customer_id, $listing_id, $bid_amount, $product['auction_round']);

            if ($stmt->execute()) {
                $success = "Your bid of ₹" . number_format($bid_amount, 2) . " has been placed!";
                // Update minimum bid for next bidder
                $minBid = $bid_amount + 1;
            } else {
                $error = "Error placing bid: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Bid - <?= htmlspecialchars($product['product']) ?></title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }

        .navbar {
            width: 100%;
            background-color: #4CAF50;
            padding: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: flex-end;
        }

        .navlist a {
            color: #4CAF50;
            background-color: white;
            font-size: 1rem;
            margin-right: 15px;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 25px;
            transition: transform 0.3s, background-color 0.3s;
        }

        .navlist a:hover {
            background-color: darkgreen;
            color: white;
            transform: scale(1.1);
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        h1, h2 {
            color: #4CAF50;
            margin-bottom: 10px;
        }

        #timer {
            font-size: 1.5em;
            color: #ff5722;
            margin: 15px 0;
            padding: 10px;
            background-color: #fff8f0;
            border-radius: 5px;
        }

        form {
            margin: 20px 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="number"],
        button {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: box-shadow 0.3s, border-color 0.3s;
        }

        input[type="number"] {
            width: 200px;
            text-align: center;
        }

        input[type="number"]:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .bids-list {
            margin-top: 30px;
        }

        .bids-list h3 {
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .bid-entry {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 1em;
            transition: background-color 0.2s;
        }

        .bid-entry:hover {
            background-color: #f9f9f9;
        }

        .bid-entry span {
            min-width: 100px;
            text-align: right;
        }

        .bid-entry span:first-child {
            flex-grow: 1;
            text-align: left;
        }

        .bid-entry .price {
            color: #4CAF50;
            font-weight: bold;
        }

        .winner {
            margin: 20px 0;
            padding: 20px;
            background-color: #e0f7fa;
            color: #00796b;
            border-radius: 8px;
            text-align: center;
        }

        .winner h3 {
            margin-bottom: 10px;
        }

        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .bid-status {
            margin: 15px 0;
            font-weight: bold;
        }

        .highlight {
            animation: highlight 2s;
        }

        @keyframes highlight {
            0% { background-color: #ffff99; }
            100% { background-color: transparent; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="navlist">
            <a href="customer_dashboard.php">Home</a>
        </div>
    </div>

    <div class="container">
        <h1>Place Your Bid</h1>
        <h2><?= htmlspecialchars($product['product']) ?></h2>
        <p>Starting Price: ₹<?= number_format($product['starting_price'], 2) ?></p>
        
        <div id="timer"></div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($current_time < $auction_start_time): ?>
            <div class="alert">
                Auction starts at <?= date("F j, Y H:i:s", $auction_start_time) ?>
            </div>
        <?php elseif (!$auction_active && isset($winner)): ?>
            <div class="winner">
                <h3>🎉 Auction Winner 🎉</h3>
                <p>Congratulations, <strong><?= htmlspecialchars($winner['username']) ?></strong>!</p>
                <p>Winning Bid: <strong>₹<?= number_format($winner['bid_amount'], 2) ?></strong></p>
                
                <form action="payment_page.php" method="GET">
                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
                    <input type="hidden" name="listing_id" value="<?= htmlspecialchars($listing_id) ?>">
                    <input type="hidden" name="winner_id" value="<?= htmlspecialchars($winner['user_id']) ?>">
                    <button type="submit">Proceed to Payment</button>
                </form>
            </div>
        <?php elseif ($auction_active): ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="form-group">
                    <label for="bid_amount">Your Bid Amount (₹)</label>
                    <input type="number" id="bid_amount" name="bid_amount" 
                           min="<?= $minBid ?>" step="1" 
                           value="<?= $minBid ?>" required>
                </div>
                
                <div class="bid-status">
                    Minimum bid: ₹<?= number_format($minBid, 2) ?>
                </div>
                
                <button type="submit">Place Bid</button>
            </form>
        <?php else: ?>
            <div class="alert">
                This auction has ended. Check back soon for the next round!
            </div>
        <?php endif; ?>

        <div class="bids-list">
            <h3>Current Bids</h3>
            <div id="bids-container">
                <?php if ($allBids->num_rows > 0): ?>
                    <?php while ($bid = $allBids->fetch_assoc()): ?>
                        <div class="bid-entry">
                            <span><?= htmlspecialchars($bid['username']) ?></span>
                            <span class="price">₹<?= number_format($bid['bid_amount'], 2) ?></span>
                            <span><?= date("H:i:s", strtotime($bid['bid_time'])) ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No bids yet. Be the first to bid!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Timer functionality
        const auctionEndTime = <?= $auction_end_time * 1000 ?>;
        const auctionStartTime = <?= $auction_start_time * 1000 ?>;
        let currentTime = <?= $current_time * 1000 ?>;
        
        function updateTimer() {
            currentTime += 1000;
            
            if (currentTime < auctionStartTime) {
                // Auction hasn't started
                const timeUntilStart = auctionStartTime - currentTime;
                document.getElementById('timer').textContent = `Auction starts in: ${formatTime(timeUntilStart)}`;
                return;
            }
            
            if (currentTime >= auctionEndTime) {
                // Auction ended
                document.getElementById('timer').textContent = "Auction Ended";
                clearInterval(timerInterval);
                return;
            }
            
            // Auction is active
            const timeLeft = auctionEndTime - currentTime;
            document.getElementById('timer').textContent = `Time Left: ${formatTime(timeLeft)}`;
        }
        
        function formatTime(ms) {
            const days = Math.floor(ms / (1000 * 60 * 60 * 24));
            const hours = Math.floor((ms % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((ms % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((ms % (1000 * 60)) / 1000);
            
            return `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }
        
        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
        
        // AJAX bid updates
        function fetchBids() {
            fetch(`?listing_id=<?= $listing_id ?>&ajax=1`)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    // Update bids list
                    const container = document.getElementById('bids-container');
                    if (data.bids.length > 0) {
                        container.innerHTML = data.bids.map(bid => `
                            <div class="bid-entry">
                                <span>${escapeHtml(bid.username)}</span>
                                <span class="price">₹${parseFloat(bid.bid_amount).toFixed(2)}</span>
                                <span>${new Date(bid.bid_time).toLocaleTimeString()}</span>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p>No bids yet. Be the first to bid!</p>';
                    }
                    
                    // Highlight new bids
                    const entries = container.querySelectorAll('.bid-entry');
                    if (entries.length > 0) {
                        entries[0].classList.add('highlight');
                    }
                    
                    // Update timer if needed
                    if (!data.auction_active) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error fetching bids:', error);
                });
        }
        
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
        
        // Fetch bids every 3 seconds if auction is active
        <?php if ($auction_active): ?>
            setInterval(fetchBids, 3000);
            
            // Also fetch after placing a bid
            document.querySelector('form')?.addEventListener('submit', function() {
                setTimeout(fetchBids, 1000);
            });
        <?php endif; ?>
    </script>
    <script>
    window.auctionStarted = <?= $auction_active ? 'true' : 'false' ?>;

    setInterval(function() {
        fetch(window.location.href + '&ajax=1')
            .then(response => response.json())
            .then(data => {
                if (data.auction_active && !window.auctionStarted) {
                    window.auctionStarted = true;
                    location.reload();
                }
            });
    }, 5000);
</script>

</body>
</html>