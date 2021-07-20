<?php 

include 'functions.php';

$pdo = pdo_connect_mysql();


session_start();




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









//using prepared statements to avoid SQL injection

$stmt = $pdo->prepare('SELECT * FROM tickets ORDER BY created DESC');
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);






// redirector if the person is not logged in
if (!isset($_SESSION ['loggedin'])) 
{
    header('Location: index.html');
    exit;
}



?>



<html>

<head>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<title> Home Page </title>

</head>



<body class="loggedin">

<nav class="navtop">


    <div class = "jumbotron text-center">

    <h1> Tickets </h1>

    <a class="btn btn-outline-danger" href="logout.php">Logout</a>

    </div>

</nav>

<div class="content">

    <h2> Welcome, <?=$_SESSION['name']?> </h2>

</div>

</body>

</html>




<?=template_header('Tickets')?>

<div>


    <p> View the tickets below, click on any to view details and comments</p>


    <div>
        <a href="createticket.php" class="btn btn-primary btn-lg"> Create a Ticket</a>
    </div>

    <br>

    <div class="container">
        <?php foreach ($tickets as $ticket): ?>
        <a class="btn btn-outline-dark" href="viewticket.php?id=<?=$ticket['id']?>" class="ticket">
        
        <span class="con">
          

          
            
        
         

           
            <?php if ($ticket['status'] == 'open'): ?>

          
            <span class="con">
                <span class="title"><?=htmlspecialchars($ticket['ticketstitle'], ENT_QUOTES)?></span>
              <br>
                <span class="msg"><?=htmlspecialchars($ticket['ticketsmessage'], ENT_QUOTES)?></span>
              <br>
            </span>
        
            <span class="con created"><?=date('F dS, G:ia', strtotime($ticket['created']))?></span>

            </a>


            <?php elseif ($ticket['status'] == 'resolved'): ?>

                <span class="con">
                <span class="title"><?=htmlspecialchars($ticket['ticketstitle'], ENT_QUOTES)?></span>
              <br>
                <span class="msg"><?=htmlspecialchars($ticket['ticketsmessage'], ENT_QUOTES)?></span>
              <br>
            </span>
        
            <span class="con created"><?=date('F dS, G:ia', strtotime($ticket['created']))?></span>

            </a>




            <?php elseif ($ticket['status'] == 'closed'): ?>

                <span class="con">
                <span class="title">Closed</span>
            
                </span>
        
            

           
           
           
           
           
           
            <?php endif;?>

            </span>

            <?php endforeach; ?>
           
           
           
           
            </div>



</div>

<?=template_footer()?>