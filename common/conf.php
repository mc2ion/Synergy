<?php
/* General */
$typeUser["Super Usuario"]    = "administrador"; //Super usuario puede entrar a todo.
$typeUser["Supervisor"]       = "cliente";
$typeUser["Administrador"]    = "cliente-administrador";


/* Author: Marion Carambula
Archivo de configuraci�n. Aqu� ir� establecido que campos son obligatorios en cada secci�n, 
tipo de input para cada columna, los tama�os de imagenes permitidos, los directorios donde se va
a almacenar las imagenes entre otros.
*/

$general["client"]["image_folder"]                      = "files/client/";
$general["client"]["image_format"]                      = array("png"); //Aqui van definidos todos los tipo de imagenes permitidos para la secci�n correspondiente
$general["client"]["image_size"]                        = "50000"; //Aqui van definido el tama�o maximo permitido para la imagen en Bytes
$general["client"]["image_width"]                       = "300";   //Aqui van definido el ancho maximo permitido para la imagen en px
$general["client"]["image_height"]                      = "300";   //Aqui van definido la altura maxima permitido para la imagen en px

$general["event"]["image_folder"]                       = "files/event/";
$general["event"]["image_format"]                       = array("png");
$general["event"]["image_size"]                         = "400000"; //Bytes
$general["event"]["image_width"]                        = "2560";  //px
$general["event"]["image_height"]                       = "1440"; 
$general["event"]["image_type"]                         = "rectangle"; // Si es cuadrada no hace falta indicarlo 

$general["session"]["image_folder"]                     = "files/session/";
$general["session"]["image_format"]                     = array("png");
$general["session"]["image_size"]                       = "50000"; //Bytes
$general["session"]["image_width"]                      = "300";  //px
$general["session"]["image_height"]                     = "300"; 

$general["user"]["image_folder"]                        = "files/user/";
$general["user"]["image_format"]                        = array("png");
$general["user"]["image_size"]                          = "50000"; //Bytes
$general["user"]["image_width"]                         = "300";  //px
$general["user"]["image_height"]                        = "300";

$general["profile"]["image_folder"]                        = "files/user/";
$general["profile"]["image_format"]                        = array("png");
$general["profile"]["image_size"]                          = "50000"; //Bytes
$general["profile"]["image_width"]                         = "300";  //px
$general["profile"]["image_height"]                        = "300";

$general["speaker"]["image_folder"]                     = "files/speaker/";
$general["speaker"]["image_format"]                     = array("png");
$general["speaker"]["image_size"]                       = "50000"; //Bytes
$general["speaker"]["image_width"]                      = "300";  //px
$general["speaker"]["image_height"]                     = "300"; 

$general["exhibitor"]["image_folder"]                   = "files/exhibitor/";
$general["exhibitor"]["image_format"]                   = array("png");
$general["exhibitor"]["image_size"]                     = "50000"; //Bytes
$general["exhibitor"]["image_width"]                    = "300";  //px
$general["exhibitor"]["image_height"]                   = "300"; 

/* Para todas las secciones se debe definir los campos obligatorios. Si todos los campos lo son se puede colocar
   $input[section]["manage"]["mandatory"]                = "*"; si s�lo algunos campos lo son, hay que definirlos en un arreglo.
   
   Todos las columnas por defecto son manejadas como input tipo "text". Para aquellas columnas donde deseamos que se muestre otro input
   como un select, o password por ejemplo tenemos que definirlo. Actualmente solo se est�n permitiendo los siguientes tipos:
   password, file, date (para que se muestre un datepicker), time (para que se muestre un timepicker), textarea  y select.
   Adicionalmente para los selects debemos definir cuales son las opciones que se van a mostrar, lo cual lo hacemos definiendo la siguiente variable
   $input[section]["manage"][columna]["options"]        = array("key"=>"value"). Por ejemplo,
   $input["user"]["manage"]["type"]["options"]          = array("1"=>"Opcion 1", "2"=>"Opci�n 2")
   
   As� definimos que en la secci�n "user", el campo "type"  que es de tipo select, muestre las opciones "Opcion 1", "Opci�n 2", con value 1, 2 respectivamente.
   
   

*/

/** Usuarios **/
$input["user"]["manage"]["mandatory"]                   = array("first_name", "last_name", "email", "password", "type", "client_id"); // Aqui van todos los campos obligatorios.
$input["user"]["manage"]["no-show"]                     = array("user_id"); // Aqui van aquellos campos que no queremos que se muestren a la hora de editar/crear porque no son editables y no se requiere que el usuario los maneje.
$input["user"]["manage"]["photo_path"]["type"]          = "file"; //Aqui definimos los campos que van a tener un input diferente a "text" que es el usado por defecto
$input["user"]["manage"]["type"]["type"]                = "select"; //Aqui definimos los campos que van a tener un input diferente a "text" que es el usado por defecto
$input["user"]["manage"]["password"]["type"]            = "password"; //Aqui definimos los campos que van a tener un input diferente a "text" que es el usado por defecto
$input["user"]["manage"]["type"]["options"]             = array("Super Usuario"=>"Super Usuario", "Supervisor"=>"Supervisor" ); // Al ser "type" un input select tenemos que definir las opciones a mostrar
$input["user"]["manage"]["client_id"]["type"]           = "select";
$input["user"]["list"]["no-show"]                       = array("user_id", "password", "photo_path"); // Aqui van aquellos campos que no queremos que se muestren en el listado.

/** Perfil de Usuario **/
$input["profile"]["manage"]["mandatory"]                   = array("first_name", "last_name"); // Aqui van todos los campos obligatorios.
$input["profile"]["manage"]["no-show"]                     = array("user_id", "client_id", "type"); // Aqui van aquellos campos que no queremos que se muestren a la hora de editar/crear porque no son editables y no se requiere que el usuario los maneje.
$input["profile"]["manage"]["password"]["type"]            = "password";
$input["profile"]["manage"]["photo_path"]["type"]          = "file"; //Aqui definimos los campos que van a tener un input diferente a "text" que es el usado por defecto


/** Cliente **/
$input["client"]["manage"]["mandatory"]                 = "*";
$input["client"]["manage"]["no-show"]                   = array("client_id", "active");
$input["client"]["manage"]["logo_path"]["type"]         = "file";
$input["client"]["list"]["no-show"]                     = array("client_id", "contact_phone","active", "primary_color1", "primary_color2", "secondary_color");
/** Fin seccion cliente **/

/** Evento **/
$input["event"]["manage"]["mandatory"]                  = array("name", "map_path", "date_ini", "address", "phone", "website", "organizers");
$input["event"]["manage"]["no-show"]                    = array("event_id", "client_id", "active");

//Si options esta vacio es porque estoy buscando un query en particular
$input["event"]["manage"]["map_path"]["type"]           = "file";
$input["event"]["manage"]["description"] ["type"]       = "textarea";
$input["event"]["manage"]["address"]["type"]            = "textarea";
$input["event"]["manage"]["date_ini"]["type"]           = "date";
$input["event"]["manage"]["date_end"] ["type"]          = "date";
$input["event"]["manage"]["social_networks"]["type"]    = "special";
$input["event"]["manage"]["social_networks"]["options"] = array("twitter"=>"Twitter", "facebook"=>"Facebook", "instagram"=>"Instagram", "linkedin"=>"LinkedIn");
$input["event"]["manage"]["organizers"]["type"]         = "special";
$input["event"]["list"]["no-show"]                      = array("event_id", "client_id", "website", "phone", "address", "active", "social_networks", "organizers", "map_path");
/** Fin seccion evento **/

/* Encuestas */
$input["survey"]["manage"]["mandatory"]                 = array("question");
$input["survey"]["manage"]["no-show"]                   = array("question_id", "event_id","active");
$input["survey"]["manage"]["question"]["type"]          = "textarea";
$input["survey"]["list"]["no-show"]                     = array("question_id", "event_id","active");

/* Evaluaciones */
$input["review"]["list"]["no-show"]                     = array("session_id");

/* Salas */
$input["room"]["manage"]["mandatory"]                   = "*";
$input["room"]["manage"]["no-show"]                     = array("room_id", "event_id","active");
$input["room"]["manage"]["question"]["type"]            = "textarea";
$input["room"]["list"]["no-show"]                       = array("room_id","event_id", "active");
/** Fin seccion evento **/

/* Sesion */
$input["session"]["manage"]["mandatory"]                = array("room_id", "title", "date", "speaker", "time", "image_path");
$input["session"]["manage"]["no-show"]                  = array("session_id", "event_id","active");
$input["session"]["manage"]["question"]["type"]         = "textarea";
$input["session"]["list"]["no-show"]                    = array("session_id","event_id", "description", "time", "link", "active");
$input["session"]["manage"]["date"]["type"]             = "date";
$input["session"]["manage"]["time"]["type"]             = "time";
$input["session"]["manage"]["description"]["type"]      = "textarea";
$input["session"]["manage"]["image_path"]["type"]       = "file";
$input["session"]["manage"]["room_id"]["type"]          = "select";

/* Speakers */
$input["speaker"]["manage"]["mandatory"]                = array("name", "company_name", "description", "image_path", "time");
$input["speaker"]["manage"]["no-show"]                  = array("speaker_id", "event_id","active");
$input["speaker"]["manage"]["other"]["type"]            = "textarea";
$input["speaker"]["manage"]["description"]["type"]      = "textarea";
$input["speaker"]["list"]["no-show"]                    = array("speaker_id","event_id", "description", "other", "active");
$input["speaker"]["manage"]["image_path"]["type"]       = "file";
$input["speaker"]["manage"]["session_title"]["type"]    = "select";

/* Expositores */
$input["exhibitor"]["manage"]["mandatory"]              = array("name", "company_name", "description", "image_path", "time", "category_id");
$input["exhibitor"]["manage"]["no-show"]                = array("exhibitor_id", "event_id","active");
$input["exhibitor"]["list"]["no-show"]                  = array("exhibitor_id","event_id","company_name", "description", "position", "other", "active");
$input["exhibitor"]["manage"]["category_id"]["type"]    = "select";
$input["exhibitor"]["manage"]["description"]["type"]    = "textarea";
$input["exhibitor"]["manage"]["other"]["type"]          = "textarea";
$input["exhibitor"]["manage"]["image_path"]["type"]     = "file";
$input["exhibitor"]["manage"]["session_title"]["type"]  = "select";

?>
