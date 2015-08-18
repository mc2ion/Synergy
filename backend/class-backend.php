<?php
/*Incluir conexion a base de datos*/
require_once("class-db-conn.php");


class backend extends db{
    
    var $app;
    var $label;
    var $permission;
    
    function __construct($label, $typeUser){
        $this->label = $label;
        parent::__construct();
        $this->app["perPage"]  = 100;
        $this->app["typeUser"] = $typeUser;
    }
    
    function login($params){
        $email = $this->clean($params["at-email"]);
        $pass  = md5($this->clean($params["at-password"]));
        $query = "SELECT t1.*, t2.name as client_name, t2.logo_path, t2.main_menu_color, t2.main_menu_color_aux, t2.button_color, t2.top_menu_color, t2.font_main_menu_color, t2.font_top_menu_color, t2.main_submenu_color from user t1 left join client t2 on (t1.client_id = t2.client_id)  WHERE email = '{$email}' AND password = '{$pass}' LIMIT 1";
        $q["user"] = $this->dbQuery($query);
        if ($q["user"]){
            if ($this->app["typeUser"][$q["user"][1]["type"]] == "cliente"){
                //Buscar sus permisos
                 $queryAux = "SELECT * from permission where user_id = '{$q["user"][1]["user_id"]}'";
                 $qAux = $this->dbQuery($queryAux);
                 foreach($qAux as $k=>$v){
                    $qr[$v["section_id"]] = $v;
                 }
                 $q["permission"] = $qr;
            }
        }
        //Verificar que el correo exista
        else{
            $query = "SELECT * from user WHERE email = '{$email}' LIMIT 1";
            $q["email"] = $this->dbQuery($query);
        }
        return $q;
   }


    function getCompleteInfo($userId){
        $query = "SELECT t1.*, t2.name as client_name, t2.logo_path, t2.main_menu_color, t2.main_menu_color_aux, t2.button_color, t2.top_menu_color, t2.font_main_menu_color, t2.font_top_menu_color, t2.main_submenu_color from user t1 left join client t2 on (t1.client_id = t2.client_id)  WHERE user_id = '{$userId}' LIMIT 1";
        $q["user"] = $this->dbQuery($query);
        if ($q["user"]){
            if ($this->app["typeUser"][$q["user"][1]["type"]] == "cliente"){
                //Buscar sus permisos
                $queryAux = "SELECT * from permission where user_id = '{$q["user"][1]["user_id"]}'";
                $qAux = $this->dbQuery($queryAux);
                foreach($qAux as $k=>$v){
                    $qr[$v["section_id"]] = $v;
                }
                $q["permission"] = $qr;
            }
        }
        return $q;
    }
    
    
    function getPermission($userId){
        $query = "SELECT * from $this->schema.permission WHERE user_id='$userId'";
        $q     = $this->dbQuery($query);
        $out   = "";
        if ($q){
            foreach ($q as $k=>$v){
                $out["section"][$v["section_id"]] = $v;
            }
        }
        return $out;
    }
    
    function getMenu($type="", $typeMenu="", $all="0"){
        if ($type == ""){
            $cond = "1 = 1";
        }else{
            $cond                          = " AND father_id != ''";
            if ($typeMenu == "menu") $cond = " AND father_id is NULL";
            $cond                         .= " AND type = '$type'";
            $cond = substr($cond, 4);
        }
        if ($all == "1"){
            $cond = " type = 'cliente' AND name != 'salas'";
        }
        $query = "SELECT * from $this->schema.section WHERE $cond ";
        $q     = $this->dbQuery($query);
        $out   = $q;
        if ($q && $typeMenu == "submenu"){
            $out = "";
            foreach ($q as $k=>$v){
                $out[$v["father_id"]][$v["section_id"]] = $v;
            }
        }   
        return $out;
    }
    
    function getSubsections(){
        $query = "SELECT * from $this->schema.subsection";
        $q     = $this->dbQuery($query);
        $out   = array();
        if ($q){
            foreach($q as $k=>$v){
                $out[$v["section_id"]] = $v;
            }
        }
        return $out;
    }
    
    function getUserInfo($id){
        $query = "SELECT * from $this->schema.user WHERE user_id='$id'";
        $q     = $this->dbQuery($query);
        return $q[1];
    }
    
    function getUserInfoByEmail($email){
        $query = "SELECT * from $this->schema.user WHERE email='$email'";
        $q     = $this->dbQuery($query);
        if ($q) return $q[1];
        else return;
    }
    
    
    function verifyPassword($id, $password){
        $query = "SELECT * from $this->schema.user WHERE user_id='$id' and password = '$password'";
        $q     = $this->dbQuery($query);
        if ($q) return $q[1];
    }
    
    function getClient($id){
        $id     = $this->clean($id);
        $query  = "SELECT * from $this->schema.client WHERE client_id='$id'";
        $q      = $this->dbQuery($query);
        if ($q)   return $q[1];
        else return $q;
    }
    
    function getClientList($noShow=array(), $menu=1, $user="administrador"){
        $extraCond = ""; global $clientId;
        if ($user == "cliente-administrador"){
            $extraCond = "AND client_id = '{$_SESSION["data"]["cliente"]}'";
        }
        if ($menu == 1){
            $query = "SELECT * from $this->schema.client WHERE active = '1' order by client_id asc";
            $q      = $this->dbQuery($query);
        }else{ 
            $q      = @$this->select("client","*", "active='1' $extraCond", "client_id", 0, $this->app["perPage"], "");
        }$out    = "";
        if ($menu) return $q;
        if ($menu == "0" && $q){
            foreach($q as $k=>$v){
                foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                        if ($sk == "logo_path"){
                            $out[$k][$sk] = "<img src='{$sv}' alt='Logo' class='logo-company'/>";
                        }else $out[$k][$sk] = $sv;
                    } 
                }
                if ($this->app["typeUser"][$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"]["2"]["update"] == "1"){
                    $out[$k]["action"] = "<a href='./manage_client.php?id={$v["client_id"]}'>{$this->label["Editar"]}</a>";
                }
            }
        }
        return $out;
    }
    
    
    /* Función que permite obtener la información de las columnas de la tabla solicitada por parametro */
    function getColumnsTable($table){
        $query = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='{$this->schema}' 
                    AND `TABLE_NAME`='$table'";
        $q = $this->dbQuery($query);
        return $q;
    }
    
    /*Funciones para los eventos */
    function getEventList($clientId, $noShow=array(), $all="0"){
        if ($all == "1")
            $q      = @$this->dbQuery("SELECT * from $this->schema.event WHERE active='1' AND client_id = '$clientId'");
        else
            $q      = @$this->select("event", "*", "active='1' AND client_id = '$clientId'", "client_id asc", 0, $this->app["perPage"], "");
        
        $out    = "";
        if ($q){
           foreach($q as $k=>$v){
                foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                        if ($sk == "map_path"){
                            $out[$k][$sk] = "<img src='{$sv}' alt='Logo' class='logo-company'/>";
                        }else $out[$k][$sk] = $sv;
                    } 
                }
                if ($this->app["typeUser"][$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || @$_SESSION["app-user"]["permission"]["3"]["update"] == "1"){
                    $out[$k]["action"] = "<a href='./manage_event.php?id={$v["event_id"]}'>{$this->label["Editar"]}</a>";
                }
            }
        }
        return $out;
    }
    function getEvent($id){
        $query = "SELECT * from $this->schema.event WHERE event_id='$id'";
        $q     = $this->dbQuery($query);
        if ($q) return $q[1];
    }
    
    /*Funciones para las encuestas */
    function getSurveyQuestionList($eventId, $noShow){
        $query = "SELECT sq.*, count(qo.option_id) as q_options from 
                    survey_question sq, question_option qo
                    WHERE sq.question_id = qo.question_id
                    AND sq.active = '1' and qo.active = '1' AND event_id = '$eventId'
                    group by qo.question_id";
        $q = $this->dbQuery($query);
        $out    = "";
        if ($q){
            foreach($q as $k=>$v){
                foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                       $out[$k][$sk] = $sv; 
                    }
                }
                if ($this->app["typeUser"][$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"]["9"]["update"] == "1"){
                    $out[$k]["action"] = "<a href='./manage_question.php?id={$v["question_id"]}'>{$this->label["Editar"]}</a>";
                }
            }
        }
        return $out;
    }
    
    function getSurvey($id){
        $query = "SELECT * from $this->schema.survey_question WHERE question_id='$id'";
        $q     = $this->dbQuery($query);
        return $q[1];
    }
    
    //Obtener respuestas asociadas a una pregunta
    function getOptions($id){
        $query = "SELECT * from $this->schema.question_option WHERE question_id='$id' AND active='1' ORDER BY option_id";
        $q     = $this->dbQuery($query);
        return $q;
    }
    
    /** Seccion rooms **/
    function getRoomList($eventId, $noshow=array(), $all="0"){
        if ($all == "1"){
            $query  = "SELECT * from $this->schema.room WHERE event_id ='$eventId' AND active='1' order by room_id asc";
            $q     = $this->dbQuery($query);
        }
        else{
            $q      = @$this->select("room", "*", "active='1' AND event_id ='$eventId'", "room_id asc", 0, $this->app["perPage"], "");
        }
        $out    = "";
        if ($q){
            foreach($q as $k=>$v){
                foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noshow)){
                        $out[$k][$sk] = $sv;
                    } 
                }
                if ($this->app["typeUser"][$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"]["4"]["update"] == "1"){
                    $out[$k]["action"] = "<a href='./manage_room.php?id={$v["room_id"]}'>{$this->label["Editar"]}</a>";
                }
            }
        }
        return $out;
    }
    
    function existsRoom($name, $id=""){
        $cond = "";
        if ($id != "") $cond = "AND room_id != '$id'";
        $query = "SELECT name from $this->schema.room WHERE name='$name' AND active='1' AND event_id = '{$_SESSION["data"]["evento"]}' $cond ";
        $q     = $this->dbQuery($query);
        if ($q) return true;
        return false;
    }
    
    function getRoom($id){
        $query = "SELECT * from $this->schema.room WHERE room_id='$id'";
        $q     = $this->dbQuery($query);
        return $q[1];
    }
    
    /** Seccion Sessiones **/
     function getSessionList($eventId, $noShow=array(), $simple="0", $all="0"){
        if ($all == "1"){
            $query = "SELECT t1.*, t2.name from $this->schema.session t1 left join $this->schema.room t2 on t1.room_id = t2.room_id WHERE t1.active='1' AND t1.event_id ='$eventId'";
            $q     = $this->dbQuery($query);
        }
        else{
            $q      = @$this->select("session t1 left join room t2 on t1.room_id = t2.room_id", "t1.*, t2.name", "t1.active='1' AND t1.event_id ='$eventId'", "t1.session_id asc", 0, $this->app["perPage"], "");
        }
        $out    = "";
        if ($simple == "0"){
            if ($q){
                foreach($q as $k=>$v){
                     $name = $v["name"];
                     unset($v["name"]);
                     foreach($v as $sk=>$sv){
                        if (!in_array($sk, $noShow)){
                            if ($sk == "image_path"){
                                $out[$k][$sk] = "<img src='{$sv}' alt='Logo' class='logo-company'/>";
                            }else $out[$k][$sk] = $sv;
                            $out[$k]["date"]    = $v["date"] . " " . $formatted = date("g:i a", strtotime($v["time_ini"])) . " - " .  $formatted = date("g:i a", strtotime($v["time_end"])) ;
                            $out[$k]["room_id"] = $name;
                        }
                    }
                    if ($this->app["typeUser"][$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"]["4"]["update"] == "1"){
                        $out[$k]["action"] = "<a href='./manage_session.php?id={$v["session_id"]}'>{$this->label["Editar"]}</a>";
                    }
                }
            }   
            return $out;
        }else return $q;
    }
    
    function getSession($id){
        $query = "SELECT * from $this->schema.session WHERE session_id='$id'";
        $q     = $this->dbQuery($query);
        if ($q){
            $formatted = date("g : i : a", strtotime($q[1]["time_ini"]));
            $q[1]["time_ini"] =  $formatted;
            $formatted = date("g : i : a", strtotime($q[1]["time_end"]));
            $q[1]["time_end"] =  $formatted;
            return $q[1];
        }
        return;

    }


    function getSessionListByRoom($roomId, $eventId, $extra = ""){
        $query = "SELECT * from $this->schema.session WHERE active='1' AND room_id ='$roomId' AND event_id = '$eventId' $extra";
        $q     = $this->dbQuery($query);
        return $q;
    }
    
    /** Seccion Usuarios **/
     function getUserList($noShow, $user="administrador"){
         $extraCond = "";
         if ($user == "cliente-administrador"){
             $extraCond = "AND client_id = '{$_SESSION["data"]["cliente"]}' AND type != 'Super Usuario'";
         }
        $q      = @$this->select("user", "*", "user_id != '{$_SESSION["app-user"]["user"]["1"]["user_id"]}' $extraCond", "user_id asc", 0, $this->app["perPage"], "");
        $out    = "";
        if ($q){
            foreach($q as $k=>$v){
                 foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                        $out[$k][$sk] = $sv;
                    } 
                }
                unset( $out[$k]["client_id"]);
                //Verificar que el usuario puede ver la seccion
                if ($this->app["typeUser"][$_SESSION["app-user"]["user"]["1"]["type"]] != "cliente" || @$_SESSION["app-user"]["permission"]["1"]["update"] == "1"){
                    $out[$k]["action"] = "<a href='./manage_user.php?id={$v["user_id"]}'>{$this->label["Editar"]}</a>";
                }
            }
        }   
        return $out;
    }
    
   
    /** Seccion Speaker **/
    function getSpeaker($id){
        $query = "SELECT * from $this->schema.speaker WHERE speaker_id='$id'";
        $q     = $this->dbQuery($query);
        if ($q){
            return $q[1];
        }
        return "";
    }

    function getSpeakerByCompany($company_name, $name, $id=""){
        $extra = "";
        if ($id != ""){ $extra = "AND speaker_id != '$id'";}
        $query = "SELECT * from $this->schema.speaker WHERE company_name='$company_name' AND name = '$name' $extra";
        $q     = $this->dbQuery($query);
        return $q;
    }
    
    function getSpeakerList($noShow){
        $q      = @$this->select("speaker", "*", "active = '1'", "speaker_id asc", 0, $this->app["perPage"], "");
        $out    = "";
        if ($q){
            foreach($q as $k=>$v){
                 foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                        if ($sk == "image_path"){
                            $out[$k][$sk] = "<img src='{$sv}' alt='Logo' class='logo-company'/>";
                        }else $out[$k][$sk] = $sv;
                    } 
                }
                if ($this->app["typeUser"][$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"]["8"]["update"] == "1"){
                    $out[$k]["action"] = "<a href='./manage_speaker.php?id={$v["speaker_id"]}'>{$this->label["Editar"]}</a>";
                }
            }
        }   
        return $out;
    }
    
      /** Seccion Expositores **/
    function getExhibitor($id){
        $query = "SELECT * from $this->schema.exhibitor WHERE exhibitor_id='$id'";
        $q     = $this->dbQuery($query);
        if ($q){
            return $q[1];
        }
        return;
    }

    function getExhibitorByName($name, $id=""){
        $extra = "";
        if ($id != ""){ $extra = "AND exhibitor_id != '$id'";}
        $query = "SELECT * from $this->schema.exhibitor WHERE company_name='$name' $extra";
        $q     = $this->dbQuery($query);
        return $q;
    }
    
    function getExhibitorList($noShow){
        $q      = @$this->select("exhibitor t1 left join category t2 on t1.category_id = t2.category_id", "t2.name as category,t1.* ", "t1.active = '1'", "exhibitor_id asc", 0, $this->app["perPage"], "");
        $out    = "";
        if ($q){
            foreach($q as $k=>$v){
                 foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                        if ($sk == "image_path"){
                            $out[$k][$sk] = "<img src='{$sv}' alt='Logo' class='logo-company'/>";
                        }else $out[$k][$sk] = $sv;
                    } 
                }
                if ($out[$k]["category"] == "")  $out[$k]["category"] = "---";
                unset($out[$k]["category_id"]);
                if ($this->app["typeUser"][$_SESSION["app-user"]["user"][1]["type"]] != "cliente" || $_SESSION["app-user"]["permission"]["7"]["update"] == "1"){
                    $out[$k]["action"] = "<a href='./manage_exhibitor.php?id={$v["exhibitor_id"]}'>{$this->label["Editar"]}</a>";
                }
            }
        }   
        return $out;
    }
    
    function getCategoryList(){
        $query  = "SELECT * from $this->schema.category where active = '1' order by category_id asc" ; 
        $q      = $this->dbQuery($query);
        return $q;
    }
    
    function getCategory($id){
        $query = "SELECT * from $this->schema.category WHERE category_id='$id'";
        $q     = $this->dbQuery($query);
        if ($q){
            return $q[1];
        }
        return $q;
    }
    
    
    function uploadImage($path, $input,$format_accepted, $size, $width, $height, $square=""){
        $uploadOk["result"] = 1;
        $target_file        = $path;
        $imageFileType      = pathinfo($target_file,PATHINFO_EXTENSION);
        //Verificar el tamaño
        if ($_FILES[$input]["tmp_name"] != ""){
            $size = getimagesize($_FILES[$input]["tmp_name"]);
            $w = $size[0];
            $h = $size[1];

            // Verificar si el archivo ya existe
            if (file_exists($target_file)) {
                $uploadOk["message"] =  $this->label["Archivo duplicado"];
                $uploadOk["result"] = 0;
            }
            else if ($_FILES[$input]["size"] > $size) {
                $uploadOk["message"] = $this->label["El peso de la imagen no es válido"];
                $uploadOk["result"]= 0;
            }
            // Verificar el formato
            else if(!in_array($imageFileType, $format_accepted))
            {
                $uploadOk["result"] = 0;
                $uploadOk["message"] = $this->label["Formato de la imagen no válido."];
            }
            //  Verificar las dimensiones
            else if ($w > $width || $h > $height){
                $uploadOk["result"] = 0;
                $uploadOk["message"] = $this->label["La imagen proporcionada excede con el tamaño máximo permitido"] ;
                return $uploadOk;
            }
            else if ($square=="" && $w != $h){
                $uploadOk["result"] = 0;
                $uploadOk["message"] = $this->label["La imagen proporcionada debe ser cuadrada"];
                return $uploadOk;
            }
            /*else if ($square != "" && ($w <= $h || $w == $h)){
                $uploadOk["result"] = 0;
                $uploadOk["message"] = $this->label["La imagen proporcionada debe ser rectangular. Más ancha que alta"];
                return $uploadOk;
            }*/
            // Varificar algun error
            if ($uploadOk["result"] != 0) {
                if (move_uploaded_file($_FILES[$input]["tmp_name"], $target_file)) {
                    $uploadOk["result"] = 1;
                } else {
                    $uploadOk["result"]  = 0;
                }
            }
        }else{
                $uploadOk["message"] = $this->label["La imagen proporcionada excede con el tamaño máximo permitido"] ;
                $uploadOk["result"]  = 0;

        }
        return $uploadOk;    
    }

    function getSurveyReport($eventId){
        $query  =   "select s.*, count(qr.user_id) as result
                    from
                    (select c.logo_path, e.name as event_name, sq.question_id, sq.question, qo.option_id, qo.optionDesc
                    from event e, survey_question sq, question_option qo, client c
                    where c.client_id = e.client_id and e.event_id = {$eventId} and e.event_id = sq.event_id and sq.question_id = qo.question_id and sq.active = 1 and qo.active = 1
                    order by sq.question_id, qo.option_id) as s
                    left join question_result qr
                    on s.question_id = qr.question_id and s.option_id = qr.option_id
                    group by s.question_id, s.option_id";
        $q      = $this->dbQuery($query);
        $aux    = "";
        if ($q){
            $id = 0;
            foreach($q as $k=>$v){
                $aux["logo_path"]                   = $v["logo_path"];
                $aux["survey"][$v["question_id"]]["event"]    = $v["event_name"];
                $aux["survey"][$v["question_id"]]["question"] = $v["question"];
                $aux["survey"][$v["question_id"]]["option"][$v["option_id"]]["option"]   = $v["optionDesc"];
                $aux["survey"][$v["question_id"]]["option"][$v["option_id"]]["result"]   = $v["result"];
            }
        }
        return $aux;
    }


    
    function getReviewReport($sessionId){
        //Obtener detalles de la sesion
        $query = "SELECT c.logo_path, S.title AS session_title, R.name, S.date AS date, S.speaker AS speaker,
                S.time_ini AS time_ini, S.time_end as time_end,
                COUNT(RE.review_id) AS reviewers, SUM(RE.ranking)/COUNT(RE.review_id) AS ranking
                FROM session S, client c, event e, room R, review RE
                WHERE S.session_id = '$sessionId' AND S.active = 1 AND S.room_id = R.room_id AND S.session_id = RE.session_id
                AND c.client_id = e.client_id AND e.event_id = s.event_id
                ";
        $q["details"]      = $this->dbQuery($query);
        if (!empty($q["details"][1]["name"])){
            $q["logo_path"]    =  $q["details"]["1"]["logo_path"];
            foreach($q["details"] as $k=>$v){
                unset($q["details"][$k]["time_ini"]);
                unset($q["details"][$k]["time_end"]);
                unset($q["details"][$k]["logo_path"]);
                $q["details"][$k]["time"] = date("g:i  a", strtotime($v["time_ini"])) . " - ".date("g:i  a", strtotime($v["time_end"])) ;
                //$q["details"][$k]["time_end"] = date("g:i  a", strtotime($v["time_end"]));
            }
        }else{
            return "";
        }


        //Obtener detalles de las votacion
        $query =    "SELECT R.ranking, COUNT(R.review_id) AS reviewers
                    FROM review R
                    WHERE R.session_id = '$sessionId'
                    GROUP BY R.ranking ASC";
        $r     =    $this->dbQuery($query);
        foreach($r as $k=>$v){
            $q["reviews"][$v["ranking"]] = $v;
        }
        
        //Obtener comentarios
        $query              =  "SELECT R.ranking, R.comment
                                FROM review R
                                WHERE R.session_id = '$sessionId' AND R.comment IS NOT NULL";       
        $q["comments"]      = $this->dbQuery($query);
        return $q;
    }
    
    /*Evaluaciones*/
    function getReviewList($noShow=array()){
        $eventId = $_SESSION["data"]["evento"];
        $query   = "SELECT t1.session_id, t2.title,  t2.speaker, t2.date, t2.time_ini, t2.time_end
                    FROM review t1 left join session t2 on t1.session_id = t2.session_id 
                    WHERE t2.active = '1' AND t2.event_id = '$eventId' GROUP BY session_id ORDER BY t1.session_id asc";
        $q       = $this->dbQuery($query);         
                    
        $out     = "";
        if ($q){
            foreach($q as $k=>$v){
                 foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                        $out[$k][$sk] = $sv;
                    } 
                }
                $formattedIni       =   date("g:i  a", strtotime($out[$k]["time_ini"]));
                $formattedEnd       =   date("g:i  a", strtotime($out[$k]["time_end"]));
                unset($out[$k]["time_ini"]);
                unset($out[$k]["time_end"]);
                $out[$k]["time"]    =   $formattedIni . " - " . $formattedEnd;
                $out[$k]["action"]  = "<a href='./review_report.php?id={$v["session_id"]}'>{$this->label["Ver Resultados"]}</a>";
            }
        }   
        return $out;
        
    }
    function select($table, $fields="*", $where="", $order="id asc", $from=0, $size=10, $group=""){
        if($order=="") $order = "id asc";
        $filter = "";
        $where = trim($where)==""? "1=1":$where;
        $query = "select $fields from $table where $where $filter $group order by $order";
        $count = "select count(*) as count from $table where $where $filter $group order by $order";
        $q  = @$this->dbQuery($query);
        $r  = @$this->dbQuery($count);
        $this->app["count"] = $r["1"]["count"];
        return $q;
    }

    function insertRow($table, $data){
        return $this->dbInsert($this->schema.".".$table, $data);
    }
    
    function updateRow($table, $data, $condition){
        return $this->dbUpdate($this->schema.".".$table, $data, $condition);
    }
    
    function deleteRow($table, $condition){
        $query = "DELETE from $this->schema.$table WHERE $condition";
        return $this->dbQuery($query);
    }
    
    function selectRow($table, $condition){
        $query = "SELECT * from $this->schema.$table WHERE $condition";
        return $this->dbQuery($query);
    }
    

}

?>