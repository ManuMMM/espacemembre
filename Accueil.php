<?php
// We start the session before writing HTML code
session_start();
// Check if a session is already active or if cookie exist
if (isset($_SESSION['pseudo']) AND !empty($_SESSION['pseudo'])) {
    $pseudo = $_SESSION['pseudo'];
} elseif (isset($_COOKIE['pseudo']) AND !empty($_COOKIE['pseudo'])) {
    $pseudo = $_COOKIE['pseudo'];
};
// Check that all tokens are here and that the id is correct
if ((isset($_SESSION['logged']) AND !empty($_SESSION['logged'])) || (isset($_COOKIE['tokenSession']) AND !empty($_COOKIE['tokenSession']))){
    require 'db.php'; // Connect to the database
    $req = $db->prepare('SELECT * FROM membres WHERE pseudo = :pseudo ');
    $req->bindParam(':pseudo', $pseudo);
    $req->execute();
    $membre = $req->fetch();
    $dbTokenSession = $membre['token_connection'];
    if((isset($_SESSION['logged']) AND password_verify($_SESSION['logged'], $dbTokenSession)) || (isset($_COOKIE['tokenSession']) AND password_verify($_COOKIE['tokenSession'], $dbTokenSession))){
        if(!isset($_SESSION['logged']) AND $_COOKIE['tokenSession'] AND $_COOKIE['pseudo']){
            $_SESSION['id'] = $membre['id'];
            $_SESSION['pseudo']= $membre['pseudo'];
        }
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
                    <button><a href="signout.php">Se déconnecter (Session destroy)</a></button>
                    <button><a href="signout2.php">Se déconnecter (Cookies destroy)</a></button>
                </p>
            </body>
        </html>

<?php
    } else {
        header('Location: signin.php');
    }
}else {
    header('Location: signin.php');
}
?>