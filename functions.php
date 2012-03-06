<?php
/* Copy your code here as a function
*
*  -Sample code to create functions:
*  function sampleFunction() 
*  {
*    **PHP code here**
*  }
*
*  -Sample code to call the functions:
*  sampleFunction();
*  
*  -OR if you return a value from a function you can do something like this:
*  $sampleFunctionReturn = sampleFunction();
*  if($sampleFunctionReturn == "1") {
*  
*  -IF you are returning value "true":
*  $sampleFunctionReturn = sampleFunction();
*  if($sampleFunctionReturn) {
*/

/* DB functions
/*************************************************************************/
function dbConnect()
{
  $connection = mysql_connect("ramen.cs.man.ac.uk",
  "11_COMP10120_W6", "tnw3thLHt7WEjJXU")
  or die('Could not connect: ' . mysql_error());
  mysql_select_db("11_COMP10120_W6", $connection)
  or die('Could not select database');
}
/*************************************************************************/


/* RSS functions
/*************************************************************************/

/* Checkif user already have rss with same url
/*************************************************************************/
function checkUserRssDublicate($userID, $requestedURL)
{
  $checkDublicate = mysql_query("SELECT * FROM rss WHERE URL='$requestedURL' LIMIT 1") or die('Problem occured1');
  if (mysql_num_rows($checkDublicate) != 0)
  {
    while ($rssCheckRow = mysql_fetch_array($checkDublicate))
    {
      $rssID = $rssCheckRow['ID'];
    }
    
    $checkLink = mysql_query("SELECT * FROM link WHERE ID_USER='$userID' AND ID_RSS='$rssID' LIMIT 1");
    if (mysql_num_rows($checkLink) != 0)
    {
      return TRUE;
    } else {
      return FALSE;
    }
  } else {
    return FALSE;
  }
}
/*************************************************************************/

/* remove rss
/*************************************************************************/
function removeRSS($rss_ID, $user_ID)
{
  $removeRss = mysql_query("DELETE FROM link WHERE ID_USER='$user_ID' AND ID_RSS='$rss_ID'") or die(mysql_error());
}

/*************************************************************************/

/* edit rss
/*************************************************************************/
function editRSS($rss_ID, $user_ID, $requestedTITLE)
{
  $editRss = mysql_query("UPDATE link SET TITLE='$requestedTITLE' WHERE ID_USER='$user_ID' AND ID_RSS='$rss_ID'") or die(mysql_error());
}

/*************************************************************************/

/* get user rss values
/*************************************************************************/

function getUserRSSvalues($user_ID, $rss_ID)
{
  $rssToGet = mysql_query("SELECT * FROM link WHERE ID_USER='$user_ID' AND ID_RSS='$rss_ID'") or die('Problem occured');
  if (mysql_num_rows($rssToGet) != 0)
  {
    while ($rssGetValueRow = mysql_fetch_array($rssToGet))
    {
      global $rssTitle;
      global $rssPosition;
      
      $rssTitle = $rssGetValueRow['TITLE'];
      $rssPosition = $rssGetValueRow['POSITION'];
      
      return $rssTitle;
      return $rssPosition;
    }
  } else {
    header("location:rsslist.php");
  }
}
/*************************************************************************/

/* list user rss
/*************************************************************************/

function listUserRSS($user_ID)
{
  $rssToRemove = mysql_query("SELECT * FROM link WHERE ID_USER='$user_ID'") or die('Problem occured');
  if (mysql_num_rows($rssToRemove) != 0)
  {
    while ($rssDisplayRow = mysql_fetch_array($rssToRemove))
    {
      $rssID = $rssDisplayRow['ID_RSS'];
      $rssTitle = $rssDisplayRow['TITLE'];
      
      echo "RSS Name: " . $rssTitle . "&nbsp;&nbsp;&nbsp;<a href='editrss.php?id=$rssID'>Edit</a>&nbsp;&nbsp;<a href='removerss.php?id=$rssID'>Remove</a></br>";
    }
  }
}
/*************************************************************************/


/* check if url is added to database before
/*************************************************************************/
function checkRssArchive($requestedURL)
{
  $checkArchive = mysql_query("SELECT * FROM rss WHERE URL='$requestedURL' LIMIT 1") or die('Problem occured');
  if ((mysql_num_rows($checkArchive)) != 0)
  {
    return TRUE;
  } else {
    return FALSE;
  }
}
/*************************************************************************/

/* add RSS
/*************************************************************************/
function addRssToArchive($userID, $customTitle, $requestedURL, $rssPosition, $hidden)
{
  $rssFile = new SimpleXMLElement($requestedURL, null, true);
  foreach ($rssFile->channel as $rssParsed)
  {
    $rssParsedTitle = $rssParsed->title;
  }
  
  $rssArchiveQuery = "INSERT INTO `rss` (`URL`, `HIDDEN`, `TITLE`) VALUES ('$requestedURL','$hidden','$rssParsedTitle')";
  mysql_query($rssArchiveQuery) or die('Problem occured');
  
  $getRssID = mysql_query("SELECT * FROM rss WHERE URL='$requestedURL' LIMIT 1") or die('Problem occured');
  while ($getIDRow = mysql_fetch_array($getRssID))
  {
    $rssID = $getIDRow['ID'];
  }
  $rssLinkQuery = "INSERT INTO `link` (`ID_USER`, `ID_RSS`, `POSITION`, `TITLE`) VALUES ('$userID','$rssID','$rssPosition','$customTitle')";
  mysql_query($rssLinkQuery) or die('Problem occured');  
}
/*************************************************************************/

/* add RSS to link
/*************************************************************************/
function addRssToLink($userID, $customTitle, $requestedURL, $rssPosition, $hidden)
{
  $getRssID = mysql_query("SELECT * FROM rss WHERE URL='$requestedURL' LIMIT 1") or die('Problem occured');
  while ($getIDRow = mysql_fetch_array($getRssID))
  {
    $rssID = $getIDRow['ID'];
  }
  
  
  $checkDubicateLink = mysql_query("SELECT * FROM link WHERE ID_RSS='$rssID' AND ID_USER='$userID' LIMIT 1") or die('Problem occured');
  if(mysql_num_rows($checkDubicateLink) == 0)
  {
    $rssLinkQuery = "INSERT INTO `link` (`ID_USER`, `ID_RSS`, `POSITION`, `TITLE`) VALUES ('$userID','$rssID','$rssPosition','$customTitle')";
    mysql_query($rssLinkQuery) or die('Problem occured'); 
  } 
}
/*************************************************************************/

/* show user rss
/*************************************************************************/
function showRss($userID, $rssPosition)
{
  $getRssCustom = mysql_query("SELECT * FROM link WHERE ID_USER='$userID' AND POSITION='$rssPosition' LIMIT 1") or die(mysql_error());
  while ($getCustomRow = mysql_fetch_array($getRssCustom))
  {
    $rssTITLE = $getCustomRow['TITLE'];
    $rssID = $getCustomRow['ID_RSS'];
  }
  
  echo "<h1>" . $rssTITLE . "</h1>";
  $getRssContent = mysql_query("SELECT * FROM rss WHERE ID='$rssID' LIMIT 1") or die('Problem occured2');
  while ($getContentRow = mysql_fetch_array($getRssContent))
  {
    $rssURL = $getContentRow['URL'];
  }
  
  $rssFile = new SimpleXMLElement($rssURL, null, true);
  $itemCount = 0;
  foreach ($rssFile->channel->item as $rssItem)
  {
    $itemCount++;
    echo "<p>\n";
    foreach ($rssItem->children() as $rssChild)
    {
      switch ($rssChild->getName())
      {
        case 'title':
          echo "<b>$rssChild</b><br />\n";
          break;
        case 'link':
          printf('<a href="%s>%s</a><br />' . "\n", $rssChild, $rssChild);
          break;
        default:
          echo nl2br($rssChild) . "<br />\n";
          break;
      }
    }
    echo "</p>\n";
    if ($itemCount >= 5) {
      break;
    }
  }
}
/*************************************************************************/


/* Logout
/*************************************************************************/
function logout()
{
  unset($_COOKIE['rss_nibbles_hu']);
  session_unset($_SESSION['rss_nibbles_hu']);
  session_destroy();
  header("location:index.php");
}
/*************************************************************************/

/* ChangePass
/*************************************************************************/
function changePass($user_ID, $new_Pass)
{
  $changepass = mysql_query("SELECT * FROM users WHERE ID='$user_ID' LIMIT 1") or die('Problem occured1');
  while ($passRow = mysql_fetch_array($changepass)) {
    $salt = $passRow['SALT'];
    $pepper = $passRow['PEPPER'];
  }
  
  $securePassword = sha1($pepper . md5($new_Pass . $salt) . $salt);
  
  $updatePass = mysql_query("UPDATE users SET PASSWORD='$securePassword' WHERE ID='$user_ID' LIMIT 1") or die('Problem occured1');
  
}
/*************************************************************************/


/* Login
/*************************************************************************/
function login($username, $password)
{
  global $hashedUsername;
  $login = mysql_query("SELECT * FROM users WHERE USERNAME='$username' LIMIT 1") or die('Problem occured1');
  while ($loginRow = mysql_fetch_array($login)) {
    $salt = $loginRow['SALT'];
    $pepper = $loginRow['PEPPER'];
    $hashedUsername = $loginRow['HASHED_USERNAME'];
    
  }
  
  $securePassword = sha1($pepper . md5($password . $salt) . $salt);
  
  $loginConfirm = mysql_query("SELECT * FROM users WHERE USERNAME='$username' AND PASSWORD='$securePassword' LIMIT 1") or die('Problem occured2');
  if(mysql_num_rows($loginConfirm) == 1)
  {
    return TRUE;
  } else {
    return FALSE;
  }
}

function loggedInUser()
{
  if (isset($_SESSION['rss_nibbles_hu']))
  {
    $hashedUsername = $_SESSION['rss_nibbles_hu'];
    $sessionConfirm = mysql_query("SELECT * FROM users WHERE HASHED_USERNAME='$hashedUsername' LIMIT 1") or die('Problem occured');
    if(mysql_num_rows($sessionConfirm) == 1)
    {
      global $userID;
      while ($checkedRow = mysql_fetch_array($sessionConfirm)) {
        $userID = $checkedRow['ID'];
      }
      return $userID;
      return TRUE;
    } else {
      return FALSE;
    }
  } else if (isset($_COOKIE["rss_nibbles_hu"])) {
    $hashedUsername = $_COOKIE["rss_nibbles_hu"];
    $sessionConfirm = mysql_query("SELECT * FROM users WHERE HASHED_USERNAME='$hashedUsername' LIMIT 1") or die('Problem occured');
    if(mysql_num_rows($sessionConfirm) == 1)
    {
      global $userID;
      while ($checkedRow = mysql_fetch_array($sessionConfirm)) {
        $userID = $checkedRow['ID'];
      }
      return $userID;
      return TRUE;
    } else {
      return FALSE;
    }    
  } else {
    return FALSE;
  }
}

/*************************************************************************/



/* Email
/*************************************************************************/
function email($to, $title, $message)
{
  mail($to, $title, $message,
           "From: \"RSS Nibbles\" <no-reply@rssnibbles.manchester>\r\n" .
           "X-Mailer: PHP/" . phpversion());
}
/*************************************************************************/


?>
