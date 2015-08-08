<?php
/*
    Author: Marion Carambula
    Sección de usuarios
*/
include ("./common/common-include.php");

//Verificar que el usuario tiene  permisos
$sectionId = "6";
if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "cliente" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}


$exhibitors = $backend->getExhibitorList($input["exhibitor"]["list"]["no-show"]);

?>

<!DOCTYPE html>
<html lang="es">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("expositores"); ?>
    <div class="content">
        <div class="title"><?= $label["Expositores"]?> 
         <?php if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["create"] == "1"){?>
             <a href="./manage_exhibitor.php" class="add"><?= $label["Añadir nuevo"]?></a>
        <?php } ?> 
        </div>
        <?= $globalMessage ?>
        <?php if ($exhibitors){ ?>
        <?=       @$ui->buildTable($exhibitors) ?>
        <?php }else{
                if (isset($_GET["fireUI"]["filter"])) {?>
                    <?= $label["No hay resultados que coincidan con su búsqueda"]?>. <a href="<?= @$_SERVER['HTTP_REFERER'] ?>" class="back"><?= $label["Volver"]?></a>
                <?php }else{ ?>
                -- <?= $label["No hay expositores disponibles"];?> --
                 <?php } ?>
        <?php }?>
    </div>
     <?= my_footer() ?>
  </body>
</html>                                                                                                                                                                        