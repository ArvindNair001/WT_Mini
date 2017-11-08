<?php
  include('classes/DB.php');
  if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))){
      if(password_verify($password, DB::query('SELECT password FROM users WHERE username = :username',array(':username'=>$username))[0]['password'])){
        $cstrong = True;
        $token= bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
        $user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
        DB::query('INSERT INTO login_tokens (token,user_id)VALUES (:token,:user_id)', array(':token'=>sha1($token),':user_id'=>$user_id));

        setcookie("MID",$token,time()+60*60*24*7*4,'/',NULL,NULL,TRUE); //primary token
        $temp_token= bin2hex(openssl_random_pseudo_bytes(32,$cstrong));
        setcookie("TMID",$temp_token,time()+60*60*24*7,'/',NULL,NULL,TRUE); //secondary token
        echo "logging in...";

      }
      else {
        echo "invalid password";
      }
    }
    else {
      echo "user not exist";
    }
  }
?>
<h1>login to your account</h1>
<form class="" action="login.php" method="post">
  <input type="text" name="username" placeholder="Username ..."><p \>
  <input type="password" name="password" placeholder="Password ..."><p \>
  <input type="submit" name="login" value="Login">
</form>
