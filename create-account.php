<?php
  include('classes/DB.php');
  include('classes/Mail.php');

  if(isset($_POST['create_acc'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

  if(!DB::query('SELECT username FROM users WHERE username = :username', array(':username'=>$username))){
    if(strlen($username)>=4 && strlen($username)<=32){
        if (preg_match('/[a-zA-z0-9_]+/', $username)) {
          if(filter_var($email,FILTER_VALIDATE_EMAIL)) {
            if(!(DB::query('SELECT email FROM users WHERE email=:email',array(':email'=>$email)))){

            if (strlen($password)>=6 && strlen($password)<=60) {
              DB::query('INSERT INTO users (username,password,email,verified) VALUES (:username,:password,:email,\'0\')',array(':username'=>$username,':password'=>password_hash($password,PASSWORD_BCRYPT),':email'=>$email));
              Mail::sendMail('Welcome to Sambandh Matrimony', 'Your account has been created!', $email);
              echo "success";
            }
            else {
              echo "invalid password length";
            }
          }
          else {
            echo "email address already exist";
          }

        }
        else {
          echo "invalid email";
        }
      }

        else {
          echo "invalid username";
        }
      }
      else {
        echo "username length should be 4-32";
      }
    }
  else {
    echo "user already exist!";
  }
}
?>
<h1>Register</h1>
<form class="" action="create-account.php" method="post"><p />
  <input type="text" name="username" placeholder="Username ..."><p />
  <input type="password" name="password" placeholder="Password ..."><p />
  <input type="email" name="email" placeholder="johnappleseed@gmail.com"><p />
  <input type="submit" name="create_acc" value="Create Account">
</form>


// TODO Create a validation for duplicate email using queries
