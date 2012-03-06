<?php
  session_start();
  require_once('functions.php');
  
  dbConnect();
  if (loggedInUser())
  {
    // NEEDS TO BE SANITIZED!!//
    $rss_ID = $_GET['id']; /////
    ////////////////////////////
    if(isset($rss_ID))
    {
      removeRSS($rss_ID, $userID);
      header("location:rsslist.php");
    }
  }
?>
