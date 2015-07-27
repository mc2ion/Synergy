<?php

function my_header($upload=""){
$out = 
<<<EOT
    <meta charset="utf-8"/>
    <title>Synergy - Administrador Web</title>
    <link rel="stylesheet" href="./css/style.css"/>
    <link href="./js/jquery-ui.css" rel="stylesheet">
   <script src="./js/jquery.js"></script>
    <script src="./js/jquery-ui.js"></script>
    <script src="./js/common.js"></script>
    <link rel='stylesheet' type='text/css'href='./js/timepicker/css/timepicki.css'/>
    <script type='text/javascript'src='./js/timepicker/js/timepicki.js'></script>
EOT;
    return $out;
}

function top_bar($user){
    global $backend;
    $out = '<div class="top-bar">
        <div class="user-info-div">';
    if ($user["photo_path"] != ""){
        $out .= "<div class='img'><img src='./{$user["photo_path"] }' class='image-photo' alt='{$user["first_name"]}'></div>";
    }
   // $out .= "<div class='user-info'>";
    $out .= '<div class="top-sb submenu-holder">';
    $out .= '<a rel="nofollow" href="#">'.$user["first_name"]. ' ' . $user["last_name"]. '<span class="darrow">▼</span></a>';
    $out .= '   <div class="subm">
                    <div class="arrow-up"></div>
                    <ul class="sbm ">
                        <li class="sbm"><a class="w" rel="nofollow" href="./manage_password.php">Cambiar Contraseña</a></li>
                    </ul>
                </div>
            </div>';
    $out .= '<div class="img"><a href="?logout" class="logout">(Salir)</a></div>';
    $out .= '</div>
        </div>';
    //</div>';
    return $out;
  
}

function menu($selected=""){
    global $backend; global $label; 
    $id = "";
        
    //Verificar si el usuario tiene imagen
    $user   = $_SESSION["app-user"]["user"][1];    
    $client = @$_SESSION["data"]["cliente"] ;
    $event  = @$_SESSION["data"]["evento"] ;
    
    $out  = top_bar($user);
    $out .= '<div class="menu">';
    //Menu exclusivo de administradores
    $out .= menuAdministrator($user, $selected, $label);
    $out .= "<form action='./index.php' method='post'>";
    $showEventList = 0;
    
    //Si el usuario es tipo administrador se muestra la lista de clientes,
    // si es tipo client, se mostrará únicamente la lista de eventos
    if ($user["type"] == "administrador"){
        $clients = $backend->getClientList();
        if ($clients){
            $showEventList = 1;
            $out .= '<div class="mng">';
            $out .= '<select name="c" class="clients">';
            $out .= "<option value=''>{$label["Seleccionar cliente"]}</option>";
            foreach ($clients as $k=>$v){
                $id  =  $v["client_id"];
                $sel = ""; if ($client == $id) $sel = "selected";
                $out .= '<option value="'.$id.'" '.$sel.'>'.$v["name"].'</option>';
            }
            $out .= '</select>
                </div>';
        }
    }else{
        $showEventList = 1;
        $out .= "<div class='mng-text'><input type='hidden' name='c' value='".$user["client_id"]."'>{$user["client_name"]}</div>";
        $_SESSION["data"]["cliente"] = $user["client_id"];
    }
    
    if ($showEventList){
        $out .= '<div class="mng">';
        $out .= "<select name='e' class='events'>";
        $aux  = "<option value=''>{$label["Seleccionar evento"]}</option>";
        if (isset($_SESSION["data"]["cliente"])){
            $events = $backend->getEventList($_SESSION["data"]["cliente"]);
            if($events){
                foreach ($events as $k => $v){
                    $id  = $v["event_id"];
                    $sel = ""; if ($event == $id) $sel = "selected"; 
                    $aux .= "<option value='{$id}' $sel>{$v["name"]}</option>";
                }
            }else{
                $aux = "<option value=''>{$label["No hay eventos asociados aun"]}</option>";
            }
         }
        $out .= $aux;
        $out .= '</select>';
        $out .= "<input type='submit' value='{$label["Seleccionar"]}'/></div>";
    }
    $out .= '</form><div style="clear:both"></div>';
    $out .= menuCLiente($user, $selected,$label);
    $out .="</div>";
    return $out;      
}

function menuAdministrator($user, $selected="", $label){
    global $backend;
   
    $out = "";
     /* Menu superior, sera visible únicamente si el usuario es de tipo administrador*/
    if ($user["type"] == "administrador"){
        $menuTop   =  $backend->getMenu("administrador", "menu");
        $out = '<div class="topu">
                    <ul>';
                    foreach($menuTop as $k=>$v){
                         $t  = isset($label[$v["name"]]) ? $label[$v["name"]]: $v["name"];
                         $sel  = ''; if ($selected == $v["name"]) $sel = "class='selected'";
                         $out .= '<li><a href="./'.$v["file"].'" '.$sel.'>'.ucfirst($t).'</a></li>';
                     }
        $out .=   ' </ul>
                </div>';
   }
   return $out;
}

function menuCLiente($user, $selected="", $label){
    global $backend;
    /* Menu inferior, sera visible para todos los usuarios */
    $menu           =  $backend->getMenu("cliente", "menu");
    $submenu        =  $backend->getMenu("cliente", "submenu");
    $event          =  @$_SESSION["data"]["evento"];
    $client          =  @$_SESSION["data"]["cliente"];
    
    if ($user["type"] != "administrador")  $permission     =  $_SESSION["app-user"]["permission"];
    $out = '<div class="infu">
                    <ul>';
                    foreach($menu as $k=>$v){
                         $t  = isset($label[$v["name"]]) ? $label[$v["name"]]: $v["name"];
   
                        // Verifico si el usuario es tipo administrador
                        // o si es cliente, que tenga permiso para ver la seccion
                        if ($user["type"] == "administrador" || ($user["type"] == "cliente" && isset($permission[$v["section_id"]]) && $permission[$v["section_id"]]["read"] == "1")){
                            $extra = ""; $class=""; 
                            $sel  = ''; if ($selected == $v["name"]) $sel = "class='selected'";
                            if (isset($submenu[$v["section_id"]])){ 
                                 if ($client != "" && $event != ""){
                                      $out .= "<li class='{$v["name"]}'>
                                            <a href='{$v["file"]}' $sel>".ucfirst($t)."</a>";
                                     $out .= "<ul class='submenu'>";
                                       foreach($submenu[$v["section_id"]] as $sk =>$sv){
                                            $ts  = isset($label[$sv["name"]]) ? $label[$sv["name"]]: $sv["name"];
                                            $selAux  = ''; if ($selected == $sv["name"]) $selAux = "class='selected'";
                                            $out .= "<li><a href='{$sv["file"]}' $selAux>".ucfirst($ts)."</a></li>";
                                       }
                                       $out .= '</ul>';
                                     $out .= "</li>";
                                }
                            }
                            else {
                                if ($v["name"] == "eventos" && $client != ""){ 
                                        $out .= '<li class="'.$v["name"].'"><a href="./'.$v["file"].'" '.$sel.'>'.ucfirst($t). " $extra ".'</a></li>';
                                }else{
                                    if ($client != "" && $event != ""){
                                        $out .= '<li class="'.$v["name"].'"><a href="./'.$v["file"].'" '.$sel.'>'.ucfirst($t). " $extra ".'</a></li>';
                                    }
                                }
                                
                            }
                        }
                     }
    $out .=   ' </ul>
            </div>';
   return $out;
}

function randomNumber($length) {
    $result = '';

    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
}


