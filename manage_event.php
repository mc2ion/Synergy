<?php
/*
    Author: Marion Carambula
    Sección de usuarios
*/
include ("./common/common-include.php");

//Verificar que el usuario tiene  permisos
$sectionId = 3;
if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "cliente" && $_SESSION["app-user"]["permission"][$sectionId]["read"] == "0"){ header("Location: ./index.php"); exit();}


//Obtener las columnas a editar/crear
$section    = "event";
$columns    = $backend->getColumnsTable($section);
$id         =  $message      =  $error   =  $alert    = "";
//Creamos un array vacio para que aparezca un box inicial
$socials    = array("0"=>array("type"=>"", "value"=>""));
$organizers = array("0"=>array("name"=>"", "description"=>""));
$client     = $_SESSION["data"]["cliente"];


//Agregar nuevo evento
if (isset($_POST["add"]) ||  isset($_POST["edit"])){
    $en["client_id"]                = $client;
    $event                          = @$backend->getEvent($_POST["id"]);
    if (isset($_SESSION["event"]["map_path"])) $en["map_path"] = $_SESSION["event"]["map_path"];
    else if ($event)  $en["map_path"]    = $event["map_path"] ;
   
   // Verificar únicamente que la imagen no sea vacía, en el caso en el que no tenga ninguna imagen asociada
   if (isset($_FILES["map_path"]) && $_FILES["map_path"]["name"]  != "" ){
        $path = $general[$section]["image_folder"].uniqid($client)."_".basename($_FILES["map_path"]["name"]);
        $resultUpload = $backend->uploadImage($path,
                                            "map_path", 
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
            $_SESSION["event"]["map_path"] =  $path;
            $en["map_path"]       =  $path ;
        }
   }else if ($event["map_path"] == "" && !isset($_SESSION["event"]["map_path"])){
        $error = 1;
        $missing["map_path"] = 1;
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
                    if ($v["COLUMN_NAME"] != "social_networks" && $v["COLUMN_NAME"] != "organizers"){
                        if ($v["COLUMN_NAME"] != "map_path" && $_POST[$v["COLUMN_NAME"]] == "") {
                            $error =  1;
                            $missing[$v["COLUMN_NAME"]] = 1;
                        }else{
                            if ($v["COLUMN_NAME"] != "map_path" ){
                                $en[$v["COLUMN_NAME"]] = $backend->clean($_POST[$v["COLUMN_NAME"]]);
                            }
                        }
                    }else{
                        //Caso de las redes sociales manejado de forma aparte
                        if ($v["COLUMN_NAME"] == "social_networks"){
                            if ($_POST["network"][0] == ""){
                                $error  = 1;
                                $missing["social_networks"] = 1;
                            }else{
                                foreach($_POST["network"] as $k=>$v){
                                    $network[$k]["type"]  =  $v;
                                    $network[$k]["value"] =  $_POST["value"][$k];
                                    $network[$k]["title"] =  $_POST["title"][$k];
                                    if ($network[$k]["value"] == "" || $network[$k]["type"]  == "" || $network[$k]["title"]  == ""){
                                        $error = 1;
                                        $message = "<div class='error'>{$label["Verifique la información de las redes sociales"]}</div>";
                                    }
                                }
                            }
                        }
                         //Caso de los organizadores manejado de forma aparte
                        else if ($v["COLUMN_NAME"] == "organizers"){
                            if ($_POST["name_organizer"][0] == ""){
                                $error  = 1;
                                $missing["organizers"] = 1;
                            }else{
                                foreach($_POST["name_organizer"] as $k=>$v){
                                    $organizers[$k]["name"]          = $v;
                                    $organizers[$k]["description"]   = $_POST["desc_organizer"][$k];
                                    $organizers[$k]["url"]           = $_POST["url_organizer"][$k];
                                    if ($organizers[$k]["name"] == "" || $organizers[$k]["description"] == "" || $organizers[$k]["url"] == ""){
                                        $error = 1;
                                        $message = "<div class='error'>{$label["Verifique la información de los organizadores"]}</div>";
                                    }
                                }
                            }
                        }
                    }
                }else{
                    if ($v["COLUMN_NAME"] != "map_path" && $v["COLUMN_NAME"] != "social_networks" && $v["COLUMN_NAME"] != "organizers"){
                        $en[$v["COLUMN_NAME"]] = $_POST[$v["COLUMN_NAME"]];
                    }else{ 
                        if ($v["COLUMN_NAME"] == "social_networks"){
                            foreach($_POST["network"] as $k=>$v){
                                $network[$k]["type"]  = $v;
                                $network[$k]["value"] =  $_POST["value"][$k];
                                $network[$k]["title"] =  $_POST["title"][$k];
                                $errorT = $errorV = $errorTT = "";
                                if ($network[$k]["value"] == "") { $errorV = 1;}
                                if ($network[$k]["type"]  == "") { $errorT = 1;}
                                if ($network[$k]["title"] == "") { $errorTT = 1;}
                                if ($errorT != $errorV && $errorV!= $errorTT){
                                    $error = 1;
                                    $message = "<div class='error'> {$label["Verifique la información de las redes sociales"]}</div>";
                                }
                            }
                        }
                         //Caso de los organizadores manejado de forma aparte
                        else if ($v["COLUMN_NAME"] == "organizers"){
                            foreach($_POST["name_organizer"] as $k=>$v){
                                $organizers[$k]["name"]          = $v;
                                $organizers[$k]["description"]   = $_POST["desc_organizer"][$k];
                                $organizers[$k]["url"]           = $_POST["url_organizer"][$k];
                                $errorN = $errorD = $errorU = "";
                                if ($organizers[$k]["name"] == ""){ $errorN = 1;}
                                if ($organizers[$k]["description"] == ""){ $errorD = 1;}
                                if ($errorN != $errorD && $errorD != $errorU){
                                    $error = 1;
                                    $message = "<div class='error'>{$label["Verifique la información de los organizadores"]}</div>";
                                }
                            }
                        }
                    }
                }
            }
       }
   }
   $en["social_networks"] = json_encode($network);
   $en["organizers"]      = json_encode($organizers);
    if ($_POST["date_end"] == "") $en["date_end"] = $_POST["date_ini"];
    if (isset($en["date_ini"])){
        if ($en["date_ini"] > $en["date_end"] ){
            $error = 1;
            $message = "<div class='error'>".$label["La fecha de inicio no puede ser mayor que la fecha de fin"]. "</div>";
       }
   }

    //Pais
    $en["country"]      = $country[$en["country"]];

    if (!$error){
       //Agregar el codigo del pais
       $en["phone"] = "(".$_POST["phone_code"] . ")". $en["phone"];


       if (isset($_POST["add"])){
           $id = $backend->insertRow("event", $en);
           if ($id > 0) { 
                unset($_SESSION["event"]["map_path"]);
                $_SESSION["message"] = "<div class='succ'>".$label["Evento creado exitosamente"]. "</div>";
                header("Location: ./events.php");
           }else{
                $_SESSION["message"] = "<div class='error'>".$label["Hubo un problema con la creación"]. "</div>";
           }
       }else{
            $idE             = $backend->clean($_POST["id"]);
            $id              = $backend->updateRow($section, $en, " event_id = '$idE' ");

           //Verificar que las sesiones asociadas no queden por fuera de la fecha
           $sessions = $backend->getSessionList($idE,array(), "1", "1");
           foreach($sessions as $k=>$v){
               if ($v["date"] < $en["date_ini"] || $v["date"] > $en["date_end"]) {
                   if ($en["date_ini"] == $en["date_end"]) {
                        //Modificar directamente
                       $in["date"] = $en["date_ini"];
                       $backend->updateRow("session", $in, "session_id = '{$v["session_id"]}'");
                   }else{
                       $alert .= "ID: {$v["session_id"]} -  Título: {$v["title"]} <br/>";
                   }
               }
           }
           if ($alert == ""){
              if ($id > 0) {
                    unset($_SESSION["event"]["map_path"]);
                    $_SESSION["message"] = "<div class='succ'>".$label["Evento editado exitosamente"] ."</div>";
                    header("Location: ./events.php");
               }else{
                    $message = "<div class='error'>".$label["Hubo un problema con la edición"]. "</div>";
               }
           }else{
               if ($id > 0) {
                   unset($_SESSION["event"]["map_path"]);
                   $_SESSION["message"] = "<div class='succ'>".$label["Evento editado exitosamente"] ."</div>";
               }
           }
       
       }
       unset($_SESSION["map_path"]);
   }else{
        if (isset($missing)) $message    .= "<div class='error'>".$label["Por favor ingrese todos los datos requeridos"]. "</div>";
        $event      = $en;
        $event["phone_code"]    = $_POST["phone_code"];
        $event["phone"]         = $_POST["phone"];
        $socials    = json_decode($event["social_networks"], true);
        $organizers = json_decode($event["organizers"], true);
   }
}
//Borrar Evento
if (isset($_POST["delete"])){
    //Verificar si el evento a borrar es el que esta escogido actualmente
    //1. Borrar entrada
    $id              = $backend->clean($_POST["id"]);
    $en["active"]    = "0";
    $id = $backend->updateRow($section, $en, " event_id = '$id' ");
   
    //2. Borrar imagen  asociada
    @unlink($_POST["img"]);
  
    //3. Borrar dato sobre el evento actual si se esta borrando ese
    if ($_SESSION["data"]["evento"] == $_POST["id"]) unset($_SESSION["data"]["evento"]);
    if ($id > 0) { 
        $_SESSION["message"] = "<div class='succ'>".$label["Evento borrado exitosamente"] ."</div>";
        header("Location: ./events.php");
    }else{
        $message = "<div class='error'>".$label["Hubo un problema con el borrado"]. "</div>";
    }
    
}

/** Fin acciones **/

//Si el parametro id esta definido, estamos editando la entrada
if (isset($_GET["id"]) && $_GET["id"] > 0 ){
    $id             = $backend->clean($_GET["id"]);
    $title          = $label["Editar Evento"];
    $action         = "edit";
    if (!$error){
        $event          = $backend->getEvent($id);
        if (!$event){
            $_SESSION["message"] = "<div class='error'>".$label["Evento no encontrado"] ."</div>";
            header("Location: ./events.php");
            exit();
        }
        $limiter = explode(")", $event["phone"]);
        if (count($limiter) > 1){
            $event["phone_code"]    = substr($limiter[0],1);
            $event["phone"]         = $limiter[1];
        }else{
            $event["phone"]         = $limiter[0];
        }
        //Redes sociales del evento
        $socials = json_decode($event["social_networks"], true);
        if (!$socials) $socials = array("0"=>array("type"=>"", "value"=>"", "title"=>""));
        //Organizadores
        $organizers = json_decode($event["organizers"], true);
        if (!$organizers) $organizers = array("0"=>array("name"=>"", "description"=>"", "url"=>""));
    }
}else{
    $title = $label["Crear Evento"];
    $action = "add";
}


/* Armar mensaje para los tipos permitidos*/
$imageTypeAux = "";
foreach ($general[$section]["image_format"] as $k){
    $imageTypeAux .= "," . $k ;
}
$imageType = $label["Formatos permitidos:"]."<b> ". substr($imageTypeAux, 1). "</b>" ;
if (isset($general["$section"]["image_type"]) &&  $general["$section"]["image_type"] == "rectangle") $extra = "(Rectangular)";
$imageSize = "Tamaño máximo permitido: <b> ".$general[$section]["image_width"]."x".$general[$section]["image_height"] . "</b>" ;
$s = $general[$section]["image_size"] / 1000;
$imageW = "Peso máximo permitido: <b>". $s ."KB</b>" ;


?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
     <script>
         alert = '<?= $alert?>';
          $(function() {
            $( "#dialog-confirm" ).dialog({
                  autoOpen: false,
                  resizable: false,
                  height:160,
                  modal: true,
                  buttons: {
                  "Si": function() {
                   $( this ).dialog( "close" );
                   $('<input />').attr('type', 'hidden')
                  .attr('name', "delete")
                  .attr('value', "1")
                  .appendTo('#form');
                   $("#form").submit();
                 },
                 "Cancelar": function() {
                  $( this ).dialog( "close" );
                }
              }
            });

              $( "#dialog" ).dialog({
                  autoOpen: false,
                  resizable: false,
                  height:260,
                  modal: true,
                  buttons: {
                      "Continuar": function() {
                          $( this ).dialog( "close" );
                          window.location.href = "./events.php";
                      }
                  }
              });
            
            $(".dltP").on("click", function(e) {
                e.preventDefault();
                $("#dialog-confirm").dialog("open");
            });

            if (alert){
                $(".sessiones").html(alert);
                $("#dialog").dialog("open");
            }
          });
    </script>
  </head>
  <body>
    <?= menu("eventos"); ?>
    <div class="content">
        <div class="title-manage"><?= $title?></div>
        <?=$message ?>
        <form id="form" method="post" enctype="multipart/form-data">
            <?php if ($action == "edit") {?>
            <input type="hidden" name="img" value="<?=  $event["map_path"]?>" />
            <input type="hidden" name="id"  value="<?=  $_GET["id"]?>" />
            <?php } ?>
            <table class="manage-content">
            <?php foreach ($columns as $k=>$v) {
                    $mandatory = ""; $classMand = "";
                    if (!in_array($v["COLUMN_NAME"],$input["event"]["manage"]["no-show"])){
                        $type       = (isset($input["event"]["manage"][$v["COLUMN_NAME"]]["type"])) ? $input["event"]["manage"][$v["COLUMN_NAME"]]["type"] :  "";
                        $value      = (isset($event[$v["COLUMN_NAME"]])) ? $event[$v["COLUMN_NAME"]] : "";
                        if ($input[$section]["manage"]["mandatory"] == "*") {$classMand = "class='mandatory'"; $mandatory = "(<img src='images/mandatory.png' class='mandatory'>)";}
                        else if (in_array($v["COLUMN_NAME"], $input[$section]["manage"]["mandatory"])) { $classMand = "class='mandatory'"; $mandatory = "(<img src='./images/mandatory.png' class='mandatory'>)";}        
              ?>
                <?php // Se hace la verificacion del tipo del input para cada columna ?>
                    <tr class="tr_<?=$v["COLUMN_NAME"]?>">
                        <td class="tdf"><?=(isset($label[$v["COLUMN_NAME"]])) ? $label[$v["COLUMN_NAME"]]: $v["COLUMN_NAME"]?>  <?=$mandatory?>:</td>
                        <td class="<?=$v["COLUMN_NAME"]?>-td">
                       
                <?php // Tipo por defecto. Se muestra un input text?>
                <?php   
                        if ($type == ""){ 
                ?>
                    <?php if ($v["COLUMN_NAME"] == "phone"){
                            $valueCode = (isset($event[$v["COLUMN_NAME"]."_code"])) ? $event[$v["COLUMN_NAME"]."_code"] : "";
                    ?>
                        <input type="text" name="<?= $v["COLUMN_NAME"]?>_code" value="<?= $valueCode ?>" class="code" readOnly="true" />
                        <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value ?>" class="phone" />
                    <?php  }else{ ?>
                        <input type="text" name="<?= $v["COLUMN_NAME"]?>" value="<?=$value ?>" <?= $classMand?>  />
                    <?php } ?>
                 <?php // Tipo File. Se muestra un input file ?>
                 <?php } else if ( $type == "file") { ?>
                        <?php if ($value != "") {?>
                            <img class='manage-image' src='./<?=$value?>'/>
                        <?php } ?>
                        <input type="file" name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?>/>
                        <img src="./images/info.png" class="information" alt="Información" />
                        <div class="image_format"><?= $imageType?>. <?= $imageSize?>. <?= $imageW?></div>
                 <?php // Tipo textarea. Se muestra un textarea ?>
                 <?php } else if ($type == "textarea") { ?>
                        <textarea name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?>><?=$value?></textarea>
                 <?php // Tipo date. Se muestra un text pero especial para tener el date picker ?>
                 <?php } else if ($type == "date") { ?>
                        <input type="text"  class="datepicker <?=substr($classMand,7, 9) ?> " name="<?= $v["COLUMN_NAME"]?>" value="<?=$value?>" autocomplete="off" />
                 <?php // Tipo select. Se muestra un select con sus opciones ?>
                 <?php } else if ($type == "select") { 
                        $options = $input["event"]["manage"][$v["COLUMN_NAME"]]["options"];
                    ?>
                            <select name="<?= $v["COLUMN_NAME"]?>" <?= $classMand?>>
                                <option value=""><?= $label["Seleccionar"]?></option>
                                <?php foreach ($options as $sk=>$sv){
                                    $selected=""; if($value == $sv) $selected = "selected";
                                    ?>
                                    <option value="<?=$sk?>" <?= $selected?>><?= $sv?></option>
                                <?php }?>
                            </select>
                 <?php }else if ($type == "special" && $v["COLUMN_NAME"] == "social_networks"){
                            $options = $input["event"]["manage"]["social_networks"]["options"];
                            
                    ?>
                            <div class="add-e"><a href="javascript:void(0)"><?= $label["Agregar nueva"]?></a></div>
                            <?php foreach ($socials as $mk=>$mv){
                                $valueNetwork   = $mv["value"];
                                $titleNetwork   = $mv["title"];
                                ?>
                            <div class="networks">
                                <div class="c2 left">
                                    <select name="network[]">
                                        <option value=""><?= $label["Seleccionar"]?></option>
                                        <?php foreach((array)$options as $sk=>$sv){ 
                                            $selected=""; if($mv["type"] == $sk) $selected = "selected";
                                        
                                        ?>
                                            <option value="<?= $sk?>" <?= $selected?>><?=$sv?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="c2 right">
                                    <input name="title[]" type="text" placeholder="Nombre" value="<?= $titleNetwork?>"/>
                                </div>
                                <div>
                                    <input style="margin-top:5px; clear: both" name="value[]" type="text" placeholder="URL de la red social" value="<?= $valueNetwork?>"/>
                                </div>
                                <div class="delete i<?= $mk ?>"><a href="javascript:void(0)"><?= $label["Eliminar"]?></a></div>
                            </div>
                            <?php } ?>
                     <?php }else if ($type == "special" && $v["COLUMN_NAME"] == "organizers"){?>
                        </td>
                    </tr>
                    <tr class="organizers-name">
                            <td colspan="2" class="organizer-td">
                            <div class="add-org"><a href="javascript:void(0)"><?=$label["Agregar nuevo"] ?></a></div>
                            <?php foreach((array)$organizers as $sk=>$sv){
                            $name = $sv["name"];
                            $desc = $sv["description"];
                            $url  = $sv["url"];
                            ?>
                                <div class="organizer">
                                    <div class="org-name">
                                        <div class="label"><?= $label["Nombre"]?> (<img src='images/mandatory.png' class='mandatory'>):</div>
                                        <div class="value"><input type="text" name="name_organizer[]" value="<?= $name?>"/></div>
                                    </div>
                                    <div class="org-url">
                                        <div class="label"><?= $label["URL"]?> (<img src='images/mandatory.png' class='mandatory'>):</div>
                                        <div class="value"><input type="text" name="url_organizer[]" value="<?= $url?>"/></div>
                                    </div>
                                    <div class="org-desc">
                                        <div class="label"><?= $label["Descripción"]?> (<img src='images/mandatory.png' class='mandatory'>):</div>
                                        <div class="value"><textarea name="desc_organizer[]"><?= $desc?></textarea></div>
                                    </div>
                                    <div class="delete-org i<?= $sk ?>"><a href="javascript:void(0)"><?= $label["Eliminar"]?></a></div>
                                </div>
                            <?php  } ?>
                 <?php } ?>
                        <div class="missing-error">
                        <?php if (isset($missing[$v["COLUMN_NAME"]])) { ?>
                            <?= $label["Este campo es obligatorio"]?>
                        <?php } ?>
                        </div>
                 </tr>
                 <?php                        
                    }
                }
            ?>
            <tr>
                <td></td>
                <td class="action">
                    <input type="submit" name="<?= $action?>" value="<?= $label["Guardar"]?>" />
                    <?php if ($action == "edit" && ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"][$sectionId]["delete"] == "1")){
                        if (@$_SESSION["data"]["evento"] == $_GET["id"]) { ?>
                            <input type="submit" class="important dltP" name="delete" value="<?= $label["Borrar"]?>" />
                        <?php }else{ ?>
                            <input type="submit" class="important dlt" name="delete"  value="<?= $label["Borrar"]?>" />
                        <?php } ?>
                    <?php } ?>
                    <a href="./events.php"><?= $label["Volver"]?></a>
                </td>
            </tr>
            </table>
        </form>
    </div>
    <div id="dialog-confirm" title="Confirmación">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Está a punto de borrar el evento sobre el que está trabajando actualmente. ¿Desea continuar?</p>
    </div>
    <div id="dialog" title="Confirmación">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Recuerde que debe modificar las fechas de las sesiones asociadas al evento para mantener consistencia en los datos.</p>
        <p>Estas son las sesiones que debe modificar:</p>
        <div class="sessiones"></div>
    </div>
    <?= my_footer() ?>
  </body>
</html>