<?php
include_once("config.php");

// #Inactividad, sesión expirada#
if (isset($_SESSION["usercode"])){
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > ($expireTime * 60))) {
		// última actividad hace 30 minutos, elimina la sesion (30 * 60)
		session_unset();
		session_destroy();
		// echo "location: ".$location_logout_expire;
		header("location: ../index.php");
		exit;
	}
	$_SESSION['LAST_ACTIVITY'] = time();
	$usertype = $_SESSION['usertype'];
}

?>
