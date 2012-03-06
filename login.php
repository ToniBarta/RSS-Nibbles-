<?php
  session_start();
  require_once('functions.php');
  
  dbConnect();
  
  if (loggedInUser())
  {
    header("location:myrss.php");
  } else {
  
    if (isset($_POST['formsubmit'])) {
      $username = $_POST['username']; 
      $password = $_POST['password'];
      $keeplogin = $_POST['keeplogin'];
    
      $sanitize = array("'", '*', ';', '.', ':', '"', '`', '=', '#', '*', '%', '[', ']', '-', '$', '<', '>','(', ')', '~', '^', '{', '}', ',', '|');
    
      $username = stripslashes($username);
      $username = str_replace($sanitize, '', $username);
      $username = trim($username);
    
      $loginConfirm = login($username, $password);
      if($loginConfirm) {
        $_SESSION['rss_nibbles_hu'] = $hashedUsername;
        if (isset($keeplogin)) {
          setcookie("rss_nibbles_hu", $hashedUsername, time()+3600*24*7, "/");
        }
        header("location:myrss.php");
      } else {
        $errors[] = 'Invalid username and/or password!';
      }
    }
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" SYSTEM "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<head>
</head>
<body>
<?php if(count($errors)){ ?>
			<ul id="error">
			<?php foreach($errors as $error){ ?>
				<li><?php echo $error; ?></li>
			<?php } ?>
			</ul>
		<?php } ?>
		
<form name="login" id="login" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

<input type="hidden" name="formsubmit" id="formsubmit" value="1">
<label>Username:<br>
<input type="text" name="username" id="username" size="20">
</label>
</br>
<label>Password:<br>
<input type="password" name="password" id="password" size="20">
</label>
</br>
<label>
<input type="checkbox" name="keeplogin" id="keeplogin" value="1"> Keep me logged in
</label>
</br>
<input type="submit" name="submit" id="submit" value="Log In">
</form>
