<?php
/*
    Author: Marion Carambula
    Archivo inicial del administrador web
*/
include ("./common/common-include.php");
?>

<!DOCTYPE html>
<html lang="es">
  <head>
        <?= my_header()?>
  </head>
  <body>
    <?= menu(); ?>
    <div class="content">
        <div class="title"><?= $label["Bienvenido"]?></div>
        <div class="tutorial">
            <img src="./images/test.png?v=01" alt="Tutorial"/>
        </div>
    </div>
     <?= my_footer() ?>
  </body>
</html>