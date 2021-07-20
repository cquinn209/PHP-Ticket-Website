<?php
session_start();
$_SESSION['timestamp']=time();


$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
$password = '';



//Clear session data after 5 mins idle

$autologout=300;
$lastactive = $_SESSION['timestamp'] ?? 0;

if (time()-$lastactive>$autologout){
    $_SESSION = array();
        setcookie(session_name(), false, time()-3600);
        session_destroy();
    }
else
{
    $_SESSION['timestamp']=time();
}





$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno() ) {

    exit('failed to connect to database' . mysqli_connect_error());
}

if ( !isset($_POST['username'], $_POST['password']) ) {

	exit('Please fill both fields out');
}


if(isset($_POST['g-recaptcha-response'])){
    $captcha=$_POST['g-recaptcha-response'];

}
if(!$captcha){

    echo '<script language="javascript">';
    echo 'alert("Please complete the reCAPTCHA")';
    echo '</script>';
    echo '<a href="index.html" class="btn btn-primary btn-lg"> Return</a>';
    echo '<br>';
    echo '<img  src="jeez.jpg" >';
    exit;

}





//Preparing SQL for injection prevention
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')){
    $stmt->bind_param('s', $_POST['username']);

    $stmt->execute();

    $stmt->store_result();

    if ($stmt-> num_rows >0) {
        $stmt->bind_result($id,$password);
        $stmt->fetch();
    }
    
// https://www.w3schools.com/PHP/php_mysql_prepared_statements.asp






    //account actually exists so create session

    if (password_verify($_POST['password'],$password)){
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $_POST['username'];
        $_SESSION['id'] = $id;
        
        header('Location: homepage.php');


    } 

  


    else
    {
        echo 'Invalid username/password, please retry';
    }
}

else
{
    echo 'Incorrect username/password, please retry';
}





//Captcha

$secretKey = "6LdRFekZAAAAACgdZ9GOgOsF3UnQb1pov2sHD1ii";
$ip = $_SERVER['REMOTE_ADDR'];
 // post request to server
$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
$response = file_get_contents($url);
$responseKeys = json_decode($response,true);




$stmt->close();


?>


<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>

<br>

<img  src="jeez.jpg" >

<br>

<a href="index.html" class="btn btn-primary btn-lg"> Return</a>
