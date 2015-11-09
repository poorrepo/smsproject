<?php
    header ('Content-type: text/html; charset=utf-8');

    // Ladowanie konfiguracji
    require_once './configs/config.inc';
    
    // klasa mdb2
    require_once './class/baza.class.php';    
       
    
    // jesli nie mamy swojej sesji to ja zakladamy
    if(!isset($_SESSION['sesja'])) 
    {   
        //sesja nie istenieje
        //deklarowanie sesji
        $_SESSION['sesja'] = ''; 

        if (!isset($_SESSION['inicjuj']))
        {
            session_regenerate_id(); // Session Fixation - zamienia istniejacy identyfikator sesji na nowy
            $_SESSION['inicjuj'] = true;
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        }

        // Session Hijacking
        if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'])
        {
            print_error_message('Wykryto próbę przejęcia sesji! Zaloguj się ponownie.');

            $_SESSION = array();

            header("Location: index.php");
            exit();
        }
    }

    //////////////////////
    // Logowanie
    function login() { 
           
            $zalogowany = false; 

            // Sprawdzenie czy jest juz sesja z loginem i haslem
            if (isset($_SESSION['login']) && isset($_SESSION['haslo']))
            { 
                    $login = $_SESSION['login']; 
                    $haslo = $_SESSION['haslo']; 
                    $metod = "session"; 
            } 
            elseif (isset($_POST['login']) && isset($_POST['haslo']))
            { 
                    // login zabezpieczony przeciw atakowi SQL Injection
                    $login = mysql_escape_string( strtolower($_POST['login']) ); 
                    $haslo = md5($_POST['haslo']); 
                    $metod = "post"; 
            } 
                       
            // Jesli dane zostaly dostarczone z sesji lub formularza logowania
            if ( isset($metod) )
            { 
                    $baza = new baza;
                    $baza->mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
                    $q = "
                    SELECT COUNT(id_user) AS ilosc, id_user, user_name, user_pass, user_imie, user_nazwisko, disabled, access_level
                    FROM users
                    WHERE `user_name`='".$login."' AND `user_pass`='".$haslo."'
                    GROUP BY 'id_user';
                    " ;

                    $res = $baza->mdb2->queryAll($q);
                    
                    // Jeśli baza danych zwróciła tylko jeden wynik to haslo i login zostały odnalezione
                    if (isset($res[0]['ilosc']) && ($res[0]['ilosc']== 1 ) && ($res[0]['disabled'] == 0))
                    { 
                        $zalogowany = true;  
                        // Jeśli dane nie pochodzą z sesji, to umieszczamy je w sesji
                        if($metod != "session")
                        { 
                            $_SESSION['id_user'] = $res[0]['id_user'];
                            $_SESSION['user_name'] = $res[0]['user_name'];
                            $_SESSION['user_pass'] = $res[0]['user_pass'];
                            $_SESSION['user_nazwisko'] = $res[0]['user_nazwisko'];
                            $_SESSION['user_imie'] = $res[0]['user_imie'];
                            $_SESSION['access_level'] = $res[0]['access_level'];
                            // Odczyt uprawnień i zapis do zmiennej sesyjnej
                            
                            print_r($_SESSION);
                        } 
                        $_SESSION['sesja']=1;
                    } 
                    else
                    {
                        //bledne dane logowanie, brak uzytkownika w systemie
                        $_SESSION['sesja']=0;
                        $zalogowany = false;  
                        
                    }
                    unset($baza);
            } 
            return $zalogowany; 
    } 
    
    function logout_site()
    {
        session_destroy();

        print_error_message("Zostałeś wylogowany z systemu");

        header( "refresh:2;index.php" );  
    }
    
    function print_error_message($msg) {
        echo '
            <table class="content">
                <tr>
                    <td>'.
                        $msg
                         .'
                     </td>
                </tr>
            </table>
        ';
    }
    
    function get_from_db($query) {
        $baza = new baza;

        if (PEAR::isError($baza))
        {
            print_error_message('Nie mozna sie polaczyc z baza danych: ' . $db->getMessage());
            die();
        }

        $baza->mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
        return $baza->mdb2->queryAll($query);
    }
?>