<?php
/*
    Author: Marion Carambula
    Manejo de la contraseña
*/
include ("./common/common-include.php");

$userId = $_SESSION["app-user"]["user"][1]["user_id"];

$title  = $label["Cambiar contraseña"];
$message = "";

if(isset($_POST["edit"])){
    $oldPass = $_POST["oldPass"];
    $newPass = $_POST["newPass"];
    if ($oldPass == "")                   { $message = "<div class='error'>{$label["Ingrese su contraseña actual"]}</div>";}
    else if ($newPass == "")              { $message = "<div class='error'>{$label["Ingrese su nueva contraseña"]}</div>";}
    else if (strlen($newPass) < 4)        { $message = "<div class='error'>{$label["La nueva contraseña debe tener al menos 4 caracteres"]}</div>";}
    else {
        $correct = $backend->verifyPassword($userId, md5($oldPass));
        if ($correct) {
            $en["password"] = md5($newPass);
            $backend->updateRow("user", $en, "user_id = '$userId'");
            $message = "<div class='succ'>{$label["Su contraseña fue modificada exitosamente"]}</div>";
            unset($_POST); 
        }else{
            $message = "<div class='error'>{$label["Su contraseña actual no es válida"]}.</div>";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="es">
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
                <td><?= $label["Contraseña anterior"]?>:</td>
                <td><input type="password" name="oldPass" value="<?= @$_POST["oldPass"]?>"/></td>
            </tr>
            <tr>
                <td><?= $label["Contraseña nueva"]?>:</td>
                <td><input type="password" name="newPass" value="<?= @$_POST["newPass"]?>"/></td>
            </tr>
            <tr>
                <td></td>
                <td class="action">
                    <input type="submit" name="edit" value="<?= $label["Guardar"]?>" />
                    <a href="./manage_profile.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            </table>
        </form>
    </div>
     <?= my_footer() ?>
  </body>
</html>