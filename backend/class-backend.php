<?php
/*Incluir conexion a base de datos*/
require_once("class-db-conn.php");


class backend extends db{
    
    var $app;
    var $label;
    var $permission;
    
    function __construct($label){
        $this->label = $label;
        parent::__construct();
        $this->app["perPage"] = 1;
    }
    
    function login($params){
        $email = $this->clean($params["at-email"]);
        $pass  = md5($this->clean($params["at-password"]));
        $query = "SELECT t1.*, t2.name as client_name from user t1 left join client t2 on (t1.client_id = t2.client_id)  WHERE email = '{$email}' AND password = '{$pass}' LIMIT 1";
        $q["user"] = $this->dbQuery($query);
        if ($q["user"]){
            if ($q["user"][1]["type"] == "cliente"){
                //Buscar sus permisos
                 $queryAux = "SELECT * from permission where user_id = '{$q["user"][1]["user_id"]}'";
                 $qAux = $this->dbQuery($queryAux);
                 $q["permission"] = $qAux;
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
    
    function getMenu($type="", $typeMenu=""){
        if ($type == ""){
            $cond = "1 = 1";
        }else{
            $cond                          = " AND father_id != ''";
            if ($typeMenu == "menu") $cond = " AND father_id is NULL";
            $cond                         .= " AND type = '$type'";
            $cond = substr($cond, 4);
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
        $out;
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
    
    function verifyPassword($id, $password){
        $query = "SELECT * from $this->schema.user WHERE user_id='$id' and password = '$password'";
        $q     = $this->dbQuery($query);
        if ($q) return $q[1];
    }
    
    function getClient($id){
        $id     = $this->clean($id);
        $query  = "SELECT * from $this->schema.client WHERE client_id='$id'";
        $q      = $this->dbQuery($query);
        return $q[1];
    }
    
    function getClientList($noShow=array(), $menu=1){
        if ($menu == 1){
            $query = "SELECT * from $this->schema.client WHERE active = '1' order by client_id asc";
            $q      = $this->dbQuery($query);
        }else{ 
            $q      = @$this->select("client","*", "active='1'", "client_id", 0, $this->app["perPage"], "", $_GET["fireUI"]);
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
                if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"]["2"]["update"] == "1"){
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
    function getEventList($clientId, $noShow=array()){
        $q      = @$this->select("event", "*", "active='1' AND client_id = '$clientId'", "client_id asc", 0, $this->app["perPage"], "", $_GET["fireUI"]);
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
                if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || @$_SESSION["app-user"]["permission"]["3"]["update"] == "1"){
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
                       if ($v["position"] == "")  $out[$k]["position"] = "N/A";                        
                    } 
                }
                if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"]["9"]["update"] == "1"){
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
        $query = "SELECT * from $this->schema.question_option WHERE question_id='$id' AND active='1' ORDER BY position, option_id";
        $q     = $this->dbQuery($query);
        return $q;
    }
    
    /** Seccion rooms **/
    function getRoomList($eventId, $noshow=array(), $all="0"){
        if ($all == "1"){
            $query  = "SELECT * from $this->schema.room WHERE event_id ='$eventId' order by room_id asc";
            $q     = $this->dbQuery($query);
        }
        else{
            $q      = @$this->select("room", "*", "active='1' AND event_id ='$eventId'", "room_id asc", 0, $this->app["perPage"], "", $_GET["fireUI"]);
        }
        $out    = "";
        if ($q){
            foreach($q as $k=>$v){
                foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noshow)){
                        $out[$k][$sk] = $sv;
                    } 
                }
                if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"]["4"]["update"] == "1"){
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
            $q      = @$this->select("session t1 left join room t2 on t1.room_id = t2.room_id", "t1.*, t2.name", "t1.active='1' AND t1.event_id ='$eventId'", "t1.session_id asc", 0, $this->app["perPage"], "", $_GET["fireUI"]);
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
                            $out[$k]["room_id"] = $name;
                        } 
                    }
                    if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"]["4"]["update"] == "1"){
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
            $formatted = date("g : i : a", strtotime($q[1]["time"]));
            $q[1]["time"] =  $formatted;
            return $q[1];
        }
        return;
      
    }
    
    /** Seccion Usuarios **/
     function getUserList($noShow){
        $q      = @$this->select("user t1 left join client t2 on t1.client_id = t2.client_id", "t1.*, t2.name", "", "user_id asc", 0, $this->app["perPage"], "", $_GET["fireUI"]);
        $out    = "";
        if ($q){
            foreach($q as $k=>$v){
                 foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                        $out[$k][$sk] = $sv;
                    } 
                }
                $out[$k]["client"] = ($out[$k]["name"] != "")? $out[$k]["name"] : "N/A";
                unset( $out[$k]["client_id"]);
                unset( $out[$k]["name"]);
                //Verificar que el usuario puede ver la seccion
                if ($_SESSION["app-user"]["user"]["1"]["type"] == "administrador" || @$_SESSION["app-user"]["permission"]["1"]["update"] == "1"){
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
        return;
    }
    
    function getSpeakerList($noShow){
        $q      = @$this->select("speaker", "*", "active = '1'", "speaker_id asc", 0, $this->app["perPage"], "", $_GET["fireUI"]);
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
                if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"]["7"]["update"] == "1"){
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
    
    function getExhibitorList($noShow){
        $q      = @$this->select("exhibitor t1 left join category t2 on t1.category_id = t2.category_id", "t2.name as category,t1.* ", "t1.active = '1'", "exhibitor_id asc", 0, $this->app["perPage"], "", $_GET["fireUI"]);
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
                if ($_SESSION["app-user"]["user"][1]["type"] == "administrador" || $_SESSION["app-user"]["permission"]["6"]["update"] == "1"){
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
        $target_file = $path;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Verificar si el archivo ya existe
        if (file_exists($target_file)) { 
            $uploadOk["message"] =  $this->label["Archivo duplicado"]; 
            $uploadOk["result"] = 0;
        }
        //  Verificar el peso
        if ($_FILES[$input]["size"] > $size) {
            $uploadOk["message"] = $this->label["El tamaño de la imagen es muy grande. Tamaño máximo permitido:"].$size/1000 . "KB";
            $uploadOk["result"]= 0;    
        }
        //Verificar el tamaño
        $size = getimagesize($_FILES[$input]["tmp_name"]);
        $w = $size[0];
        $h = $size[1];
        if ($square=="" && $w != $h){
            $uploadOk["result"] = 0;
            $uploadOk["message"] = $this->label["La imagen proporcionada debe ser cuadrada"];
            return $uploadOk;
        }
        if ($square != "" && ($w < $h || $w == $h)){
            $uploadOk["result"] = 0;
            $uploadOk["message"] = $this->label["La imagen proporcionada debe ser rectangular. Más ancha que alta"];
            return $uploadOk;
        }
        
        if ($w > $width || $h > $height){
            $uploadOk["result"] = 0;
            $uploadOk["message"] = $this->label["La imagen proporcionada excede con el tamaño máximo permitido"] . ": {$width}x{$height}" ;
            return $uploadOk;
        }
        // Verificar el formato
        if(!in_array($imageFileType, $format_accepted)) 
        {
            $uploadOk["result"] = 0;
            $uploadOk["message"] = $this->label["Formato de la imagen no válido."];
        }
        // Varificar algun error
        if ($uploadOk["result"] != 0) {
            if (move_uploaded_file($_FILES[$input]["tmp_name"], $target_file)) {
                $uploadOk["result"] = 1;
            } else {
                $uploadOk["result"]  = 0;
            }
        }
        return $uploadOk;    
        
    }

    function getSurveyReport($eventId){
        $query  =   "select s.*, count(qr.user_id) as result
                    from
                    (select e.name as event_name, sq.question_id, sq.question, qo.option_id, qo.optionDesc
                    from event e, survey_question sq, question_option qo
                    where e.event_id = {$eventId} and e.event_id = sq.event_id and sq.question_id = qo.question_id and sq.active = 1 and qo.active = 1
                    order by sq.position, qo.position) as s
                    left join question_result qr
                    on s.question_id = qr.question_id and s.option_id = qr.option_id
                    group by s.question_id, s.option_id";
        $q      = $this->dbQuery($query);
        $aux    = "";
        if ($q){
            $id = 0;
            foreach($q as $k=>$v){
                $aux[$v["question_id"]]["event"]    = $v["event_name"];
                $aux[$v["question_id"]]["question"] = $v["question"];
                $aux[$v["question_id"]]["option"][$v["option_id"]]["option"]   = $v["optionDesc"];
                $aux[$v["question_id"]]["option"][$v["option_id"]]["result"]   = $v["result"];
            }
        }
        return $aux;
    }
    
    
    function getReviewReport($sessionId){
        //Obtener detalles de la sesion
        $query = "SELECT R.name , S.TITLE AS session_title, S.DATE AS date, S.SPEAKER AS speaker, 
                S.TIME AS time,
                COUNT(RE.review_id) AS reviewers, SUM(RE.ranking)/COUNT(RE.review_id) AS ranking
                FROM session S, room R, review RE
                WHERE S.session_id = '$sessionId' AND S.active = 1 AND S.room_id = R.room_id AND S.session_id = RE.session_id";
        $q["details"]      = $this->dbQuery($query);
        if ($q["details"]){
            foreach($q["details"] as $k=>$v){
                $q["details"][$k]["time"] = date("g:i  a", strtotime($v["time"]));
            }
            
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
        $query   = "SELECT t1.session_id, t2.title, t2.speaker, t2.time 
                    FROM review t1 left join session t2 on t1.session_id = t2.session_id 
                    WHERE t2.active = '1' AND t2.event_id = '$eventId' ORDER BY t1.session_id asc";
        $q       = $this->dbQuery($query);         
                    
        /*$q      = @$this->select("review t1 left join session t2 on t1.session_id = t2.session_id ", 
                                "t1.session_id, t2.title, t2.speaker, t2.time", 
                                "t2.active = '1'", 
                                "t1.session_id asc", 0, $this->app["perPage"], "GROUP BY t1.session_id", $_GET["fireUI"]);
        */
        $out     = "";
        if ($q){
            foreach($q as $k=>$v){
                 foreach($v as $sk=>$sv){
                    if (!in_array($sk, $noShow)){
                        $out[$k][$sk] = $sv;
                    } 
                }
                $formatted          =   date("g:i  a", strtotime($out[$k]["time"]));
                $out[$k]["time"]    =   $formatted;
                $out[$k]["action"]  = "<a href='./review_report.php?id={$v["session_id"]}'>{$this->label["Ver Resultados"]}</a>";
            }
        }   
        //$this->app["count"] = count($q);
        return $out;
        
    }
    function select($table, $fields="*", $where="", $order="id asc", $from=0, $size=10, $group="", $fireUI=""){
        if($order=="") $order = "id asc";
        if(is_array($fireUI)){
            if($fireUI["orderBy"]["field"]!="") {$dir[0] = "asc"; $dir[1] = "desc"; $order = "{$fireUI["orderBy"]["field"]} {$dir[$fireUI["orderBy"]["direction"]]}";}
            if($fireUI["filter"])               {foreach($fireUI["filter"] as $k=>$v){ $filter .= " AND `$k` LIKE '%".$this->clean($v)."%'";}}
            if($fireUI["currentPage"]!="")      {$from = ($fireUI["currentPage"]-1) * $size; }
            
        }
        
        $where = trim($where)==""? "1=1":$where;
        $query = "select $fields from $table where $where $filter $group order by $order limit $from, $size";
        $count = "select count(*) as count from $table where $where $filter $group order by $order";
        $q  = $this->dbQuery($query);
        $r  = $this->dbQuery($count);
        $this->app["count"] = $r["1"]["count"];
        return $q;
    }

    function insertRow($table, $data){
        return $this->dbInsert($table, $data);
    }
    
    function updateRow($table, $data, $condition){
        return $this->dbUpdate($table, $data, $condition);
    }
    
    function deleteRow($table, $condition){
        $query = "DELETE from $table WHERE $condition";
        return $this->dbQuery($query);
    }
    
    function selectRow($table, $condition){
        $query = "SELECT * from $table WHERE $condition";
        return $this->dbQuery($query);
    }
}

?>