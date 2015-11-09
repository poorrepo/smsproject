<?php

    function print_tasks_list() {
        print_tasks_form_pt1();
        
        echo '
            <textarea style="width: 100%" name="number_contact_textarea"></textarea>
        ';
        
        print_tasks_form_pt2();
    }
      
    function print_tasks_form_pt1() {
        echo '
                <form action="index.php" method="POST">
                      <table class="content">
                      <tr>
                          <td>
                               Treść wiadomości
                          </td>
                          <td>
                               Termin wysłania
                          </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;" >
                                  <textarea style="width: 100%; min-height: 200px;" name="message_content"></textarea>
                            </td>
                            <td style="width: 450px; text-align: right">
                                <input type="text" id="datetimepicker" name="send_time"/>
                            </td>
                        </tr>
                      <tr>
                      </table>
                      <table class="content">
                        <tr><td colspan="2">Adresaci</td></tr>
                      </tr>
                      <tr>
                          <td colspan="2">';
    }
    
    function print_tasks_form_pt2() {
              echo '         
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value="Dodaj zadania"/>
                    
                    <input type="hidden" value="task" name="kat"/>
                    <input type="hidden" value="verifytasks" name="id"/>
                </td>
            </tr>
         </table>
         </form>';
        echo "
        <script>
                 
        
        $('#datetimepicker').datetimepicker({value:'".date('Y/m/d H:i:s')."'});
        $('#datetimepicker').datetimepicker(
        {
            minDate: 0,
            inline:true,
        });
        
        </script>
        ";   
    }
    

    
    function print_tasks_addressbook() {

        $query = "SELECT id_contact, contact_number, contact_nickname, contact_name, contact_lastname, contact_address, contact_email FROM sms.contacts ORDER BY id_contact ASC;" ;
        $res = get_from_db($query);
        
        print_tasks_form_pt1();
        echo  '
                    <select id="number_contact" class="multiselect" multiple="multiple" name="number_contact[]">';
                    foreach ($res as $row) 
                    {
                        echo '<option value="'.$row["contact_number"].'">'.$row["contact_nickname"].' @ '.$row["contact_name"].' '.$row["contact_lastname"].' '.$row["contact_number"].' '.$row["contact_email"].' '.$row["contact_address"].'</option>';
                    }

         echo '     </select>';
         
         print_tasks_form_pt2();

    }
  
    //sprawdzanie czy numer jest na blackliscie
    function check_if_blacklisted($number)
    {    
        $query = "select black_list_numbers_number from black_list_numbers;";
        $db_blacklisted_numbers = get_from_db($query);

        //usuwanie bialych spacji itp - przydane dla konca linii z listy
        $number=str_replace(' ', '', $number);

        $found_in_blacklist=0;
        foreach($db_blacklisted_numbers as $blacklisted_number)
        {
            $blnumber = $blacklisted_number['black_list_numbers_number'];
            
            //echo "<br>$blnumber ? $number<br>";
            
            $number_lenght=strlen($number);
            $blacklisted_number_lenght=strlen($blnumber);
            
            $number_arr=str_split($number);
            $blnumber_arr=str_split($blnumber);
            
            $index=0;
            if($number_lenght == $blacklisted_number_lenght)
            {
                $found=0;
                foreach($blnumber_arr as $char)
                {
                    if(($char == $number_arr[$index]) || $char == "X")
                    {
                        //echo $char."=".$number_arr[$index]."<br>";
                        $found++;
                        $index++;
                    }
                    else
                    {
                        //echo $char."!=".$number_arr[$index]."<br>";
                        $index++;
                        break;
                    }   
                }
                if($found == $index)
                {
                    $found_in_blacklist++;
                    break;
                }
            }
            unset($index);
        }
        if($found_in_blacklist > 0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
        
    //weyfikacja zadan, czy znajduja sie na zakazanej liscie
    function verify_tasks() {
        
        
        
        //spradzenie czy przeslane dane istnieja
        if(
                isset($_POST["message_content"]) && 
                !empty($_POST["message_content"]) &&                
                isset($_POST["send_time"]) && 
                !empty($_POST["send_time"]) &&
                (
                    (isset($_POST["number_contact"]) && !empty($_POST["number_contact"])) || (isset($_POST["number_contact_textarea"]) && !empty($_POST["number_contact_textarea"]))
                )
          ) 
        {   
            $wiadomosc = $_POST["message_content"];
            $termin_wyslania = $_POST["send_time"];
            
            echo '
                 
                 <form action="index.php" method="POST">
                 <table class="content">
                    <tr><td colspan="2">Podsumowanie</td></tr>
                    <tr>
                        <td>Treść wiadomośći</td>
                        <td>'.$wiadomosc.'</td>
                    </tr>
                    <tr>
                        <td>Termin wysłania</td>
                        <td>'.$termin_wyslania.'</td>
                    </tr>                    
                    <tr>
                        <td>Odbiorcy</td>
                        <td>';
                            
                            //wyswietlanei podsumowania i weryfikacja numerow telefonow - czy sa globalnie zabronione
                                    
                            
                            //w zaleznosci czy z listy czy z ksaizki adresowej
                            if(isset($_POST["number_contact"]))
                            {
                                
                                foreach($_POST["number_contact"] as $contact_number)
                                {
                                    if(check_if_blacklisted($contact_number))
                                    {
                                        echo '<p class="alert">'.$contact_number.' <=> numer zablokowany</p>';
                                    }
                                    else
                                    {
                                        echo '<p class="normal">'.$contact_number.'</p>';
                                        echo '<input type="hidden" name="number_contact[]" value="'.$contact_number.'"/>';
                                    } 
                                }
                            }
                            else
                            {
                                $number_contact = trim($_POST["number_contact_textarea"]);
                                $number_contact = explode ("\n", $number_contact);
                                foreach($number_contact as $contact_number)
                                {
                                    if(check_if_blacklisted($contact_number))
                                    {
                                        echo '<p class="alert">'.$contact_number.' <=> numer zablokowany</p>';
                                    }
                                    else
                                    {
                                        echo '<p class="normal">'.$contact_number.'</p>
                                        <input type="hidden" name="number_contact[]" value="'.$contact_number.'"/>';
                                    }
                                }
                            }            
                 echo ' </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="add_task_submit" value="Wykonaj"/>
                        </td>
                    </tr>
                  </table>
                  <input type="hidden" name="kat" value="task"/>
                  <input type="hidden" name="id" value="addtasks"/>
                  <input type="hidden" name="send_time" value="'.$termin_wyslania.'"/>
                  <input type="hidden" name="message_content" value="'.$wiadomosc.'"/>
                </from>';
                 
        }
        else
        {
            print_error_message("Pusta wiadomość lub brak zdefiniowanego odbiorcy");
        }
    }

    //dodwanie zadan do bazy danych
    function insert_task_to_db($contact_number, $wiadomosc, $user_id, $termin_wyslania)
    {
        $baza = new baza;
        
        if (PEAR::isError($baza))
        {
            echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
            die();
        }
        
        $wyniki = $baza->mdb2->query("INSERT INTO `sms`.`tasks` (`phone_number`, `message`, `sender`, `send_time`) VALUES ('".$contact_number."', '".$wiadomosc."', '".$user_id."', '".$termin_wyslania."');");
        return $wyniki;
    }
    
    function add_tasks()
    {
        if(
         isset($_POST["message_content"]) && 
         !empty($_POST["message_content"]) &&                
         isset($_POST["send_time"]) && 
         !empty($_POST["send_time"]) &&
         isset($_POST["number_contact"]) && 
         !empty($_POST["number_contact"]))
         {
            
            $wiadomosc = $_POST["message_content"];
            $termin_wyslania = $_POST["send_time"];
            $user_id = $_SESSION["id_user"];
            
            
            //jak ok to dodawanie zadan do bazy danych aplikacji
            foreach($_POST["number_contact"] as $contact_number)
            {
                $result = insert_task_to_db($contact_number, $wiadomosc, $user_id, $termin_wyslania);

                //sprwadzenei statusu bazy danych, jak sie udalo dodac rekord to zwraca 1(true), jak nie to blad
                if(!isset($result->result))
                {
                    echo "Blad bazy danych";
                };
            }
         }
         else
         {
            print_error_message("Niekomplene dane");
            die();
         }
    }
    ?>