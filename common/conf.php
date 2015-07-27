<?php
/* Author: Marion Carambula
Archivo de configuración. Aquí irá establecido que campos son obligatorios en cada sección 
Si se puede utilizar * para indicar que todos los campos lo son
*/

$general["client"]["image_folder"]     = "files/client/";
$general["client"]["image_format"]     = array("png");
$general["client"]["image_size"]       = "50000"; //Bytes
$general["client"]["image_width"]      = "300";  //px
$general["client"]["image_height"]     = "300"; 

$general["event"]["image_folder"]       = "files/event/";
$general["event"]["image_format"]       = array("png");
$general["event"]["image_size"]         = "400000"; //Bytes
$general["event"]["image_width"]        = "2560";  //px
$general["event"]["image_height"]       = "1440"; 
$general["event"]["image_type"]         = "rectangle"; // Si es cuadrada no hace falta indicarlo 

$general["session"]["image_folder"]     = "files/session/";
$general["session"]["image_format"]     = array("png");
$general["session"]["image_size"]       = "50000"; //Bytes
$general["session"]["image_width"]      = "300";  //px
$general["session"]["image_height"]     = "300"; 

$general["user"]["image_folder"]                 = "files/user/";
$general["user"]["image_format"]                 = array("png");
$general["user"]["image_size"]                   = "50000"; //Bytes
$general["user"]["image_width"]                  = "300";  //px
$general["user"]["image_height"]                 = "300"; 

$general["speaker"]["image_folder"]                   = "files/speaker/";
$general["speaker"]["image_format"]                   = array("png");
$general["speaker"]["image_size"]                     = "50000"; //Bytes
$general["speaker"]["image_width"]                    = "300";  //px
$general["speaker"]["image_height"]                   = "300"; 

$general["exhibitor"]["image_folder"]                   = "files/exhibitor/";
$general["exhibitor"]["image_format"]                   = array("png");
$general["exhibitor"]["image_size"]                     = "50000"; //Bytes
$general["exhibitor"]["image_width"]                    = "300";  //px
$general["exhibitor"]["image_height"]                   = "300"; 

/** Usuarios **/
$input["user"]["manage"]["mandatory"]            = array("first_name", "last_name", "email", "password", "type");
$input["user"]["manage"]["no-show"]              = array("user_id");
$input["user"]["manage"]["photo_path"]["type"]   = "file";
$input["user"]["manage"]["type"]["type"]         = "select";
$input["user"]["manage"]["password"]["type"]     = "password";
$input["user"]["manage"]["type"]["options"]      = array("administrador"=>"Administrador", "cliente"=>"Cliente" );
$input["user"]["manage"]["client_id"]["type"]    = "select";
$input["user"]["list"]["no-show"]                 = array("user_id", "password", "photo_path");

/** Cliente **/
$input["client"]["manage"]["mandatory"]             = "*";
$input["client"]["manage"]["no-show"]               = array("client_id", "active");
$input["client"]["manage"]["logo_path"]["type"]     = "file";
$input["client"]["list"]["no-show"]                 = array("client_id", "contact_phone","active");
/** Fin seccion cliente **/

/** Evento **/
$input["event"]["manage"]["mandatory"]              = array("name", "map_path", "date_ini", "address", "phone", "website", "organizers");
$input["event"]["manage"]["no-show"]                = array("event_id", "client_id", "active");

//Si options esta vacio es porque estoy buscando un query en particular
$input["event"]["manage"]["map_path"]["type"]                        = "file";
$input["event"]["manage"]["description"] ["type"]                    = "textarea";
$input["event"]["manage"]["address"]["type"]                         = "textarea";
$input["event"]["manage"]["date_ini"]["type"]                        = "date";
$input["event"]["manage"]["date_end"] ["type"]                       = "date";
$input["event"]["manage"]["social_networks"]["type"]                 = "special";
$input["event"]["manage"]["social_networks"]["options"]              = array("twitter"=>"Twitter", "facebook"=>"Facebook", "instagram"=>"Instagram", "linkedin"=>"LinkedIn");
$input["event"]["manage"]["organizers"]["type"]                      = "special";
$input["event"]["list"]["no-show"]                                   = array("event_id", "client_id", "website", "phone", "address", "active", "social_networks", "organizers", "map_path");
/** Fin seccion evento **/

/* Encuestas */
$input["survey"]["manage"]["mandatory"]              = array("question");
$input["survey"]["manage"]["no-show"]                = array("question_id", "event_id","active");
$input["survey"]["manage"]["question"]["type"]       = "textarea";
$input["survey"]["list"]["no-show"]                  = array("question_id", "event_id","active");

/* Evaluaciones */
$input["review"]["list"]["no-show"]                  = array("session_id");

/* Salas */
$input["room"]["manage"]["mandatory"]               = "*";
$input["room"]["manage"]["no-show"]                 = array("room_id", "event_id","active");
$input["room"]["manage"]["question"]["type"]        = "textarea";
$input["room"]["list"]["no-show"]                   = array("room_id","event_id", "active");
/** Fin seccion evento **/

/* Sesion */
$input["session"]["manage"]["mandatory"]              = array("room_id", "title", "date", "speaker", "time");
$input["session"]["manage"]["no-show"]                = array("session_id", "event_id","active");
$input["session"]["manage"]["question"]["type"]       = "textarea";
$input["session"]["list"]["no-show"]                  = array("session_id","event_id", "description", "time", "link", "active");
$input["session"]["manage"]["date"]["type"]           = "date";
$input["session"]["manage"]["time"]["type"]           = "time";
$input["session"]["manage"]["description"]["type"]    = "textarea";
$input["session"]["manage"]["image_path"]["type"]     = "file";
$input["session"]["manage"]["room_id"]["type"]        = "select";

/* Speakers */
$input["speaker"]["manage"]["mandatory"]              = array("name", "company_name", "description", "image_path", "time");
$input["speaker"]["manage"]["no-show"]                = array("speaker_id", "event_id","active");
$input["speaker"]["manage"]["other"]["type"]          = "textarea";
$input["speaker"]["manage"]["description"]["type"]    = "textarea";
$input["speaker"]["list"]["no-show"]                  = array("speaker_id","event_id", "description", "other", "active");
$input["speaker"]["manage"]["image_path"]["type"]     = "file";
$input["speaker"]["manage"]["session_title"]["type"]  = "select";

/* Expositores */
$input["exhibitor"]["manage"]["mandatory"]              = array("name", "company_name", "description", "image_path", "time");
$input["exhibitor"]["manage"]["no-show"]                = array("exhibitor_id", "event_id","active");
$input["exhibitor"]["list"]["no-show"]                  = array("exhibitor_id","event_id","company_name", "description", "position", "other", "active");
$input["exhibitor"]["manage"]["category_id"]["type"]    = "select";
$input["exhibitor"]["manage"]["description"]["type"]    = "textarea";
$input["exhibitor"]["manage"]["other"]["type"]          = "textarea";
$input["exhibitor"]["manage"]["image_path"]["type"]     = "file";
$input["exhibitor"]["manage"]["session_title"]["type"]  = "select";





?>
