<?php
/*
    Author: Marion Carambula
    Sección de usuarios
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = 2;
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php?e={$globalEventId}&c={$globalClientId}"); exit();}


$clients = $backend->getClientList($input["client"]["list"]["no-show"], 0);



?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("clientes"); ?>
    <div class="content">
        <div class="title"><?= $label["Clientes"]?> 
        
        <?php if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || @$_SESSION["app-user"]["permission"][$sectionId]["create"] == "1"){?>
        <a href="./manage_client.php" class="add"><?= $label["Añadir nuevo"]?></a>
        <?php } ?>
        </div>
        <?= $globalMessage ?>
        <?php if ($clients){?>
        <?= @$ui->buildTable($clients,1,1)?>
        <?php }else{?>
            -- <?= $label["No hay clientes activos"]?> --
        <?php }?>
    </div>
  </body>
</html>                                                                                                                                                                        