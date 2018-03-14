<?php
// We start the session before writing HTML code
session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>SignIn</title>
        <link href="style-inscription.css" rel="stylesheet" />
    </head>
    <body>
        <?php
            require 'db.php'; // Connect to the database
            // Check if we got POST values to use
            if(isset($_POST['pseudo']) && isset($_POST['pass']))
            {
                // Sécurité ---------------------------------------------------------------------------
                /*
                htmlentities()  ==> convert all applicable characters to HTML entities
                htmlspecialchars() ==> convert special characters to HTML entities
                addslashes() ==> Add backslashes in front of the characters that need to be quoted
                password_hash() ==> Return the string hashed
                 */
                $pseudo = addslashes(htmlspecialchars(htmlentities(trim($_POST['pseudo']))));
                $pass = $_POST['pass'];
                // ------------------------------------------------------------------------------------
                $req = $db->prepare('SELECT pseudo FROM membres WHERE pseudo = :pseudo '); // Prepare a request to look in the "pseudo" column from the "membres" table where the pseudo matches the pseudo submitted by the user
                $req->bindParam(':pseudo', $pseudo); 
                $req->execute();
                $req->closeCursor();
                $count = $req->rowCount(); // Do rowCount() on the request, it returns a value > 0 if there is a match. We stock in a variable $count
                if($count != 0) // Check the existence of the a result
                {
                    $req = $db->prepare('SELECT id, pass FROM membres WHERE pseudo = :pseudo '); // Prepare a request to look in the "pass" column from the "membres" table where the pseudo matches the pseudo submitted by the user
                    $req->bindParam(':pseudo', $pseudo); 
                    $req->execute();
                    $data = $req->fetch();
                    $hash = $data['pass'];
                    $req->closeCursor();
                    // Comparaison du pass envoyé via le formulaire avec la base
                    if(password_verify ($pass , $hash))
                    {
                        $_SESSION['id'] = $data['id'];
                        $_SESSION['pseudo'] = $pseudo;
                        header('Location: Accueil.php');
                    } else {
                        $_SESSION['pass'] = '';
                        $message = 'Identifiant ou mot de passe incorrect';
                        echo $message;
                    }
                } else {
                    $_SESSION['login'] = '';
                    $_SESSION['pass'] = '';
                    $message = 'Identifiant ou mot de passe incorrect';
                    echo $message;
                }
            }
        ?>
        <form action="signin.php" method="post">
            <p>
                <label for="pseudo">Login:</label><input type="text" name="pseudo" id="pseudo" <?php if(isset($_SESSION['login'])) { echo 'value="'.$_SESSION['login'].'"';} else { echo 'placeholder="Identifiant"'; } ?> autofocus autocomplete required/><br>
                <label for="pass">Mot de passe:</label><input type="password" name="pass" id="pass" <?php if(isset($_SESSION['pass'])) { echo 'value="'.$_SESSION['pass'].'"';} else { echo 'placeholder="Mot de passe"'; } ?> required/><br>
                <input type="checkbox" name="rememberMe" id="rememberMe"><label for="rememberMe">Se souvenir de moi</label>
                <input type="submit" value="Se connecter" />
            </p>
        </form>
    </body>
</html>