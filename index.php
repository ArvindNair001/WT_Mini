<?php
include('./classes/DB.php');
include('./classes/Login.php');

  if(Login::isLoggedIn()){
    echo "logged In ". Login::isLoggedIn();
  }
  else {
    echo "not Logged in";
  }
?>
