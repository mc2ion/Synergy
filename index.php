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
        <div><p>Escoje en el menú lateral la sección sobre la cual deseas trabajar.<p></div>
    </div>
     <?= my_footer() ?>
  </body>
</html>