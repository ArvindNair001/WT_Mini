<?php
  include('./classes/DB.php');
  include('./classes/Login.php');
  include('./classes/Mail.php');

  if(isset($_POST['reset'])){
    $cstrong = True;
    $token= bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
    $email = $_POST['email'];
    $user_id = DB::query('SELECT id FROM users WHERE email=:email',array('email'=>$email))[0]['id'];
    DB::query('INSERT INTO password_tokens (token,user_id)VALUES (:token,:user_id)', array(':token'=>sha1($token),':user_id'=>$user_id));
    //Mail::sendMail('Forgot Password!', "<a href='http://localhost/sambandh/change-password.php?token=$token'>http://localhost/sambandh/change-password.php?token=$token</a>", $email);
    echo "email sent";
    echo "\n$token";
  }


?>
<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
  <input type="text" name="email" placeholder="email address ..."><p \>
  <input type="submit" name="reset" value="Reset Password">
</form>
