<?php
/*
    Author: Marion Carambula
    Sección de sitios
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = "11"; $read      = 0;

$read       = verify_permissions($sectionId, "sites.php");

//Obtener las columnas a editar/crear
$section        = "site";
$columns        = $backend->getColumnsTable($section);
$id             = $message = $error = $site =  "";


//Agregar /editar una sala
if (isset($_POST["add"]) || isset($_POST["edit"])){
   $en["event_id"]     = $_SESSION["data"]["evento"];
   
   $site               = @$backend->getSite($_POST["id"]);
   if (isset($_SESSION["site"]["image_path"])) $en["image_path"]   = $_SESSION["site"]["image_path"];
   else if ($site)  $en["image_path"]                              = $site["image_path"];
    
    //Subir la imagen
    if (isset($_FILES["image_path"]) && $_FILES["image_path"]["name"]  != "" ){
        $path = $general[$section]["image_folder"].uniqid()."_".basename($_FILES["image_path"]["name"]);
        $resultUpload = $backend->uploadImage($path,
                                            "image_path", 
                                            $general[$section]["image_format"], 
                                            $general[$section]["image_size"],
                                            $general[$section]["image_width"],
                                            $general[$section]["image_height"],
                                            @$general[$section]["image_type"] 
                                            );
        if ($resultUpload["result"] == "0"){
            $error   = 1;
            $message = "<div class='error'>{$resultUpload["message"]}</div>";
        }else{
            $_SESSION["site"]["image_path"] =  $path;
            $en["image_path"]               =  $path ;
        }
   }else if (@$site["image_path"] == "" && !isset($_SESSION["site"]["image_path"])){
        $error = 1;
        $missing["image_path"] = 1;
   }
    //Guardar en bd el cliente
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
                            $en[$v["COLUMN_NAME"]] = $backend->clean($_POST[$v["COLUMN_NAME"]]);
                        }
                    }
                }else if ($v["COLUMN_NAME"] != "image_path" ){
                    $en[$v["COLUMN_NAME"]] = $backend->clean($_POST[$v["COLUMN_NAME"]]);
                }
            }
       }
   }
  
   if (!$error){
        $en["link"]   = addhttp($en["link"]);
        if (isset($_POST["add"])){
           $id = $backend->insertRow($section, $en);
           if ($id > 0) { 
                $_SESSION["message"] = "<div class='succ'>".$label["Sitio de interes creado exitosamente"]. "</div>";
                header("Location: ./sites.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
           }
       }else{
           $id      = $backend->clean($_POST["id"]);
           $rid     = $backend->updateRow($section, $en, " site_id = '$id' ");
           if ($rid > 0) { 
                $_SESSION["message"] = "<div class='succ'>".$label["Sitio de interes editado exitosamente"] ."</div>";
                header("Location: ./sites.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
           }
       }
   }else{
        if (isset($missing)) $message = "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $site    = $en;
   }
}
//Borrar Sitio
if (isset($_POST["delete"])){
   $id              = $backend->clean($_POST["id"]);
   $en["active"]    = "0";
   @$backend->updateRow("site", $en, " site_id = '$id' ");
   
   //Borrar imagen asociada
   @unlink($_POST["img"]);
  
   header("Location: ./sites.php");
   exit();
}

//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    if ($read)     $title          = $label["Ver sitio de interes"];
    else           $title          = $label["Editar sitio de interes"];
    $action         = "edit";
    if (!$error)    {
        $site           = $backend->getSite($id);
        if (!$site){
                $_SESSION["message"] = "<div class='error'>".$label["Sitio de interes no encontrado"]  ."</div>";
                header("Location: ./sites.php");
                exit();
        }
    }
}else{
    $title = $label["Crear sitio de interes"];
    $action = "add";
}

$imageTypeAux = "";
foreach ($general[$section]["image_format"] as $k){
    $imageTypeAux .= "," . $k ;
}
$imageType = $label["Formatos permitidos:"]."<b> ". substr($imageTypeAux, 1). "</b>" ;
$extra     = "(Cuadrada)";
if (isset($general["$section"]["image_type"]) &&  $general["$section"]["image_type"] == "rectangle") $extra = "(Rectangular)";
$imageSize = "Tamaño máximo permitido: <b> ".$general[$section]["image_width"]."x".$general[$section]["image_height"] . " $extra </b>" ;
$s = $general[$section]["image_size"] / 1000;
$imageW = "Peso máximo permitido: <b>". $s ."KB</b>" ;

?>
<!DOCTYPE html>
<html lang="es">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("sitios de interes"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
         <?=$message ?>
        <form id="form" method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") {?>
                 <input type="hidden" name="img" value="<?=  $site["image_path"]?>" />
                <input type="hidden" name="id"   value="<?=  $_GET["id"]?>" />
            <?php } ?>
            
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = $classMand = $readOnly = "" ;
                    if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                        if ($read) $readOnly = "disabled";
                        $type  = (isset($input[$section]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input[$section]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($site[$v["COLUMN_NAME"]])) ? $site[$v["COLUMN_NAME"]] : "";            
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
                    <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value ?>" <?= $classMand?> <?= $readOnly?> />
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ($type == "file") { ?>
                        <?php if ($value != "") {?>
                            <img class='manage-image' src='./<?=$value?>'/>
                        <?php } ?>
                        <?php if (!$read) { ?>
                        <input type="file" name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?> />
                        <img src="./images/info.png" class="information" alt="Información" />
                        <div class="image_format"><?= $imageType?>. <?= $imageSize?>. <?= $imageW?></div>
                        <?php } ?>
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type== "textarea") { ?>
                       <textarea name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?> <?= $readOnly?> ><?= $value?></textarea>
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { ?>
                       <select name="<?= $value ?>" <?= $classMand?> <?= $readOnly?> ></select>
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
                    <?php if (!$read){ ?>
                    <input type="submit" name="<?= $action?>" value="<?= $label["Guardar"]?>" />
                    <?php } ?>
                    <?php if ($action == "edit" && ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] != "cliente"|| $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                        <input type="button" class="important dltP" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./sites.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            
            </table>
            
        </form>
    </div>
    <?= include('common/dialog.php'); ?>
    <?= my_footer() ?>
  </body>
</html>