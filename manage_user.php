<?php
/*
    Author: Marion Carambula
    Sección de usuarios
*/
include ("./common/common-include.php");

//Verificar que el usuario tiene  permisos
$sectionId = "1";
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}

//Obtener las columnas a editar/crear
$columns = $backend->getColumnsTable("user");

//Obtener los clientes actuales
$clients    = $backend->getClientList(array(), "1");
$sections   = $backend->getMenu();

$label["client_id"] = "Cliente";
$id         = $message  = $error    = "";
$user       = "";
$section    = "user";


//Agregar nuevo usuario
if (isset($_POST["add"]) || isset($_POST["edit"])){
    //Subir la imagen
    $user                         = @$backend->getUserInfo($_POST["id"]);
    if (isset($_SESSION["user"]["photo_path"])) $en["photo_path"]   = $_SESSION["user"]["photo_path"];
    else if ($user)  $en["photo_path"]                              = $user["photo_path"];
    
    if (isset($_FILES["photo_path"]) && $_FILES["photo_path"]["name"]  != "" ){
        $path = $general[$section]["image_folder"].uniqid()."_".basename($_FILES["photo_path"]["name"]);
        $resultUpload = $backend->uploadImage($path,
                                            "photo_path", 
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
            $_SESSION["user"]["photo_path"] =  $path;
            $en["photo_path"]               =  $path ;
        }
   }
   
   //Mejorar esto
    if (isset($_POST["edit"])){
         unset($columns[5]);
    }
    
   //Guardar en bd el usuario
   foreach ($columns as $k=>$v) {
        if (isset($input[$section]["manage"]["mandatory"])){
            //Verifico  únicamente los campos visibles
            if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                // Verifico los obligatorios
                if ($input[$section]["manage"]["mandatory"] == "*"
                    ||  in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])){
                    //Verifico si estan vacios. Para mostrar el error.
                    if ($v["COLUMN_NAME"] != "photo_path" && $_POST[$v["COLUMN_NAME"]] == "") {
                        $error =  1;
                        $missing[$v["COLUMN_NAME"]] = 1;
                    }else{
                        if ($v["COLUMN_NAME"] != "photo_path" ){
                            $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                        }
                    }
                }else if ($v["COLUMN_NAME"] != "photo_path" ){
                    $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                }
            }
       }
   }
   
   //Verificar que el correo no exista ya.
   if (isset($_POST["add"])){
        $cond = "email = '{$en["email"]}'";
   }else{
        $cond = "email = '{$en["email"]}' AND user_id != '{$_POST["id"]}'";
   }
   
  if ($backend->selectRow("user", $cond)){
        $message    .= "<div class='error'>".$label["El correo ingresado ya existe"]. "</div>";
        $error = 1;
  }
   
   
   /* Permisologia */
    foreach ($sections as $k=>$v){
        $id             = $backend->clean($_POST["id"]);
        if (isset($_POST[$v["section_id"]."_create"])){ $create = 1;}
        if (isset($_POST[$v["section_id"]."_read"]))  { $create = 1;}
        if (isset($_POST[$v["section_id"]."_update"])){ $create = 1;}
        if (isset($_POST[$v["section_id"]."_delete"])){ $create = 1;}
        $in["section_id"] = $v["section_id"];
        $in["user_id"]    = $id;
        $in["create"]     = isset($_POST[$v["section_id"]."_create"])? "1" : "0";
        $in["update"]     = isset($_POST[$v["section_id"]."_update"])? "1" : "0";
        $in["delete"]     = isset($_POST[$v["section_id"]."_delete"])? "1" : "0";
        $in["read"]       = isset($_POST[$v["section_id"]."_read"])? "1" : "0";
        $p["section"][$v["section_id"]] = $in;
    }
   
   if (!$error){
       if (isset($_POST["add"])){
            if ($en["client_id"] == "") unset($en["client_id"]);
            $en["password"] = md5($en["password"]);
            $id = @$backend->insertRow($section, $en);
            if ($id > 0) { 
                /* Permisologia */
                foreach ($p["section"] as $k=>$v){
                    @$backend->insertRow("permission", $v);
                }
                $_SESSION["message"] = "<div class='succ'>".$label["Usuario creado exitosamente"]. "</div>";
                header("Location: ./users.php");
                exit();
            }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
            }
       }else{
            $id             = $backend->clean($_POST["id"]);
            if ($en["client_id"] == "") unset($en["client_id"]);
            $rid            = @$backend->updateRow($section, $en, " user_id = '$id' ");
            if ($rid > 0) { 
                /* Permisologia */
                //3 casos posibles.. Si se esta editando a administrador borrar posibles permisos anteriores
                if ($en["type"] == "administrador"){
                    //Si estoy cambiando de "cliente" a "administrador" debo borrar los permisos
                    $backend->deleteRow("permission", "user_id = '$id' ");
                }else{
                    //Si es cliente hay que verificar si ya tenia permisos anteriormente
                    $permissions = $backend->getPermission($id);
                    foreach ($p["section"] as $k=>$v){
                        if ($permissions != ""){
                            $backend->updateRow("permission", $v, " section_id = '{$v["section_id"]}' AND user_id='$id'");
                        }else{
                            $backend->insertRow("permission", $v);
                        }
                    }
                }
               $_SESSION["message"] = "<div class='succ'>".$label["Usuario editado exitosamente"] ."</div>";
               header("Location: ./users.php");
               exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
           }
       }
   }else{
        if (isset($missing)) $message    .= "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $user                             = $en;
        $permissions                      = $p; 
   }
   
}


//Borrar Usuario
if (isset($_POST["delete"])){
   $id              = $backend->clean($_POST["id"]);
   
   $image           = $backend->getUserInfo($_POST["id"]);
   //1. Borrar usuario
   $id = $backend->deleteRow("user", " user_id = '$id' ");
   
   //2. Borrar imagen  asociado
   unlink($image["photo_path"]);
   
   //3. Borrar permisos
   $backend->deleteRow("permission", "user_id = '$id' ");
 
   
   if ($id > 0) { 
        $_SESSION["message"] = "<div class='succ'>".$label["Usuario borrado exitosamente"] ."</div>";
        header("Location: ./users.php");
        exit();
   }else{
        $message = "<div class='error'>".$label["Hubo un problema con el borrado"]. "</div>";
   }
}

//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    $title          = $label["Editar Usuario"];
    $action         = "edit";
    if (!$error)  { 
        $user         = $backend->getUserInfo($_GET["id"]); 
        if (!$user){
            $_SESSION["message"] = "<div class='error'>".$label["Usuario no encontrado"] ."</div>";
            header("Location: ./users.php");
            exit();
        }
        $permissions = $backend->getPermission($id); 
    }
    //No mostrar la contraseña al editar
    array_push($input[$section]["manage"]["no-show"], "password");
}else{
    $title = $label["Crear Usuario"];
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
  <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
     <?= my_header()?>
  </head>
  <body>
    <?= menu("usuarios"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
        <?= $message ?>
        <form method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") {?>
                <input type="hidden" name="img" value="<?=  $client["logo_path"]?>" />
                <input type="hidden" name="id"  value="<?=  $_GET["id"]?>" />
            <?php } ?>
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = "";
                    if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                        $type  = (isset($input[$section]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input[$section]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($user[$v["COLUMN_NAME"]])) ? $user[$v["COLUMN_NAME"]] : "";    
                        if ($input[$section]["manage"]["mandatory"] == "*") $mandatory = "(<img src='images/mandatory.png' class='mandatory'>)";
                        else if (in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])) $mandatory = "(<img src='./images/mandatory.png' class='mandatory'>)";        
            ?>
                    
                <?php // Se hace la verificacion del tipo del input para cada columna ?>
                    <tr class="<?= $v["COLUMN_NAME"] ?>">
                        <td class="tdf"><?=(isset($label[$v["COLUMN_NAME"]])) ? $label[$v["COLUMN_NAME"]]: $v["COLUMN_NAME"] ?> <?=$mandatory?>:</td>
                <?php // Tipo por defecto. Se muestra un input text ?>
                        <td colspan="4">
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
                 <?php // Tipo password. ?>
                 <?php } else if ($type== "password") { ?>
                        <input type="password" name="<?= $v["COLUMN_NAME"]?>" value="<?= $value?>" autocomplete="off"/>
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { ?>
                            <?php if ($v["COLUMN_NAME"] == "client_id"){?>
                                <select name="<?= $v["COLUMN_NAME"]?>">
                                <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($clients as $sk=>$sv){
                                     $sel = ""; if ($sv["client_id"] == $value) $sel = "selected";
                                    ?>
                                    <option value="<?=$sv["client_id"]?>" <?= $sel?> ><?= $sv["name"]?></option>
                                <?php }?>
                                </select>
                            <?php }else{
                                $options = $input[$section]["manage"][$v["COLUMN_NAME"]]["options"]; ?>
                                <select name="<?= $v["COLUMN_NAME"]?>">
                                 <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($options as $sk=>$sv){
                                    $selected=""; if($value == $sk) $selected = "selected";
                                    ?>
                                    <option value="<?=$sk?>" <?= $selected?>><?= $sv?></option>
                                <?php }?>
                            </select>
                            <?php } ?>
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
            <tr class="permi">
                <td colspan="5" class="tr_permi"><?= $label["Permisologia"]?></td>
            </tr>
            <tr class="permi">
                <td></td>
                <td><?= $label["Crear"]?></td>
                <td><?= $label["Leer"]?></td>
                <td><?= $label["Editar"]?></td>
                <td><?= $label["Eliminar"]?></td>
            </tr >   
                <?php foreach ($sections as $k=>$v){?>
                    <tr class="permi" >
                        <td>
                            <?= (isset($label[$v["name"]])) ? $label[$v["name"]]: ucfirst($v["name"]) ?>
                        </td>
                        <td style="width: 15%;"><input type="checkbox" value="1" name="<?= $v["section_id"]?>_create" <?= (@$permissions["section"][$v["section_id"]]["create"] == "1")? "checked": ""?>/></td>
                        <td style="width: 15%;"><input type="checkbox" value="1" name="<?= $v["section_id"]?>_read" <?= (@$permissions["section"][$v["section_id"]]["read"] == "1")? "checked": ""?>/></td>
                        <td style="width: 15%;"><input type="checkbox" value="1" name="<?= $v["section_id"]?>_update" <?= (@$permissions["section"][$v["section_id"]]["update"] == "1")? "checked": ""?>/></td>
                        <td style="width: 15%;"><input type="checkbox" value="1" name="<?= $v["section_id"]?>_delete" <?= (@$permissions["section"][$v["section_id"]]["delete"] == "1")? "checked": ""?>/></td>
                    </tr>
                <?php } ?>
            
            <tr >
                <td></td>
                <td class="action" colspan="4">
                    <input type="submit" name="<?= $action?>" value="<?= $label["Guardar"]?>" />
                    <?php if ($action == "edit" && ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                        <input type="submit" class="important" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./users.php"><?= $label["Cancelar"]?></a>
                </td>
            </tr>
            
            </table>
            
        </form>
    </div>
    <?php if (isset($user["type"]) && $user["type"] == "administrador" || !isset($user["type"])){ ?>
        <style>
        tr.client_id {
          display: none;
        }   
        tr.permi{display: none;}
        </style>
    <?php } ?> 
    
  </body>
  
</html>