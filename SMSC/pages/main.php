<?php

    function print_main_head() {
        echo '<table class="print_main_head">
                <tr>
                    <td>
                        <h2><a href="index.php">SMSC: SMS Client</a></h2>
                    </td>
                </tr>
              <table>';        
    }
    
    function print_main_menu() {        
        //w zalezonosci od poziomu uprawnien
        echo '
            <table class="menu_tab">
                <tr>
                    <td>
                <ul class="menu menum">
                    <li class="menui">
                        <a class="menui">
                            <span>Zaplanuj zadanie</span>
                            <![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
                            <ul class="menum">
                                <li class="menui">
                                    <a class="menui" href="index.php?kat=task&id=multi-list">
                                        Odczyt z listy
                                    </a>
                                </li>
                                   <li class="menui">
                                    <a class="menui" href="index.php?kat=task&id=multi-addressbook">
                                        Odczyt z książki adresowej
                                    </a>
                                </li>
                            </ul>                
                        <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    </li>
                    <li class="menui">
                        <a class="menui">
                            <span>Książka adresowa</span>
                            <![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
                            <ul class="menum">
                                <li class="menui">
                                    <a class="menui" href="index.php?kat=addressbook&id=view">
                                        Przeglądaj
                                    </a>
                                </li>
                                <li class="menui">
                                    <a class="menui" href="index.php?kat=addressbook&id=addcontact">
                                        Dodaj kontakt
                                    </a>
                            </ul>                
                        <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    </li>
                    <li class="menui">
                        <a class="menui" href="index.php?kat=mytasks">
                            <span>Historia zadań</span>
                            <![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->             
                            <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    </li>
                ';

                if($_SESSION['access_level'] >= 100)
                {
                    echo '
                    <li class="menui">
                        <a class="menui">
                            <span>Użytkownicy</span>
                            <![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
                            <ul class="menum">
                                <li class="menui">
                                    <a class="menui" href="index.php?kat=users&id=view">
                                        Przeglądaj
                                    </a>
                                </li>
                                <li class="menui">
                                    <a class="menui" href="index.php?kat=users&id=add">
                                        Dodaj użytkownika
                                    </a>
                            </ul>                
                        <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    </li>
                    <li class="menui">
                        <a class="menui" href="index.php?kat=settings&id=view">
                            <span>Ustawienia</span>
                            <![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->             
                            <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    </li>
                   
                    ';
                }

                echo '
                    <li class="menui">
                        <a class="menui" href="./index.php?kat=logout">
                            <span>Wyloguj</span>
                            <![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->

                        <!--[if lte IE 6]></td></tr></table></a><![endif]-->
                    </li>
                    </ul>
                   <td>
                  <tr>
                 </table>
            ';
    }
    
   
    //podstawowe informacje o SMSServer
    
    
    
    function print_simple_stats() {
        
        $calkowita_ilosc_zadan_query="SELECT count(id_task) as kolejka FROM sms.tasks;";
        $ilosc_zadan_uzytkownika_query="SELECT count(id_task) as kolejka FROM sms.tasks WHERE sender='1".$_SESSION['id_user']."';";
        
        $res1 = get_from_db($calkowita_ilosc_zadan_query);
        $res2 = get_from_db($ilosc_zadan_uzytkownika_query);
        
        echo '
            <table class="content">
                <tr><td colspan="2">Statystyki</td></tr>
                <tr>
                    <td>Aplikacja ONLINE</td>';
                    if(check_if_server_working())
                    {
                        echo '<td style="background-color: green">TAK</td>';
                    }
                    else 
                    {
                        echo '<td style="background-color: red">NIE</td>';  
                    }
        echo '      </td>
                </tr>
                <tr>
                    <td>Ilość Twoich zadań w kolejce</td><td>'.$res1[0]['kolejka'].'</td>
                </tr>
                <tr>
                    <td>Ilość wszystkich zadań w kolejce</td><td>'.$res2[0]['kolejka'].'</td>
                </tr>                
            </table>';
    }
    
    function print_message_board(){
        
        $baza = new baza;
        $baza->mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
        $q = "select date_message_board, message_message_board from message_board ORDER BY date_message_board DESC;;" ;

        $res = $baza->mdb2->queryAll($q);

        echo '
                <table class="content">
                    <tr><td colspan="2">Aktualności</td></tr>';
                    foreach ($res as $row) 
                    {
                        echo '<tr><td>'.$row["date_message_board"].'</td><td>'.$row["message_message_board"].'</td></tr>';
                    }
        
        echo '</table>';
    }
    
    function print_main_body() {
        echo'
            <table class="content">
                <tr><td colspan="2">Informacje o użytkowniku</td></tr>
                <tr>
                    <td>Użytkownik</td><td>'.$_SESSION['user_name'].'</td>
                </tr>
                    <td>Imię</td><td>'.$_SESSION['user_imie'].'</td>
                </tr>
                <tr><td>Nazwisko</td><td>'.$_SESSION['user_nazwisko'].'</td></tr>
                <tr>
                    <td>
                        Adres IP
                     </td>
                     <td>
                        '.$_SERVER['REMOTE_ADDR'].'
                    </td>
                </tr>
                <tr>
                    <td>
                        User-agent
                     </td>
                     <td>
                        '.$_SERVER['HTTP_USER_AGENT'].'
                    </td>
                </tr>                
             </table>';
            echo "<br/>";
                print_message_board();
                echo "<br/>";
                print_simple_stats();
        
    }
?>