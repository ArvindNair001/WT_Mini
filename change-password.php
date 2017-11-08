<?php
  include('./classes/DB.php');
  include('./classes/Login.php');

  $tokenisValid = False;

  if(Login::isLoggedIn()){
    echo "logged In ". Login::isLoggedIn();

      if(isset($_POST['confirm'])){
        $oldpassword = $_POST['currentpass'];
        $newpass = $_POST['newpass'];
        $confirm = $_POST['newpass_confirm'];
        $user_id = Login::isLoggedIn();
        if(password_verify($oldpassword,DB::query('SELECT password FROM users where id = :userid', array(':userid'=>$user_id))[0]['password'])){
          if ($newpass>=6 && $newpass<=60) {
              if ($newpass === $newpass_confirm) {
                DB::query('UPDATE users SET password=:newpassword WHERE id = :user_id',array(':newpassword'=>password_hash($newpass,PASSWORD_BCRYPT),':user_id'=>$user_id));
                echo "password changed successfully";
              }
          }
        }
        else {
          echo "invalid password";
        }
      }

  }
  else {
    if(isset($_GET['token'])){
      $token = $_GET['token'];
      $shatoken = sha1($token);
      echo $shatoken;
      if(DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))){
        $user_id = DB::query('SELECT user_id FROM password_tokens WHERE token=:token',array(':token'=>sha1($token)))[0]['user_id'];
        $tokenisValid = True;

          if(isset($_POST['confirm'])){
            $newpass = $_POST['newpass'];
            $newpass_confirm = $_POST['newpass_confirm'];
              if (strlen($newpass)>=6 && strlen($newpass)<=60) {
                  if ($newpass == $newpass_confirm) {
                    DB::query('UPDATE users SET password=:newpassword WHERE id = :user_id',array(':newpassword'=>password_hash($newpass,PASSWORD_BCRYPT),':user_id'=>$user_id));
                    echo "password changed successfully";
                    DB::query('DELETE FROM password_tokens WHERE user_id = :user_id',array(':user_id'=>$user_id));
                  }
                  else {
                    echo "<script>alert('Passwords do not match');</script>";
                  }
              }
              else {
                echo "<script>alert('invalid lenght');</script>";

              }
            }
          }
            else {
              echo "invalid token";
            }
    }
    else {
      die("not Logged in");
    }
  }

?>
<h1>Change your password</h1>
<form action="<?php if(!$tokenisValid) {echo 'change-password.php';} else{ echo 'change-password.php?token='.$token;} ?>" method="post">
  <?php if(!$tokenisValid) {echo '<input type="password" name="currentpass" placeholder="Current Password ...">';} ?>
  <input type="password" name="newpass" placeholder="New Password ...">
  <input type="password" name="newpass_confirm" placeholder="Confirm Password ...">
  <input type="submit" name="confirm" value="Confirm">
</form>
