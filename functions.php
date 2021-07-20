<?php

function pdo_connect_mysql() {

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phpticketsystem';

try
{
    return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);


}
catch (PDOException $exception)
{
    exit('failed to connect');
}

}



function template_header($title) 
{
    echo <<<EOT

    <!DOCTYPE html>
    <html>
    
    <head>
    <title> $title </title>
    </head>

    <body>

    <nav class="navtop">

    <div>
    <h1> </h1>
    </div>


    </nav>

EOT;
}




function template_footer()
{
    echo <<<EOT
    </body>
    </html>
    EOT;
}
?>