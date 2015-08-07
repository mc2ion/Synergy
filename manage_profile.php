<?php
/*
    Author: Marion Carambula
    Manejo de perfil
*/
include ("./common/common-include.php");

if (!isset($_GET["id"])) {header("Location: ./index.php"); exit();}

//Obtener las columnas a editar/crear
$columns = $backend->getColumnsTable("user");
unset($columns["8"]);

$label["client_id"] = "Cliente";
$id         = $message  = $error    =  $cond = "";
$user       = "";
$en         = array();
$section    = "user";


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

    //Eliminar contraseña ya que ese valor no va si se esta editando
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
    if (isset($_POST["add"]) && isset($en["email"])){
        $cond = "email = '{$en["email"]}'";
    }else if (isset($_POST["edit"]) && isset($en["email"])){
        $cond = "email = '{$en["email"]}' AND user_id != '{$_POST["id"]}'";
    }
    if ($cond != ""){
        if ($backend->selectRow("user", $cond)){
            $message    .= "<div class='error'>".$label["El correo ingresado ya existe"]. "</div>";
            $error = 1;
        }
    }

    if (!$error){

        $id             = $backend->clean($_POST["id"]);
        if ($en["client_id"] == "") unset($en["client_id"]);
        //Si estoy cambiando de "cliente" a "administrador" debo borrar los permisos
        if ($typeUser[$en["type"]] == "administrador") { $en["client_id"] = null;}
        $rid            = @$backend->updateRow($section, $en, " user_id = '$id' ");

        if ($rid > 0) {
            /* Permisologia */
            //2 casos posibles. Si se esta editando a administrador borrar posibles permisos anteriores
            if ($typeUser[$en["type"]] == "administrador"){
                //Si estoy cambiando de "cliente" a "administrador" debo borrar los permisos
                $backend->deleteRow("permission", "user_id = '$id' ");
            }else{
                $permissions = $backend->getPermission($id);
                foreach ($p["section"] as $k=>$v){
                    $v["user_id"] = $id;
                    //Si es cliente hay que editar permisos anteriormente (si los tiene)
                    if ($permissions != ""){
                        $backend->updateRow("permission", $v, " section_id = '{$v["section_id"]}' AND user_id='$id'");
                    }
                    //Sino tiene permisos asociados, crearlos
                    else{
                        $backend->insertRow("permission", $v);
                    }
                }
            }
            if ($_SESSION["app-user"]["user"]["1"]["user_id"] == $id){
                //Verificar si el usuario se está editando a sí mismo
                $_SESSION["app-user"]["user"]["1"]                  = $backend->getUserInfo($id);
                $_SESSION["app-user"]["user"]["1"]["client_name"]   = $backend->getClient($id)["name"];
                if ($_POST["type"] == "cliente")     $_SESSION["app-user"]["permission"]  = $backend->getPermission($id);
            }

            $_SESSION["message"] = "<div class='succ'>".$label["Usuario editado exitosamente"] ."</div>";
            header("Location: ./users.php");
            exit();
        }else{
            $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
        }
    }else{
        if (isset($missing)) $message    .= "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $user                             = $en;
        $permissions                      = $p;
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
    }
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
    <form id="form" method="post" enctype="multipart/form-data">
        <?php if ($action == "edit") {?>
            <input type="hidden" name="img" value="<?=  $client["logo_path"]?>" />
            <input type="hidden" name="id"  value="<?=  $_GET["id"]?>" />
        <?php } ?>
        <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                $mandatory = $classMand = "" ;
                if (!in_array($v["COLUMN_NAME"],$input[$section]["manage"]["no-show"])){
                    $type  = (isset($input[$section]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input[$section]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                    $value = (isset($user[$v["COLUMN_NAME"]])) ? $user[$v["COLUMN_NAME"]] : "";
                    if ($input[$section]["manage"]["mandatory"] == "*") {$classMand = "class='mandatory'"; $mandatory = "(<img src='images/mandatory.png' class='mandatory'>)";}
                    else if (in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])) { $classMand = "class='mandatory'"; $mandatory = "(<img src='./images/mandatory.png' class='mandatory'>)";}
                    ?>

                    <?php // Se hace la verificacion del tipo del input para cada columna ?>
                    <tr class="<?= $v["COLUMN_NAME"] ?>">
                        <td class="tdf"><?=(isset($label[$v["COLUMN_NAME"]])) ? $label[$v["COLUMN_NAME"]]: $v["COLUMN_NAME"] ?> <?=$mandatory?>:</td>
                        <?php // Tipo por defecto. Se muestra un input text ?>
                        <td colspan="4">
                            <?php
                            if ($type == ""){
                                ?>
                                <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value ?>" <?=$classMand?> />
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
                                <input type="password" name="<?= $v["COLUMN_NAME"]?>" value="<?= $value?>" autocomplete="off" <?=$classMand?>/>
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
                    <?php if ($action == "edit" && ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "administrador" || $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                        <input type="submit" class="important" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./users.php"><?= $label["Cancelar"]?></a>
                </td>
            </tr>

        </table>

    </form>
</div>
</body>
<?= my_footer() ?>
</html>