<?php
/*
    Author: Marion Carambula
    Sección de sesiones
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
$sectionId = "4";
$event     = $_SESSION["data"]["evento"];
$client    = $_SESSION["data"]["cliente"];
$read      = 0;

$read       = verify_permissions($sectionId, "sessions.php");

//Obtener las columnas a editar/crear
$section        = "session"; 
$columns        = $backend->getColumnsTable($section);
$rooms          = $backend->getRoomList($_SESSION["data"]["evento"], array(), "1");
$id             = $message  = $error    = "";
$speakers       = array("1"=>"");
$exhibitors     = array("1"=>"");
$listSpeakers   = $backend->getSpeakerList($_SESSION["data"]["evento"]);
$listExhibitors = $backend->getExhibitorList($_SESSION["data"]["evento"]);



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
   //Guardar en bd la sesion
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
   
   $errorInterno = 0;
   //Speakers asociados
    foreach($_POST["speaker"] as $k=>$v){
        $in[$k]["speaker_id"] = $v;
        $speakers[$k+1]["speaker_id"] = $v;
        if ($v == ""){ $errorInterno = 1;}
    }
    
    //Expositores asociados
    foreach($_POST["exhibitor"] as $k=>$v){
        $in[$k]["exhibitor_id"] = $v;
        $exhibitors[$k+1]["exhibitor_id"] = $v;
        if ($v == ""){ $errorInterno = 1;}
    }
      
    if ($errorInterno) {$error = "1";  $message = "<div class='error'>Debe agregar al menos un speaker o expositor</div>"; }
    
    //Debo darle formato al time.
    $timeEnd = $timeIni = "";
    if (!$error){
        if ($en["time_ini"] != ""){
            $t= str_replace(' ', '', $en["time_ini"]);
            $h = explode(":", $t);
            $ho = $h[0].":".$h[1]. " " .$h[2];
            $date       = date("H:i",strtotime($ho));
            $timeIni    = $date;
        }

        if ($en["time_end"] != ""){
            $t= str_replace(' ', '', $en["time_end"]);
            $h = explode(":", $t);
            $ho = $h[0].":".$h[1]. " " .$h[2];
            $date       = date("H:i",strtotime($ho));
            $timeEnd    = $date;
        }
        if ($timeIni == $timeEnd ){ $error= 1; $message = "<div class='error'>".$label["La hora de inicio y fin proporcionada no pueden ser iguales"]. "</div>";}
        if ($timeIni > $timeEnd ) { $error= 1; $message = "<div class='error'>".$label["La hora de inicio debe ser menor a la hora fin"]. "</div>";}

        $sId = @$_POST["id"]; $x = "";
        if ($sId != "") $x = "AND session_id != '$sId'";
        //Verificar que no haya otra sesion en esa fecha, hora y sala si estoy creado una nueva sala
        $extra = "AND date = '{$en["date"]}' AND
                ((time_ini <= '$timeIni' AND time_end > '$timeIni' AND time_end <= '$timeEnd')
                    OR 	(time_ini >= '$timeIni' AND time_end >= '$timeEnd' AND time_ini < '$timeEnd')
                    OR 	(time_ini >= '$timeIni' AND time_end <= '$timeEnd')
                    OR    (time_ini <= '$timeIni' AND time_end >= '$timeEnd')
                ) $x";
        $sesions = $backend->getSessionListByRoom($en["room_id"], $_SESSION["data"]["evento"], $extra );
        if (count($sesions) == "1") {$error = "1"; $message = "<div class='error'>".$label["Disculpe, ya existe una sesión creada para dicha sala, fecha y hora"]. "</div>";}

    }


    if (!$error){
        $en["time_ini"] = $timeIni;
        $en["time_end"]  = $timeEnd;

        //Agregar http en caso necesario
        $en["link"] = addhttp($en["link"]);

        if (isset($_POST["add"])){
            $id = $backend->insertRow($section, $en);
            foreach($in as $k=>$v){
                unset($in);
                $sp = @$v["speaker_id"];
                $xh = @$v["exhibitor_id"];
                if ($sp != ""){
                    $in["session_id"]   = $id;
                    $in["speaker_id"]   = $sp;
                    $backend->insertRow("session_speaker", $in);
                }
                unset($in);
                if ($xh != ""){
                    $in["session_id"]       = $id;
                    $in["exhibitor_id"]     = $xh;
                    $backend->insertRow("session_speaker", $in);
                }
            }
           if ($id > 0) { 
                unset($_SESSION["session"]["image_path"]);
                $_SESSION["message"] = "<div class='succ'>".$label["Sesión creada exitosamente"]. "</div>";
                header("Location: ./sessions.php");
                exit();
           }else{
                $message = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
           }
       }else{
           $sid            = $backend->clean($_POST["id"]);
           $id             = $backend->updateRow($section, $en, " session_id = '$sid' ");
           
           $speakers       = $backend->getSpeakers($sid);
           $exhibitors     = $backend->getExhibitors($sid);
           $activeSpk      = $_POST["activeSpk"];
           foreach($activeSpk as $k=>$v){
                 $spk_id[$v] = $v;
           }
           $activeExh      = $_POST["activeExh"];
           foreach($activeExh as $k=>$v){
                 $exh_id[$v] = $v;
           }
           
           //2. Eliminadas
           foreach($speakers as $k=>$v){
                if (!in_array($v["session_speaker_id"], $spk_id)) {
                    $backend->deleteRow("session_speaker", " session_speaker_id = '{$v["session_speaker_id"]}' ");
                }
           }
           foreach($exhibitors as $k=>$v){
                if (!in_array($v["session_speaker_id"], $exh_id)) {
                    $backend->deleteRow("session_speaker", " session_speaker_id = '{$v["session_speaker_id"]}' ");
                    echo "borrar " . $v["session_speaker_id"];
                }
           }
           
           //3. Modificadas  y creadas 
           foreach($_POST["speaker"] as $k=>$v){
                unset($inA);
                $inA["session_id"]   = $sid;
                $inA["speaker_id"]   = $v;
                $active              = $_POST["activeSpk"][$k];
                //Modificadas
                if ($active != ""){
                    $backend->updateRow("session_speaker", $inA, " session_speaker_id = '{$active}' ");
                }
                //Creadas
                else{
                   $backend->insertRow("session_speaker", $inA);
                }
           }
           
           //3. Modificadas  y creadas 
           foreach($_POST["exhibitor"] as $k=>$v){
                unset($inA);
                $inA["session_id"]      = $sid;
                $inA["exhibitor_id"]    = $v;
                $active                 = $_POST["activeExh"][$k];
                //Modificadas
                if ($active != ""){
                    $backend->updateRow("session_speaker", $inA, " session_speaker_id = '{$active}' ");
                }
                //Creadas
                else{
                   $backend->insertRow("session_speaker", $inA);
                }
           }
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
   $idS             = $backend->clean($_POST["id"]);
   $en["active"]    = "0";
   //1. Borrar entrada
   $id = $backend->updateRow($section, $en, " session_id = '$idS' ");
   
   //2. Borrar imagen asociada
   @unlink($_POST["img"]);
   
   //3. Borrar speakers/expositores asociados
   $backend->deleteRow("session_speaker", " session_id = '$idS' ");
   
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
    if ($read)      $title          = $label["Ver Sesión"];
    else            $title          = $label["Editar Sesión"];
    $action         = "edit";
    

    //Obtener informacion de los speakers asociados
    $speakers    = $backend->getSpeakers($id);
    if (empty($speakers)) $speakers = array("1"=>"");
    $exhibitors  = $backend->getExhibitors($id);
    if (empty($exhibitors)) $exhibitors = array("1"=>"");
    
    if (!$error)    {
        $session        = $backend->getSession($_GET["id"]);
        if (!$session){
            $_SESSION["message"] = "<div class='error'>".$label["Sesión no encontrada"]  ."</div>";
            header("Location: ./sessions.php");
            exit();
        }
    }
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
$extra     = "(Cuadrada)";
if (isset($general["$section"]["image_type"]) &&  $general["$section"]["image_type"] == "rectangle") $extra = "(Rectangular)";
$imageSize = "Tamaño máximo permitido: <b> ".$general[$section]["image_width"]."x".$general[$section]["image_height"] . " $extra </b>" ;
$s = $general[$section]["image_size"] / 1000;
$imageW = "Peso máximo permitido: <b>". $s ."KB</b>" ;

//Evento asociado
$eventInfo =  $backend->getEvent($event);
$startDate = $eventInfo["date_ini"];
$endDate   = $eventInfo["date_end"];

?>

<!DOCTYPE html>
<html lang="es">
  <head>
      <script>
          startDate = '<?= $startDate?>';
          endDate   = '<?= $endDate?>';
      </script>
     <?= my_header()?>
  </head>
  <body>
    <?= menu("sesiones"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
        <?=$message ?>
        <form id="form" method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") { ?>
                <input type="hidden" name="id"  value="<?=$_GET["id"]?>" />
                <input type="hidden" name="img" value="<?=$session["image_path"]?>" />
            <?php } ?>
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = $classMand =  $readOnly = "" ;
                    if (!in_array($v["COLUMN_NAME"],$input["session"]["manage"]["no-show"])){
                        if ($read) $readOnly = "disabled";
                        $type  = (isset($input["session"]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input["session"]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value = (isset($session[$v["COLUMN_NAME"]])) ? $session[$v["COLUMN_NAME"]] : "";
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
                        <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?= $value ?>" <?= $classMand ?> <?= $readOnly ?>/>
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ( $type == "file") { ?>
                        <?php if ($value != "") {?>
                            <img class='manage-image' src='./<?=$value?>'/>
                        <?php } ?>
                        <?php if (!$read) { ?>
                            <img src="./images/info.png" class="information" alt="Información" />
                            <input type="file" name="<?= $v["COLUMN_NAME"]?>" />
                            <div class="image_format"><?= $imageType?>. <?= $imageSize?>. <?= $imageW?></div>
                        <?php } ?>
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type == "textarea") { ?>
                        <textarea name="<?= $v["COLUMN_NAME"]?>" <?= $classMand ?> <?= $readOnly?>><?=$value?></textarea>
                 <?php // Tipo date. Se muestra un text pero especial para tener el date picker ?>
                 <?php } else if ($type == "time") { ?>
                       <input type="text" class="timepicker <?=substr($classMand,7, 9) ?>" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off"  <?= $readOnly?>/>
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 
                 <?php } else if ($type == "date") { ?>
                        <input type="text" class="datepicker <?=substr($classMand,7, 9) ?>" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off" <?= $readOnly?> />
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { ?>
                            <?php if ($v["COLUMN_NAME"] == "room_id"){?>
                                <select name="<?= $v["COLUMN_NAME"]?>" <?= $classMand ?> <?= $readOnly?>>
                                <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($rooms as $sk=>$sv){
                                     $sel = ""; if ($sv["room_id"] == $value) $sel = "selected";
                                    ?>
                                    <option value="<?=$sv["room_id"]?>" <?= $sel?> ><?= $sv["name"]?></option>
                                <?php }?>
                                </select>
                            <?php }else{ ?>
                                <select name="<?= $v["COLUMN_NAME"]?>" <?= $classMand ?> <?= $readOnly?>>
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
            <?php $readOnly = ""; if ($read) $readOnly = "disabled"; ?>
            <tr class="organizers-name">
                <td colspan="2" class="options-title"><?= $label["Presentadores"]?></td>
            </tr>
            <tr>
                <td class="speakers-td" colspan="2">
                    <?php if (!$read) {?>
                        <div class="add-spk"><a href="javascript:void(0)"><?= $label["Agregar nueva"]?></a></div>
                    <?php } ?>
                    <?php foreach ((array)$speakers as $mk=>$mv){
                    ?>
                        <div class="speaker">
                            <div class="opt-desc">
                                <div class="label"><?= $label["Nombre"]?> <?=$mandatory?>:</div>
                                <div class="value">
                                    <select name="speaker[]" <?= $readOnly?> > 
                                        <option value=""><?= $label["Seleccionar"]?></option>
                                        <?php foreach ($listSpeakers as $x=>$y){ 
                                            $selected = ""; if ($y["speaker_id"] == @$mv["speaker_id"]) $selected = "selected";
                                            ?>
                                            <option value="<?= $y["speaker_id"]?>" <?=$selected?>><?= $y["name"]?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if (!$read) {?>
                            <div class="delete-spk i<?= ($mk) - 1 ?>"><a href="javascript:void(0)"><?= $label["Eliminar"]?></a></div>
                            <?php } ?>
                            <div class="hidden"><input type="hidden" name="activeSpk[]" value="<?= @$mv["session_speaker_id"]?>"/></div>
                        </div>
                    <?php } ?>
                    <div class="missing-error">
                    <?php if (isset($missing["options"])) { ?>
                                <?= $label["Este campo es obligatorio"]?>
                    <?php } ?>
                    </div>
               </td>
            </tr>
            <tr class="organizers-name">
                <td colspan="2" class="options-title"><?= $label["Expositores"]?></td>
            </tr>
            <tr>
                <td class="exhibitors-td" colspan="2">
                    <?php if (!$read) {?>
                        <div class="add-exh"><a href="javascript:void(0)"><?= $label["Agregar nueva"]?></a></div>
                    <?php } ?>
                    <?php foreach ((array)$exhibitors as $mk=>$mv){
                    ?>
                        <div class="exhibitor">
                            <div class="opt-desc">
                                <div class="label"><?= $label["Empresa"]?> <?=$mandatory?>:</div>
                                <div class="value">
                                    <select name="exhibitor[]" <?= $readOnly?> > 
                                        <option value=""><?= $label["Seleccionar"]?></option>
                                        <?php foreach ($listExhibitors as $x=>$y){ 
                                           $selected = ""; if ($y["exhibitor_id"] == $mv["exhibitor_id"]) $selected = "selected";
                                        ?>
                                            <option value="<?= $y["exhibitor_id"]?>" <?= $selected?>><?= $y["company_name"]?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if (!$read) {?>
                            <div class="delete-exh i<?= $mk - 1 ?>"><a href="javascript:void(0)"><?= $label["Eliminar"]?></a></div>
                            <?php } ?>
                            <div class="hidden"><input type="hidden" name="activeExh[]" value="<?= @$mv["session_speaker_id"]?>"/></div>
                        </div>
                    <?php } ?>
                    <div class="missing-error">
                    <?php if (isset($missing["options"])) { ?>
                                <?= $label["Este campo es obligatorio"]?>
                    <?php } ?>
                    </div>
               </td>
            </tr>
            <tr>
                <td></td>
                <td class="action">
                    <?php if (!$read){ ?>
                        <input type="submit" name="<?= $action?>" value="<?= $label["Guardar"]?>" />
                    <?php } ?>
                    <?php if ($action == "edit" && ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] != "cliente"|| $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){?>
                        <input type="button" class="important dltP" name="delete" value="<?= $label["Borrar"]?>" />
                    <?php } ?>
                    <a href="./sessions.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            
            </table>
            
        </form>
    </div>
     <?= include('common/dialog.php'); ?>
     <?= my_footer() ?>
  </body>
</html>