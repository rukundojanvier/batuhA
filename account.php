<?php
include_once 'dbConnection.php';
session_start();
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
if (!$email) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Netcamp | Online Examination</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

/* ================= GLOBAL ================= */
body{
    margin:0;
    padding:0;
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#dfe9f3,#ffffff);
    color:#2c3e50;
}

/* ================= NAVBAR ================= */
.navbar{
    background:linear-gradient(90deg,#141e30,#243b55);
    padding:15px 40px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    box-shadow:0 4px 20px rgba(0,0,0,0.15);
    position:sticky;
    top:0;
    z-index:1000;
}

.navbar .logo{
    color:#fff;
    font-size:22px;
    font-weight:600;
}

.navbar ul{
    list-style:none;
    display:flex;
    margin:0;
    padding:0;
}

.navbar ul li{
    margin-left:20px;
}

.navbar ul li a{
    text-decoration:none;
    color:#fff;
    padding:8px 15px;
    border-radius:6px;
    transition:0.3s;
}

.navbar ul li a:hover,
.active{
    background:#00c6ff;
    color:#fff;
}

/* ================= CONTAINER ================= */
.container{
    max-width:1100px;
    margin:40px auto;
    background:#fff;
    padding:40px;
    border-radius:20px;
    box-shadow:0 10px 40px rgba(0,0,0,0.08);
}

/* ================= TABLE ================= */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th{
    background:#4e73df;
    color:white;
    padding:14px;
    font-weight:500;
}

table td{
    padding:12px;
    text-align:center;
}

table tr:nth-child(even){
    background:#f8f9fc;
}

table tr:hover{
    background:#e3f2fd;
    transition:0.2s;
}

/* ================= BUTTONS ================= */
.btn{
    padding:8px 16px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
    transition:0.3s;
}

.btn-start{
    background:#1cc88a;
    color:white;
}
.btn-start:hover{ background:#17a673; }

.btn-lock{
    background:#f6c23e;
    color:white;
}
.btn-lock:hover{ background:#dda20a; }

.btn-restart{
    background:#e74a3b;
    color:white;
}
.btn-restart:hover{ background:#c0392b; }

/* ================= ALERT ================= */
.alert{
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

.success{ background:#d4edda;color:#155724; }
.warning{ background:#fff3cd;color:#856404; }

/* ================= QUIZ PANEL ================= */
.quiz-box{
    padding:30px;
    background:#f9fbff;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.05);
}

/* ================= FOOTER ================= */
.footer{
    text-align:center;
    padding:20px;
    background:#243b55;
    color:white;
    margin-top:40px;
}

.footer a{
    color:#00c6ff;
    text-decoration:none;
}

@media(max-width:768px){
    .navbar{flex-direction:column;}
    .navbar ul{flex-direction:column;}
    .navbar ul li{margin:10px 0;}
    .container{padding:20px;}
}

</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
<div class="navbar">
    <div class="logo">Netcamp Examination</div>
    <ul>
        <li><a href="account.php?q=1" class="<?php if(@$_GET['q']==1) echo 'active'; ?>">Home</a></li>
        <li><a href="account.php?q=2" class="<?php if(@$_GET['q']==2) echo 'active'; ?>">History</a></li>
        <li><a href="account.php?q=3" class="<?php if(@$_GET['q']==3) echo 'active'; ?>">Ranking</a></li>
        <li><a href="logout.php?q=account.php">Logout</a></li>
    </ul>
</div>

<div class="container">

<?php
/* ================= HOME ================= */
if(@$_GET['q']==1){

$free_eid = null;
$fr = mysqli_query($con,"SELECT eid FROM quiz ORDER BY date ASC LIMIT 1");
if($fr && $frow = mysqli_fetch_array($fr)){
    $free_eid = $frow['eid'];
}

$paid_eids = array();
$pq = mysqli_query($con,"SELECT eid FROM payment WHERE email='$email' AND status='paid'");
while($prow = mysqli_fetch_array($pq)){
    $paid_eids[$prow['eid']] = true;
}

$result = mysqli_query($con,"SELECT * FROM quiz ORDER BY date DESC");
echo "<h2>Available Quizzes</h2>";
echo "<table>
<tr>
<th>S.N.</th>
<th>Topic</th>
<th>Total Questions</th>
<th>Marks</th>
<th>Time (min)</th>
<th>Action</th>
</tr>";

$c=1;
while($row=mysqli_fetch_array($result)){
$title=$row['title'];
$total=$row['total'];
$sahi=$row['sahi'];
$time=$row['time'];
$eid=$row['eid'];

$allowed=false;
if($eid === $free_eid) $allowed=true;
if(isset($paid_eids[$eid])) $allowed=true;

$q12=mysqli_query($con,"SELECT score FROM history WHERE eid='$eid' AND email='$email'");
$rowcount=mysqli_num_rows($q12);

echo "<tr>
<td>".$c++."</td>
<td>$title</td>
<td>$total</td>
<td>".($sahi*$total)."</td>
<td>$time</td>
<td>";


if($rowcount==0){
    if($allowed){
        echo "<a href='account.php?q=quiz&step=2&eid=$eid&n=1&t=$total'><button class='btn btn-start'>Start</button></a>";
    }else{
        // Show payment modal trigger instead of navigating away
        echo "<button class='btn btn-lock' onclick=\"showPaymentModal('$eid','$title',$total)\">Pay</button>";
    }
}else{
    echo "<a href='update.php?q=quizre&step=25&eid=$eid&n=1&t=$total'><button class='btn btn-restart'>Restart</button></a>";
}


echo "</td></tr>";
}

echo "</table>";

// Payment Modal HTML with Mobile Money instructions
echo '<div id="paymentModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;padding:32px 24px 24px 24px;border-radius:16px;max-width:400px;margin:auto;position:relative;top:10vh;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <h3 style="margin-top:0;">Unlock Quiz</h3>
        <div id=\'payQuizInfo\'></div>
        <form id="payForm" method="POST" action="pay.php" enctype="multipart/form-data" style="margin-top:18px;">
            <input type="hidden" name="eid" id="payEid" value="">
            <input type="hidden" name="amount" value="10">
            <div style="margin-bottom:12px;">
                <label>Payment Method:</label><br>
                <select name="method" id="payMethod" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ccc;" onchange="showMomoInstructions()">
                    <option value="">Select</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Mobile Money">Mobile Money</option>
                    <option value="PayPal">PayPal</option>
                </select>
            </div>
            <div id="momoInstructions" style="display:none;margin-bottom:12px;background:#f8f9fc;padding:10px 12px;border-radius:8px;">
                <b>Mobile Money Payment:</b><br>
                Send <b>10 USD</b> to <b>0789833322</b> (Rukundo Janvier).<br>
                After payment, enter your transaction ID below and upload a screenshot of your payment.<br>
                <input type="text" name="momo_txn" id="momoTxnInput" placeholder="Transaction ID" style="width:100%;padding:8px;margin-top:8px;border-radius:6px;border:1px solid #ccc;"><br>
                <label style="margin-top:10px;display:block;">Screenshot:</label>
                <input type="file" name="momo_screenshot" id="momoScreenshotInput" accept="image/*" style="margin-top:4px;" >
            </div>
            <button type="submit" class="btn btn-start" style="width:100%;">Pay & Unlock</button>
            <button type="button" onclick="closePaymentModal()" class="btn btn-restart" style="width:100%;margin-top:10px;">Cancel</button>
        </form>
    </div>
</div>';

// Payment Modal JS
echo '<script>
function showPaymentModal(eid, title, total) {
    document.getElementById("paymentModal").style.display = "flex";
    document.getElementById("payEid").value = eid;
    document.getElementById("payQuizInfo").innerHTML = `<b>Quiz:</b> ${title}<br><b>Questions:</b> ${total}<br><b>Amount:</b> $10`;
    document.getElementById("payMethod").value = "";
    document.getElementById("momoInstructions").style.display = "none";
}
function closePaymentModal() {
    document.getElementById("paymentModal").style.display = "none";
}
function showMomoInstructions() {
    var method = document.getElementById("payMethod").value;
    var momoDiv = document.getElementById("momoInstructions");
    var momoInput = document.getElementById("momoTxnInput");
    var momoScreenshot = document.getElementById("momoScreenshotInput");
    if(method === "Mobile Money") {
        momoDiv.style.display = "block";
        momoInput.required = true;
        momoScreenshot.required = true;
    } else {
        momoDiv.style.display = "none";
        momoInput.required = false;
        momoScreenshot.required = false;
    }
}
</script>';
}

/* ================= HISTORY ================= */
if(@$_GET['q']==2){
    echo "<h2>Your Quiz History</h2>";
    // Show user info
    $userInfo = mysqli_query($con, "SELECT name, college FROM user WHERE email='$email'");
    if($userInfo && $urow = mysqli_fetch_array($userInfo)) {
        echo "<div style='margin-bottom:18px;'><b>User:</b> ".$urow['name']." &nbsp; <b>College:</b> ".$urow['college']." &nbsp; <b>Email:</b> $email</div>";
    }
    $q = mysqli_query($con,"SELECT * FROM history WHERE email='$email' ORDER BY date DESC");
    echo "<table><tr><th>S.N.</th><th>Quiz</th><th>Questions Solved</th><th>Right</th><th>Wrong</th><th>Score</th><th>Action</th></tr>";
    $c=1;
    while($row=mysqli_fetch_array($q)){
        $eid=$row['eid'];
        $s=$row['score'];
        $w=$row['wrong'];
        $r=$row['sahi'];
        $qa=$row['level'];
        $q23=mysqli_query($con,"SELECT title FROM quiz WHERE eid='$eid'");
        $title='';
        if($row2=mysqli_fetch_array($q23)) $title=$row2['title'];
        // Check if paid or free
        $isPaid = false;
        $pq = mysqli_query($con,"SELECT eid FROM payment WHERE email='$email' AND eid='$eid' AND status='paid'");
        if($pq && mysqli_num_rows($pq)>0) $isPaid = true;
        echo "<tr><td>".$c++."</td><td>$title</td><td>$qa</td><td>$r</td><td>$w</td><td>$s</td><td>";
        if($isPaid){
            echo "<span style='color:#1cc88a;font-weight:500;'>Unlocked</span>";
        }else{
            echo "<button class='btn btn-lock' onclick=\"showPaymentModal('$eid','$title',$qa)\">Pay</button>";
        }
        echo "</td></tr>";
    }
    echo "</table>";
}

/* ================= RANKING ================= */
if(@$_GET['q']==3){
    echo "<h2>Ranking</h2>";
    $q = mysqli_query($con,"SELECT * FROM rank ORDER BY score DESC");
    echo "<table><tr><th>Rank</th><th>Name</th><th>College</th><th>Email</th><th>Score</th></tr>";
    $c=1;
    while($row=mysqli_fetch_array($q)){
        $e=$row['email'];
        $s=$row['score'];
        $q12=mysqli_query($con,"SELECT name, college FROM user WHERE email='$e'");
        $name=''; $college='';
        if($row2=mysqli_fetch_array($q12)){
            $name=$row2['name'];
            $college=$row2['college'];
        }
        echo "<tr><td>".$c++."</td><td>$name</td><td>$college</td><td>$e</td><td>$s</td></tr>";
    }
    echo "</table>";
}

/* ================= QUIZ ================= */
if(@$_GET['q']=='quiz' && @$_GET['step']==2){
$eid=$_GET['eid'];
$sn=$_GET['n'];
$total=$_GET['t'];

$q=mysqli_query($con,"SELECT * FROM questions WHERE eid='$eid' AND sn='$sn'");
$row=mysqli_fetch_array($q);
$qns=$row['qns'];
$qid=$row['qid'];

echo "<div class='quiz-box'>";
echo "<h3>Question $sn</h3>";
echo "<p>$qns</p>";

$q=mysqli_query($con,"SELECT * FROM options WHERE qid='$qid'");
echo "<form action='update.php?q=quiz&step=2&eid=$eid&n=$sn&t=$total&qid=$qid' method='POST'>";
while($row=mysqli_fetch_array($q)){
echo "<p><input type='radio' name='ans' value='".$row['optionid']."'> ".$row['option']."</p>";
}
echo "<button class='btn btn-start'>Submit</button>";
echo "</form></div>";
}

/* ================= RESULT ================= */
if(@$_GET['q']=='result'){
$eid=$_GET['eid'];
$q=mysqli_query($con,"SELECT * FROM history WHERE eid='$eid' AND email='$email'");
$row=mysqli_fetch_array($q);

echo "<h2>Result</h2>";
echo "<p>Total Questions: ".$row['level']."</p>";
echo "<p>Right: ".$row['sahi']."</p>";
echo "<p>Wrong: ".$row['wrong']."</p>";
echo "<p>Score: ".$row['score']."</p>";
}

?>

</div>


<div style="margin:40px auto 0 auto; max-width:600px; background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.07); padding:32px 24px 24px 24px; text-align:center;">
    <img src="img/ulk_logo.png" alt="ULK Logo" style="max-width:120px; margin-bottom:18px; display:block; margin-left:auto; margin-right:auto;">
    <h3 style="margin:0 0 10px 0; color:#243b55;">Developers</h3>
    <div style="font-size:1.1em; color:#222; margin-bottom:8px;">Rukundo Janvier vs Shyaka Aime</div>
    <img src="image/download.png" alt="" style="max-width:100px; margin:12px auto 8px auto; display:block;">
    <div style="color:#444;">ULK Gisenyi Campus</div>
</div>

<div class="footer">
© <?php echo date("Y"); ?> Netcamp Examination System | Designed Modern UI
</div>

</body>
</html>