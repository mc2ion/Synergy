<?php
    include (__dir__."/../common/conf.php");
    session_start();
    header("Content-type: text/css; charset: UTF-8");
    $main_menu              = "#000000";
    $top_menu               = "#fbe336";
    $button                 = "#e8b113";
    $main_menu_axu          = "#333333";
    $submenu                = "#161616";
    $font_main_menu         = "#ffffff";
    $font_top_menu          = "#000000";

    if ($typeUser[$_SESSION["app-user"]["user"][1]["type"]] == "cliente"){
        $main_menu          = $_SESSION["app-user"]["user"][1]["main_menu_color"];
        $top_menu           = $_SESSION["app-user"]["user"][1]["top_menu_color"];
        $main_menu_axu      = $_SESSION["app-user"]["user"][1]["main_menu_color_aux"];
        $button             = $_SESSION["app-user"]["user"][1]["button_color"];
        $submenu            = $_SESSION["app-user"]["user"][1]["main_submenu_color"];
        $font_main_menu     = $_SESSION["app-user"]["user"][1]["font_main_menu_color"];
        $font_top_menu     = $_SESSION["app-user"]["user"][1]["font_top_menu_color"];
    }
?>/* Generales */
*{ font-family: "Trebuchet MS", Helvetica, sans-serif; margin: 0; padding: 0; font-size: 13px;}
body{ background: white; }
body.login {background: #E9E9E9;}
/* Menu y barra superior */
.menu{width: 250px; background-color: <?= $main_menu?>;  position: fixed; height: 100%;/* border-right: 1px solid rgb(126, 126, 126); */ z-index: 2;text-align: center;}
.menu li { list-style-type: none;}
.menu li a { display: inline-block; color: <?= $font_main_menu?>; text-decoration: none; width: 217px; padding: 15px; border-left: 3px solid <?= $main_menu?>; position: relative;}
.menu li a:hover{background: <?= $main_menu_axu?>;border-left: 3px solid <?= $top_menu ?>;}
.menu li a.selected{ background: <?= $main_menu_axu?>; border-left: 3px solid <?= $top_menu ?>; width: 217px;}
.submenu li a.selected{ background: <?= $main_menu_axu?>; border-left: 3px solid <?= $top_menu ?>; width: 192px;}
.logo {margin: 25px 0px 0px; max-width: 180px; max-height: 60px;}
.admin {color:white; text-align: center; padding: 15px 0px;}
.top-bar{width: 100%; background-color: <?= $top_menu ?>; padding: 5px 0px;  text-align: right; font-size: 12px;margin-right: 0px;position: fixed;box-shadow: 0px 0px 4px gray;z-index: 1;height: 30px;}
.logout {color: <?= $font_top_menu?>;margin-right: 0px;display: inline-block;margin-left: 5px;font-weight: bold;margin-left: 15px;margin-right: 10px;}
.content {margin-left: 250px;padding: 60px 30px;}
.topu{text-align: left; padding: 20px 0px;border-bottom: 1px solid #333333;}
.mng select{  width: 96%; padding: 5px !important;  margin: 10px auto;  display: block;}
.user-info{display: inline-block;  position: relative;  top: 1px;  text-align: left;margin-right: 20px;line-height: 30px;}
.img {display: inline-block;}
.arrow{position: absolute;  right: 10px;}
ul.submenu {  background-color: <?= $submenu?>;}
.submenu li a{border-left: 3px solid <?= $submenu?>; width: 192px;padding-left: 40px;}
.dcjq-icon {  height: 17px;  width: 17px;  display: inline-block;  background: url(../images/expand.png) no-repeat top;  border-radius: 3px;  -moz-border-radius: 3px;  -webkit-border-radius: 3px;  position: absolute;  right: 10px;}
span.fireUI_filter img { width: 10px; margin-top: 5px;}
/* Listado */
.title {    font-size: 22px;    margin-bottom: 15px;}
.title-manage{   font-size: 22px;  margin-bottom: 25px;    border-bottom: 1px solid gray;    padding-bottom: 5px;}
.add { font-size: 13px;  text-decoration: none;    padding: 7px; color: #000; display: inline-block; line-height: 16px;      border: 1px solid #848484;  margin-left: 10px;  transition: background 2s ;  -webkit-transition: all 0.5s; position:relative; top:-3px; cursor: pointer; background: <?= $top_menu ?>; /* Old browsers */  background: -moz-linear-gradient(top,  <?= $top_menu ?> 0%, <?= $button ?> 100%); /* FF3.6+ */  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?= $top_menu ?>), color-stop(100%,<?= $button ?>)); /* Chrome,Safari4+ */  background: -webkit-linear-gradient(top,  <?= $top_menu ?> 0%,<?= $button ?> 100%); /* Chrome10+,Safari5.1+ */  background: -o-linear-gradient(top,  <?= $top_menu ?> 0%,<?= $button ?> 100%); /* Opera 11.10+ */  background: -ms-linear-gradient(top,  <?= $top_menu ?> 0%,<?= $button ?> 100%); /* IE10+ */  background: linear-gradient(to bottom,  <?= $top_menu ?> 0%,<?= $button ?> 100%); /* W3C */  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?= $top_menu ?>', endColorstr='<?= $button ?>',GradientType=0 ); /* IE6-9 */;}
.add:hover{    text-decoration: underline;}
fieldset {   border: none; }
label{display: block;  width: 200px; vertical-align: top;}
.fireUI-table a {  color: #333;}
.fireUI-table img { width: 30px;}
.center{ text-align: center;}
.center a { color: rgb(0, 131, 185); text-decoration: none;}
.center a:hover { text-decoration: underline;}
.description {resize: none; height: 300px;width: 100%;}
form#fireUI-table-form * {  font-size: 13px;  table-layout: fixed;}
img.image-photo {height: 30px; vertical-align: middle; margin-right: 15px;    border-radius: 50%;    width: 30px;    -moz-border-radius: 50%;    -webkit-border-radius: 50%;}
span.td-logo_path {text-align: center;}
/* Formularios editar y crear */
input[type="file"]{width: 270px;}
.manage-content{border-collapse: collapse;}
tr.organizers-name td { padding-top: 10px;}
.content table.manage-content td {  padding-bottom: 8px;   position: relative;}
.content table.manage-content{width: 550px;}
.content table.manage-content td.tdf {width: 215px;font-weight: 500;vertical-align: top;}
.content table.manage-content input[type="text"], .content table.manage-content input[type="password"] {   width: 100%;   padding: 6px 2px;    border: 1px solid rgb(182, 182, 182);   box-sizing: border-box;}
.content table.manage-content input[type="submit"], .mng input[type=submit] {  border: none;  padding: 8px 15px;  color: black; cursor: pointer;   border: 1px solid #848484;  background: <?= $top_menu ?>; /* Old browsers */  background: -moz-linear-gradient(top, <?= $top_menu ?> 0%, <?= $button ?> 100%); /* FF3.6+ */  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?= $top_menu ?>), color-stop(100%,<?= $button ?>)); /* Chrome,Safari4+ */  background: -webkit-linear-gradient(top, <?= $top_menu ?> 0%,<?= $button ?> 100%); /* Chrome10+,Safari5.1+ */  background: -o-linear-gradient(top, <?= $top_menu ?> 0%,<?= $button ?> 100%); /* Opera 11.10+ */  background: -ms-linear-gradient(top, <?= $top_menu ?> 0%,<?= $button ?> 100%); /* IE10+ */  background: linear-gradient(to bottom, <?= $top_menu ?> 0%,<?= $button ?> 100%); /* W3C */  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?= $top_menu ?>', endColorstr='<?= $button ?>',GradientType=0 ); /* IE6-9 */;  margin-right: 5px;}
.content table.manage-content input[type="submit"]:hover { text-decoration: underline;}
.content table.manage-content input[type="submit"].important{
background: #7d7e7d; /* Old browsers */  background: -moz-linear-gradient(top, #7d7e7d 0%, #0e0e0e 100%); /* FF3.6+ */  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#7d7e7d), color-stop(100%,#0e0e0e)); /* Chrome,Safari4+ */  background: -webkit-linear-gradient(top, #7d7e7d 0%,#0e0e0e 100%); /* Chrome10+,Safari5.1+ */  background: -o-linear-gradient(top, #7d7e7d 0%,#0e0e0e 100%); /* Opera 11.10+ */  background: -ms-linear-gradient(top, #7d7e7d 0%,#0e0e0e 100%); /* IE10+ */  background: linear-gradient(to bottom, #7d7e7d 0%,#0e0e0e 100%); /* W3C */  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7d7e7d', endColorstr='#0e0e0e',GradientType=0 ); /* IE6-9 */;
color: white !important;
}
.content table.manage-content .important:hover{ background-color: rgb(184, 184, 184) !important;}
.content table.manage-content a { color:black; text-decoration:none;}
.content table.manage-content a:hover { text-decoration:underline;}
.content table.manage-content .action { text-align: right; padding-top: 25px;}
.manage-content select {  width: 100%;  padding: 5px 2px;  border: 1px solid rgb(182, 182, 182);}
.manage-content textarea {  width: 100%;  height: 100px;  resize: none;  border: 1px solid rgb(182, 182, 182);  padding: 4px;  box-sizing: border-box;}
img.ui-datepicker-trigger {  position: absolute;  right: 3px;  top: 3px;  width: 22px;}
.datepicker, .timepicker{cursor:pointer;}
.org-desc {margin-top: 10px;}
.organizer, .option {background-color: rgb(244, 244, 244); padding: 7px 0px;    border-radius: 2px;    -moz-border-radius: 2px;    -webkit-border-radius: 2px;}
.c2 {width: 49%; float: left;}
.c2.left{margin-right: 1%;}
.c2.right{margin-left: 1%;}
.add-e, .add-org, .add-opt{position: absolute;  right: -95px;  top: 7px;}
.add-e a, .add-org a, .add-opt a{color: rgb(68, 68, 68) !important;}
.delete, .delete-org, .delete-opt{position: absolute;  right: -55px; top: 7px;}
.delete a, .delete-org a, .delete-opt a{ color:rgb(185, 0, 28) !important;}
.add-org {    top: 20px;}
.delete.i0, .delete-org.i0, .delete-opt.i0 {display: none;}
.networks, .organizer, .option {margin-bottom: 5px;display: inline-block;width: 100%; position: relative;}
.manage-image{max-width: 200px; padding-bottom: 10px;}
.succ{font-size: 20px;    margin-bottom: 25px;background-color: rgb(217, 249, 207);  padding: 6px 5px;color: #545454;}
.center{text-align: center;}
.organizer .top {margin-bottom: 10px;}
tr.tr_organizers * {font-size: 16px;}
tr.tr_organizers td {border-bottom: 1px solid #848484;margin-bottom: 10px;}
.label {    display: table-cell;    width: 215px;vertical-align: top;}
.value {    display: table-cell;    width: 335px;}
.options-title { font-size: 16px; border-bottom: 1px solid #848484;margin-bottom: 10px;}
td.option-td {padding-top: 10px;}
.option textarea{height: 50px;}
.add-opt {top:17px; right: -90px;}
.missing-error {  color: rgb(185, 0, 28);  margin-top: 2px;  font-size: 12px;  margin-bottom: 5px;}
div.error {color: #545454;  margin-bottom: 20px;  font-size: 16px;background-color: #FBE0E0;padding: 10px 5px;}
label.error { color: #A80000; width: 100%; padding-top: 2px;}
input.mandatory.error, select.mandatory.error, textarea.mandatory.error  {border: 1px solid #A80000 !important;}
table.fireUI-table td span{   white-space: nowrap;  overflow: hidden;   text-overflow: ellipsis;   width: 100%;   display: inline-block;}
.form{position:relative;background: #333;width:285px;margin:40px auto 0;padding:40px;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;-webkit-box-shadow:0 1px 2px 0 rgba(0,0,0,.15);-moz-box-shadow:0 1px 2px 0 rgba(0,0,0,.15);box-shadow:0 1px 2px 0 rgba(0,0,0,.15);border: 1px solid gray;}
.form h2 {margin: 0 0 20px;line-height: 1; color: white; font-size: 16px; font-weight: 300;}
.form input {outline: none; display: block; width: 100%; margin: 0 0 20px; padding: 10px 15px;  border: 1px solid #d9d9d9; -webkit-border-radius: 3px; -moz-border-radius: 3px;  border-radius: 3px; color: #494949; -webkti-box-sizing: border-box; -moz-box-sizing: border-box;  box-sizing: border-box; font-size: 14px; font-wieght: 400; -webkit-font-smoothing: antialiased;  -moz-osx-font-smoothing: grayscale; -webkit-transition: all 0.3s linear 0s;  -moz-transition: all 0.3s linear 0s;  -ms-transition: all 0.3s linear 0s; -o-transition: all 0.3s linear 0s;  transition: all 0.3s linear 0s;}
.form input:focus { color: #333333; border: 1px solid #33b5e5;}
.form button{cursor:pointer;background: <?= $top_menu ?>; /* Old browsers */  background: -moz-linear-gradient(top, <?= $top_menu ?> 0%, <?= $button ?> 100%); /* FF3.6+ */  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?= $top_menu ?>), color-stop(100%,<?= $button ?>)); /* Chrome,Safari4+ */  background: -webkit-linear-gradient(top, <?= $top_menu ?> 0%,<?= $button ?> 100%); /* Chrome10+,Safari5.1+ */  background: -o-linear-gradient(top, <?= $top_menu ?> 0%,<?= $button ?> 100%); /* Opera 11.10+ */  background: -ms-linear-gradient(top, <?= $top_menu ?> 0%,<?= $button ?> 100%); /* IE10+ */  background: linear-gradient(to bottom, <?= $top_menu ?> 0%,<?= $button ?> 100%); /* W3C */  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?= $top_menu ?>', endColorstr='<?= $button ?>',GradientType=0 ); /* IE6-9 */;width:100%;padding:10px 15px;margin-bottom:25px;border:0; color: #333;font-size:14px;font-weight:400;}
#dialog-confirm {display: none;}
.form button:hover { text-decoration: underline;}
.form footer{background: rgb(255, 237, 94);width:100%;padding:15px 40px;margin:0 0 -40px -40px;-webkit-border-radius:0 0 3px 3px;-moz-border-radius:0 0 3px 3px;border-radius:0 0 3px 3px;color:#666;font-size:12px;text-align:center}
.form footer a{color:#333;text-decoration:none}
.info{width:100%;margin:40px auto;text-align:center}
.info h1{margin:0;padding:0;font-size:24px;font-weight:400;color:#333}
.content-login{position:absolute;top:50%;left:50%;margin-left: -200px;margin-top:-250px;width: 400px;}
.mng input[type=submit]{float:right;margin-right:5px;padding:6px}
.infu,.title>div{margin-top:10px;text-align: left;}
.td-image_path {text-align: center !important;}
.image_format { display: none; margin-top: 5px;  color: rgb(84, 84, 84);  font-style: italic;  font-size: 12px;}
img.mandatory {  width: 9px;}
.opt-position {  margin-top: 10px;}
.actions{float: right; margin-bottom:10px;}
.action a:hover {  text-decoration: underline;}
.action a {  color: black;  text-decoration: none;}
.tr_permi{  border-bottom: 1px solid rgb(0, 0, 0); margin-bottom: 10px;  font-size:16px;}
.mng-text{  width: 96%; margin: 2%;color: <?= $font_main_menu?>; text-align: center;  padding: 15px 0px 5px;}
div.top-sb.submenu-holder a {text-decoration: none; color: <?= $font_top_menu?>;}
div.top-sb.submenu-holder {display: inline-block;position: relative;line-height: 29px;}
div.submenu-holder:hover ul.sbm { display: block;}
ul.sbm {/* display: none; */ position: absolute;  margin-top: 1px; z-index: 200; min-width: 140px; background-color: rgb(245, 245, 245);box-shadow: 0px 0px 2px gray;margin-left: -10px;}
span.darrow {    margin-left: 5px;    font-size: 9px;    top: -1px;    position: relative;}
li.sbm { line-height: 36px; text-align: center; list-style-type: none;}
.arrow-up {width: 0; height: 0; border-left: 5px solid transparent;	border-right: 5px solid transparent;border-bottom: 5px solid #D5D5D5;	margin: 0 auto;}
.subm{margin:6px 0px;display: none;}
a.w {    height: 100%;    width: 100%;    display: inline-block;}
img.information { cursor: pointer;    position: absolute;    right: 0px; top: -2px;}
table th span { margin-right: 5px;}
ul.sesList{margin-left: 35px;}
.ui-dialog p {    margin: 0px 0px 10px 0px;}
.darrow-menu{position: absolute;top:18px;right: 10px; font-size:10px;}
.back{color:black;}
/* Login */
.login .error{text-align: center;}
.error-login {color: #FF9F9F; padding-bottom: 15px;}
.succ-login  {color: #6DD76D; padding-bottom: 15px;}
input[type="text"]:disabled, input[type="password"]:disabled {/* background-color: white; *//* border: none !important; *//* color: black; */}
a.pass {position: absolute;top: 0px;right: -140px; color: rgb(68, 68, 68) !important;padding: 5px 5px;}
div.pass{line-height:25px;}
.fireUI-table{width:100%;border-collapse: collapse; margin-top:20px; border: 1px solid rgb(180, 180, 180); font-size:12px;}
.fireUI-table td{border:1px solid #ccc;padding:4px;}
.fireUI-table thead{border:1px solid #ccc}
.fireUI-table *{text-align:left;}
.fireUI-table thead th{ font-size:12px;position:relative; background-color: <?= $main_menu_axu?>; color:<?= $font_main_menu?>; padding: 5px 4px; border: 1px solid #CCCCCC;}
.fireUI_navigation li{padding:10px;}
.fireUI-table span {overflow: hidden; text-overflow: ellipsis;}
.dataTables_filter label{ display: inline-block !important; width:100%; margin-bottom:15px; vertical-align:middle;}
.dataTables_filter input{ padding:5px;}
input.code {width: 14% !Important;float: left;margin-right: 1%;}
input.phone {float: left;width: 85% !important;}
