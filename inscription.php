<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Inscription</title>
    </head>
    <style>
    form
    {
        text-align: center;
    }
    </style>
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
                            // header('Location: signin.php');
                        }
                    } else {
                        $message = 'Vos mots de passe ne sont pas identique.';
                    }
                } else {
                    $message = 'Votre adresse e-mail n\'est pas valide.';
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
                <label for="pseudo">Pseudonyme souhaité</label> : <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo souhaité" autofocus required/><br>
                <label for="message">Mot de passe</label> :  <input type="password" name="pass" id="pass" placeholder="Mot de passe"  required/><br>
                <label for="message">Confirmation du mot de passe</label> :  <input type="password" name="passconfirmation" id="passconfirmation" placeholder="Mot de passe"  required/><br>
                <label for="message">Adresse e-mail</label> :  <input type="text" name="email" id="email" placeholder="Adresse e-mail"  required/><br><br>
                <input type="submit" value="Envoyer" />
            </p>
        </form>
    </body>
</html>
      