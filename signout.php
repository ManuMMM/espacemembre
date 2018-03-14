<?php  
    // On démarre la session
    session_start();
  
    if(isset($_SESSION['id']))  // If connected, deconnection & redirection to the inscription page
    {
        // Delete session variables & the session
        $_SESSION = array();
        session_destroy();
  
        // Delete the auto-connection cookies
        //setcookie('login', '');
        //setcookie('pass_hache', '');
          
        header('Location: inscription.php');
  
    }else{ // If not connected, redirection to the incription page
        header('Location: inscription.php');
    }
?>
