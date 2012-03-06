<?php
  session_start();
  require_once('functions.php');
  
  dbConnect();
  
  if (loggedInUser())
  {
    if (isset($_POST['formsubmit'])) {
      $pass1 = $_POST['pass1']; 
      $pass2 = $_POST['pass2'];
          
      if((isset($pass1)) && (isset($pass2))) {
        if($pass1 == $pass2){
          changePass($userID, $pass1);
        } else {
          $errors[] = 'Passwords do not match!';
        }
      } else {
        $errors[] = 'Fields cannot be empty!';
      }
    }
  } else {
    header("location:login.php");
  }
?>




<!-- HTML tags --> 
<form name="changepass" id="changepass" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

<input type="hidden" name="formsubmit" id="formsubmit" value="1">
<label>New Password:<br>
<input type="password" name="pass1" id="pass1" size="20">
</label>
<label>Password Confirmation:<br>
<input type="password" name="pass2" id="pass2" size="20">
</label>
</br>
<input type="submit" name="submit" id="submit" value="Change password">
</form>

<!-- HTML tags --> 
