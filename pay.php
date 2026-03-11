<?php
include_once 'dbConnection.php';
session_start();
if(!isset($_SESSION['email'])){
    header('location:index.php');
    exit;
}
$email = $_SESSION['email'];
$eid = isset($_GET['eid']) ? $_GET['eid'] : '';

// lookup quiz title for display
$title = '';
if($eid){
    $q = mysqli_query($con,"SELECT title FROM quiz WHERE eid='$eid'") or die('Error');
    if($r = mysqli_fetch_array($q)){
        $title = $r['title'];
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // record payment (simulated)
    $amount = $_POST['amount'];
    $method = $_POST['method'];
    // validate data minimally
    if($eid && $amount){
        mysqli_query($con,"INSERT INTO payment(email,eid,amount,method,status) VALUES('$email','$eid','$amount','$method','paid')") or die('Error inserting payment');
        header("location:account.php?q=paid&eid=$eid");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Payment</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>
<body style="padding:20px;">
<div class="container">
    <h3>Unlock Topic</h3>
    <?php if($title): ?>
        <p>You are about to unlock: <strong><?php echo htmlspecialchars($title); ?></strong></p>
    <?php endif; ?>
    <p>The first topic is always free. To access additional topics you need to make a one-time payment. Payment is processed same day; Rwandan mobile money (MTN MoMo, Airtel Money) or bank transfer are accepted. Use the numbers listed below.</p>
    <ul>
        <li>MTN MoMo: +2507XXXXXXXX</li>
        <li>Airtel Money: +2507XXXXXXXX</li>
    </ul>
    <p>After sending the money, enter the amount and the method you used below and click "I have paid".</p>
    <form method="post" class="form-inline">
        <div class="form-group">
            <label>Amount (RWF)</label>
            <input type="number" name="amount" class="form-control" required value="500" />
        </div>
        <div class="form-group" style="margin-left:10px;">
            <label>Method</label>
            <select name="method" class="form-control">
                <option>MTN MoMo</option>
                <option>Airtel Money</option>
                <option>Bank Transfer</option>
            </select>
        </div>
        <input type="hidden" name="eid" value="<?php echo htmlspecialchars($eid); ?>" />
        <button type="submit" class="btn btn-primary" style="margin-left:10px;">I have paid</button>
    </form>
    <p style="margin-top:20px;">If you are a foreign visitor, contact <a href="mailto:support@example.com">support@example.com</a> for details on international payment.</p>
</div>
</body>
</html>