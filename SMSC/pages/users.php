<?php

    function update_user()
    {
         //sprawdzanie czy pola wypelnione  

        if
        (
            (isset($_POST['id_user'])) &&
            (!empty($_POST['id_user'])) &&
            (isset($_POST['user_name'])) &&
            (!empty($_POST['user_name'])) &&
            (isset($_POST['user_imie'])) &&
            (!empty($_POST['user_imie'])) &&
            (isset($_POST['user_nazwisko'])) &&
            (!empty($_POST['user_nazwisko'])) &&
            (isset($_POST['user_dzial'])) &&
            (!empty($_POST['user_dzial'])) &&
            (isset($_POST['access_level'])) &&
            (!empty($_POST['access_level'])) &&
            (isset($_POST['disabled']))
        )
        {
            //update contact
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query=
            "
                UPDATE `sms`.`users` 
                SET 
                `user_name`='".$_POST['user_name']."', 
                `access_level`='".$_POST['access_level']."', 
                `user_imie`='".$_POST['user_imie']."', 
                `user_nazwisko`='".$_POST['user_nazwisko']."',
                `user_dzial`='".$_POST['user_dzial']."', 
                `disabled`=".$_POST['disabled']."
                WHERE `id_user`='".$_POST['id_user']."
            ';";

            
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
            die();
        }    
    }
    function print_editusers_view()
    {
        if(isset($_POST["id_user"]) && !empty($_POST["id_user"]))
        {
            $id_user=$_POST["id_user"];
             
            echo '<h2>Nowe dane użytkownika</h2>
                  <form action="index.php" method="POST">
                    <table class="content">
                        <tr>
                            <td>ID</td>
                            <td>Login</td>
                            <td>Imię</td>
                            <td>Nazwisko</td>
                            <td>Dział</td>
                            <td>Data rejestracji</td>
                            <td>Zablokowany</td>
                            <td>Poziom</td>
                            <td>Edytuj</td>
                            <td>Usun</td>
                        </tr>';
             
           
            $query="SELECT 
            user_name, 
            user_imie, 
            user_nazwisko, 
            user_dzial,
            register_date, 
            disabled, 
            access_level 
            FROM sms.users
            WHERE id_user = '".$id_user."';";
            
            $res = get_from_db($query);

            foreach ($res as $row) 
            {
                echo '<tr>
                        <td>'.$id_user.'</td>
                        <td><input type="text" name="user_name" value="'.$row["user_name"].'"/></td>
                        <td><input type="text" name="user_imie" value="'.$row["user_imie"].'"/></td>
                        <td><input type="text" name="user_nazwisko" value="'.$row["user_nazwisko"].'"/></td>
                        <td><input type="text" name="user_dzial" value="'.$row["user_dzial"].'"/></td>
                        <td>'.$row["register_date"].'</td>
                        <td><input type="text" name="disabled" value="'.$row["disabled"].'"/></td>
                        <td><input type="text" name="access_level" value="'.$row["access_level"].'"/></td>
                        <td>
                                <input type="hidden" name="id_user" value="'.$id_user.'" />
                                <input type="hidden" name="kat" value="users" />
                                <input type="hidden" name="id" value="editsave" />
                                <input type="submit" value="Zapisz" />
                            </form>
                        </td>
                        <td>
                            <form action="index.php" method="POST">
                                <input type="hidden" name="id_user" value="'.$id_user.'" />
                                <input type="hidden" name="kat" value="users" />
                                <input type="hidden" name="id" value="deluser" />
                                <input style="background-color: red;" type="submit" value="Usun" />
                            </form>
                        </td>                        
                      </tr>'
                    ;
            }

            
            echo '             
                    </table>
                    
                    <h2>Zmien haslo</h2>
                    <form action="index.php" method="POST">
                    <table class="content">
                        <tr>
                            <td>
                                Nowe hasło
                            </td>
                            <td>
                                <input type="text" name="newpassword"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="kat" value="users"/>
                                <input type="hidden" name="id" value="newpass"/>
                                <input type="hidden" name="id_user" value="'.$id_user.'"/>
                                <input type="submit" name="setnewpassword"/>
                            </td>
                        </tr>
                    </table>
                    </form>';
        }
    }
    
    function update_userpass()
    {
        if(
                isset($_POST['id_user']) && 
                !empty($_POST['id_user']) &&
                isset($_POST['newpassword']) &&
                !empty($_POST['newpassword'])
          )
        {
            $newpass=md5($_POST['newpassword']);
            $id_user=$_POST['id_user'];
            
            //update password
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query="
            UPDATE `sms`.`users` 
            SET `user_pass`='".$newpass."' 
            WHERE `id_user`='".$id_user."';";
                          
            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
    }
    function print_users_view()
    {
        $query='SELECT 
            id_user, 
            user_name, 
            user_imie, 
            user_nazwisko, 
            user_dzial,
            register_date, 
            disabled, 
            access_level 
            FROM sms.users;';
        $res = get_from_db($query);
        
        echo '
            <div class="content">
                <table>
                <tr>
                    <td>ID</td>
                    <td>Login</td>
                    <td>Imię</td>
                    <td>Nazwisko</td>
                    <td>Dział</td>
                    <td>Data rejestracji</td>
                    <td>Zablokowany</td>
                    <td>Poziom</td>
                    <td>Edytuj</td>
                </tr>
                ';
        
        foreach($res as $row)
        {
            echo '<tr>
                    <td>'.$row["id_user"].'</td>
                    <td>'.$row["user_name"].'</td>
                    <td>'.$row["user_imie"].'</td>
                    <td>'.$row["user_nazwisko"].'</td>
                    <td>'.$row["user_dzial"].'</td>
                    <td>'.$row["register_date"].'</td>
                    <td>'.$row["disabled"].'</td>
                    <td>'.$row["access_level"].'</td>
                    <td>
                        <form action="index.php" method="POST">
                            <input type="hidden" name="id_user" value="'.$row["id_user"].'"/>
                            <input type="hidden" name="kat" value="users" />
                            <input type="hidden" name="id" value="edituser" />                                
                            <input type="submit" value="Edytuj"/>
                        </form>
                    </td>
                  </tr>                       
                ';
        }
        echo '</table>
            </div>';
        
    }
    
    function add_user()
    {
        if
            (
                isset($_POST['user_pass']) &&
                !empty($_POST['user_pass']) &&
                isset($_POST['user_imie']) &&
                !empty($_POST['user_imie']) &&
                isset($_POST['user_nazwisko']) &&
                !empty($_POST['user_nazwisko']) &&
                isset($_POST['user_dzial']) &&
                !empty($_POST['user_dzial']) &&  
                isset($_POST['disabled']) &&
                isset($_POST['access_level']) &&
                !empty($_POST['access_level'])               
            )
        {
            $pass=md5($_POST['user_pass']);
            
            //add user
            $baza = new baza;
            
            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query="INSERT INTO `sms`.`users` 
                (
                    `user_name`, 
                    `user_pass`, 
                    `user_imie`, 
                    `user_nazwisko`, 
                    `user_dzial`, 
                    `disabled`,
                    `access_level`
                ) 
                VALUES 
                (
                    '".$_POST['user_name']."', 
                    '".$pass."', 
                    '".$_POST['user_imie']."', 
                    '".$_POST['user_nazwisko']."', 
                    '".$_POST['user_dzial']."', 
                    ".$_POST['disabled'].",
                    '".$_POST['access_level']."'
                );";
            
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
            die();
        }
    }
    
    function print_users_add()
    {
        echo '<div class="content">
                  <form action="index.php" method="POST">
                    <table>
                        <tr>
                            <td>Login</td>
                            <td>Hasło</td>
                            <td>Imię</td>
                            <td>Nazwisko</td>
                            <td>Dział</td>
                            <td>Zablokowany</td>
                            <td>Poziom</td>
                            <td>Dodaj</td>
                        </tr>';  
        
        echo '<tr>
                        <td><input type="text" name="user_name"/></td>
                        <td><input type="text" name="user_pass"/></td>
                        <td><input type="text" name="user_imie"/></td>
                        <td><input type="text" name="user_nazwisko"/></td>
                        <td><input type="text" name="user_dzial" value="brak"/></td>
                        <td><input type="text" name="disabled" value="0"/></td>
                        <td><input type="text" name="access_level" value="1"/></td>
                        <td>
                                <input type="hidden" name="kat" value="users" />
                                <input type="hidden" name="id" value="addusersave" />
                                <input type="submit" value="Dodaj" />
                        </td>                       
                      </tr>
                   </table>'
                    ;
    }
    
    function del_user()
    {
        if(
            isset($_POST['id_user']) &&
            !empty($_POST['id_user'])
           )
            {
            $id_user=$_POST['id_user'];
            
            //delete user
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query="DELETE FROM `sms`.`users` WHERE `id_user`='".$id_user."';";

            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        } 
    }
?>