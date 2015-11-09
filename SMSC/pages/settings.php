<?php
    function print_settings_view() {
        
        echo '<h2>Dodaj aktualności</h2>';
        add_news_print();
        
        echo '<h2>Modyfikuj aktualności</h2>';
        news_mangament_print();
        
        echo '<h2>Zablokuj numer</h2>';
        add_blacklist_number_print();
        
        echo '<h2>Zablokowane numery</h2>';
        blacklist_management_print();
    }
    
    function get_news() {
        $baza = new baza;
        
         $query="SELECT 
            id_message_board, 
            message_message_board,
            date_message_board
            FROM sms.message_board;";
        
        if (PEAR::isError($baza))
        {
            echo 'Nie mozna sie polaczyc z baza danych: ' . $baza->getMessage();
            die();
        }

        
        $baza->mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
        return $baza->mdb2->queryAll($query);
    }
        
    function news_mangament_print() {
        echo '
        <table class="content">
        <tr>
            <td>ID</td>
            <td>Wiadomość</td>
            <td>Data wiadomośći</td>
            <td>Modyfikuj</td>
            <td>Usuń</td>
        </tr>
        ';
        $news = get_news();
        foreach($news as $msg)
        {
            echo '<tr>
                    <td>
                        <form action="index.php" method="POST">
                        '.$msg['id_message_board'].'
                    </td>
                    <td>
                        <input type="text" name="message_message_board" value="'.$msg['message_message_board'].'"/>
                    </td>
                    <td>
                        '.$msg['date_message_board'].'
                    </td>
                    <td>
                        <input type="hidden" name="update_news_id" value="'.$msg['id_message_board'].'"/>
                        <input type="hidden" name="kat" value="settings"/>
                        <input type="hidden" name="id" value="updatenews"/>
                        <input type="submit" name="update_news_submit" value="Aktualizuj"/>
                        </form>
                    </td>
                    <td>
                        <form action="index.php" method="POST">
                            <input type="hidden" name="delete_news" value="'.$msg['id_message_board'].'"/>
                            <input type="hidden" name="kat" value="settings"/>
                            <input type="hidden" name="id" value="deletenews"/>
                            <input type="submit" name="delete_news_submit" value="Usuń"/>
                        </form>
                    </td>
                  </tr>';
        }

  echo' </table>
        ';
    }
    
    function add_news_print() {
        echo '
            <form action="index.php" method="POST">
            <table class="content">
                <tr>
                    <td>Treść</td>
                    <td>Akcja</td>
                </tr>
                <tr>
                    <td>
                        <textarea style="width: 100%; min-height: 200px;" name="news_content"></textarea>
                    </td>
                    <td>
                        <input type="hidden" name="kat" value="settings"/>
                        <input type="hidden" name="id" value="addnews"/>
                        <input type="submit" name="add_news_submit"/>
                    </td>
                </tr>
             </table>
             </form>';
        
    }
    
    function add_news() {
        if(isset($_POST['news_content']) && !empty($_POST['news_content']))
        {
            //add msg
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query="
                INSERT INTO `sms`.`message_board` 
                (
                    `message_message_board`
                ) 
                VALUES 
                (
                    '".$_POST['news_content']."'
                );";
            
            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
    }
    
    function delete_news() {
        if((isset($_POST['delete_news'])) && (!empty($_POST['delete_news'])))
        {
            //mysql
            $delete_news_id=$_POST['delete_news'];
            
            //delete user
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query="DELETE FROM `sms`.`message_board` WHERE `id_message_board`='".$delete_news_id."';";
            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
    }

    function update_news() {
        if(
                isset($_POST['update_news_id']) &&
                !empty($_POST['update_news_id']) &&
                isset($_POST['message_message_board']) &&
                !empty($_POST['message_message_board']
        )
        )
        {
        //mysql
            $id_message_board=$_POST['update_news_id'];
            $message_message_board=$_POST['message_message_board'];
            
            //update msg
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }
            
            $query="
                UPDATE `sms`.`message_board` 
                SET `message_message_board`='".$message_message_board."' 
                WHERE `id_message_board`='".$id_message_board."';";
            
            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
    }
    
    function blacklist_management_print() {
        echo '
        <table class="content">
        <tr>
            <td>ID</td>
            <td>Numer</td>
            <td>Komentarz</td>
            <td>Modyfikuj</td>
            <td>Usuń</td>
        </tr>
        ';
        
        $query="SELECT `id_black_list_number`, `black_list_numbers_number`, `black_list_numbers_comment` FROM sms.black_list_numbers";
        $blacklisted_numbers=get_from_db($query);
        
        foreach($blacklisted_numbers as $blnumber)
        {
            echo '<tr>
                    <td>
                        <form action="index.php" method="POST">
                        '.$blnumber['id_black_list_number'].'
                    </td>
                    <td>
                        <input type="text" name="black_list_numbers_number" value="'.$blnumber['black_list_numbers_number'].'"/>
                    </td>                    
                    <td>
                        <input type="text" name="black_list_numbers_comment" value="'.$blnumber['black_list_numbers_comment'].'"/>
                    </td>
                    <td>
                        <input type="hidden" name="update_blacklist_number_id" value="'.$blnumber['id_black_list_number'].'"/>
                        <input type="hidden" name="kat" value="settings"/>
                        <input type="hidden" name="id" value="updatebln"/>
                        <input type="submit" name="update_bln_submit" value="Aktualizuj"/>
                        </form>
                    </td>
                    <td>
                        <form action="index.php" method="POST">
                            <input type="hidden" name="delete_blacklist_number_id" value="'.$blnumber['id_black_list_number'].'"/>
                            <input type="hidden" name="kat" value="settings"/>
                            <input type="hidden" name="id" value="deletebln"/>
                            <input type="submit" name="delete_bln_submit" value="Usuń"/>
                        </form>
                    </td>
                  </tr>';
        }

  echo' </table>
        ';
    }
    
    function remove_number_from_blacklist() {
        if((isset($_POST['delete_blacklist_number_id'])) && (!empty($_POST['delete_blacklist_number_id'])))
        {
            //mysql
            $deletebln=$_POST['delete_blacklist_number_id'];
            
            //delete user
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query="DELETE FROM `sms`.`black_list_numbers` WHERE `id_black_list_number`='".$deletebln."';";

            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
    }
    
    function update_blacklist_number(){
            if(
                isset($_POST['update_blacklist_number_id']) &&
                !empty($_POST['update_blacklist_number_id']) &&
                isset($_POST['black_list_numbers_comment']) &&
                !empty($_POST['black_list_numbers_comment']) &&
                isset($_POST['black_list_numbers_number']) &&
                !empty($_POST['black_list_numbers_number']
        )
        )
        {
        //mysql
            $update_blacklist_number_id=$_POST['update_blacklist_number_id'];
            $black_list_numbers_number=$_POST['black_list_numbers_number'];
            $black_list_numbers_comment=$_POST['black_list_numbers_comment'];
            
            //update msg
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }
            
            $query="
                UPDATE `sms`.`black_list_numbers` 
                SET 
                `black_list_numbers_number`='".$black_list_numbers_number."', 
                `black_list_numbers_comment`='".$black_list_numbers_comment."' 
                WHERE `id_black_list_number`='".$update_blacklist_number_id."';";
            
            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
    }

    function add_blacklist_number_print() {
        echo '
            <form action="index.php" method="POST">
            <table class="content">
                <tr>
                    <td>Numer</td>
                    <td>Komentarz</td>
                    <td>Akcja</td>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="add_blacklist_number"/>
                    </td>
                    <td>
                        <input type="text" name="add_blacklist_comment"/>
                    </td>
                    <td>
                        <input type="hidden" name="kat" value="settings"/>
                        <input type="hidden" name="id" value="addblacklistnumber"/>
                        <input type="submit" name="add_new_blacklsit_number"/>
                    </td>
                </tr>
             </table>
             </form>';
        
    }
    
    function add_number_to_blacklist() {
        if(
                isset($_POST['add_blacklist_number']) && 
                !empty($_POST['add_blacklist_number']) && 
                isset($_POST['add_blacklist_comment']) && 
                !empty($_POST['add_blacklist_comment']))            
        {
            //add msg
            $baza = new baza;

            if (PEAR::isError($baza))
            {
                echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                die();
            }

            $query="
                INSERT INTO `sms`.`black_list_numbers` 
                (
                    `black_list_numbers_number`,
                    `black_list_numbers_comment`
                ) 
                VALUES 
                (
                    '".$_POST['add_blacklist_number']."',
                    '".$_POST['add_blacklist_comment']."'    
                );";
            
            $wyniki = $baza->mdb2->query($query);
            if(!isset($wyniki->result))
            {
                echo "Blad bazy danych";
                die();
            }
        }
    }
    ?>