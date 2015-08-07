<?php
session_start();
include (__dir__."/../common/labels.php");
include("class-backend.php"); $backend = new backend($label);
$clientId = isset($_POST["id"]) ? $_POST["id"] :"" ;
$out    = "";
if ($clientId){
    $events = $backend->getEventList($clientId);
    if ($events){
        foreach ($events as $k=>$v){
            $out[$v["event_id"]] = $v["name"];
        }
    }
}
echo json_encode($out);
?>
