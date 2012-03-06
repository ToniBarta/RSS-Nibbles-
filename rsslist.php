<?php
  session_start();
  require_once('functions.php');
  
  dbConnect();
  if (loggedInUser())
  {
    listUserRSS($userID);
  }
?>
