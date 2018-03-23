<?php

function fetchData($query, $data = array()){
    $host = 'localhost';
    $db_name= 'mechanik';
    $db_user = 'root';
    $db_password = '';

    $con = new PDO("mysql:host=$host;dbname=$db_name",$db_user, $db_password);
    $stmt = $con->prepare($query);
    $stmt->execute($data);

    $con = null;

    return $stmt;
}

?>