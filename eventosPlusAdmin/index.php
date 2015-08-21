<?php
/*
    Author: Marion Carambula
    Archivo inicial del administrador web
*/
include ("./common/common-include.php");

$client = @$_SESSION["data"]["cliente"] ;
$event  = @$_SESSION["data"]["evento"] ;

//Verify is we can show another script
if ($client != "" && $event != "" ) {header("Location: ./sessions.php"); exit();}
else if ($client != "" && $event == "") {header("Location: ./events.php"); exit();}

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