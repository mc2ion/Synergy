<?php
session_start();
/* Author: Marion Carambula*/
/*Archivo que contendrá el include de todos los archivos y funciones que son comunes para todos los archivos.*/

//Verificar mensajes de éxito o fracaso.
$clientId = $eventId  = $globalMessage = "";
//Cliente seleccionado
if (isset($_POST["c"])){ 
    $_SESSION["data"]["cliente"]   = $_POST["c"];   
    $clientId                      = $_SESSION["data"]["cliente"];
}
//Evento seleccionado
if (isset($_POST["e"])){
    $_SESSION["data"]["evento"]    = $_POST["e"];
    $eventId                       = $_SESSION["data"]["evento"] ;
}

# Incluir Archivo de etiquetas 
include ("labels.php");


# Incluir Archivo de configuracion
include ("conf.php");

# Incluir Archivo de backend (aqui estaran las principales funcionalidades de backend) 
include ('__dir__'."/../backend/class-backend.php"); $backend = new backend($label, $typeUser);

# Incluir Archivo de funciones frontend
include ("functions.php");


# Incluir Archivo de funcion UI 
include ("class-ui.php"); $ui = new ui(); $ui->setLabel($label);

//Logout
if (isset($_GET["logout"])){
    session_destroy();
    header("Location: ./login.php");
    exit();
}

$query          = $_SERVER['PHP_SELF'];
$path           = pathinfo( $query );
$currentFile    = $path['basename'];

//Verificar que el usuario haya iniciado sesion
if ($currentFile != "login.php") {
    $logged = $_SESSION["logged"];
    if (!$logged){
        header("Location: ./login.php");
        exit();
    }
}

//Mensajes de error o éxito
if (isset($_SESSION["message"])){
    $globalMessage = $_SESSION["message"];
    unset($_SESSION["message"]);
}

?>
