<?php
// We start the session before writing HTML code
session_start();
if(isset($_SESSION['id'])){
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php 
            if (isset($_SESSION['id']) AND isset($_SESSION['pseudo']))
            {
                echo 'Bonjour ' . $_SESSION['pseudo'];
            }
        ?>
        <p>
            <button><a href="signout.php">Se déconnecter</a></button>
        </p>
    </body>
</html>

<?php
}else {
    header('Location: inscription.php');
}
?>
