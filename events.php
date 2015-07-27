<?php
/*
    Author: Marion Carambula
    Sección de eventos
*/
include ("./common/common-include.php");
//print_r($_SESSION);

//Verificar que el usuario tiene  permisos
$sectionId = "3";
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php?e={$globalEventId}&c={$globalClientId}"); exit();}

$out    = "";
$events = $backend->getEventList($_SESSION["data"]["cliente"], $input["event"]["list"]["no-show"]);

if (isset($_SESSION["map_path"])) unset($_SESSION["map_path"]);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("eventos"); ?>
    <div class="content">
        <div class="title"><?= $label["Eventos"]?>
        <?php if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["create"] == "1"){?>
         <a href="./manage_event.php" class="add"><?= $label["Añadir nuevo"]?></a>
        <?php } ?>
        </div>
        <?= $globalMessage ?>
        <?php if ($events){ ?>
        <?=       @$ui->buildTable($events) ?>
        <?php }else{ ?>
                -- <?= $label["No hay eventos disponibles"];?> --
        <?php }?>
    </div>
  </body>
</html>                                                                                                                                                                        