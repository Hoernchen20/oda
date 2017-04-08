<?php
include "dbconnect.inc.php";
include "dbfunction.inc.php";

define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");
 
define("SECURE", TRUE);    // NUR FÜR DIE ENTWICKLUNG!!!!
 
function sec_session_start() {
    $session_name = 'sec_session_id';   // vergib einen Sessionnamen
    $secure = SECURE;
    // Damit wird verhindert, dass JavaScript auf die session id zugreifen kann.
    $httponly = true;
    // Zwingt die Sessions nur Cookies zu benutzen.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Holt Cookie-Parameter.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Setzt den Session-Name zu oben angegebenem.
    session_name($session_name);
    session_start();            // Startet die PHP-Sitzung 
    session_regenerate_id();    // Erneuert die Session, löscht die alte. 
}

function login($email, $password) {
    $UserData = GetUserData($email);
 //   var_dump($UserData);

    if ( $UserData != NULL ) {
        // Wenn es den Benutzer gibt, dann wird überprüft ob das Konto
        // blockiert ist durch zu viele Login-Versuche 
        
        if ( password_verify($password, $UserData->password) ) {
            echo "<p>Passwort korrekt</p>";
            
            // Hole den user-agent string des Benutzers.
            $user_browser = $_SERVER['HTTP_USER_AGENT'];
            // XSS-Schutz, denn eventuell wir der Wert gedruckt
            $user_id = preg_replace("/[^a-zA-Z0-9]+/", "", $UserData->_id);
            $_SESSION['user_id'] = $user_id;
            // XSS-Schutz, denn eventuell wir der Wert gedruckt
            $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $UserData->email);
            $_SESSION['username'] = $username;
            $_SESSION['login_string'] = hash('sha512', $UserData->password . $user_browser);
            return true;
        } else {
            echo "<p>Passwort inkorrekt</p>";
        }

/*
        if (checkbrute($user_id, $mysqli) == true) {
            // Konto ist blockiert 
            // Schicke E-Mail an Benutzer, dass Konto blockiert ist
            return false;
        } else {
            // Überprüfe, ob das Passwort in der Datenbank mit dem vom
            // Benutzer angegebenen übereinstimmt.
            if ($db_password == $password) {
                // Passwort ist korrekt!
                
            } else {
                // Passwort ist nicht korrekt
                // Der Versuch wird in der Datenbank gespeichert
                $now = time();
                $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                VALUES ('$user_id', '$now')");
                return false;
            }
        }*/
    } else {
        echo "<p>Es gibt keinen Benutzer.</p>";
        return false;
    }
}

function login_check() {
    // Überprüfe, ob alle Session-Variablen gesetzt sind 
    if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Hole den user-agent string des Benutzers.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        $UserData = GetUserPassword($user_id);
        
        if ($UserData != NULL) {
            $login_check = hash('sha512', $UserData->password . $user_browser);

            if ($login_check == $login_string) {
                // Eingeloggt!!!! 
                return true;
            } else {
                // Nicht eingeloggt
                return false;
            }
        } else {
            // Nicht eingeloggt
            return false;
        }
    } else {
        // Nicht eingeloggt
        return false;
    }
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // Wir wollen nur relative Links von $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}
