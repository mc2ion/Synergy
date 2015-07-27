<?php
/*
    Author: Marion Carambula
    Sección de usuarios
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = "1";

if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php?e={$globalEventId}&c={$globalClientId}"); exit();}

$out = "";
$users = $backend->getUserList($input["user"]["list"]["no-show"]);


?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("usuarios"); ?>
    <div class="content">
        <div class="title"><?= $label["Usuarios"]?>
        <?php if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["create"] == "1"){?>
         <a href="./manage_user.php" class="add"><?= $label["Añadir nuevo"]?></a>
        <?php } ?>
        </div>
        <?= $globalMessage ?>
        <?php if ($users){ ?>
        <?=       @$ui->buildTable($users, 1,1) ?>
        <?php }else{ ?>
                -- <?= $label["No hay usuarios disponibles"];?> --
        <?php }?>
    </div>
  </body>
</html>                                                                                                                                                                        