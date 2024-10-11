<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:index.php');
}

// Fetch all pending transactions
$transactions_query = $conn->query("SELECT * FROM pending_transactions ORDER BY transaction_date DESC");
$transactions = $transactions_query->fetch_all(MYSQLI_ASSOC);

// Sample data for the receipt (default values)
$shopName = "Mr. Boy Special Tea";
$receiptTitle = "CASH RECEIPT";
$items = [
    ["description" => "Tea", "price" => 0.00],
    ["description" => "Cookies", "price" => 0.00],
];
$total = 0.00;
$cash = 0.00;
$change = $cash - $total;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="pending.css">
    <link rel="stylesheet" href="meme.css">
    <link rel="stylesheet" type="text/css" href="./css/main.css">
    <link rel="stylesheet" type="text/css" href="./css/admin.css">
    <link rel="stylesheet" type="text/css" href="./css/util.css">
    <title>Pending Transactions</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <style>
        /* Receipt Popup Styles */
        .receipt-popup, .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            justify-content: center;
            align-items: center;
        }
        .overlay {
            background: rgba(0, 0, 0, 0.7);
            z-index: 999;
        }
        .receipt-popup {
            background: white;
            border: 1px solid black;
            padding: 20px;
            z-index: 1000;
            max-width: 350px;
            text-align: center;
        }
        .receipt-popup h1, .receipt-popup h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .line {
            margin: 10px 0;
            border-top: 1px dashed black;
        }
        .description-table, .total {
            width: 100%;
            margin: 10px 0;
        }
        .thank-you {
            margin: 20px 0;
            font-weight: bold;
            font-size: 25px;
        }
    </style>
</head>
<body>
<div class="order-side">
    <?php include 'order_side.php'; ?>
</div>
    
<div class="container">
    <h1>Pending Transactions</h1>

    <div class="search-section">
        <input type="text" class="search-bar" placeholder="Search" id="search-transaction" onkeyup="searchTransactions()">
        <button class="search-button">Search</button>
        <select class="filter-dropdown" id="filter-dropdown" onchange="filterTransactions()">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Trans #</th>
                    <th>Date</th>
                    <th>Order</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="transaction-table-body">
                <?php foreach ($transactions as $transaction): ?>
                    <tr id="transaction-row-<?= htmlspecialchars($transaction['id']) ?>">
                        <td><?= htmlspecialchars($transaction['id']) ?></td>
                        <td><?= date("Y-m-d H:i:s", strtotime($transaction['transaction_date'])) ?></td>
                        <td class="order-details"><?= htmlspecialchars($transaction['order_details']) ?></td>
                        <td><?= number_format($transaction['total_amount'], 2) ?></td>
                        <td>
                            <button class="pay-now-button" onclick="showReceipt(<?= htmlspecialchars($transaction['id']) ?>, <?= htmlspecialchars($transaction['total_amount']) ?>)">View Receipt</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Receipt Popup -->
<div class="overlay" id="popup-overlay" onclick="closePopup()"></div>
<div class="receipt-popup" id="receipt-popup">
    <h1><?php echo $shopName; ?></h1>
    <div class="line"></div>
    <h2><?php echo $receiptTitle; ?></h2>
    <div class="line"></div>

    <table class="description-table">
        <tr>
            <th>Description</th>
            <th>Price</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['description']); ?></td>
            <td><?php echo number_format($item['price'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="line"></div>

    <div class="total">
        <span>Total: </span>
        <span id="popup-total">0.00</span>
    </div>
    <div class="total">
        <span>Cash: </span>
        <span id="popup-cash">0.00</span>
    </div>
    <div class="total">
        <span>Change: </span>
        <span id="popup-change">0.00</span>
    </div>

    <div class="line"></div>

    <div class="thank-you">THANK YOU!</div>
</div>

<script>
    function showReceipt(transactionId, totalAmount) {
        document.getElementById('popup-total').innerText = totalAmount.toFixed(2);
        document.getElementById('popup-cash').innerText = '0.00';
        document.getElementById('popup-change').innerText = '0.00';
        document.getElementById('receipt-popup').style.display = 'flex';
        document.getElementById('popup-overlay').style.display = 'block';
    }

    function closePopup() {
        document.getElementById('receipt-popup').style.display = 'none';
        document.getElementById('popup-overlay').style.display = 'none';
    }
</script>
</body>
</html>
