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
      <script src="./js/jssor.slider.min.js"></script>
      <link rel="stylesheet" href="./css/slider.css"/>
      <script>
          jQuery(document).ready(function ($) {
              var options = {
                  $AutoPlay: false,
                  $BulletNavigatorOptions: {
                      $Class: $JssorBulletNavigator$,
                      $ChanceToShow: 2,
                      $SpacingX: 10,
                      $SpacingY: 10,
                      $AutoCenter: 1
                  },
                  $ArrowNavigatorOptions: {
                      $Class: $JssorArrowNavigator$,
                      $ChanceToShow: 2,
                      $AutoCenter: 2                }
              };
              var jssor_slider1 = new $JssorSlider$('slider1_container', options);

          });
      </script>

  </head>
  <body>
    <?= menu(); ?>
    <div class="content">
        <div class="title"><?= $label["Bienvenido"]?></div>
        <div class="tutorial">
            <div id="slider1_container" style="position: relative; top: 0px; left: 0px; width: 800px; height: 398px; margin: 0 auto; padding-bottom:40px; background:#F9E347;">
                <!-- Slides Container -->
                <div u="slides" style="cursor: move; position: absolute; overflow: hidden; left: 0px; top: 0px; width: 800px; height: 398px;">
                    <div><img u="image" src="./images/Tutorial/clientes.png" /></div>
                    <div><img u="image" src="./images/Tutorial/usuarios.png" /></div>
                    <div><img u="image" src="./images/Tutorial/eventos.png" /></div>
                    <div><img u="image" src="./images/Tutorial/sesiones.png" /></div>
                    <div><img u="image" src="./images/Tutorial/salas.png" /></div>
                    <div><img u="image" src="./images/Tutorial/expositores.png" /></div>
                    <div><img u="image" src="./images/Tutorial/speakers.png" /></div>
                </div>
                <div u="navigator" class="jssorb01" style="bottom: 16px; right: 10px;">
                    <!-- bullet navigator item prototype -->
                    <div u="prototype"></div>
                </div>
            </div>
        </div>
        </div>
     <?= my_footer() ?>
  </body>
</html>