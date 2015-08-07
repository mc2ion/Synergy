<?php
/* General */
$typeUser["Super Usuario"]    = "administrador"; //Super usuario puede entrar a todo.
$typeUser["Supervisor"]       = "cliente";
$typeUser["Administrador"]    = "cliente-administrador";


/* Author: Marion Carambula
Archivo de configuración. Aquí irá establecido que campos son obligatorios en cada sección, 
tipo de input para cada columna, los tamaños de imagenes permitidos, los directorios donde se va
a almacenar las imagenes entre otros.
*/

$general["client"]["image_folder"]                      = "files/client/";
$general["client"]["image_format"]                      = array("png"); //Aqui van definidos todos los tipo de imagenes permitidos para la sección correspondiente
$general["client"]["image_size"]                        = "50000"; //Aqui van definido el tamaño maximo permitido para la imagen en Bytes
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
   $input[section]["manage"]["mandatory"]                = "*"; si sólo algunos campos lo son, hay que definirlos en un arreglo.
   
   Todos las columnas por defecto son manejadas como input tipo "text". Para aquellas columnas donde deseamos que se muestre otro input
   como un select, o password por ejemplo tenemos que definirlo. Actualmente solo se están permitiendo los siguientes tipos:
   password, file, date (para que se muestre un datepicker), time (para que se muestre un timepicker), textarea  y select.
   Adicionalmente para los selects debemos definir cuales son las opciones que se van a mostrar, lo cual lo hacemos definiendo la siguiente variable
   $input[section]["manage"][columna]["options"]        = array("key"=>"value"). Por ejemplo,
   $input["user"]["manage"]["type"]["options"]          = array("1"=>"Opcion 1", "2"=>"Opción 2")
   
   Así definimos que en la sección "user", el campo "type"  que es de tipo select, muestre las opciones "Opcion 1", "Opción 2", con value 1, 2 respectivamente.
   
   

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
$input["client"]["manage"]["country"]["type"]                     = "select";
$input["client"]["manage"]["country"]["options"]                  = array(
    'Afganistán'=>'Afganistán',
    'Albania'=>'Albania',
    'Alemania'=>'Alemania',
    'Andorra'=>'Andorra',
    'Angola'=>'Angola',
    'Anguila'=>'Anguila',
    'Antártica'=>'Antártica',
    'Antigua y Barbuda'=>'Antigua y Barbuda',
    'Antillas Holandesa'=>'Antillas Holandesas',
    'Arabia Saudá'=>'Arabia Saudá',
    'Argelia'=>'Argelia',
    'Argentina'=>'Argentina',
    'Armenia'=>'Armenia',
    'Aruba'=>'Aruba',
    'Australia'=>'Australia',
    'Austria'=>'Austria',
    'Azerbaiján'=>'Azerbaiján',
    'Bélgica'=>'Bélgica',
    'Bahamas'=>'Bahamas',
    'Bahrain'=>'Bahrain',
    'Bangladesh'=>'Bangladesh',
    'Barbados'=>'Barbados',
    'Belarus'=>'Belarus',
    'Belice'=>'Belice',
    'Benin'=>'Benin',
    'Bermuda'=>'Bermuda',
    'Bolivia'=>'Bolivia',
    'Bosnia-Hercegovina'=>'Bosnia-Hercegovina',
    'Botswana'=>'Botswana',
    'Brasil'=>'Brasil',
    'Brunei Darussalam'=>'Brunei Darussalam',
    'Bulgaria'=>'Bulgaria',
    'Burkina Faso'=>'Burkina Faso',
    'Burundi'=>'Burundi',
    'Bután'=>'Bután',
    'Cabo Verde'=>'Cabo Verde',
    'Camboya'=>'Camboya',
    'Camerún'=>'Camerún',
    'Canadá'=>'Canadá',
    'Chad'=>'Chad',
    'Chile'=>'Chile',
    'China'=>'China',
    'Chipre'=>'Chipre',
    'Ciudad del Vaticano'=>'Ciudad del Vaticano',
    'Colombia'=>'Colombia',
    'Comoras'=>'Comoras',
    'Congo'=>'Congo',
    'Corea del Norte'=>'Corea del Norte',
    'Corea del Sur'=>'Corea del Sur',
    'Costa de Marfil'=>'Costa de Marfil',
    'Costa Rica'=>'Costa Rica',
    'Croacia'=>'Croacia',
    'Cuba'=>'Cuba',
    'Dinamarca'=>'Dinamarca',
    'Djibuti'=>'Djibuti',
    'Dominica'=>'Dominica',
    'Ecuador'=>'Ecuador',
    'Egipto'=>'Egipto',
    'El Salvador'=>'El Salvador',
    'Emiratos Árabes Unidos'=>'Emiratos Árabes Unidos',
    'Eritrea'=>'Eritrea',
    'Eslovaquia'=>'Eslovaquia',
    'Eslovenia'=>'Eslovenia',
    'España'=>'España',
    'Estonia'=>'Estonia',
    'Etiopía'=>'Etiopía',
    'Federación Rusa'=>'Federación Rusa',
    'Fiji'=>'Fiji',
    'Filipinas'=>'Filipinas',
    'Finlandia'=>'Finlandia',
    'Francia'=>'Francia',
    'Francia Metropolitana'=>'Francia Metropolitana',
    'Gabón'=>'Gabón',
    'Gambia'=>'Gambia',
    'Georgia'=>'Georgia',
    'Georgia del Sur e Islas Sandwich del Sur'=>'Georgia del Sur e Islas Sandwich del Sur',
    'Ghana'=>'Ghana',
    'Gibraltar'=>'Gibraltar',
    'Grecia'=>'Grecia',
    'Groenlandia'=>'Groenlandia',
    'Guadalupe'=>'Guadalupe',
    'Guam'=>'Guam',
    'Guatemala'=>'Guatemala',
    'Guayana Francesa'=>'Guayana Francesa',
    'Guinea'=>'Guinea',
    'Guinea Ecuatorial'=>'Guinea Ecuatorial',
    'Guinea-Bissau'=>'Guinea-Bissau',
    'Guyana'=>'Guyana',
    'Haití'=>'Haití',
    'Honduras'=>'Honduras',
    'Hong Kong'=>'Hong Kong',
    'Hungría'=>'Hungría',
    'India'=>'India',
    'Indonesia'=>'Indonesia',
    'Irán'=>'Irán',
    'Irak'=>'Irak',
    'Irlanda'=>'Irlanda',
    'Isla Bouvet'=>'Isla Bouvet',
    'Isla Christmas'=>'Isla Christmas',
    'Isla Norfolk'=>'Isla Norfolk',
    'Islandia'=>'Islandia',
    'Islas Caimanes'=>'Islas Caimanes',
    'Islas Cocos (Keeling)'=>'Islas Cocos (Keeling)',
    'Islas Cook'=>'Islas Cook',
    'Islas Faroe'=>'Islas Faroe',
    'Islas Heard y Mc Donald'=>'Islas Heard y Mc Donald',
    'Islas Malvinas'=>'Islas Malvinas',
    'Islas Marianas Septentrionales'=>'Islas Marianas Septentrionales',
    'Islas Marshall'=>'Islas Marshall',
    'Islas Salomón'=>'Islas Salomón',
    'Islas Svalbard y Jan Mayen'=>'Islas Svalbard y Jan Mayen',
    'Islas Turks y Caicos'=>'Islas Turks y Caicos',
    'Islas Vírgenes (Británicas)'=>'Islas Vírgenes (Británicas)',
    'Islas Vírgenes (EEUU)'=>'Islas Vírgenes (EEUU)',
    'Islas Wallis y Futuna'=>'Islas Wallis y Futuna',
    'Israel'=>'Israel',
    'Italia'=>'Italia',
    'Jamaica'=>'Jamaica',
    'Japón'=>'Japón',
    'Jordania'=>'Jordania',
    'Katar'=>'Katar',
    'Kazajistán'=>'Kazajistán',
    'Kenia'=>'Kenia',
    'Kirguizistán'=>'Kirguizistán',
    'Kiribati'=>'Kiribati',
    'Kuwait'=>'Kuwait',
    'Líbano'=>'Líbano',
    'Laos, República Popular'=>'Laos, República Popular',
    'Lesoto'=>'Lesoto',
    'Letonia'=>'Letonia',
    'Liberia'=>'Liberia',
    'Libia'=>'Libia',
    'Liechtenstein'=>'Liechtenstein',
    'Lituania'=>'Lituania',
    'Luxemburgo'=>'Luxemburgo',
    'México'=>'México',
    'Mónaco'=>'Mónaco',
    'Macao'=>'Macao',
    'Macedonia'=>'Macedonia',
    'Madagascar'=>'Madagascar',
    'Malasia'=>'Malasia',
    'Malaui'=>'Malaui',
    'Maldivas'=>'Maldivas',
    'Mali'=>'Mali',
    'Malta'=>'Malta',
    'Marruecos'=>'Marruecos',
    'Martinica'=>'Martinica',
    'Mauricio'=>'Mauricio',
    'Mauritania'=>'Mauritania',
    'Mayotte'=>'Mayotte',
    'Micronesia'=>'Micronesia',
    'Moldova'=>'Moldova',
    'Mongolia'=>'Mongolia',
    'Montserrat'=>'Montserrat',
    'Mozambique'=>'Mozambique',
    'Myanmar'=>'Myanmar',
    'Níger'=>'Níger',
    'Namibia'=>'Namibia',
    'Nauru'=>'Nauru',
    'Nepal'=>'Nepal',
    'Nicaragua'=>'Nicaragua',
    'Nigeria'=>'Nigeria',
    'Niue'=>'Niue',
    'Noruega'=>'Noruega',
    'Nueva Caledonia'=>'Nueva Caledonia',
    'Nueva Zelanda'=>'Nueva Zelanda',
    'Omán'=>'Omán',
    'Países Bajos'=>'Países Bajos',
    'Pakistán'=>'Pakistán',
    'Palau'=>'Palau',
    'Panamá'=>'Panamá',
    'Papua Nueva Guinea'=>'Papua Nueva Guinea',
    'Paraguay'=>'Paraguay',
    'Perú'=>'Perú',
    'Pitcairn'=>'Pitcairn',
    'Polinesia Francesa'=>'Polinesia Francesa',
    'Polonia'=>'Polonia',
    'Portugal'=>'Portugal',
    'Puerto Rico'=>'Puerto Rico',
    'Reino Unido'=>'Reino Unido',
    'República Árabe de Siria'=>'República Árabe de Siria',
    'República Centroafricana'=>'República Centroafricana',
    'República Checa'=>'República Checa',
    'República Dominicana'=>'República Dominicana',
    'Reunión'=>'Reunión',
    'Ruanda'=>'Ruanda',
    'Rumanía'=>'Rumanía',
    'Sahara Occidental'=>'Sahara Occidental',
    'Samoa'=>'Samoa',
    'Samoa Americana'=>'Samoa Americana',
    'San Cristóbal y Nevis'=>'San Cristóbal y Nevis',
    'San Marino'=>'San Marino',
    'San Vicente y las Granadinas'=>'San Vicente y las Granadinas',
    'Santa Elena'=>'Santa Elena',
    'Santa Lucía'=>'Santa Lucía',
    'Santo Tomé y Príncipe'=>'Santo Tomé y Príncipe',
    'Senegal'=>'Senegal',
    'Serbia y Montenegro'=>'Serbia y Montenegro',
    'Seychelles'=>'Seychelles',
    'Sierra Leona'=>'Sierra Leona',
    'Singapur'=>'Singapur',
    'Somalía'=>'Somalía',
    'Sri Lanka'=>'Sri Lanka',
    'St Pierre y Miquelon'=>'St Pierre y Miquelon',
    'Suazilandia'=>'Suazilandia',
    'Sudáfrica'=>'Sudáfrica',
    'Sudán'=>'Sudán',
    'Suecia'=>'Suecia',
    'Suiza'=>'Suiza',
    'Surinam'=>'Surinam',
    'Túnez'=>'Túnez',
    'Tailandia'=>'Tailandia',
    'Taiwan'=>'Taiwan',
    'Tanzanía'=>'Tanzanía',
    'Tayiquistán'=>'Tayiquistán',
    'Territorios australes y antárticos franceses'=>'Territorios australes y antárticos franceses',
    'Territorios Británicos del Océano Índico'=>'Territorios Británicos del Océano Índico',
    'Timor Oriental'=>'Timor Oriental',
    'Togo'=>'Togo',
    'Tokelau'=>'Tokelau',
    'Tonga'=>'Tonga',
    'Trinidad y Tobago'=>'Trinidad y Tobago',
    'Turkmenistán'=>'Turkmenistán',
    'Turquía'=>'Turquía',
    'Tuvalu'=>'Tuvalu',
    'Ucrania'=>'Ucrania',
    'Uganda'=>'Uganda',
    'Uruguay'=>'Uruguay',
    'USA'=>'USA',
    'Uzbekistán'=>'Uzbekistán',
    'Vanuatu'=>'Vanuatu',
    'Venezuela'=>'Venezuela',
    'Vietnam'=>'Vietnam',
    'Yemen'=>'Yemen',
    'Zaire'=>'Zaire',
    'Zambia'=>'Zambia',
    'Zimbabue'=>'Zimbabue',
    'Otro'=>'Otro',
	);
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
