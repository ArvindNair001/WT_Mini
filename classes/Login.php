<?php
  class Login {
    public static function isLoggedIn() {
      if(isset($_COOKIE['MID'])){
        if(DB::query('SELECT user_id FROM login_tokens WHERE token=:token',array(':token'=>sha1($_COOKIE['MID'])))){
            $user_id = DB::query('SELECT user_id FROM login_tokens WHERE token=:token',array(':token'=>sha1($_COOKIE['MID'])))[0]['user_id'];

            if(isset($_COOKIE['TMID'])){
                return $user_id;
            }
            else {
              $cstrong = True;
              $token= bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
              DB::query('INSERT INTO login_tokens (token,user_id)VALUES (:token,:user_id)', array(':token'=>sha1($token),':user_id'=>$user_id));
              DB::query('DELETE FROM login_tokens WHERE token=:token',array(':token'=>$_COOKIE['MID']));
              setcookie("MID",$token,time()+60*60*24*7*4,'/',NULL,NULL,TRUE); //primary token
              $temp_token= bin2hex(openssl_random_pseudo_bytes(32,$cstrong));
              setcookie("TMID",$temp_token,time()+60*60*24*7,'/',NULL,NULL,TRUE); //secondary token
              return $user_id;

            }
          }
      }
      return False;
    }
  }
?>
