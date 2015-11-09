<?php
    session_start(); 
    
?>


<?php

ob_start();


///////////////////////////////////////////////////////////////////////
if(empty($_SESSION["sesja"]))
{
	$_SESSION["sesja"]=0;
}

    require_once './configs/common.inc';
    require_once './pages/main.php';
    require_once './pages/login.php';
    require_once './pages/default.php';
    require_once './pages/tasks.php';
    require_once './pages/mytasks.php';
    require_once './pages/addressbook.php';    
    require_once './pages/users.php';
    require_once './pages/settings.php';
    require_once './pages/server_api.php';
    
    /*
    echo check_if_blacklisted("73000");
    echo '<br><br>';
    echo check_if_blacklisted("79000");
    echo '<br><br>';
    echo check_if_blacklisted("79531");
    */
    function include_footer()
    {
        echo '
            <br>
             <table class="content">
                <tr></tr>
                <tr>
                    <td>
                        <p class="footer">
                        Copyright © '; echo date("Y"); echo ' Tomasz Żmijowski
                        </p>                        
                    </td>
                </tr>
              </table>';
    }
    
    function include_header() {
        echo '
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>SMSC: SMS Client</title> 
        ';
        
        //podstawowe
        echo '
            <link rel="Stylesheet" type="text/css" href="styles/index.css" />            
            <link rel="Stylesheet" type="text/css" href="styles/content.css" />
            <link rel="Stylesheet" type="text/css" href="styles/style_menu.css" />  
            <link rel="Stylesheet" type="text/css" href="styles/login.css" />  
            <link rel="Stylesheet" type="text/css" href="styles/style_main_head.css" />';
        
        //jquery
        
        echo '
            <script type="text/javascript" src="javascript/jquery/jquery-1.9.1.js"></script>
            <script type="text/javascript" src="javascript/jquery/jquery-ui-1.10.3.custom.js"></script>
            <link type="text/css" rel="stylesheet" href="styles/ui-lightness/jquery-ui-1.10.3.custom.css" />
        ';
        
        //multiselect
        echo '
            <link rel="stylesheet" href="styles/multiselect/common.css" type="text/css" /> 
            <link type="text/css" href="styles/multiselect/ui.multiselect.css" rel="stylesheet" />            
            <script type="text/javascript" src="javascript/multiselect/plugins/localisation/jquery.localisation-min.js"></script>
            <script type="text/javascript" src="javascript/multiselect/plugins/scrollTo/jquery.scrollTo-min.js"></script>
            <script type="text/javascript" src="javascript/multiselect/ui.multiselect.js"></script>';
        //timepicker
        
        echo'
           <link rel="stylesheet" href="styles/jquery.datetimepicker.css" type="text/css" /> 
           <script type="text/javascript" src="javascript/jquery/jquery.datetimepicker.js"></script>';
        }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include_header(); ?>
        
        <script type="text/javascript">
        $(function(){
                $.localise('ui-multiselect', {/*language: 'en',*/ path: 'javascript/multiselect/locale/'});
                $(".multiselect").multiselect();
        });
	</script> 
    </head>
    <body>
       
        <?php
          
            //jezeli kat
            if(isset($_GET['kat'])){$kat = $_GET['kat'];}
            elseif(isset($_POST['kat'])){$kat = $_POST['kat'];}
            else{$kat = "";}
            
            if(isset($_GET['id'])){$id = $_GET['id'];}
            elseif(isset($_POST['id'])){$id = $_POST['id'];}
            else{$id = "";}

            
            //sprawdzanie czy user zalogowany
            if($_SESSION['sesja'] == 1)
            {
                //wylogowywanie
                if($kat == "logout")
                {
                    logout_site();
                    exit();
                }
                //echo wyswietlanie strony wewnetarzenej  
                echo '<table>
                        <tr>
                            <td>';
                                print_main_head();
                                print_main_menu();
                        echo '</td>
                        </tr>
                        <tr>
                            <td>
                                ';
                
                switch ($kat)
                {
                    case "main":
                    {
                        echo '<h2>Strona domowa</h2>';
                        print_main_body();
                        break;
                    }
                    case "task":
                    {
                        switch( $id )
                        {
                            case "multi-list":
                            {
                                echo '<h2>Dodaj zadanie</h2>';
                                print_tasks_list();
                                break;
                            } 
                             case "multi-addressbook":
                            {
                                echo '<h2>Dodaj zadanie</h2>';
                                print_tasks_addressbook();
                                break;
                            } 
                             case "verifytasks":
                             {
                                echo '<h2>Dodaj zadanie - weryfikacja</h2>'; 
                                verify_tasks();
                                break;
                            } 
                            case "addtasks":
                            {
                                add_tasks();
                                header("Location: ./index.php?kat=mytasks");
                                break;
                            }
                        }
                        break;
                    }
                    case "addressbook":
                    {
                        switch( $id )
                        {
                            case "view":
                            {
                                echo '<h2>Kontakty - książka adresowa</h2>';                                
                                print_myaddressbook_page();
                                break;
                            }
                            case "edit":
                            {
                                echo '<h2>Kontakty - edycja</h2>';                                
                                print_editcontact_page();
                                break;
                            }                            
                            case "editsave":
                            {
                                update_contact();    
                                header("Location: ./index.php?kat=addressbook&id=view");
                                break;
                            }
                            case "addcontact":
                            {
                                echo '<h2>Kontakty - dodaj nowy kontakt</h2>';                                
                                print_addcontact_page();
                                break;
                            }      
                            case "addcontactsave":
                            {
                                add_contact();
                                header("Location: ./index.php?kat=addressbook&id=view");
                                break;
                            }                            
                            case "delcontact":
                            {                              
                                delete_contact();
                                header("Location: ./index.php?kat=addressbook&id=view");
                                break;
                            }
                            
                        }
                        break;
                    }
                    case "mytasks":
                    {
                        echo '<h2>Historia zadań</h2>';
                        print_mytasks_page();
                        break;
                    }  
                    
                    case "users":
                    {
                        if($_SESSION['access_level'] == 100)
                        { 
                            switch( $id )
                            {
                                case "view":
                                {
                                    echo '<h2>Podgląd użytkowników</h2>';
                                    print_users_view();
                                    break;
                                }
                                case "edituser":
                                {
                                    echo '<h2>Edycja użytkowników</h2>';
                                    print_editusers_view();
                                    break;
                                }
                                case "editsave":
                                {
                                    update_user();
                                    header("Location: ./index.php?kat=users&id=view");
                                    break;
                                }
                                case "newpass":
                                {
                                    update_userpass();
                                    header("Location: ./index.php?kat=users&id=view");
                                    break;
                                }    
                                case "add":
                                {
                                    echo '<h2>Nowy użytkownik</h2>';
                                    print_users_add();
                                    break;
                                }
                                case "addusersave":
                                {
                                    add_user();
                                    header("Location: ./index.php?kat=users&id=view");
                                    break;
                                }
                                case "deluser":
                                {
                                    del_user();
                                    header("Location: ./index.php?kat=users&id=view");
                                    break;
                                }
                            }
                        }
                        else {
                            echo '<h2>Strona domowa</h2>';
                            print_main_body();
                            
                            }
                        break;
                    }
                    case "settings":
                    {
                        if($_SESSION['access_level'] == 100)
                        { 
                            switch( $id )
                            {
                                case "view":
                                {
                                    print_settings_view();
                                    break;
                                }
                                case "updatenews":
                                {
                                    update_news();
                                    header("Location: ./index.php?kat=settings&id=view");
                                    break;
                                }
                                case "addnews":
                                {
                                    add_news();
                                    header("Location: ./index.php?kat=settings&id=view");
                                    break;
                                }                                
                                case "deletenews":
                                {
                                    delete_news();
                                    header("Location: ./index.php?kat=settings&id=view");
                                    break;
                                } 
                                case "updatebln":
                                {
                                    update_blacklist_number();
                                    header("Location: ./index.php?kat=settings&id=view");
                                    break;
                                } 
                                case "deletebln":
                                {
                                    remove_number_from_blacklist();
                                    header("Location: ./index.php?kat=settings&id=view");
                                    break;
                                } 
                                case "addblacklistnumber":
                                {
                                    add_number_to_blacklist();
                                    header("Location: ./index.php?kat=settings&id=view");
                                    break;
                                } 
                                
                            }
                        }
                        else
                        {
                            echo '<h2>Strona domowa</h2>';
                            print_main_body();
                        }
                        break;
                    }
                    default:
                    {
                        echo '<h2>Strona domowa</h2>';
                        print_main_body();
                        break;
                    }
                        
                }
                
                //opcje dla administratora
                
                       echo '</td>
                        </tr></table>';
            }
            else
            {
                switch ($kat)
                {
                    case "login":
                    {
                        if(login())
                        {
                            //print_login_success();
                            header("Location: ./index.php");
                        }
                        else
                        {
                            print_login_fail();
                            print_login_form();
                        }
                        break;
                    }
                    default :
                    {
                       //wyswietlanie strony logowania
                        print_login_form(); 
                    }
                }
            }
      ?>
        
    </body>
</html>

<?php
    if($_SESSION['sesja'] == 1)
    {
        include_footer();
    }
    ob_end_flush();
?>