<?php
/* Función para incluir el header */
function my_header(){
$out = 
<<<EOT
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Synergy - Administrador Web</title>
    <link rel="stylesheet" type='text/css' href='css/style.php' />
    <script src="./js/jquery.js"></script>
    <script type='text/javascript'src='./js/jquery.validate.min.js'></script>
    <link href="./js/jquery-ui.css" rel="stylesheet">
    <script src="./js/jquery-ui.js"></script>
    <script src="./js/common.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="./js/datatable/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="./js/datatable/js/jquery.dataTables.js"></script>
EOT;
    return $out;
}

/* Función para incluir en el footer */
function my_footer(){
    $out = 
<<<EOT
    <link rel='stylesheet' type='text/css'href='./js/timepicker/css/timepicki.css'/>
    <script type='text/javascript'src='./js/timepicker/js/timepicki.js'></script>
EOT;
    return $out;
}

/* Función para crear el top bar del administrador */
function top_bar($user){
    global $backend; global $label;
    $out = '<div class="top-bar">
        <div class="user-info-div">';
    if ($user["photo_path"] != ""){
        $out .= "<div class='img'><img src='./{$user["photo_path"] }' class='image-photo' alt='{$user["first_name"]}'></div>";
    }
    $out .= '<div class="top-sb submenu-holder">';
    $out .= '<a rel="nofollow" href="#">'.$user["first_name"]. ' ' . $user["last_name"]. '<span class="darrow">▼</span></a>';
    $out .= '   <div class="subm">
                    <div class="arrow-up"></div>
                    <ul class="sbm ">
                        <li class="sbm"><a class="w" rel="nofollow" href="./manage_profile.php">'.$label["Perfil de Usuario"].'</a></li>
                    </ul>
                </div>
            </div>';
    $out .= '<div class="img"><a href="?logout" class="logout">(Salir)</a></div>';
    $out .= '</div>
        </div>';
    return $out;
}

/* Función para crear el menu lateral del administrador */
function menu($selected=""){
    global $backend; global $label; global $typeUser;
    $id = "";
        
    //Verificar si el usuario tiene imagen
    $user   = $_SESSION["app-user"]["user"][1];    
    $client = @$_SESSION["data"]["cliente"] ;
    $event  = @$_SESSION["data"]["evento"] ;
    $clients= array();
    
    $out  = top_bar($user);
    $out .= '<div class="menu">';
    if ($typeUser[$user["type"]] == "administrador"){
        $out .= "<a href='./index.php'><img src='./images/logo.png' alt='Logo' class='logo'></a>";
    }else{
        $out .= "<a href='./index.php'><img src='{$user["logo_path"]}' alt='Logo' class='logo'></a>";
    }
    //Menu exclusivo de administradores
    $out .= menuAdministrator($user, $selected, $label);
    $out .= "<form action='./index.php' method='post'>";
    $showEventList = 0;
    
    //Si el usuario es tipo administrador se muestra la lista de clientes,
    // si es tipo client, se mostrará únicamente la lista de eventos
    if ($typeUser[$user["type"]] == "administrador"){
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
        }else{
            $showEventList = 0;
        }
    }else{
        $showEventList = 1;
        $out .= "<div class='mng-text'><input type='hidden' name='c' value='".$user["client_id"]."'>{$user["client_name"]}</div>";
        $_SESSION["data"]["cliente"] = $user["client_id"];
    }
    if ($showEventList == "1"){
        $out .= '<div class="mng">';
        $out .= "<select name='e' class='events'>";
        $aux  = "<option value=''>{$label["Seleccionar evento"]}</option>";
        if (isset($_SESSION["data"]["cliente"])){
            $events = $backend->getEventList($_SESSION["data"]["cliente"], array(), "1");
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
    $out .= menuCLiente($user, $selected,$label,$clients);
    $out .="</div>";
    return $out;      
}

function menuAdministrator($user, $selected="", $label){
    global $backend; global $typeUser;
   
    $out = "";
     /* Menu superior, sera visible únicamente si el usuario es de tipo administrador*/
    if ($typeUser[$user["type"]] == "administrador" || $typeUser[$user["type"]] == "cliente-administrador"){
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

function menuCLiente($user, $selected="", $label, $clientList){
    global $backend; global $typeUser;
    /* Menu inferior, sera visible para todos los usuarios */
    $menu           =  $backend->getMenu("cliente", "menu");
    $submenu        =  $backend->getMenu("cliente", "submenu");
    $event          =  @$_SESSION["data"]["evento"];
    $client         =  @$_SESSION["data"]["cliente"];
    $out            = "";

    if ($typeUser[$user["type"]] == "cliente")  $permission     =  $_SESSION["app-user"]["permission"];
    if ((($typeUser[$user["type"]] == "administrador"  ) && $clientList!= "" && !empty($clientList) && $client != "") || ($typeUser[$user["type"]] != "administrador" && $client != "")){
        $out = '<div class="infu">
                    <ul>';
                    foreach($menu as $k=>$v){
                         $t  = isset($label[$v["name"]]) ? $label[$v["name"]]: $v["name"];
   
                        // Verifico si el usuario es tipo administrador
                        // o si es cliente, que tenga permiso para ver la seccion
                        if ($typeUser[$user["type"]] != "cliente" || ($typeUser[$user["type"]] == "cliente" && isset($permission[$v["section_id"]]) && $permission[$v["section_id"]]["read"] == "1")){
                            $extra = ""; $class=""; 
                            $sel  = ''; if ($selected == $v["name"]) $sel = "class='selected'";
                            if (isset($submenu[$v["section_id"]])){ 
                                 if ($event != ""){
                                      $out .= "<li class='{$v["name"]} sub'>
                                            <a href='{$v["file"]}' $sel>".ucfirst($t). "<span class=\"darrow-menu\">▼</span></a>";
                                     $out .= "<ul class='submenu'>";
                                       foreach($submenu[$v["section_id"]] as $sk =>$sv){
                                            $continue = 1;
                                            if ($sv["name"] != "salas"){
                                                if (isset($permission[$sv["section_id"]]) && $permission[$sv["section_id"]]["read"] != "1"){
                                                    $continue = 0;
                                                }
                                            }
                                            if ($continue){
                                                $ts  = isset($label[$sv["name"]]) ? $label[$sv["name"]]: $sv["name"];
                                                $selAux  = ''; if ($selected == $sv["name"]) $selAux = "class='selected'";
                                                $out .= "<li><a href='{$sv["file"]}' $selAux>".ucfirst($ts)."</a></li>";
                                            }
                                       }
                                       $out .= '</ul>';
                                     $out .= "</li>";
                                }
                            }
                            else {
                                if ($v["name"] == "eventos" && $client != ""){ 
                                        $out .= '<li class="'.$v["name"].'"><a href="./'.$v["file"].'" '.$sel.'>'.ucfirst($t). " $extra ".'</a></li>';
                                }else{
                                    if ($event != ""){
                                        $out .= '<li class="'.$v["name"].'"><a href="./'.$v["file"].'" '.$sel.'>'.ucfirst($t). " $extra ".'</a></li>';
                                    }
                                }
                                
                            }
                        }
                     }
        $out .=   ' </ul>
            </div>';
    }
   return $out;
}

function randomNumber($length) {
    $result = '';

    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
}


function colourBrightness($hex, $percent) {
    // Work out if hash given
    $hash = '';
    if (stristr($hex,'#')) {
        $hex = str_replace('#','',$hex);
        $hash = '#';
    }
    /// HEX TO RGB
    $rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
    //// CALCULATE
    for ($i=0; $i<3; $i++) {
        // See if brighter or darker
        if ($percent > 0) {
            // Lighter
            $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
        } else {
            // Darker
            $positivePercent = $percent - ($percent*2);
            $rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
        }
        // In case rounding up causes us to go to 256
        if ($rgb[$i] > 255) {
            $rgb[$i] = 255;
        }
    }
    //// RBG to Hex
    $hex = '';
    for($i=0; $i < 3; $i++) {
        // Convert the decimal digit to hex
        $hexDigit = dechex($rgb[$i]);
        // Add a leading zero if necessary
        if(strlen($hexDigit) == 1) {
            $hexDigit = "0" . $hexDigit;
        }
        // Append to the hex string
        $hex .= $hexDigit;
    }
    return $hash.$hex;
}

//Obtener color de la letra (blanco o negro)
function getFontColor($hexcolor){
    $r = hexdec(substr($hexcolor,1,2));
    $g = hexdec(substr($hexcolor,3,2));
    $b = hexdec(substr($hexcolor,5,2));
    $yiq = (($r*299)+($g*587)+($b*114))/1000;
    return ($yiq >= 150) ? '#000000' : '#FFFFFF';
}

//Agregar http a los urls que no lo tenga
function addhttp($url) {
    if ($url == "") return "";
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

//Funcion para generar un contraseña aleatoria
function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

?>