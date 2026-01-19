<?php

      $host = 'localhost';
      $db   = 'ecowaste';
      $user = 'root';
      $password = 12345678;
      $charset = 'utf8mb4';

      $dns = "mysql:host=$host;dbname=$db;charset=$charset";
      try{
        $pdo = new PDO($dns, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }catch(PDOException $e){
        echo "Connection failed: " . $e->getMessage();
      }

?>