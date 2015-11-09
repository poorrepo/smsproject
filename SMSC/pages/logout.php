<?php
session_start();

session_destroy();
?>

<html>
<head>
<title>BackupMT - LOGOUT</title>

</head>
    <body>
    
    <script language="javascript"> 
        <!-- 
        setTimeout('document.location="./../index.php"', 2000);
        //--> 
    </script>

    <?php
        echo "
            Zostales wylogowany.<br>
            Za moment zostaniesz przekierowany na <a href='./../index.php'>strone glowna.</a>
            ";
    ?>

    </body>
</html>