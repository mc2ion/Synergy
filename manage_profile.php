<?php
/*
    Author: Marion Carambula
    Manejo de perfil
*/
include ("./common/common-include.php");

//Obtener las columnas a editar/crear
$columns = $backend->getColumnsTable("user");


$id         = $message  = $error    =  $cond = "";
$user       = "";
$en         = array();
$section    = "profile";
$userType   = $typeUser[$_SESSION["app-user"]["user"][1]["type"]];


//Agregar nuevo usuario
if (isset($_POST["edit"])){
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
                    if ($userType == "cliente" && $v["COLUMN_NAME"] != "email" && $v["COLUMN_NAME"] != "password" ){
                        $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                    }else if ($userType != "cliente" && $v["COLUMN_NAME"] != "password" ){
                        $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                    }
                }
            }
        }
    }

    //Verificar que el correo no exista ya.
    /*if (isset($_POST["add"]) && isset($en["email"])){
        $cond = "email = '{$en["email"]}'";
    }else if (isset($_POST["edit"]) && isset($en["email"])){
        $cond = "email = '{$en["email"]}' AND user_id != '{$_POST["id"]}'";
    }
    if ($cond != ""){
        if ($backend->selectRow("user", $cond)){
            $message    .= "<div class='error'>".$label["El correo ingresado ya existe"]. "</div>";
            $error = 1;
        }
    }*/

    if (!$error){
        $id             = $backend->clean($_POST["id"]);
        $rid            = @$backend->updateRow("user", $en, " user_id = '$id' ");
        if ($rid > 0) {
            $_SESSION["app-user"]["user"]["1"]                  = $backend->getUserInfo($id);
            $_SESSION["app-user"]["user"]["1"]["client_name"]   = @$backend->getClient($id)["name"];
            $_SESSION["app-user"]["user"]["1"]["logo_path"]     = @$backend->getClient($id)["logo_path"];
            $message = "<div class='succ'>".$label["Perfil de usuario editado exitosamente"] ."</div>";
        }else{
            $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
        }
    }else{
        if (isset($missing)) $message    .= "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $user                             = $en;
    }
}


//Si el parametro id esta definido, estamos editando la entrada
if (isset($_SESSION["app-user"]["user"][1])){
    $id             = $backend->clean($_SESSION["app-user"]["user"]["1"]["user_id"]);
    $title          = $label["Editar perfil de usuario"];
    $action         = "edit";
    if (!$error)  {
        $user         = $backend->getUserInfo($id);
        if (!$user){
            //$_SESSION["message"] = "<div class='error'>".$label["Usuario no encontrado"] ."</div>";
            header("Location: ./index.php");
            exit();
        }
    }
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
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?= my_header()?>
</head>
<body>
<?= menu(); ?>
<div class="content">
    <div class="title-manage"><?= $title?></div>
    <?= $message ?>
    <div style="clear: both;"></div>
    <form id="form" method="post" enctype="multipart/form-data">
        <?php if ($action == "edit") {?>
            <input type="hidden" name="img" value="<?=  $client["logo_path"]?>" />
            <input type="hidden" name="id"  value="<?= $id ?>" />
        <?php } ?>
        <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                $mandatory = $classMand = "" ;
                if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                    $type  = (isset($input[$section]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input[$section]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                    $value = (isset($user[$v["COLUMN_NAME"]])) ? $user[$v["COLUMN_NAME"]] : "";
                    if ($input[$section]["manage"]["mandatory"] == "*") {$classMand = "class='mandatory'"; $mandatory = "(<img src='images/mandatory.png' class='mandatory'>)";}
                    else if (in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])) { $classMand = "class='mandatory'"; $mandatory = "(<img src='./images/mandatory.png' class='mandatory'>)";}
                    $disabled = "";
                    if ($userType == "cliente"){
                        $disabled = ""; if ($v["COLUMN_NAME"] == "email" || $v["COLUMN_NAME"] == "password" ) $disabled = "disabled";
                    }else{
                        if ($v["COLUMN_NAME"] == "password" ) $disabled = "disabled";
                    }
                    ?>

                    <?php // Se hace la verificacion del tipo del input para cada columna ?>
                    <tr class="<?= $v["COLUMN_NAME"] ?>">
                        <td class="tdf"><?=(isset($label[$v["COLUMN_NAME"]])) ? $label[$v["COLUMN_NAME"]]: $v["COLUMN_NAME"] ?> <?=$mandatory?>:</td>
                        <?php // Tipo por defecto. Se muestra un input text ?>
                        <td colspan="4">
                            <?php
                            if ($type == ""){
                                ?>
                                <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value ?>" <?=$classMand?> <?= $disabled?> />
                                <?php // Tipo File. Se muestra un input file ?>
                            <?php } else if ($type == "file") {
                                ?>

                                <?php if ($value != "") {?>
                                    <img class='manage-image' src='./<?=$value?>'/>
                                <?php } ?>
                                <input type="file" name="<?= $v["COLUMN_NAME"]?>" <?=$classMand?> />
                                <img src="./images/info.png" class="information" alt="Información" />
                                <div class="image_format"><?= $imageType?>. <?= $imageSize?>. <?= $imageW?></div>
                                <?php // Tipo textarea. Se muestra un textarea ?>
                            <?php } else if ($type== "textarea") { ?>
                                <textarea name="<?= $v["COLUMN_NAME"]?>" <?=$classMand?>><?= $value?></textarea>
                                <?php // Tipo password. ?>
                            <?php } else if ($type== "password") { ?>
                                <input type="password" name="<?= $v["COLUMN_NAME"]?>" value="**********" autocomplete="off" <?=$classMand?> <?= $disabled?>/>
                                <a class="pass add" href="./manage_password.php"><?= $label["Cambiar contraseña"]?></a>
                                <?php // Tipo select. Se muestra un select con sus opciones ?>
                            <?php } else if ($type == "select") { ?>
                                <?php if ($v["COLUMN_NAME"] == "client_id"){?>
                                    <select name="<?= $v["COLUMN_NAME"]?>" <?=$classMand ?> >
                                        <option value=""><?= $label["Seleccionar"]?></option>
                                        <?php foreach ($clients as $sk=>$sv){
                                            $sel = ""; if ($sv["client_id"] == $value) $sel = "selected";
                                            ?>
                                            <option value="<?=$sv["client_id"]?>" <?= $sel?> ><?= $sv["name"]?></option>
                                        <?php }?>
                                    </select>
                                <?php }else{
                                    $options = $input[$section]["manage"][$v["COLUMN_NAME"]]["options"]; ?>
                                    <select name="<?= $v["COLUMN_NAME"]?>" <?=$classMand ?>>
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
            <tr >
                <td></td>
                <td class="action" colspan="4">
                    <input type="submit" name="<?= $action?>" value="<?= $label["Guardar"]?>" />
                </td>
            </tr>
        </table>
    </form>
</div>
</body>
<?= my_footer() ?>
</html>