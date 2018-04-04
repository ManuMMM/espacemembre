<?php  
    // Initialize the session.
    session_start();
  
    if(isset($_SESSION['id']))  // If connected, deconnection & redirection to the inscription page
    {
        // ------------------------------------------------------------------------------------------------
        // -------------------------- Delete session variables & the session ------------------------------
        // ------------------------------------------------------------------------------------------------
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session.
        session_destroy();
        // ------------------------------------------------------------------------------------------------
        
        // Delete the session cookies
        // http://php.net/manual/fr/function.setcookie.php
        if (isset($_COOKIE['tokenSession'])) {
            unset($_COOKIE['tokenSession']);
            // empty value and old timestamp
            setcookie('tokenSession', '', time() - 3600, null, null, false, true);
            
        }
        if (isset($_COOKIE['pseudo'])) {
            unset($_COOKIE['pseudo']);
            // empty value and old timestamp
            setcookie('pseudo', '', time() - 3600, null, null, false, true);
        }
        // Redirection vers la page d'inscription
        header('Location: signin.php');
  
    }else{ // If not connected, redirection to the incription page
        header('Location: inscription.php');
    }
?>
