<?php
/*
    Author: Marion Carambula
    Sección de speakers
*/
include ("./common/common-include.php");
$sectionId = "7";
//Verificar que el usuario tiene  permisos
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}


$speakers = $backend->getSpeakerList($input["speaker"]["list"]["no-show"]);


?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("presentadores"); ?>
    <div class="content">
        <div class="title"><?= $label["Presentadores"]?> 
         <?php if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["create"] == "1"){?>
              <a href="./manage_speaker.php" class="add"><?= $label["Añadir nuevo"]?></a>
        <?php } ?>
        </div>
        <?= $globalMessage ?>
        <?php if ($speakers){ ?>
        <?=       @$ui->buildTable($speakers, 1,1) ?>
        <?php }else{ ?>
                -- <?= $label["No hay presentadores disponibles"];?> --
        <?php }?>
    </div>
  </body>
</html>                                                                                                                                                                        