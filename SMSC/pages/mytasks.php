<?php

    function find_user_by_id($id_user)
    {
        //check user
        $baza = new baza;
        
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
        
        if (PEAR::isError($baza))
        {
            echo 'Nie mozna sie polaczyc z baza danych: ' . $baza->getMessage();
            die();
        }

        
        $baza->mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
        $wyniki = $baza->mdb2->queryAll($query);
        
        
        foreach($wyniki as $user)
        {
            return $user;
        }
    }
    function print_mytasks_page()
    {
        //jak wcisniety byl przycisk to wywolaj kasowanie zadan

        if(isset($_POST['del_task_submit'])){ del_finished_tasks(); }
        if(isset($_POST['cancel_task_submit'])){ cancel_task(); }

        if($_SESSION['access_level'] >= 100)
        {
            $query="SELECT 
            id_user,
            user_name, 
            user_imie, 
            user_nazwisko, 
            user_dzial
            FROM sms.users;";
            
            $users = get_from_db($query);
            

            echo '<table class="content">
                    <tr></tr>
                    <tr>
                        <td>
                    <form action="index.php" method="POST">
                        <select name="find_user_id">
                        <option value="'.$_SESSION["user_name"].'">Moje -> '.$_SESSION["user_name"].'</option>
                        <option value="*">Wszystkie</option>';
            
            foreach ($users as $single_user_row) 
            {
                echo '<option value="'.$single_user_row["id_user"].'">'.
                        $single_user_row["user_name"].' @ '.
                        $single_user_row["user_imie"].' '.
                        $single_user_row["user_nazwisko"].' '.
                        $single_user_row["user_dzial"].'</option>';
            }
            echo '
                        </select>
                        <input type="hidden" name="kat" value="mytasks"/>
                        <input style="width: 100px;"type="submit" value="Wyszukaj" name="find_user"/>';
                        
                        //akt user
                        //all
                        //z bazy
                        
                        if((isset($_POST['find_user_id']))&&(!empty($_POST['find_user_id'])))
                        { 
                            if($_POST['find_user_id'] === $_SESSION['id_user'])
                            {
                                $dbres["user_name"] = $_SESSION['user_name'];
                            }
                            else
                            {
                                if($_POST['find_user_id'] === '*')
                                {
                                    $dbres["user_name"] = 'wszystkie';
                                }
                                else
                                {
                                    $selected_user_id = $_POST['find_user_id'];
                                    $dbres = find_user_by_id($selected_user_id);
                                }
                            }
                        }
                        else
                        {
                            $dbres["user_name"] = $_SESSION['user_name'];

                        }
                       
                        //if(isset($_SESSION['id_user'])&&!empty($_SESSION['id_user'])){ $selected_user = $_SESSION['id_user'];}

                        echo '<p class="msg">Wybrany użytkownik: '.$dbres["user_name"].'</p>';
            echo '</form>
                    </td>
                    </tr>
                </table>';
        }
        
        print_current_tasks();
        print_mytasks();
    }
    
    //aktualizacja || del taska
    function del_finished_tasks(){
        if(isset($_POST['del_finished_tasks']) && !empty($_POST['del_finished_tasks']))
        {
            $del_this=$_POST['del_finished_tasks'];
            foreach ($del_this as $task_id)
            {
                if($_SESSION['access_level'] >= 100)
                {
                    $query="DELETE FROM `sms`.`archive_tasks` WHERE `id_archive_task`='".$task_id."';";
                }
                else 
                {
                    $query=
                    "
                        UPDATE `sms`.`archive_tasks` 
                        SET 
                        `rec_bin`='1'
                        WHERE `id_archive_task`='".$task_id."';";
                }
                
                 //delete task
                $baza = new baza;

                if (PEAR::isError($baza))
                {
                    echo 'Nie mozna sie polaczyc z baza danych: ' . $baza->getMessage();
                    die();
                }

                $wyniki = $baza->mdb2->query($query);
                if(!isset($wyniki->result))
                {
                    echo "Blad bazy danych";
                    die();
                }
            }
        }
    }
    
    //anulowanie
    function cancel_task(){
        if(isset($_POST['cancel_tasks']) && !empty($_POST['cancel_tasks']))
            {
                $cancel_this=$_POST['cancel_tasks'];
                        
                foreach ($cancel_this as $task_id)
                {
                    //delete task
                    $baza = new baza;

                    if (PEAR::isError($baza))
                    {
                        echo 'Nie mozna sie polaczyc z baza danych: ' . $db->getMessage();
                        die();
                    }

                    $query="DELETE FROM `sms`.`tasks` WHERE `id_task`='".$task_id."';";

                    $wyniki = $baza->mdb2->query($query);
                    if(!isset($wyniki->result))
                    {
                        echo "Blad bazy danych";
                        die();
                    }
                }
            }    
        }
    
    //wykonane zadania
    function print_mytasks(){
        if(isset($_POST['find_user_id']) && !empty($_POST['find_user_id']))
        {
            switch($_POST['find_user_id'])
            {
                case '*':
                {
                    $query = "SELECT id_archive_task, phone_number, message, send_time, executed_by, rec_bin FROM sms.archive_tasks;";
                    break;
                }   
                default :
                {
                    $query = "SELECT id_archive_task, phone_number, message, send_time, executed_by, rec_bin FROM sms.archive_tasks WHERE sender='".$_POST['find_user_id']."';";
                    break;
                }
            }
        }
        else
        {
            $query = "SELECT id_archive_task, phone_number, message, send_time, executed_by, rec_bin FROM sms.archive_tasks where sender='".$_SESSION['id_user']."';";
        }
        
        $res = get_from_db($query);  
        
        echo '
                <h2>Zadania wykonane</h2>
                <form action="index.php" method="POST">
                <table class="content">
                    <tr>
                        <td>Numer</td>
                        <td>Wiadomość</td>
                        <td>Termin wysłania</td>
                        <td>Obsłużono przez</td>
                        <td>Usun</td>
                    </tr>';
        
          
        foreach ($res as $single_arch) {
            if($single_arch['rec_bin'] == 1)
            {    
                if($_SESSION['access_level'] >= 100)
                {   
                echo '<tr>';
                    echo '
                        <td>'.$single_arch["phone_number"].'</td>
                        <td>'.$single_arch["message"].'</td>
                        <td>'.$single_arch["send_time"].'</td>
                        <td>'.$single_arch["executed_by"].'</td>
                        <td style="background-color: orange;"><input type="checkbox" name="del_finished_tasks[]" value="'.$single_arch["id_archive_task"].'"/></td>
                    </tr>';
                }
            }
            else
            {
              echo '<tr>';
                echo '
                    <td>'.$single_arch["phone_number"].'</td>
                    <td>'.$single_arch["message"].'</td>
                    <td>'.$single_arch["send_time"].'</td>
                    <td>'.$single_arch["executed_by"].'</td>
                    <td><input type="checkbox" name="del_finished_tasks[]" value="'.$single_arch["id_archive_task"].'"/></td>
                </tr>';    
            }
        }
            echo '
                    <tr>
                        <td colspan="4"></td>
                        <td>
                            <input type="hidden" name="kat" value="mytasks"/>
                            <input style="background-color: red;" type="submit" name="del_task_submit" value="Usun"/>
                        </td>
                    <tr>
                    </table>
                </form>';
        echo '</div>';
       
    }

    //zadania w trakcie realizacji
    function print_current_tasks() {
                if(isset($_POST['find_user_id']) && !empty($_POST['find_user_id']))
        {
            switch($_POST['find_user_id'])
            {
                case '*':
                {
                    $query = "SELECT id_task, phone_number, message, send_time, selected_by FROM sms.tasks;";
                    break;
                }   
                default :
                {
                    $query = "SELECT id_task, phone_number, message, send_time, selected_by FROM sms.tasks where sender='".$_POST['find_user_id']."';";
                    break;
                }
            }
        }
        else
        {
            $query = "SELECT id_task, phone_number, message, send_time, selected_by FROM sms.tasks where sender='".$_SESSION['id_user']."';";
        }
        
        $res = get_from_db($query);

        echo '
                <h2>Zadania w trakcie realizacji</h2>
                <form action="index.php" method="POST">
                <table class="content">
                    <tr>
                        <td>Numer</td>
                        <td>Wiadomość</td>
                        <td>Termin wysłania</td>
                        <td>Obsługiwane przez</td>
                        <td>Anuluj</td>
                    </tr>';
                    
        foreach ($res as $single_task) {
            echo '
                    <tr>
                        <td>'.$single_task["phone_number"].'</td>
                        <td>'.$single_task["message"].'</td>
                        <td>'.$single_task["send_time"].'</td>';
                        if(!empty($single_task["selected_by"]))
                        {
                            echo '
                            <td style="background-color: green;">'.$single_task["selected_by"].'</td>
                            <td>
                                <input type="checkbox" disabled="disabled" name="cancel_tasks[]" value="'.$single_task["id_task"].'"/>
                            </td>';
                        }
                        else
                        {
                            echo '
                                
                            <td style="background-color: grey;">'.$single_task["selected_by"].'</td>
                            <td>
                                <input type="checkbox" name="cancel_tasks[]" value="'.$single_task["id_task"].'"/>
                            </td>';
                        }
               echo '</tr>';
        }
            echo '
                    </form>
                    <tr>
                        <td colspan="4"></td>
                        <td>
                            <input type="hidden" name="kat" value="mytasks"/>
                            <input style="background-color: red;" type="submit" name="cancel_task_submit" value="Anuluj"/>
                        </td>
                        </tr>
                    </table>';
    }
?>