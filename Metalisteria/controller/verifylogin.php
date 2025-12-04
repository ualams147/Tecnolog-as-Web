<?php
require 'config.php';
include("funciones.php");

function verifylogin($mysqli,$login,$pwd){
    if (emailValidation($login)) {
        $query = 
        "SELECT id, rol
        FROM clientes 
        WHERE email = ? AND password = ?";
        $result = $mysqli->prepare($query, array($login, sha1($pwd)));
        if (mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            $_SESSION["usercode"] = $row["id"];
            $_SESSION["usertype"] = $row["rol"];
            return TRUE;
        }
    }
    return FALSE;
}


$login = trim($_POST["email"]);
$pwd = trim($_POST["password"]);
$error = "";

if (verifylogin($mysqli,$login,$pwd)){
        header("location: ../index.php");
} else {
    header("location: ../login.php?error=1");
}


?>