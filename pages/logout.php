<?php
$_SESSION["cp_user"] = false;
session_regenerate_id(true);

unset($_SESSION["cp_user"]);

header("Location:".CUSTOMER_PORTAL_URL."/")

?>