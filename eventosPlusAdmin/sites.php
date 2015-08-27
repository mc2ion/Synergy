<?php
/*
    Author: Marion Carambula
    Sección de sitios de interes
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos

$sectionId = "11";
if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "cliente" && @$_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}

$event      = $_SESSION["data"]["evento"];
$sites      = $backend->getSitesList($event, $input["site"]["list"]["no-show"]);


unset($_SESSION["site"]["image_path"]);

?>

<!DOCTYPE html>
<html lang="es">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("sitios de interes"); ?>
    <div class="content">
        <div class="title"><?= $label["Sitios de interes"]?> 
         <?php if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"][$sectionId]["create"] == "1"){?>
        <a href="./manage_site.php" class="add"><?= $label["Añadir nueva"]?></a>
        <?php } ?>
        </div>
        <?= $globalMessage ?>
        <?php if ($sites) { ?>
        <?= @$ui->buildTable($sites)?>
        <?php }else{ ?>
            -- <?= $label["No hay sitios de interes disponibles"];?> --
        <?php }?>
    </div>
     <?= my_footer() ?>
  </body>
</html>