<?php
/*
    Author: Marion Carambula
    Sección de eventos
*/
include ("./common/common-include.php");
//print_r($_SESSION);

//Verificar que el usuario tiene  permisos
$sectionId = "3";
if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "cliente" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}

$events     = $backend->getEventList($_SESSION["data"]["cliente"], $input["event"]["list"]["no-show"]);

if (isset($_SESSION["map_path"])) unset($_SESSION["map_path"]);
?>

<!DOCTYPE html>
<html lang="es">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("eventos"); ?>
    <div class="content">
        <div class="title"><?= $label["Eventos"]?>
        <?php if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["create"] == "1"){?>
         <a href="./manage_event.php" class="add"><?= $label["Añadir nuevo"]?></a>
        <?php } ?>
        </div>
        <?= $globalMessage ?>
        <?php if ($events){ ?>
        <?=       @$ui->buildTable($events) ?>
        <?php }else{
                if (isset($_GET["fireUI"]["filter"])) {?>
                    <?= $label["No hay resultados que coincidan con su búsqueda"]?>. <a href="<?= @$_SERVER['HTTP_REFERER'] ?>" class="back"><?= $label["Volver"]?></a>
                <?php }else{ ?>
                -- <?= $label["No hay eventos disponibles"];?> --
                 <?php } ?>
        <?php }?>
    </div>
     <?= my_footer() ?>
  </body>
</html>                                                                                                                                                                        