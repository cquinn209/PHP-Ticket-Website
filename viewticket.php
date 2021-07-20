<?php

session_start();


//Clear session data after 5 mins idle and logout

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


include 'functions.php';

$pdo = pdo_connect_mysql();
if (!isset($_GET['id'])) {
    exit('No ID specified!');
}









// Get ticket by id
$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([ $_GET['id'] ]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$ticket) {
    exit('Invalid ID');
}




// change status
if (isset($_GET['status']) && in_array($_GET['status'], array('open', 'closed', 'resolved'))) {
   
    $stmt = $pdo->prepare('UPDATE tickets SET status = ? WHERE id = ?');
    $stmt->execute([ $_GET['status'], $_GET['id'] ]);
  
    header('Location: viewticket.php?id=' . $_GET['id']);
    exit;
}



// Comment
if (isset($_POST['ticketscommentsmsg']) && !empty($_POST['ticketscommentsmsg'])) {
    
    $stmt = $pdo->prepare('INSERT INTO tickets_comments (ticket_id, ticketscommentsmsg, createdBy) VALUES (?, ?, ?)');
    $stmt->execute([ $_GET['id'], $_POST['ticketscommentsmsg'], $_SESSION['name'] ]);
   
    header('Location: viewticket.php?id=' . $_GET['id']);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM tickets_comments WHERE ticket_id = ? ORDER BY created DESC');
$stmt->execute([ $_GET['id'] ]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>





<?=template_header('Ticket')?>



<br>

<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<div class = "jumbotron text-center">
    
    <h1> Bug ticket NO. <?=$_GET['id']?> </h1>

</div>
</head>



<div class="">
        <a href="homepage.php" class="btn btn-primary btn-lg"> Return Home</a>
 </div>
    



<div class="content view">

<br>

<h2><?=htmlspecialchars($ticket['ticketstitle'], ENT_QUOTES)?> <span <?=$ticket['status']?>">(<?=$ticket['status']?>)</span></h2>

<div class="ticket">
    <p class="created"><?=date('F dS, G:ia', strtotime($ticket['created']))?></p>
       <p class="nameofcreator"> Created By: <?=nl2br(htmlspecialchars($ticket['nameofcreator'], ENT_QUOTES))?></p>

    <br>
    <p class="ticketsmessage"><?=nl2br(htmlspecialchars($ticket['ticketsmessage'], ENT_QUOTES))?></p>
    <br>
</div>

<div class="btn btn-outline-info btn-sm">
    <a href="viewticket.php?id=<?=$_GET['id']?>&status=closed" class="btn red">Close</a>
</div>


<div class="btn btn-outline-info btn-sm">
    <a href="viewticket.php?id=<?=$_GET['id']?>&status=resolved" class="btn">Resolve</a>
</div>


<div class="btn btn-outline-info btn-sm">
    <a href="viewticket.php?id=<?=$_GET['id']?>&status=open" class="btn">Open</a>
</div>








<div class="comments">




     <?php foreach($comments as $comment):?>
  

        
        <div class="comment">


        <p>
          Posted by:  <?= nl2br(htmlspecialchars($comment['createdBy'], ENT_QUOTES))?>
        <br>
        <span><?=date('F dS, G:ia', strtotime($comment['created']))?></span>
       
       <br>
    
        <?= nl2br(htmlspecialchars($comment['ticketscommentsmsg'], ENT_QUOTES))?>
       
    </p>
     
    </div>
    

        <?php endforeach; ?>
     
        <br>
     

<?php 
if ($ticket['status'] == "open" || $ticket['status'] == "resolved") 
{
?>


        <form action="" method="post">
            <textarea name="ticketscommentsmsg" placeholder="Please enter comments here"></textarea>
            <input type="submit" value="Post Comment">
    </form>


<?php
}
?>

</div>



<?=template_footer()?>