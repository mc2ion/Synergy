<?php
/*
    Author: Marion Carambula
    Sección de usuarios
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = 2;
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}


//Obtener las columnas a editar/crear
$columns = $backend->getColumnsTable("client");
$id      =  $message =  $error = $client =  $en = "";
$section = "client";


//Agregar nuevo cliente
if (isset($_POST["add"]) || isset($_POST["edit"])){
    $client                         = @$backend->getClient($_POST["id"]);
    if (isset($_SESSION["client"]["logo_path"])) $en["logo_path"] = $_SESSION["client"]["logo_path"];
    else if ($client)  $en["logo_path"]                           = $client["logo_path"];
    
    //Subir la imagen
    if (isset($_FILES["logo_path"]) && $_FILES["logo_path"]["name"]  != "" ){
        $path = $general[$section]["image_folder"].uniqid()."_".basename($_FILES["logo_path"]["name"]);
        $resultUpload = $backend->uploadImage($path,
                                            "logo_path", 
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
            $_SESSION["client"]["logo_path"] =  $path;
            $en["logo_path"]                 =  $path ;
        }
   }else if ($client["logo_path"] == "" && !isset($_SESSION["client"]["logo_path"])){
        $error = 1;
        $missing["logo_path"] = 1;
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
                    if ($v["COLUMN_NAME"] != "logo_path" && $_POST[$v["COLUMN_NAME"]] == "") {
                        $error =  1;
                        $missing[$v["COLUMN_NAME"]] = 1;
                    }else{
                        if ($v["COLUMN_NAME"] != "logo_path" ){
                            $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                        }
                    }
                }else if ($v["COLUMN_NAME"] != "logo_path" ){
                    $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                }
            }
       }
   }
   
   
   if (!$error){
       if (isset($_POST["add"])){
            $id = @$backend->insertRow("client", $en);
            if ($id > 0) { 
                unset($_SESSION["client"]["logo_path"]);
                $_SESSION["message"] = "<div class='succ'>".$label["Cliente creado exitosamente"]. "</div>";
                header("Location: ./clients.php");
                exit(0);
            }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
            }
       }else{
            $id             = $backend->clean($_POST["id"]);
            $rid            = @$backend->updateRow("client", $en, " client_id = '$id' ");
            if ($rid > 0) { 
                unset($_SESSION["client"]["logo_path"]);
                $_SESSION["message"] = "<div class='succ'>".$label["Cliente editado exitosamente"] ."</div>";
                header("Location: ./clients.php");
                exit(0);
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
           }
       }
   }else{
        if (isset($missing)) $message    .= "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $client      = $en;
   }
}


//Borrar Cliente
if (isset($_POST["delete"])){
   $id              = $backend->clean($_POST["id"]);
   $en["active"]    = 0;
   //Borrado logico de la entrada
   @$backend->updateRow("client", $en, " client_id = '$id' ");
   
   //Borrar imagen asociada
   @unlink($_POST["img"]);
    if ($id > 0) { 
        $_SESSION["message"] = "<div class='succ'>".$label["Cliente borrado exitosamente"] ."</div>";
        header("Location: ./clients.php");
        exit();
   }else{
        $message = "<div class='error'>".$label["Hubo un problema con el borrado"]. "</div>";
   }
}

//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    $title          = $label["Editar Cliente"];
    $action         = "edit";
    if (!$error)   $client         = $backend->getClient($_GET["id"]);
}else{
    $title = $label["Crear Cliente"];
    $action = "add";
}

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
  <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("clientes"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
        <?=$message ?>
        <form method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") {?>
            <input type="hidden" name="img" value="<?=  $client["logo_path"]?>" />
            <input type="hidden" name="id"  value="<?=  $_GET["id"]?>" />
            <?php } ?>
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    if (!in_array($v["COLUMN_NAME"],$input["client"]["manage"]["no-show"])){
                        $type  = (isset($input["client"]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input["client"]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($client[$v["COLUMN_NAME"]])) ? $client[$v["COLUMN_NAME"]] : "";            
                        if ($input[$section]["manage"]["mandatory"] == "*") $mandatory = "(<img src='images/mandatory.png' class='mandatory'>)";
                        else if (in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])) $mandatory = "(<img src='./images/mandatory.png' class='mandatory'>)";
                
            ?>
                    
                <?php // Se hace la verificacion del tipo del input para cada columna ?>
                    <tr>
                        <td class="tdf"><?=(isset($label[$v["COLUMN_NAME"]])) ? $label[$v["COLUMN_NAME"]]: $v["COLUMN_NAME"] ?> <?=$mandatory?>:</td>
                        <td>
                <?php // Tipo por defecto. Se muestra un input text ?>
                <?php   
                        if ($type == ""){ 
                ?>
                        <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value ?>" />
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ($type == "file") { 
                    ?>
                        <?php if ($value != "") {?>
                            <img class='manage-image' src='./<?=$value?>'/>
                        <?php } ?>
                        <input type="file" name="<?= $v["COLUMN_NAME"]?>" />
                        <div class="image_format"><?= $imageType?>. <?= $imageSize?>. <?= $imageW?></div>
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type== "textarea") { ?>
                        <textarea name="<?= $v["COLUMN_NAME"]?>"><?= $value?></textarea>
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { 
                         $options = $input[$section]["manage"][$v["COLUMN_NAME"]]["options"];
                    ?>
                            <select name="<?= $v["COLUMN_NAME"]?>">
                                <?php foreach ($options as $sk=>$sv){
                                    $selected=""; if($value == $sk) $selected = "selected";
                                    ?>
                                    <option value="<?=$sk?>"><?= $sv?></option>
                                <?php }?>
                            </select>
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
                    <?php if ($action == "edit" && ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1" )) {?>
                    <input type="submit" class="important" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./clients.php"><?= $label["Cancelar"]?></a>
                </td>
            </tr>
            
            </table>
            
        </form>
    </div>
  </body>
</html>