<?php
  include('./classes/DB.php');
  include('./classes/Login.php');


  if(isLoggedIn()){
    echo "logged In ". isLoggedIn();

      if(isset($_POST['currentpass'])){
        $oldpassword = $_POST['currentpass'];
        $newpass = $_POST['newpass'];
        $confirm = $_POST['newpass_confirm'];

        if(password_verify($oldpassword,DB::query('SELECT password FROM users where id = :userid', array(':userid'=>Login::isLoggedIn()))[0]['password'])
      }

  }
  else {
    die("not Logged in");
  }

?>
<h1>Change your password</h1>
<form action="change-password.php" method="post">
  <input type="password" name="currentpass" placeholder="Current Password ...">
  <input type="password" name="newpass" placeholder="New Password ...">
  <input type="password" name="newpass_confirm" placeholder="Confirm Password ...">
</form>
