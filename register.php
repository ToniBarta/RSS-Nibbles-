<?php
  //import functions
  require_once('functions.php');
  
  //check if user is already logged in (by using a function from the imported file.
  /*if(checkLogin()) {
    //if so, redirect user to index.
    header("Location: index.php");
  } */

  //if formsubmit value from form is not empty
  if (isset($_POST['formsubmit'])) {

    /* some captcha code given by google
    /***********************************/
    require_once('recaptchalib.php');
    $privatekey = "6LfYRM4SAAAAAJ47wFV6wl7rw9of2gpq16CN455- ";
    $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);

 
    //if entered captcha is not correct
    if (!$resp->is_valid) {
	  $errors[] = 'Entered CAPTCHA was not correct!';
    } else {
    //if captcha is correct
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordverify = $_POST['passwordverify'];
    $email = $_POST['email'];
    $terms = $_POST['terms'];
    $adult = $_POST['adult'];

    //check if mandatory fields submitted are not empty.
    if (isset($username) && isset($email) && isset($password) && isset($passwordverify)){ 
  
      //check email format
      if ( (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email))){
        $errors[] = 'Please enter your email address in the corect format!';
	  
	  //check username format
      } else if ( preg_match("/[^a-z0-9-]/i", $username) ) {
	    $errors[] = 'Please enter your username in the correct format!';
	  
	  //check if any of the entered passwords are empty.
      } else if ( ($password || $passwordverify) == NULL) {
        $errors[] = 'You must enter your password in both password fields!';
	  
	  //check if user accepted terms&conditions.
      } else if ( $terms  != 1) {
        $errors[] = 'Please accept Terms&Conditions to register!';
	  
	  //check if entered passwords are identical.
      } else if ( $password != $passwordverify ){
        $errors[] = 'Passwords do not match!';
	
	  //check if username entered is in the allowed limits
      } else if ( strlen($username) < 4 || strlen($username) > 40 ) {
        $errors[] = 'Username must consist of 4-40 characters.';
	  
	  //check if password is in the allowed limits
      } else if ( strlen($password) < 6 || strlen($password) > 12 ) {
        $errors[] = 'Password must consist of 6-12 characters.';
	  
	  //if no errors
      } else {
	    //array we are going to replace character we don't want to be written in the sql query (sanitize)
            $sanitize = array("'", '*', ';', '.', ':', '"', '`', '´', '=', '#', '*', '%', '[', ']', '-', '$', '	', ' ', '<', '>','(', ')', '~', '^', '{', '}', ',', '|', '@');
	  
	    //same array for mail (some characters allowed; eg "@")
            $mailSanitize = array("'", '*', ';', ':', '"', '`', '´', '=', '#', '*', '%', '[', ']', '$', '	', ' ', '<', '>', '(', ')','~', '^', '{', '}', ',', '|');
	
	
	    /* Precautions for SQL injection
	    **************************************/
	  
	    $username = stripslashes($username);
	    //replace all the characters in the array with 'nothing'
            $username = str_replace($sanitize, '', $username);
	    $username = trim($username);
	
            $email = stripslashes($email);
	    //replace all the characters in the array with 'nothing'
	    $email = str_replace($mailSanitize, '', $email);
	    $email = trim($email);
	    
	    if (isset($adult)) {
	      $adult = stripslashes($adult);
	      //replace all the characters in the array with 'nothing'
	      $adult = str_replace($sanitize, '', $adult);
	      $adult = trim($adult);
	    } else {
	      $adult = 0;
	    }
	  
	    //generate a random number between 1000 and 9999
	    $salt = rand(1000,9999);
	  
	    //time since unix epoch (in miliseconds)
	    $pepper = time();
	  
	    //hash the password (using md5, sha1, salt, pepper to make it harder to crack)
	    $securePassword = sha1($pepper . md5($password . $salt) . $salt);	
	 
	    //default theme on registration
	    $theme = "orange";
	    
	    //generate hashed username
	    $hashedUsername = sha1($username);
	  
	  
	    dbConnect();
	    //check if username or email address already exists in the database
	    $uniqueConfirmation = mysql_query("SELECT * FROM users WHERE USERNAME='$username' OR EMAIL='$email'") or die('Problem occured1');
	    if(mysql_num_rows($uniqueConfirmation) != 0)
	    {
	      $errors[] = "Username and/or email address already in use.";
	    } else {
	      $registrationSQL = "INSERT INTO `users`
		      	    	       (`USERNAME`, `HASHED_USERNAME`, `PASSWORD`, `EMAIL`, `ADULT`, `SALT`, `PEPPER`, `THEME`)
			        	         VALUES ('$username','$hashedUsername','$securePassword','$email','$adult','$salt','$pepper','$theme')";
	      mysql_query($registrationSQL) or die('Problem occured2');
		  
              $welcomeMessage = 
                               "Hello $username, \n
                                Thank you for registering RSS nibbles!
                                Please click on the following link to activate your account.

                                Thank You
                                ___________________________________________________________________
                                This is an automated message please do not respond to this message.";
               
               //call function tosend email to user
               //email($email, $title, $welcomeMessage);
		   
	       //redirect user to another page
	      header("Location: registered.php");
	     } //else
      } // if there are no errors
  
    } else {
      $errors[] = 'Please fill all the mandatory fields.';
    } //else
  } // if captcha is correct
} //if form is submitted

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" SYSTEM "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<head>
<script type="text/javascript">var RecaptchaOptions = {theme:'clean'};</script>
</head>
<body>
<?php if(count($errors)){ ?>
			<ul id="error">
			<?php foreach($errors as $error){ ?>
				<li><?php echo $error; ?></li>
			<?php } ?>
			</ul>
		<?php } ?>
		
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" id="registration">
  <input type='hidden' name='formsubmit' id='formsubmit' value='1'/>
    Username * :<br>
    <input aria-required="true" type="text" autocomplete="off" name="username" 
     id="username" size="20" style="height: 28px; width: 442px;" maxlength="40" 
     value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>">
  </br>
    Password * :<br>
    <input aria-required="true" type="password" maxlength="20" 
     autocomplete="off" style="height: 28px; width: 442px;" name="password" 
     id="password" value="">
    
    <br> Password confirmation * :<br>
    <input type="password"  maxlength="20" autocomplete="off" 
     name="passwordverify" style="height: 28px; width: 442px;" 
     id="passwordverify" value="">
  </br>
    Email * :<br>
    <input aria-required="true" type="text" maxlength="100" name="email" 
     style="height: 28px; width: 442px;" autocomplete="off"  id="email" 
     value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>">
  </br>
  <?php 
    require_once('recaptchalib.php');
    $publickey = "6LfYRM4SAAAAAGAh1PeG35oUO94lFCdMixkUZvsz ";
    echo recaptcha_get_html($publickey);
  ?>
    I have read and accept <a href="termsconditions.php">terms & conditions</a>
     * :
    <input type="checkbox" name="terms" id="terms" value="1" />
  </br>
    I am above the age of 18 :
    <input type="checkbox" name="adult" id="adult" value="1" />
  </br>
    <input type="submit" name="registrationsubmit" id="registrationsubmit"
     value="Register">
  </br>
</form>
</body>
</html>
