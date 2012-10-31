<?php
if(!empty($_SESSION["cp_user"])) {
    header("Location:home.html");
    exit();
}

if(!empty($_POST["submit"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {

    $connection = getConnection();

    $username = filter_var($_POST["username"], FILTER_SANITIZE_EMAIL);
    $passwort = htmlentities(strip_tags($_POST["password"]));

    $result = $connection->login($username, $passwort);

    if($result != false) {
        $_SESSION["cp_user"] = $result;
        session_regenerate_id(true);
        header("Location:".CUSTOMER_PORTAL_URL."/".REDIRECT_AFTER_LOGIN);
        exit();
    }
}

echo $hView->render("pages/login.phtml");