<?php
  include('classes/DB.php');
  include('classes/Login.php');


  if(!Login::isLoggedIn()){
    die("Session Expired");
    //TODO: ADD a redirect to login
  }
  if(isset($_POST['logout'])){


    if(isset($_POST['alldevices'])){
      DB::query('DELETE FROM login_tokens WHERE user_id=:user_id', array(':user_id'=>Login::isLoggedIn()));

    }

    else {
      if(isset($_COOKIE['MID'])){
        DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['MID'])));
      }
      setcookie("MID",'1',time()-3600);
      setcookie("TMID",'1',time()-3600);
    }

  }


?>

<form action="logout.php" method="post">
  <input type="checkbox" name="alldevices" value="">
  <p>Sign out of All Devices</p><p \>
  <input type="submit" name="logout" value="Logout">
</form>
