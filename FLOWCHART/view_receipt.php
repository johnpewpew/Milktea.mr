<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:index.php');
}

$transaction_id = $_GET['id'];
$transaction_query = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
$transaction_query->bind_param('i', $transaction_id);
$transaction_query->execute();
$transaction = $transaction_query->get_result()->fetch_assoc();

if (!$transaction) {
    echo "Transaction not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Transaction #<?= $transaction['id'] ?></title>
    <link rel="stylesheet" href="transaction.css">
</head>
<body>
    <div class="container">
        <h1>Receipt - Transaction #<?= $transaction['id'] ?></h1>
        <p>Date: <?= date("Y-m-d H:i:s", strtotime($transaction['transaction_date'])) ?></p>
        <p>Order Details: <pre><?= htmlspecialchars($transaction['order_details']) ?></pre></p>
        <p>Total Amount: <?= number_format($transaction['total_amount'], 2) ?></p>
        <p>Payment Status: <?= htmlspecialchars($transaction['payment_status']) ?></p>
        <a href="transactions.php" class="back-button">Back to Transactions</a>
    </div>
</body>
</html>
