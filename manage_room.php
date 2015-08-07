<?php
/*
    Author: Marion Carambula
    Sección de usuarios
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = "4";
if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "cliente" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}


//Obtener las columnas a editar/crear
$section        = "room";
$columns        = $backend->getColumnsTable($section);
$id             = $message = $error = $room = $sessions =  "";
//Sessiones asociadas a la sala
if (isset($_GET["id"])) $sessions       = $backend->getSessionListByRoom($_GET["id"], $_SESSION["data"]["evento"]); 
 

//Agregar nuevo cliente
if (isset($_POST["add"]) || isset($_POST["edit"])){
   $en["event_id"]     = $_SESSION["data"]["evento"];
   foreach ($columns as $k=>$v) {
        if (isset($input[$section]["manage"]["mandatory"])){
            //Verifico que los elementos mostrados en el formulario
            if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                // Verifico los obligatorios
                if ($input[$section]["manage"]["mandatory"] == "*" || in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])){
                    if ($_POST[$v["COLUMN_NAME"]] == "") {
                        $error =  1;
                        $missing[$v["COLUMN_NAME"]] = 1;
                    }else $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                }else{
                    $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                }
            }
       }
   }
   
   //Verificar que el nombre de la sala no exista
   if (isset($en["name"])){
        $exists = $backend->existsRoom($en["name"], @$_POST["id"]);
        if ($exists) {
            $error = 1;
            $message = "<div class='error'>".$label["Ya existe una sala con este nombre"]. "</div>";
        }
   }
  
   if (!$error){
       if (isset($_POST["add"])){
           $id = $backend->insertRow($section, $en);
           if ($id > 0) { 
                $_SESSION["message"] = "<div class='succ'>".$label["Sala creada exitosamente"]. "</div>";
                header("Location: ./rooms.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
           }
       }else{
           $id      = $backend->clean($_POST["id"]);
           $rid     = $backend->updateRow($section, $en, " room_id = '$id' ");
           if ($rid > 0) { 
                $_SESSION["message"] = "<div class='succ'>".$label["Sala editada exitosamente"] ."</div>";
                header("Location: ./rooms.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
           }
       }
   }else{
        if (isset($missing)) $message = "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $room    = $en;
   }
}
//Borrar Sala
if (isset($_POST["delete"])){
   $id              = $backend->clean($_POST["id"]);
   $en["active"]    = "0";
   @$backend->updateRow("room", $en, " room_id = '$id' ");
   if ($sessions) {
    //Eliminar sessiones asociadas
        foreach($sessions as $k=>$v){
            $enAux["active"] = "0";
            @$backend->updateRow("session", $enAux, " session_id = '{$v["session_id"]}' ");
        }
   }
   $_SESSION["message"] = "<div class='succ'>".$label["Sala borrada exitosamente"]. "</div>";
   header("Location: ./rooms.php");
   exit();
}



//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    $title          = $label["Editar Sala"];
    $action         = "edit";
    if (!$error)    {
        $room           = $backend->getRoom($_GET["id"]);
        if (!$room){
                $_SESSION["message"] = "<div class='error'>".$label["Sala no encontrada"]  ."</div>";
                header("Location: ./rooms.php");
                exit();
        }
    }
}else{
    $title = $label["Crear Sala"];
    $action = "add";
}


?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
     <script>
          $(function() {
            $( "#dialog-confirm" ).dialog({
                  autoOpen: false,
                  resizable: false,
                  height: 300,
                  modal: true,
                  buttons: {
                  "Si": function() {
                   $( this ).dialog( "close" );
                   $('<input />').attr('type', 'hidden')
                  .attr('name', "delete")
                  .attr('value', "1")
                  .appendTo('#form');
                   $("#form").submit();
                 },
                 "Cancelar": function() {
                  $( this ).dialog( "close" );
                }
              }
            });
            
            $(".dltP").on("click", function(e) {
                e.preventDefault();
                $("#dialog-confirm").dialog("open");
            });
          });
    </script>
  </head>
  <body>
    <?= menu("salas"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
         <?=$message ?>
        <form id="form" method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") {?>
                <input type="hidden" name="id"  value="<?=  $_GET["id"]?>" />
            <?php } ?>
            
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = $classMand = "" ;
                    if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                        $type  = (isset($input[$section]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input[$section]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($room[$v["COLUMN_NAME"]])) ? $room[$v["COLUMN_NAME"]] : "";            
                        if ($input[$section]["manage"]["mandatory"] == "*") {$classMand = "class='mandatory'"; $mandatory = "(<img src='images/mandatory.png' class='mandatory'>)";}
                        else if (in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])) { $classMand = "class='mandatory'"; $mandatory = "(<img src='./images/mandatory.png' class='mandatory'>)";}        
            ?>
                    
                <?php // Se hace la verificacion del tipo del input para cada columna ?>
                    <tr>
                        <td class="tdf"><?=(isset($label[$v["COLUMN_NAME"]])) ? $label[$v["COLUMN_NAME"]]: $v["COLUMN_NAME"] ?> <?= $mandatory?>:</td>
                        <td>
                <?php // Tipo por defecto. Se muestra un input text ?>
                <?php   
                        if ($type == ""){ 
                ?>
                       <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value ?>" <?= $classMand?> />
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ($type == "file") { ?>
                       <input type="file" name="<?= $v["COLUMN_NAME"]?>"   />
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type== "textarea") { ?>
                       <textarea name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?>><?= $value?></textarea>
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { ?>
                       <select name="<?= $value ?>" <?= $classMand?>></select>
                 <?php } ?>
                    <div class="missing-error">
                        <?php if (isset($missing[$v["COLUMN_NAME"]])) { ?>
                            <?= $label["Este campo es obligatorio"]?>
                        <?php } ?>
                     </div>
                     </td>
                     </tr>
                 <?php                        
                    }
                }
            ?>
            <tr>
                <td></td>
                <td class="action">
                    <input type="submit" name="<?= $action?>" value="<?= $label["Guardar"]?>" />
                    <?php if ($action == "edit" && ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                        <?php if ($sessions) { ?> 
                                <input type="submit" class="important dltP" name="delete" value="<?= $label["Borrar"]?>" />
                        <?php }else{ ?>
                                <input type="submit" class="important dlt" name="delete"  value="<?= $label["Borrar"]?>" />
                        <?php } ?>
                    <?php } ?>
                    <a href="./rooms.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            
            </table>
            
        </form>
    </div>
    <div>
    <?php 
       if ($sessions){
    ?>
    <div id="dialog-confirm" title="Confirmación">
        <p>
            <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
            Está a punto de borrar una sala que tiene sesiones asociadas, las cuales serán borradas también. ¿Desea continuar?.
        </p>
        <p> Sesiones asociadas a esta sala:
        <?php 
           $r      = "<ul class='sesList'>";
            foreach($sessions as $k=>$v){
                $r .= "<li><b>ID</b>: {$v["session_id"]} - <b>Título</b>: {$v["title"]}</li>";
           }
           $r      .= "</ul>";
        ?>
        <?= $r ?>
        </p>
    </div>  
   <?php } ?>
    </div>
     <?= my_footer() ?>
  </body>
</html>