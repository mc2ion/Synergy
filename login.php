<?php
/*
    Author: Marion Carambula
    Archivo login del administrador web
*/
include ("./common/common-include.php");
$email = $password = $message = "" ;

if (isset($_POST["login"])){
    if (isset($_POST["at-email"]))     $email      = $_POST["at-email"];
    if (isset($_POST["at-password"]))  $password   = $_POST["at-password"];
    $user = $backend->login($_POST);
    if (empty($user["user"])){
        $message = "<div class='error'>{$label["Datos incorrectos"]}</div>";
    }else{
        $_SESSION["logged"] = 1;
        unset($user["user"]["1"]["password"]);
        unset($user["user"]["1"]["email"]);
        $_SESSION["app-user"] = $user;
        header("Location: ./index.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?= my_header()?>
  </head>
  <body class="login">
    <form method="post">
        <div class="content-login">
             <div class='info'>
                  <h1><?= $label["Bienvenido al administrador web"]?> </h1>
            </div>
            <?= $message ?>
             <div class='form'>
              <div class='login'>
                <h2><?= $label["Ingrese a su cuenta"]?></h2>
                <form>
                  <input name="at-email" placeholder='<?= $label["Correo"]?>' type='text' value="<?= $email?>"/>
                  <input name="at-password" placeholder='<?= $label["Contraseña"]?>' type='password' value="<?= $password?>"/>
                  <button name="login">Ingresar</button>
                </form>
              </div>
              <footer>
                <a href=''><?= $label["Olvido su contraseña"]?></a>
              </footer>
            </div>
        </div>
    </form>
  </body>
</html>