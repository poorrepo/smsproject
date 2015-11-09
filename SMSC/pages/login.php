<?php
    function print_login_form() {
           
        echo '
            <form action="index.php" method="post">
                <table class="login_table">
                    <tr>
                        <td>';
                            print_error_message('Logowanie SMSC: SMS Client');
                  echo '</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>    
                        <td>Login</td>
                        <td><input type="text" name="login"/></td>
                        
                    </tr>
                    <tr>
                        <td>Hasło</td>
                        <td><input type="password" name="haslo"/></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="kat" value="login"/>
                            <input type="submit" value="Zaloguj!"/>
                        </td>
                    </tr>
                </table>
            </form>';
    }
    
    function print_login_success() {
        print_error_message('Zalogowano poprawnie!');
    }
    
    function print_login_fail() {
        print_error_message('Logowanie nie powiodło sie!');
    }
?>