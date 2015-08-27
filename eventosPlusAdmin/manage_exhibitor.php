<?php
/*
    Author: Marion Carambula
    Sección de expositores
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = "7";
$read      = 0;

$read       = verify_permissions($sectionId, "exhibitors.php");

//Obtener las columnas a editar/crear
$section    = "exhibitor"; 
$columns    = $backend->getColumnsTable($section);

$eventId    = $_SESSION["data"]["evento"];
$clientId   = $_SESSION["data"]["cliente"];

$id         = $message  = $error    = $categoryExh =  "";

//Agregar nuevo speaker
if (isset($_POST["add"]) ||  isset($_POST["edit"])){
    $message = "";
    //Subir la imagen. Verificar errores
   $en["event_id"]      = $eventId;
   $exhibitor           = @$backend->getExhibitor($_POST["id"]);
   if (isset($_SESSION["exhibitor"]["image_path"])) $en["image_path"] = $_SESSION["exhibitor"]["image_path"];
   else  if ($exhibitor)  $en["image_path"]    = $exhibitor["image_path"] ;
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
            $_SESSION["exhibitor"]["image_path"] =  $path;
            $en["image_path"] =  $path ;
        }
   }else if ($exhibitor["image_path"] == "" && !isset($_SESSION["exhibitor"]["image_path"])){
        $error = 1;
        $missing["image_path"] = 1;
   }
   
   //Guardar en bd el speaker
   foreach ($columns as $k=>$v) {
        if (isset($input[$section]["manage"]["mandatory"])){
            //Verifico  únicamente los campos visibles
            if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                //Verifico el tipo de la categoria, pues de eso dependeran los campos obligatorios
                $type = "";
                if (isset($_POST["category_id"]) && $_POST["category_id"] != ""){
                    $category   = $backend->getCategory($_POST["category_id"]);
                    $type       = $category["type"];
                }
                $en["category_id"] = $_POST["category_id"];
                //Solo verifico los tipo "contenido", pues es grid solo necesito la imagen y ya la tengo
                if ($type == "" || $type == "contenido"){
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
                        $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                    }
                }else{
                    if ($v["COLUMN_NAME"] != "image_path" && $v["COLUMN_NAME"] != "category_id" && $v["COLUMN_NAME"] != "company_name" ){
                        $en[$v["COLUMN_NAME"]] = "";
                    }else if ($v["COLUMN_NAME"] == "company_name"){
                        if ($_POST[$v["COLUMN_NAME"]] == "") {
                            $error =  1;
                            $missing[$v["COLUMN_NAME"]] = 1;
                        }else{
                            $en[$v["COLUMN_NAME"]] = $backend->clean($_POST[$v["COLUMN_NAME"]]);
                        }
                    }
                }
            }
       }
   }


    if (!$error){
        //Verificar que no exista un expositor (nombre empresa) ya creado
        if ($en["company_name"] != ""){
            $created = $backend->getExhibitorByName($en["company_name"], @$_POST["id"]);
            if (count($created) > 0 ){ $error = 1; $message = "<div class='error'>".$label["El nombre de expositor proporcionado ya existe"]. "</div>"; }
        }
    }
   
   if (!$error){
        if ($en["position"] == "")      unset($en["position"]);
        if ($en["category_id"] == "")   unset($en["category_id"]);
        
        if (isset($_POST["add"])){
            $id = $backend->insertRow($section, $en);
           if ($id > 0) { 
                $_SESSION["message"] = "<div class='succ'>".$label["Expositor creado exitosamente"]. "</div>";
                unset($_SESSION["exhibitor"]["image_path"]);
                header("Location: ./exhibitors.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
           }
       }else{
           $id      = $backend->clean($_POST["id"]);
           $id      = $backend->updateRow($section, $en, "exhibitor_id = '$id' ");
           if ($id > 0) { 
                unset($_SESSION["exhibitor"]["image_path"]);
                $_SESSION["message"] = "<div class='succ'>".$label["Expositor editado exitosamente"] ."</div>";
                header("Location: ./exhibitors.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
           }
       }
   }else{
        if (isset($missing)) $message    .= "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $exhibitor    = $en;
   }
   
   
}

//Borrar speaker
if (isset($_POST["delete"])){
   $id              = $backend->clean($_POST["id"]);
   $en["active"]    = "0";
   //1. Borrar imagen expositor
   $id = $backend->updateRow($section, $en, " exhibitor_id = '$id' ");
   //2. Borrar imagen  asociado
   @unlink($_POST["img"]);
   
   if ($id > 0) { 
        unset($_SESSION["exhibitor"]["image_path"]);
        $_SESSION["message"] = "<div class='succ'>".$label["Expositor borrado exitosamente"] ."</div>";
        header("Location: ./exhibitors.php");
        exit();
   }else{
        $message = "<div class='error'>".$label["Hubo un problema con el borrado"]. "</div>";
   }
}

/** Fin acciones **/

//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    if ($read)      $title          = $label["Ver Expositor"];
    else            $title          = $label["Editar Expositor"];
    $action         = "edit";
    if (!$error)   { 
        $exhibitor        = $backend->getExhibitor($_GET["id"]);
        if (!$exhibitor){
            $_SESSION["message"] = "<div class='error'>".$label["Expositor no encontrado"]  ."</div>";
            header("Location: ./exhibitors.php");
            exit();
        }
    }
    $categoryExh    = $backend->getCategory($exhibitor["category_id"]);
}else{
    $title = $label["Crear Expositor"];
    $action = "add";    
}

$category   = $backend->getCategoryList($clientId);
$types       = array();
foreach ($category as $k=>$v){
    $types[$v["category_id"]] = $v["type"];
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

$label["position"]  = "Posición en el mapa";
?>

<!DOCTYPE html>
<html lang="es">
  <head>
     <script>
        var types = <?php echo json_encode($types); ?>;
     </script>
     <?= my_header()?>
      <style>
    <?php if ( $categoryExh && $categoryExh["type"] == "grid" || @$types[$en["category_id"]]  == "grid") {?>
        .tr_description{display:none;}
        .tr_position{display:none;}
        .tr_other{display:none;}
    <?php } ?>
                   
    </style>
  </head>
  <body>
    <?= menu("expositores"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
        <?=$message ?>
        <form id="form" method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") {?>
                <input type="hidden" name="img" value="<?=  $exhibitor["image_path"]?>" />
                <input type="hidden" name="id"  value="<?=  $_GET["id"]?>" />
            <?php } ?>
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = $classMand = $readOnly = ""; 
                    if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                        if ($read) $readOnly = "disabled";
                        $type  = (isset($input[$section]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input[$section]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($exhibitor[$v["COLUMN_NAME"]])) ? $exhibitor[$v["COLUMN_NAME"]] : "";
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
                        <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?= $value ?>" <?= $classMand?>  <?= $readOnly?>/>
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ( $type == "file") { ?>
                        <?php if ($value != "") {?>
                            <img class='manage-image' src='./<?=$value?>'/>
                        <?php } ?>
                        <?php if (!$read) { ?>
                            <input type="file" name="<?= $v["COLUMN_NAME"]?>" />
                            <img src="./images/info.png" class="information" alt="Información" />
                            <div class="image_format"><?= $imageType?>. <?= $imageSize?>. <?= $imageW?></div>
                        <?php } ?>
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type == "textarea") { ?>
                        <textarea name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?> <?= $readOnly?>><?=$value?></textarea>
                 <?php // Tipo date. Se muestra un text pero especial para tener el date picker ?>
                 <?php } else if ($type == "time") { ?>
                       <input type="text" class="timepicker <?=substr($classMand,7, 9) ?>" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off" <?= $readOnly?> />
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 
                 <?php } else if ($type == "date") { ?>
                        <input type="text" class="datepicker <?=substr($classMand,7, 9) ?>" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off" <?= $readOnly?> />
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { ?>
                            <?php if ($v["COLUMN_NAME"] == "category_id"){?>
                                <select name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?> <?= $readOnly?>>
                                <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($category as $sk=>$sv){
                                     $sel = ""; if ($sv["category_id"] == $value) $sel = "selected";
                                    ?>
                                    <option value="<?=$sv["category_id"]?>" <?= $sel?> ><?= $sv["name"]?></option>
                                <?php }?>
                                </select>
                            <?php }else{ ?>
                                <select name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?> <?= $readOnly?>>
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
            <tr class="tr_actions"> 
                <td></td>
                <td class="action">
                    <?php if (!$read) { ?>
                        <input type="submit" name="<?= $action?>" value="<?= $label["Guardar"]?>" />
                    <?php } ?>
                    <?php if ($action == "edit" && ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                        <input type="button" class="important dltP" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./exhibitors.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            
            </table>
        </form>
    </div>
     <?= include('common/dialog.php'); ?>
     <?= my_footer() ?>
  </body>
</html>