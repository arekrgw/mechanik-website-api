<?php

require_once('../database.php');
//Sprawdzenie poprawnosci podanych danych oraz rodzaju polaczenia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cookie']) && isset($_POST['cookieShort'])) {
    //Pobranie obednego czasu
    $currentTime = time();
    //Pobranie danych z tablicy POST
    $cookie = $_POST['cookie'];
    $cookieShort = $_POST['cookieShort'];
    //Znalezienie cookie w bazie danych
    $cookieCheck = fetchData('SELECT * FROM auth WHERE cookie=:cookie', array('cookie'=>$cookie));
    $cookieShortCheck = fetchData('SELECT * FROM auth WHERE cookie=:cookie', array('cookie'=>$cookieShort));
    
    //Sprawdzenie czy istnieja tylko pojedyncze cookie
    if($cookieCheck->rowCount() == 1 && $cookieShortCheck->rowCount() == 1) {
        //Przerobienie do tablicy asocjacyjnej
        $cookieCheck = $cookieCheck->fetch(PDO::FETCH_ASSOC);
        $cookieShortCheck = $cookieShortCheck->fetch(PDO::FETCH_ASSOC);

        //Jesli krotszy cookie jest wiekszy od obecnego czasu to nic sie nie dzieje zwarana jest wartosc TRUE
        if($cookieShortCheck['expiry'] > $currentTime){
            echo True.'IF 1';
        }
        //Jesli krotszy cookie jest mniejszy od obecnego czasu ale rownoczenie dlugi cookie jest wiekszy to krotki i dlugi cookie jest aktualizowany i przedluzant
        else if($cookieShortCheck['expiry'] < $currentTime && $cookieCheck['expiry'] > $currentTime)
        {
            //Aktualizowanie nowego czasu wygasniecia cookie
            $cookieShortUpdate = array(
                'expiryShort' => $currentTime + 60*60*3,
                'cookieShort' => $cookieShort 
            );
            $cookieUpdate = array(
                'expiry' => $currentTime + 60*60*24,
                'cookie' => $cookie 
            );
            $newExpiryPassShort = fetchData('UPDATE auth SET expiry=:expiryShort WHERE cookie=:cookieShort', $cookieShortUpdate);
            $newExpiryPass = fetchData('UPDATE auth SET expiry=:expiry WHERE cookie=:cookie', $cookieUpdate);
            if($newExpiryPassShort && $newExpiryPass){
                echo json_encode(array_merge($cookieUpdate, $cookieShortUpdate));
            }
        }
        //Jesli oba cookie sa mniejsze niz obecny czas to zwracana jest wartosc false i cookie sa usuwane z bazy danych. Wymagane jest ponowne zalogowanie
        else if($cookieCheck['expiry'] <= $currentTime){
            echo False.'IF 3';
            fetchData('DELETE FROM auth WHERE cookie=:cookie OR cookie=:shortCookie',
                array(
                    'cookie' => $cookie,
                    'shortCookie' => $cookieShort
                )
            );
        }
    }
    //Nastapil blad nie znaleziono cookie w bazie danych zwracane jest false
    else{
        echo False.'Błąd przy uwierzytelnianiu';
    }
    
}


?>