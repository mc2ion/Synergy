<?php
/*
    Author: Marion Carambula
    Sección de speakers
*/
include ("./common/common-include.php");

$userId = $_SESSION["app-user"]["user"][1]["user_id"];

$title  = "Cambiar contraseña";
$message = "";

if(isset($_POST["edit"])){
    $oldPass = $_POST["oldPass"];
    $newPass = $_POST["newPass"];
    $repPass = $_POST["repPass"];
    
    if ($oldPass == "") { $message = "<div class='error'>Por favor introduzca su contraseña anterior</div>";}
    else if ($newPass == "") { $message = "<div class='error'>Por favor introduzca su nueva contraseña</div>";}
    else if ($repPass == "") { $message = "<div class='error'>Por favor repita su nueva contraseña</div>";}
    else if ($repPass != $newPass) { $message = "<div class='error'>Sus contraseña deben coincidir</div>";}
    else {
        $correct = $backend->verifyPassword($userId, md5($oldPass));
        if ($correct) {
            $en["password"] = md5($newPass);
            $backend->updateRow("user", $en, "user_id = '$userId'");
            $message = "<div class='succ'>Su contraseña fue modificada exitosamente</div>";  
            unset($_POST); 
        }else{
            $message = "<div class='error'>La contraseña ingresada no corresponde a su actual contraseña.</div>";
        }  
    }
}


?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu(); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
        <?=$message ?>
        <form  method="post" enctype="multipart/form-data">
            <table class="manage-content">
            <tr>
                <td>Contraseña anterior:</td>
                <td><input type="password" name="oldPass" value="<?= @$_POST["oldPass"]?>"/></td>
            </tr>
            <tr>
                <td>Contraseña nueva:</td>
                <td><input type="password" name="newPass" value="<?= @$_POST["newPass"]?>"/></td>
            </tr>
            <tr>
                <td>Repetir Contraseña nueva:</td>
                <td><input type="password" name="repPass" value="<?= @$_POST["repPass"]?>"/></td>
            </tr>
            <tr>
                <td></td>
                <td class="action">
                    <input type="submit" name="edit" value="<?= $label["Guardar"]?>" />
                </td>
            </tr>
            </table>
        </form>
    </div>
  </body>
</html>