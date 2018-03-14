<?php
// We start the session before writing HTML code
session_start();
$_SESSION['pseudoFocus'] = false;
$_SESSION['passFocus'] = false;
$_SESSION['emailFocus'] = false;
$_SESSION['login'] = '';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>Inscription</title>
        <link href="style-inscription.css" rel="stylesheet" />
    </head>
    <body>
        <?php
            require 'db.php'; // Connect to the database
            // Check if we got POST values to use
            if(isset($_POST['pseudo']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['passconfirmation']))
            {
                // Sécurité ---------------------------------------------------------------------------
                /*
                htmlentities()  ==> convert all applicable characters to HTML entities
                htmlspecialchars() ==> convert special characters to HTML entities
                addslashes() ==> Add backslashes in front of the characters that need to be quoted
                password_hash() ==> Return the string hashed
                 */
                $pseudo = addslashes(htmlspecialchars(htmlentities(trim($_POST['pseudo']))));
                $email = addslashes(htmlspecialchars(htmlentities(trim($_POST['email']))));
                $pass = $_POST['pass'];
                $passConfirmation = $_POST['passconfirmation'];
                // ------------------------------------------------------------------------------------
                
                // Checking the email format
                if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
                {
                    if($pass == $passConfirmation) // Check that pass & passconfirmation are the same
                    {
                        $req = $db->prepare('SELECT pseudo FROM membres WHERE pseudo = :pseudo '); // Prepare a request to look in the "pseudo" column from the "membres" table where the pseudo matches the pseudo submtted by the user
                        $req->bindParam(':pseudo', $pseudo); 
                        $req->execute();
                        $count = $req->rowCount(); // Do rowCount() on the request, it returns a value > 0 if there is a match. We stock in a variable $count
                        if($count == 0) // Check the existence of the variable
                        {
                            $hashedpass = password_hash($pass, PASSWORD_DEFAULT);
                            $req = $db->prepare("INSERT INTO membres(pseudo, pass, email, date_inscription) VALUES(:pseudo, :pass, :email, NOW())");
                            $req->bindParam(':pseudo', $pseudo);
                            $req->bindParam(':pass', $hashedpass);
                            $req->bindParam(':email', $email);
                            $req->execute();
                            $req->closeCursor();
                            $_SESSION['login'] = $pseudo;
                            $_SESSION['pass'] = $pass;
                            $pseudo = NULL;
                            $email = NULL;
                            $pass = NULL;
                            header('Location: signin.php');
                        } else {
                            $message = 'Ce pseudo est déjà utilisé';
                            $pseudo = NULL;
                            $_SESSION['pseudoFocus'] = true;
                        }
                    } else {
                        $message = 'Vos mots de passe ne sont pas identique.';
                        $pass = NULL;
                        $_SESSION['passFocus'] = true;
                    }
                } else {
                    $message = 'Votre adresse e-mail n\'est pas valide.';
                    $email = NULL;
                    $_SESSION['emailFocus'] = true;
                }
            }
        ?>
        <?php
            if(isset($message)){
                echo $message;
            }
        ?>
        <form action="inscription.php" method="post">
            <p>
                <label for="pseudo">Pseudonyme souhaité:</label><input type="text" name="pseudo" id="pseudo" <?php if(isset($pseudo) && !isset($count)) { echo 'value="'.$_POST['pseudo'].'"';} else { echo 'placeholder="Pseudo souhaité"'; } ?> <?php if($_SESSION['pseudoFocus'] || (!$_SESSION['passFocus'] && !$_SESSION['emailFocus'])) { ?> autofocus <?php } ?> required/><br>
                <label for="pass">Mot de passe:</label><input type="password" name="pass" id="pass" <?php if(isset($pass) && $pass == $passConfirmation) { echo 'value="'.$_POST['pass'].'"';} else { echo 'placeholder="Mot de passe"'; } ?> <?php if($_SESSION['passFocus']) { ?> autofocus <?php } ?> required/><br>
                <label for="passconfirmation">Confirmation du mot de passe:</label><input type="password" name="passconfirmation" id="passconfirmation" placeholder="Mot de passe" required/><br>
                <label for="email">Adresse e-mail:</label><input type="text" name="email" id="email" pattern="^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$" <?php if(isset($email) && preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) { echo 'value="'.$_POST['email'].'"';} else { echo 'placeholder="Adresse e-mail"'; } ?> <?php if($_SESSION['emailFocus']) { ?> autofocus <?php } ?> required/><br><br>
                <input type="submit" value="Envoyer" />
            </p>
        </form>
    </body>
</html>
      