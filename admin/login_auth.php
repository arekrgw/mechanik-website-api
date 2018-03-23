<?php

require_once('../database.php');

//Generowanie Cookie
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $credentialsArray = array(
        'username' => htmlentities($username),
        'password' => md5($password)
    );


    $data = fetchData('SELECT * FROM admins WHERE username=:username AND password=:password', $credentialsArray);
    
    if ($data->rowCount() == 1){  
        // Generowanie nowych cookie   
        $cookie = generateRandomString(25);
        $cookieShort = generateRandomString(25);
        // Wysylanie dlugiego cookie
        $cookieArray =  array(
            'cookie'=> $cookie,
            'expiry' => time() + 60*60*24
        );
        $res = fetchData('INSERT INTO auth VALUES(null, :cookie, :expiry)', $cookieArray);
        // Wysylanie krotkiego cookie
        $cookieShortArray =  array(
            'cookieShort'=> $cookieShort,
            'expiryShort' => time() + 60*60*3
        );
        $short = fetchData('INSERT INTO auth VALUES(null, :cookieShort, :expiryShort)', $cookieShortArray);
        //Jeśli wysylanie sie powiodlo zwracane sa Cookie do klienta
        if($res && $short) {
            //Łaczenie tablic i kodownie do JSON
            echo json_encode(array_merge($cookieArray, $cookieShortArray));
        }
    }
    else
    {
        echo 'Błędne dane';
    }

}











?>