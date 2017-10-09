<?php
include('./classes/DB.php');
include('./classes/Login.php');

  if(isLoggedIn()){
    echo "logged In ". isLoggedIn();
  }
  else {
    echo "not Logged in";
  }
?>
