<?php
/*
    Author: Marion Carambula
    Sección de salas
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = "10"; $read      = 0;

$read       = verify_permissions($sectionId, "categories.php");

//Obtener las columnas a editar/crear
$section        = "category";
$columns        = $backend->getColumnsTable($section);
$id             = $message = $error = $category =  "";

//Agregar /editar una sala
if (isset($_POST["add"]) || isset($_POST["edit"])){
   $en["client_id"]     = $_SESSION["data"]["cliente"];
   foreach ($columns as $k=>$v) {
        if (isset($input[$section]["manage"]["mandatory"])){
            //Verifico que los elementos mostrados en el formulario
            if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                // Verifico los obligatorios
                if ($input[$section]["manage"]["mandatory"] == "*" || in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])){
                    if ($_POST[$v["COLUMN_NAME"]] == "") {
                        $error =  1;
                        $missing[$v["COLUMN_NAME"]] = 1;
                    }else $en[$v["COLUMN_NAME"]] = $backend->clean($_POST[$v["COLUMN_NAME"]]);
                }else{
                    $en[$v["COLUMN_NAME"]] = $backend->clean($_POST[$v["COLUMN_NAME"]]);
                }
            }
       }
   }
   //Verificar que el nombre de la categoria no exista
   if (isset($en["name"])){
        $exists = $backend->existsCategory($en["name"], @$_POST["id"]);
        if ($exists) {
            $error = 1;
            $message = "<div class='error'>".$label["Ya existe una categoria con este nombre"]. "</div>";
        }
   }
  
   if (!$error){
       if (isset($_POST["add"])){
           $id = $backend->insertRow($section, $en);
           if ($id > 0) { 
                $_SESSION["message"] = "<div class='succ'>".$label["Categoria creada exitosamente"]. "</div>";
                header("Location: ./categories.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
           }
       }else{
           $id      = $backend->clean($_POST["id"]);
           $rid     = $backend->updateRow($section, $en, " category_id = '$id' ");
           if ($rid > 0) { 
                $_SESSION["message"] = "<div class='succ'>".$label["Categoria editada exitosamente"] ."</div>";
                header("Location: ./categories.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
           }
       }
   }else{
        if (isset($missing)) $message = "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $category    = $en;
   }
}
//Borrar Sala
if (isset($_POST["delete"])){
   $id              = $backend->clean($_POST["id"]);
   $en["active"]    = "0";
   @$backend->updateRow("category", $en, " category_id = '$id' ");
   //Eliminar expositores asociados
   $enAux["active"] = "0";
   @$backend->updateRow("exhibitor", $enAux, " category_id = '{$id}' ");
   $_SESSION["message"] = "<div class='succ'>".$label["Categoria borrada exitosamente"]. "</div>";
   header("Location: ./categories.php");
   exit();
}

//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    if ($read)      $title          = $label["Ver categoria"];
    else            $title          = $label["Editar categoria"];
    $action         = "edit";
    if (!$error)    {
        $category   = $backend->getCategory($id);
        if (!$category){
                $_SESSION["message"] = "<div class='error'>".$label["Categoria no encontrada"]  ."</div>";
                header("Location: ./categories.php");
                exit();
        }
    }
}else{
    $title = $label["Crear categoria"];
    $action = "add";
}

?>
<!DOCTYPE html>
<html lang="es">
  <head>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("categorias"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
         <?=$message ?>
        <form id="form" method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") {?>
                <input type="hidden" name="id"  value="<?=  $_GET["id"]?>" />
            <?php } ?>
            
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = $classMand = $readOnly = "" ;
                    if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                        if ($read) $readOnly = "disabled";
                        $type  = (isset($input[$section]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input[$section]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($category[$v["COLUMN_NAME"]])) ? $category[$v["COLUMN_NAME"]] : "";            
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
                       <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value ?>" <?= $classMand?>  <?= $readOnly?>/>
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ($type == "file") { ?>
                       <input type="file" name="<?= $v["COLUMN_NAME"]?>"   />
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type== "textarea") { ?>
                       <textarea name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?> <?= $readOnly?>><?= $value?></textarea>
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { 
                            $options = $input[$section]["manage"][$v["COLUMN_NAME"]]["options"];
                    ?>
                        <select name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?> <?= $readOnly?>>
                            <option value=""><?= $label["Seleccionar"]?></option>
                            <?php foreach ($options as $sk=>$sv){
                                $selected=""; if($value == $sv) $selected = "selected";
                                ?>
                                <option value="<?=$sk?>" <?= $selected?>><?= $sv?></option>
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
                    <?php if (!$read) {?>
                        <input type="submit" name="<?= $action?>" value="<?= $label["Guardar"]?>" />
                    <?php } ?>
                    <?php if ($action == "edit" && ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] != "cliente"|| $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                        <input type="button" class="important dltP" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./categories.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            
            </table>
            
        </form>
    </div>
    <?= include('common/dialog.php'); ?>
    <?= my_footer() ?>
  </body>
</html>