<?php
  session_start();
  require_once('functions.php');
  
  dbConnect();
  if (loggedInUser())
  {
    if (isset($_POST['formsubmit']))
    {
      // NEEDS TO BE SANITIZED!!//
      $rss_ID = $_GET['id'];
      $requestedTITLE = $_POST['title'];
      ////////////////////////////
      if(isset($rss_ID) && isset($requestedTITLE))
      {
        editRSS($rss_ID, $userID, $requestedTITLE);
        header("location:rsslist.php");
      }
    }
  }
?>

<!-- HTML tags --> 
<?php 

  $rss_ID = $_GET['id'];
  if(isset($rss_ID))
  {
    getUserRSSvalues($userID, $rss_ID);
  }
?>

<form name="editrss" id="editrss" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

<input type="hidden" name="formsubmit" id="formsubmit" value="1">
<label>Rss TITLE:<br>
<input type="text" name="title" id="title" size="20" value="<?php echo $rssTitle; ?>">
</label>
</br>
<input type="submit" name="submit" id="submit" value="Apply Changes">
</form>

<!-- HTML tags --> 
