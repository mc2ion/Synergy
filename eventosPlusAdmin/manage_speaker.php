<?php
/*
    Author: Marion Carambula
    Sección de speakers
*/
include ("./common/common-include.php");

//Verificar que el usuario tiene  permisos
$sectionId = "8";
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}


$eventId    = $_SESSION["data"]["evento"];
$clientId   = $_SESSION["data"]["cliente"];


//Obtener las columnas a editar/crear
$section    = "speaker"; 
$columns    = $backend->getColumnsTable($section);
$sessions   = $backend->getSessionList($eventId, array(), "1", "1");
$id         = $message  = $error    = "";

//Agregar nuevo speaker
if (isset($_POST["add"]) ||  isset($_POST["edit"])){
    $message = "";
    //Subir la imagen. Verificar errores
   $en["event_id"]      = $eventId;
   $speaker             = @$backend->getSpeaker($_POST["id"]);
   if (isset($_SESSION["speaker"]["image_path"] )) $en["image_path"] = $_SESSION["speaker"]["image_path"] ;
   else if ($speaker)  $en["image_path"]    = $speaker["image_path"] ;
   
   
   // Verificar únicamente que la imagen no sea vacía, en el caso en el que no tenga ninguna imagen asociada
   if (isset($_FILES["image_path"]) && $_FILES["image_path"]["name"]  != "" ){
        $path = $general[$section]["image_folder"].uniqid($clientId.$eventId)."_".basename($_FILES["image_path"]["name"]);
        $resultUpload = $backend->uploadImage($path,
                                            "image_path", 
                                            $general[$section]["image_format"], 
                                            $general[$section]["image_size"],
                                            $general[$section]["image_width"],
                                            @$general[$section]["image_height"]
                                            );
        if ($resultUpload["result"] == "0"){
            $error   = 1;
            $message = "<div class='error'>{$resultUpload["message"]}</div>";
        }else{
            $_SESSION["speaker"]["image_path"] =  $path;
            $en["image_path"] =  $path ;
        }
   }else if ((!isset($speaker["image_path"]) ||  $speaker["image_path"] == "")  && !isset($_SESSION["speaker"]["image_path"] )){
        $error = 1;
        $missing["image_path"] = 1;
   }
   //Guardar en bd el speaker
   foreach ($columns as $k=>$v) {
        if (isset($input[$section]["manage"]["mandatory"])){
            //Verifico  únicamente los campos visibles
            if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                // Verifico los obligatorios
                if ($input[$section]["manage"]["mandatory"] == "*"
                    ||  in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])){
                    //Verifico si estan vacios. Para mostrar el error.
                    if ($v["COLUMN_NAME"] != "image_path" && $_POST[$v["COLUMN_NAME"]] == "") {
                        $error =  1;
                        $missing[$v["COLUMN_NAME"]] = 1;
                    }else{
                        if ($v["COLUMN_NAME"] != "image_path" ){
                            $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                        }
                    }
                }else if ($v["COLUMN_NAME"] != "image_path" ){
                    $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                }
            }
       }
   }

    if (!$error) {
        $created = $backend->getSpeakerByCompany($en["company_name"], $en["name"], @$_POST["id"]);
        if (count($created) > 0){
            $error = 1;
            $message = "<div class='error'>".$label["Disculpe, el nombre del speaker proporcionado ya existe"]. "</div>";
        }
    }

   if (!$error){
        if (isset($_POST["add"])){
            $id = $backend->insertRow($section, $en);
           if ($id > 0) { 
                unset($_SESSION["speaker"]["image_path"] );
                $_SESSION["message"] = "<div class='succ'>".$label["Presentador creado exitosamente"]. "</div>";
                header("Location: ./speakers.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
           }
       }else{
           $id      = $backend->clean($_POST["id"]);
           $id      = $backend->updateRow($section, $en, "speaker_id = '$id' ");
           if ($id > 0) { 
                unset($_SESSION["speaker"]["image_path"] );
                $_SESSION["message"] = "<div class='succ'>".$label["Presentador editado exitosamente"] ."</div>";
                header("Location: ./speakers.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
           }
       }
   }else{
        if (isset($missing)) $message    .= "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $speaker    = $en;
   }
   
}

//Borrar speaker
if (isset($_POST["delete"])){
   $id              = $backend->clean($_POST["id"]);
   $en["active"]    = "0";
   //1. Eliminar entrada
   $id = $backend->updateRow($section, $en, " speaker_id = '$id' ");
   //2. Eliminar imagen asociada
   @unlink($_POST["img"]);
   if ($id > 0) { 
        $_SESSION["message"] = "<div class='succ'>".$label["Presentador borrado exitosamente"] ."</div>";
        header("Location: ./speakers.php");
        exit();
   }else{
        $message = "<div class='error'>".$label["Hubo un problema con el borrado"]. "</div>";
   }
}

/** Fin acciones **/

//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    $title          = $label["Editar Presentador"];
    $action         = "edit";
    if (!$error)   { 
        $speaker        = $backend->getSpeaker($_GET["id"]);
        if (!$speaker){
            $_SESSION["message"] = "<div class='error'>".$label["Presentador no encontrado"]  ."</div>";
            header("Location: ./speakers.php");
            exit();
        }
    }
}else{
    $title = $label["Crear Presentador"];
    $action = "add";    
}

/* Armar mensaje para los tipos permitidos*/
$imageTypeAux = "";
if(isset($general[$section]["image_format"])){
    foreach ((array)$general[$section]["image_format"] as $k){
        $imageTypeAux .= "," . $k ;
    }
}
$imageType = $label["Formatos permitidos:"]."<b> ". substr($imageTypeAux, 1). "</b>" ;
$extra     = "(Cuadrada)";
if (isset($general["$section"]["image_type"]) &&  $general["$section"]["image_type"] == "rectangle") $extra = "(Rectangular)";
$imageSize = "Tamaño máximo permitido: <b> ".$general[$section]["image_width"]."x".$general[$section]["image_height"] . " $extra </b>" ;
$s = $general[$section]["image_size"] / 1000;
$imageW = "Peso máximo permitido: <b>". $s ."KB</b>" ;

$label["session_title"] = "Sesión";

?>

<!DOCTYPE html>
<html lang="es">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("presentadores"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
        <?=$message ?>
        <form id="form" method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") {?>
                <input type="hidden" name="img" value="<?=  $speaker["image_path"]?>" />
                <input type="hidden" name="id"  value="<?=  $_GET["id"]?>" />
            <?php } ?>
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = $classMand = "";
                    if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                        $type  = (isset($input[$section]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input[$section]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($speaker[$v["COLUMN_NAME"]])) ? $speaker[$v["COLUMN_NAME"]] : "";
                        if ($input[$section]["manage"]["mandatory"] == "*") {$classMand = "class='mandatory'"; $mandatory = "(<img src='images/mandatory.png' class='mandatory'>)";}
                        else if (in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])) { $classMand = "class='mandatory'"; $mandatory = "(<img src='./images/mandatory.png' class='mandatory'>)";}        
                ?>
                <?php // Se hace la verificacion del tipo del input para cada columna ?>
                    <tr class="tr_<?=$v["COLUMN_NAME"]?>">
                        <td class="tdf"><?=(isset($label[$v["COLUMN_NAME"]])) ? $label[$v["COLUMN_NAME"]]: $v["COLUMN_NAME"]?> <?= $mandatory?>:</td>
                        <td>
                <?php // Tipo por defecto. Se muestra un input text?>
                <?php   
                        if ($type == ""){ 
                ?>
                        <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?= $value ?>" <?= $classMand ?> />
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ( $type == "file") { ?>
                        <?php if ($value != "") {?>
                            <img class='manage-image' src='./<?=$value?>'/>
                        <?php } ?>
                        <input type="file" name="<?= $v["COLUMN_NAME"]?>"  />
                        <img src="./images/info.png" class="information" alt="Información" />
                        <div class="image_format"><?= $imageType?>. <?= $imageSize?>. <?= $imageW?></div>
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type == "textarea") { ?>
                        <textarea name="<?= $v["COLUMN_NAME"]?>" <?= $classMand ?>><?=$value?></textarea>
                 <?php // Tipo date. Se muestra un text pero especial para tener el date picker ?>
                 <?php } else if ($type == "time") { ?>
                       <input type="text" class="timepicker <?=substr($classMand,7, 9) ?>" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off" />
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 
                 <?php } else if ($type == "date") { ?>
                        <input type="text" class="datepicker <?=substr($classMand,7, 9) ?>" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off" />
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { ?>
                            <?php if ($v["COLUMN_NAME"] == "session_title"){?>
                                <select name="<?= $v["COLUMN_NAME"]?>" <?= $classMand ?>>
                                <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($sessions as $sk=>$sv){
                                     $sel = ""; if ($sv["title"] == $value) $sel = "selected";
                                    ?>
                                    <option value="<?=$sv["title"]?>" <?= $sel?> ><?= $sv["title"]?></option>
                                <?php }?>
                                </select>
                            <?php }else{ ?>
                                <select name="<?= $v["COLUMN_NAME"]?>" <?= $classMand ?>>
                                    <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($input[$section]["manage"][$v["COLUMN_NAME"]]["options"] as $sk=>$sv){?>
                                    <option value="<?=$sk?>"><?= $sv?></option>
                                <?php }?>
                                </select>
                            <?php } ?>
                 <?php }?>
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
                    <?php if ($action == "edit" && ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                        <input type="button" class="important dltP" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./speakers.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            
            </table>
            
        </form>
    </div>
    <?= include('common/dialog.php'); ?>
    <?= my_footer() ?>
  </body>
</html>