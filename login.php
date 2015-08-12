<?php
/*
    Author: Marion Carambula
    Archivo login del administrador web
*/
include ("./common/common-include.php");
include ("./backend/send_email.php");

//Verificar si la sesion está activa
if (isset($_SESSION["logged"]) && $_SESSION["logged"] == "1") {header("Location: ./index.php");}

$email = $password = $message = $pass = "" ;

if (isset($_POST["login"])){
    $pass = "";
    if (isset($_POST["at-email"]))     $email      = $backend->clean($_POST["at-email"]);
    if (isset($_POST["at-password"]))  $password   = $backend->clean($_POST["at-password"]);
    if ($email == "" && $password == "")        { $message = "<div class='error-login'>{$label["Ingrese sus credenciales"]}</div>";}
    else if ($email == "" && $password != "")   { $message = "<div class='error-login'>{$label["Ingrese su correo electrónico"]}</div>";}
    else if ($password == "")                   { $message = "<div class='error-login'>{$label["Ingrese su contraseña"]}</div>";}
    else{
       // $regex = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            $message = "<div class='error-login'>{$label["La dirección de correo electrónico no es válida"] }</div>";
        }else{
            $user = $backend->login($_POST);
            if (empty($user["user"])){
                if (empty($user["email"])) $message = "<div class='error-login'>{$label["La dirección de correo electrónico no es válida"] }</div>";
                else $message = "<div class='error-login'>{$label["Datos incorrectos"]}</div>";
            }else{
                $_SESSION["logged"] = 1;
                unset($user["user"]["1"]["password"]);
                unset($user["user"]["1"]["email"]);
                $_SESSION["app-user"]       = $user;
                header("Location: ./index.php");
                exit();
            }
        }
    }
}
if (isset($_POST["forgot"])){
    if (empty($_POST["at-email"])){    $message = "<div class='error-login'>{$label["Introduzca su correo"]}</div>";  $pass    = 1; }
    else{
        $email      = $backend->clean($_POST["at-email"]);
        //Obtener usuario
        $user = $backend->getUserInfoByEmail($email);
        if ($user){
            $name               = $user["first_name"] . " " . $user["last_name"];
            $newPass            = randomPassword();
            //Actualizar usuario
            $en["password"]     = md5($newPass);
            $backend->updateRow("user", $en, "user_id = '{$user["user_id"]}'");
            $success = send_email("Restablecer Contraseña", $name ,$newPass, $email, "password");
            if ($success){
                $message = "<div class='succ-login' style='display: block !important;'>{$label["Su nueva contraseña se ha enviado a su correo"]}</div>";
            } 
        }else{
            $message = "<div class='error-login' style='display: block !important;'>{$label["El correo electrónico ingresado no existe"]}</div>";
            $pass    = 1;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <?= my_header()?>
    <?php if ($pass == 1){?>
        <script>
            pass = 1;
            $(document).ready(function(){
              $(".forgot").click(); 
            })
        </script>
    <?php } ?>
  </head>
  <body class="login">
    <div class="page-wrap">
    <form method="post" id="form">
        <div class="content-login">
             <div class='form'>
                 <div class='info'>
                     <img src="./images/logo.png" alt="logo" class="logo"/>
                     <div><h1>Administrador Web<h1></div>
                 </div>
                 <div class='login'>
                <form>
                  <input name="at-email" placeholder='<?= $label["Correo"]?>' type='text' value="<?= $email?>"/>
                  <input name="at-password" placeholder='<?= $label["Contraseña"]?>' type='password' value="<?= $password?>"/>
                  <?= $message ?>
                  <button name="login"><?=$label["Ingresar"]?></button>
                </form>
              </div>
              <footer>
                <a href='javascript:void(0)' class="forgot"><?= $label["Olvido su contraseña"]?></a>
              </footer>
            </div>
        </div>
    </form>
     <?= my_footer() ?>
    </div>

  </body>
</html>