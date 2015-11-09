<?php
    
    //funckja dodawana kontaktu
    function add_contact()
    {        
        //sprawdzanie czy pola wypelnione
        if
        (
            (isset($_POST['contact_number'])) &&
            (!empty($_POST['contact_number'])) &&
            (isset($_POST['contact_nickname'])) &&
            (!empty($_POST['contact_nickname'])) &&
            (isset($_POST['contact_name'])) &&
            (!empty($_POST['contact_name'])) &&
            (isset($_POST['contact_lastname'])) &&
            (!empty($_POST['contact_lastname'])) &&
            (isset($_POST['contact_address'])) &&
            (!empty($_POST['contact_address'])) &&
            (isset($_POST['contact_email'])) &&
            (!empty($_POST['contact_email']))
        )
        {
            //update contact
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }
                               
            $query="INSERT INTO `sms`.`contacts` 
                (
                    `contact_number`, 
                    `contact_nickname`, 
                    `contact_name`, 
                    `contact_lastname`, 
                    `contact_address`, 
                    `contact_email`
                ) 
                VALUES 
                (
                    '".$_POST['contact_number']."', 
                    '".$_POST['contact_nickname']."', 
                    '".$_POST['contact_name']."', 
                    '".$_POST['contact_lastname']."', 
                    '".$_POST['contact_address']."', 
                    '".$_POST['contact_email']."'
                );
";
            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
        else
        {
            //wszytko musi byc wypelnione
            print_error_message("Wszsytkie pola musza być wypełnione");
        }
    }
    
    //funkcja aktualizacji kontaktu
    function update_contact()
    {        
        //sprawdzanie czy pola wypelnione
        if
        (
            (isset($_POST['id_contact'])) &&
            (!empty($_POST['id_contact'])) &&
            (isset($_POST['contact_number'])) &&
            (!empty($_POST['contact_number'])) &&
            (isset($_POST['contact_nickname'])) &&
            (!empty($_POST['contact_nickname'])) &&
            (isset($_POST['contact_name'])) &&
            (!empty($_POST['contact_name'])) &&
            (isset($_POST['contact_lastname'])) &&
            (!empty($_POST['contact_lastname'])) &&
            (isset($_POST['contact_address'])) &&
            (!empty($_POST['contact_address'])) &&
            (isset($_POST['contact_email'])) &&
            (!empty($_POST['contact_email']))
        )
        {
            //update contact
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }
                    
            $query="UPDATE `sms`.`contacts` 
            SET 
            `contact_number`='".$_POST['contact_number']."', 
            `contact_nickname`='".$_POST['contact_nickname']."', 
            `contact_name`='".$_POST['contact_name']."', 
            `contact_lastname`='".$_POST['contact_lastname']."', 
            `contact_address`='".$_POST['contact_address']."', 
            `contact_email`='".$_POST['contact_email']."' 
            WHERE `id_contact`='".$_POST['id_contact']."';";
            
            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
        else
        {
            //wszytko musi byc wypelnione
            print_error_message("Wszsytkie pola musza być wypełnione");
        }
    }
    
    //funckja usuwanie kontaktu
    function delete_contact()
    {
      
        //sprawdzanie czy pola wypelnione
        if
        (
            (isset($_POST['id_contact'])) &&
            (!empty($_POST['id_contact']))
        )
        {
            //delete contact
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query="DELETE FROM `sms`.`contacts` WHERE `id_contact`='".$_POST['id_contact']."';";

            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }        
    }
    
    //strona glowna ksiazki adresowej
    function print_myaddressbook_page()
    { 
        echo '
              <table class="content">
                        <tr>
                            <td>Numer</td>
                            <td>Pseudonim</td>
                            <td>Imię</td>
                            <td>Nazwisko</td>
                            <td>Adres</td>
                            <td>E-mail</td>
                            <td>Edytuj</td>
                        </tr>';
                
        $query = "SELECT id_contact, contact_number, contact_nickname, contact_name, contact_lastname, contact_address, contact_email FROM sms.contacts ORDER BY id_contact ASC;" ;
        $res = get_from_db($query);
        
        foreach ($res as $row) 
        {
            echo '<tr>
                    <td>'.$row["contact_number"].'</td>
                    <td>'.$row["contact_nickname"].'</td>
                    <td>'.$row["contact_name"].'</td>
                    <td>'.$row["contact_lastname"].'</td>
                    <td>'.$row["contact_address"].'</td>
                    <td>'.$row["contact_email"].'</td>
                    <td>
                        <form action="index.php" method="POST">
                            <input type="hidden" name="editContactID" value="'.$row["id_contact"].'" />
                            <input type="hidden" name="kat" value="addressbook" />
                            <input type="hidden" name="id" value="edit" />
                            <input type="submit" value="Edytuj" />
                        </form>
                    </td>
                  </tr>'
                ;
        }

        echo '</table>';
       
    }
 
    //edycja kontaktu
    function print_editcontact_page()
    {

        if(isset($_POST["editContactID"]) && !empty($_POST["editContactID"]))
        {
             echo '<div class="content">
                  <form action="index.php" method="POST">
                    <table>
                        <tr>
                            <td>Numer</td>
                            <td>Pseudonim</td>
                            <td>Imię</td>
                            <td>Nazwisko</td>
                            <td>Adres</td>
                            <td>E-mail</td>
                            <td>Zapisz zmiany</td>
                            <td>Usuń kontakt</td>
                        </tr>';
             
            $editContactID=$_POST["editContactID"];

            $query = "SELECT id_contact, contact_number, contact_nickname, contact_name, contact_lastname, contact_address, contact_email FROM sms.contacts WHERE id_contact = ".$editContactID." ORDER BY id_contact ASC;" ;
            $res = get_from_db($query);

            foreach ($res as $row) 
            {
                echo '<tr>
                        <td><input type="text" name="contact_number" value="'.$row["contact_number"].'"/></td>
                        <td><input type="text" name="contact_nickname" value="'.$row["contact_nickname"].'"/></td>
                        <td><input type="text" name="contact_name" value="'.$row["contact_name"].'"/></td>
                        <td><input type="text" name="contact_lastname" value="'.$row["contact_lastname"].'"/></td>
                        <td><input type="text" name="contact_address" value="'.$row["contact_address"].'"/></td>
                        <td><input type="text" name="contact_email" value="'.$row["contact_email"].'"/></td>
                        <td>
                                <input type="hidden" name="id_contact" value="'.$row["id_contact"].'" />
                                <input type="hidden" name="kat" value="addressbook" />
                                <input type="hidden" name="id" value="editsave" />
                                <input type="submit" value="Zapisz" />
                            </form>
                        </td>
                        <td>
                            <form action="index.php" method="POST">
                                <input type="hidden" name="id_contact" value="'.$row["id_contact"].'" />
                                <input type="hidden" name="kat" value="addressbook" />
                                <input type="hidden" name="id" value="delcontact" />
                                <input style="background-color: red;" type="submit" value="Usun" />
                            </form>
                        </td>                        
                      </tr>'
                    ;
            }

            echo '
                    </table>
                </div>';
        }
    }   
    
    //dodawanie kontaktu
    function print_addcontact_page()
    { 
        echo '<div class="content">
                    <form action="index.php" method="POST">
                    <table>
                        <tr>
                            <td>Numer</td>
                            <td>Pseudonim</td>
                            <td>Imię</td>
                            <td>Nazwisko</td>
                            <td>Adres</td>
                            <td>E-mail</td>
                            <td>Akcja</td>
                        </tr>';
                
           echo '<tr>
                    <td><input type="text" name="contact_number"/></td>
                    <td><input type="text" name="contact_nickname"/></td>
                    <td><input type="text" name="contact_name"/></td>
                    <td><input type="text" name="contact_lastname"/></td>
                    <td><input type="text" name="contact_address"/></td>
                    <td><input type="text" name="contact_email"/></td>
                    <td>
                        <input type="hidden" name="kat" value="addressbook" />
                        <input type="hidden" name="id" value="addcontactsave" />
                        <input type="submit" value="Dodaj kontakt" />
                    </td>
                  </tr>
                </table>
            </div>';
       
    }

?>