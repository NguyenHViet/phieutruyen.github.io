<?php
    if(isset($_SESSION['isadmin'])) {
        require_once("./component/sidebar_admin.php");
    }
    else {
        require_once("./component/sidebar_user.php");
    }
?>