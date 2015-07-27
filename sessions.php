<?php
/*
    Author: Marion Carambula
    Sección de eventos
*/
include ("./common/common-include.php");

//Verificar que el usuario tiene  permisos
$sectionId = "4";
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php?e={$globalEventId}&c={$globalClientId}"); exit();}


$out     = "";
$session = $backend->getSessionList($_SESSION["data"]["evento"], $input["session"]["list"]["no-show"]);
$rooms   = $backend->getRoomList($_SESSION["data"]["evento"]);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("sesiones"); ?>
    <div class="content">
        <div class="title"><?= $label["Sesiones"]?>
         <?php if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["create"] == "1"){?>
            <?php //Verificar que haya al menos una sala creada para poder añadir una nueva sesión 
                if (count($rooms) > 0 && $rooms != ""){
            ?>
             <a href="./manage_session.php" class="add"><?= $label["Añadir nueva"]?></a>
            <?php }else{ ?> 
                <div><?= $label["Para agregar una sesión, debe tener creado al menos una sala."]?><a href="./manage_room.php?c=<?=$globalClientId?>&e=<?=$globalEventId?>">Crear Sala</a></div>
            <?php } ?>
        <?php } ?>
        </div>
        
        <?= $globalMessage ?>
        <?php if ($session){ ?>
        <?=       @$ui->buildTable($session, 1, 1) ?>
        <?php }else{ ?>
                -- <?= $label["No hay sesiones disponibles"];?> --
        <?php }?>
    </div>
  </body>
</html>                                                                                                                                                                        