<?php
    class DB {
      private $pdo;
      public function __construct($host,$db_usrname,$db_pass,$dbname) {
        $dbchar = 'utf8';
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$dbchar";
        $pdo = new PDO($dsn,$db_usrname,$db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        // return $pdo;
        $this->pdo = $pdo;
      }

      public function query($query, $params=array()) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        if(explode(' ',$query)[0] == 'SELECT'){
          $data = $statement->fetchAll();
          return $data;
        }
      }
    }
 ?>
