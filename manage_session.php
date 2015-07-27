<?php
/*
    Author: Marion Carambula
    Sección de usuarios
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = "4";
$event     = $_SESSION["data"]["evento"];
$client    = $_SESSION["data"]["cliente"];
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}


//Obtener las columnas a editar/crear
$section    = "session"; 
$columns    = $backend->getColumnsTable($section);
$rooms      = $backend->getRoomList($_SESSION["data"]["evento"]);
$id         = $message  = $error    = "";


//Agregar nuevo evento
if (isset($_POST["add"]) ||  isset($_POST["edit"])){
    $message = "";
    //Subir la imagen. Verificar errores
   $en["event_id"]      = $event;
   $session             = @$backend->getSession($_POST["id"]);
   if (isset($_SESSION["session"]["image_path"])) $en["image_path"] = $_SESSION["session"]["image_path"];
   else  if ($session)  $en["image_path"]    = $session["image_path"] ;
   
   
   // Verificar únicamente que la imagen no sea vacía, en el caso en el que no tenga ninguna imagen asociada
   if (isset($_FILES["image_path"]) && $_FILES["image_path"]["name"]  != "" ){
        $path = $general[$section]["image_folder"].uniqid($client.$event)."_".basename($_FILES["image_path"]["name"]);
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
            $_SESSION["session"]["image_path"] =  $path;
            $en["image_path"] =  $path ;
        }
   }else if ($session["image_path"] == "" && !isset($_SESSION["session"]["image_path"])){
        $error = 1;
        $missing["image_path"] = 1;
   }
   //Guardar en bd el evento
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
   
   if (!$error){
        //Debo darle formato al time.
        //$date = date( 'H : i : A', $en["time"]);
        
         $t= str_replace(' ', '', $en["time"]);
         $h = explode(":", $t);
         $ho = $h[0].":".$h[1]. " " .$h[2];
         $date = date("H:i",strtotime($ho));
        $en["time"] = $date;
       if (isset($_POST["add"])){
            $id = $backend->insertRow($section, $en);
           if ($id > 0) { 
                unset($_SESSION["session"]["image_path"]);
                $_SESSION["message"] = "<div class='succ'>".$label["Sesión creada exitosamente"]. "</div>";
                header("Location: ./sessions.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
           }
       }else{
           $id             = $backend->clean($_POST["id"]);
           $id = $backend->updateRow($section, $en, " session_id = '$id' ");
           if ($id > 0) { 
                unset($_SESSION["session"]["image_path"]);
                $_SESSION["message"] = "<div class='succ'>".$label["Sesión editada exitosamente"] ."</div>";
                header("Location: ./sessions.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
           }
       }
   }else{
        if (isset($missing)) $message    .= "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $session    = $en;
   }
   
}

//Borrar Evento
if (isset($_POST["delete"])){
   $id              = $backend->clean($_POST["id"]);
   $en["active"]    = 0;
   //1. Borrar entrada
   $id = $backend->updateRow($section, $en, " session_id = '$id' ");
   
   //2. Borrar imagen asociada
   @unlink($_POST["img"]);
  
   if ($id > 0) { 
        $_SESSION["message"] = "<div class='succ'>".$label["Sesión borrada exitosamente"] ."</div>";
        header("Location: ./sessions.php");
        exit();
   }else{
        $message = "<div class='error'>".$label["Hubo un problema con el borrado"]. "</div>";
   }
}

/** Fin acciones **/

//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    $title          = $label["Editar Sesión"];
    $action         = "edit";
    if (!$error)    $session        = $backend->getSession($_GET["id"]);
}else{
    $title = $label["Crear Sesión"];
    $action = "add";    
}

/* Armar mensaje para los tipos permitidos*/
$imageTypeAux = "";
foreach ($general[$section]["image_format"] as $k){
    $imageTypeAux .= "," . $k ;
}
$imageType = $label["Formatos permitidos:"]."<b> ". substr($imageTypeAux, 1). "</b>" ;
$imageSize = "Tamaño máximo permitido: <b> ".$general[$section]["image_width"]."x".$general[$section]["image_height"] . "</b>" ;
$s = $general[$section]["image_size"] / 1000;
$imageW = "Peso máximo permitido: <b>". $s ."KB</b>" ;


?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("sesiones"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
        <?=$message ?>
        <form method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") { ?>
                <input type="hidden" name="id"  value="<?=$_GET["id"]?>" />
                <input type="hidden" name="img" value="<?=$session["image_path"]?>" />
                
            <?php } ?>
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = "";
                    if (!in_array($v["COLUMN_NAME"],$input["session"]["manage"]["no-show"])){
                        $type  = (isset($input["session"]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input["session"]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($session[$v["COLUMN_NAME"]])) ? $session[$v["COLUMN_NAME"]] : "";
                        if ($input[$section]["manage"]["mandatory"] == "*") $mandatory = "(<img src='images/mandatory.png' class='mandatory'>)";
                        else if (in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])) $mandatory = "(<img src='./images/mandatory.png' class='mandatory'>)";
                ?>
                <?php // Se hace la verificacion del tipo del input para cada columna ?>
                    <tr class="tr_<?=$v["COLUMN_NAME"]?>">
                        <td class="tdf"><?=(isset($label[$v["COLUMN_NAME"]])) ? $label[$v["COLUMN_NAME"]]: $v["COLUMN_NAME"]?> <?= $mandatory?>:</td>
                        <td>
                <?php // Tipo por defecto. Se muestra un input text?>
                <?php   
                        if ($type == ""){ 
                ?>
                        <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?= $value ?>" />
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ( $type == "file") { ?>
                        <?php if ($value != "") {?>
                            <img class='manage-image' src='./<?=$value?>'/>
                        <?php } ?>
                        <input type="file" name="<?= $v["COLUMN_NAME"]?>" />
                        <div class="image_format"><?= $imageType?>. <?= $imageSize?>. <?= $imageW?></div>
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type == "textarea") { ?>
                        <textarea name="<?= $v["COLUMN_NAME"]?>"><?=$value?></textarea>
                 <?php // Tipo date. Se muestra un text pero especial para tener el date picker ?>
                 <?php } else if ($type == "time") { ?>
                       <input type="text" class="timepicker" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off" />
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 
                 <?php } else if ($type == "date") { ?>
                        <input type="text" class="datepicker" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off" />
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { ?>
                            <?php if ($v["COLUMN_NAME"] == "room_id"){?>
                                <select name="<?= $v["COLUMN_NAME"]?>">
                                <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($rooms as $sk=>$sv){
                                     $sel = ""; if ($sv["room_id"] == $value) $sel = "selected";
                                    ?>
                                    <option value="<?=$sv["room_id"]?>" <?= $sel?> ><?= $sv["name"]?></option>
                                <?php }?>
                                </select>
                            <?php }else{ ?>
                                <select name="<?= $v["COLUMN_NAME"]?>">
                                    <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($input["session"]["manage"][$v["COLUMN_NAME"]]["options"] as $sk=>$sv){?>
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
                    <?php if ($action == "edit" && ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                    <input type="submit" class="important" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./sessions.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            
            </table>
            
        </form>
    </div>
  </body>
</html>