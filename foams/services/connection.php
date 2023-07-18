<?php


    $hostname = '121.121.232.54'; 
    $port        =  5433;
    $database    = 'piat_checklist';
    $username    = 'postgres';
    $password    = 'Admin123';
 
    $pdo = new PDO("pgsql:host=$hostname;dbname=$database;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     
 
   



?>