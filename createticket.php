<?php



session_start();

include 'functions.php';

$pdo = pdo_connect_mysql();
$msg = '';





//Clear session data after 5 mins idle and automatically log out

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



if (isset($_POST['title'], $_SESSION['name'], $_POST['msg'], $_POST['type'],  $_POST['assign'], $_POST['priority'])) {
    
    if (empty($_POST['title']) || empty($_POST['msg'])) {
        $msg = 'Please complete the form!';
    }

     else {

        $stmt = $pdo->prepare('INSERT INTO tickets (ticketstitle, ticketsmessage, nameofcreator, type, sssignedTo, Priority) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([ $_POST['title'], $_POST['msg'], $_SESSION['name'], $_POST['type'], $_POST['assign'], $_POST['priority']  ]);

        header('Location: viewticket.php?id=' . $pdo->lastInsertId());
    }
}
?>


<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>



<?=template_header('Create Ticket')?>

<div class = "jumbotron text-center">
    <h2>Create Ticket</h2>
</div>

<div class="">
        <a href="homepage.php" class="btn btn-primary btn-lg"> Return Home</a>
</div>


    <br>




    <!-- Doesn't accept any potential malicious characters using pattern -->

    <form action="createticket.php" method="post">
     
        <div class="col-sm-4">
        <label for="title">Title</label>
        <input type="text" pattern="[a-zA-Z0-9!@#$%^*_|]{0,100}" name="title" placeholder="Title" id="title" required>
       
        <br>
        <br>
        <br>


<!-- Message box allows the characters for user to describe the problem -->


        <label for="msg">Message</label>
        <textarea name="msg" placeholder="Enter ticket description" id="msg" required></textarea>
       
        <br>
        <br>




        <label for="type">Type</label>
        <select name="type" id="type" required>
            <option value="Development">Development</option>
            <option value="Testing">Testing</option>
            <option value="Production">Production</option>
        
        </select>
      
        <br>

        <!-- Static for demo purpose, would get a list of members from database-->
        
        <label for="assign">Assign To</label>
        <select name="assign" id="assign" required>
          
            <option value="John Romero">John Romero</option>
            <option value="John Smith">John Smith</option>
            <option value="Trevor Bruttenholm">Trevor Bruttenholm</option>
        
        </select>
      


        
        <br>

        <label for="priority">Priority</label>
        <select name="priority" id="priority" required>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
        
        </select>
      
        <br>



        <input type="submit" value="Create">
    
   
    </form>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php endif; ?>
</div>

<?=template_footer()?>
