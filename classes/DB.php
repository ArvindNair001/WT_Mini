<?php
    class DB {
      private static function connect() {
        $host = 'mysql';
        $db_usrname = 'root';
        $db_pass = 'password';
        $dbname = 'sambandh';
        $dbchar = 'utf8';
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$dbchar";
        $pdo = new PDO($dsn,$db_usrname,$db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        return $pdo;
      }

      public static function query($query, $params=array()) {
        $statement = self::connect()->prepare($query);
        $statement->execute($params);

        if(explode(' ',$query)[0] == 'SELECT'){
          $data = $statement->fetchAll();
          return $data;
        }
      }
    }
 ?>
