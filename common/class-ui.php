<?php
class ui  {
    
    var $app  = array();
    
        
    function buildTable($data,$order=0,$filter=0){
        $row = 0;
        $tr  = "";
        $out = $this->styleTables($order, $filter);
        if ($data != ""){
            foreach((array)$data as $k=>$v){    
                ($row == count($data) - 1) ? $class="class='last'" : $class = "";
                $row++; $tr .= "\n<tr $class>";
                foreach($v as $sk=>$sv) { 
                    ($this->app["labels"][$sk] != "") ? $labelsk = $this->app["labels"][$sk] : $labelsk = $sk;
                    $img = ""; if ($_GET["fireUI"]["filter"][$sk] != "") $img = "<span class='fireUI_filter'><img src='./images/filter.png'></span>";
                    if($row==1){
                        $th .= "\n  <th><span value=\"$sk\">". $labelsk."</span>$img" ; 
                                 if ($filter) $th .= "\n  <div class='fireUI_boxfilter'><input type=\"text\" placeholder='Escriba su filtro' name=\"fireUI[filter][$sk]\" value=\"".htmlspecialchars($_GET["fireUI"]["filter"][$sk])."\"/></div>"; 
                        $th .= "</th>";
                        }      
                                 $tr .= "\n  <td><span class='td-$sk'>$sv</span></td>" ; }
                $tr .= "\n</tr>";
            }
        }
        $out .= "<form id=\"fireUI-table-form\">";
        $out .=   "<table class=\"fireUI-table\">\n<thead>{$th}\n</thead>{$tr}</table>
                    <input style=\"display:none\" type=\"text\" name=\"fireUI[orderBy][field]\"     value=\"".htmlspecialchars($_GET["fireUI"]["orderBy"]["field"])."\"/>
                    <input style=\"display:none\" type=\"text\" name=\"fireUI[orderBy][direction]\" value=\"".htmlspecialchars($_GET["fireUI"]["orderBy"]["direction"])."\"/> ";
        $out .= "<input style=\"display:none\" type='submit'>
                </form>";
        $out .= $this->javascriptTable($order, $filter);
        
        return $out;
    }

    private function styleTables($order,$filter){
        $style .= "<style> 
                    .fireUI-table{width:100%;border-collapse: collapse; margin-top:20px; border: 1px solid rgb(180, 180, 180); font-size:12px;}
                    @media (min-width: 501px) {.fireUI-table tr:hover{background-color:#F7F7F7;}}
                    .fireUI-table td{border:1px solid #ccc;padding:4px;}
                    .fireUI-table thead{border:1px solid #ccc}
                    .fireUI-table *{text-align:left;}
                    .fireUI-table thead th{ font-size:12px;position:relative; background-color: #FFFF33; padding: 5px 4px; border: 1px solid #CCCCCC;}
                    .fireUI_navigation li{padding:10px;}
                    .fireUI-table span {overflow: hidden; text-overflow: ellipsis;}
                    ";
       if ($order || $filter){ 
       $style .=    "@media (min-width: 501px) {.fireUI-table thead th:hover{background: #F6E235;cursor: pointer; }}
                    .fireUI-table thead div{position:absolute;left:0px;top:38px;display:none; background:white; padding: 10px; box-shadow: 0px 0px 5px gray;}
                    .fireUI-table thead div input{padding: 3px; font-weight:normal;}";
       }
        if ($order) {
        $style .= "@media (min-width: 501px) {.fireUI-table thead span:hover{text-decoration:underline;cursor:pointer;}} ";
        }
        $style .= ".fireUI_filter{position:absolute; right:8px; top:4px;}
                    @media (min-width: 501px) {.fireUI_filter:hover{text-decoration:none !important;}}
                </style>";
        return $style;       
    }
    
    private function javascriptTable($order, $filter){
        $out = '<script>';
           if ($order){           
            $out.= '
                /* order by when clicking on stuff */
                $(".fireUI-table thead th span").click(function(event){
                event.stopPropagation();
                $("input[name=\'fireUI[orderBy][field]\']").val($(this).attr("value"));
                $("input[name=\'fireUI[orderBy][direction]\']").val(1-($("input[name=\'fireUI[orderBy][direction]\']").val()*1));
                $("#fireUI-table-form").submit(); })
                
                /* remove empty inputs on submit -- to prevent having a 2 km long URI */
                $("#fireUI-table-form").submit(function(){ $("#fireUI-table-form input").each(function(){ if($(this).val()==""){$(this).attr("disabled", "disabled")}}); return true; })
    
                /* display arrow on ordered field -- because its pretty */
                var arrow = []; arrow[0] = "&#9652;"; arrow[1] = "&#9662;"; 
                $("span[value=\'"+$("input[name=\'fireUI[orderBy][field]\']").val()+"\']").after(arrow[$("input[name=\'fireUI[orderBy][direction]\']").val()*1]);';
            }
            if ($filter){
            $out.= '/* toggle filter divs on header click */
                    $(".fireUI-table thead th").click(function(){event.stopPropagation(); $(".fireUI-table thead th div").hide(); $("div",this).show(); $(".fireUI-table thead th div input").focus(); })
                    /* open next filter box*/
                    $(".fireUI-table thead th div input").keydown(function (e){
                        var keyCode = e.keyCode || e.which; 
                        if (keyCode == 9) { 
                        e.preventDefault(); $(".fireUI-table thead th div").hide(); $(this).closest(\'th\').next().find(\'div\').show(); $(this).closest(\'th\').next().find(\'div input\').focus();
                        }
                    })
                    $( ".fireUI-table thead th div" ).last().css("right", "0px").css("left", "auto");
                    $("html").click(function() {$(".fireUI-table thead th div").hide();});';
            }
            $out.= '</script>';
            return $out;
    }
    

    function buildDatePicker($selected=""){
        if ($this->app["labels"]["date selection"] != "") $dateSel = $this->app["labels"]["date selection"]; else $dateSel = "date selection";
        if ($this->app["labels"]["Go"] != "") $go = $this->app["labels"]["Go"]; else $go = "Go";
        
        
        //Values for english
        $rel  = array("today"    =>$this->getLabel("today"),"yesterday"=>$this->getLabel("yesterday"),
                      "this week"=>$this->getLabel("this week"),"last week"=>$this->getLabel("last week"),
                      "this month"=>$this->getLabel("this month"),"last month"=>$this->getLabel("last month"),
                      "this year"=>$this->getLabel("this year"),"all time"=>$this->getLabel("all time"),
                      "custom"=>$this->getLabel("custom"));
        $label["start"] = $this->getLabel("start");
        $label["end"]   = $this->getLabel("end");
       
        $out .= "<div class='fireUI_datepicker'>
                    <form>$input
                    $dateSel";
        if ($_GET["fireUI"]["datePicker"]["relative"] == "") $_GET["fireUI"]["datePicker"]["relative"] = $selected;
        $out .= $this->arrayToSelectAux($rel,"fireUI[datePicker][relative]",$_GET["fireUI"]["datePicker"]["relative"]);
        
        $prepend = array('01','02','03','04','05','06','07','08','09'); 
        $date = array("day"=>array_merge($prepend,range(10, 31)), "month"=>array_merge($prepend,range(10, 12)),"year"=>range(date('Y')-5,date('Y')+1));
        $ver["start"] = 1; $ver["end"] = 1; 
        
        $aux = array("day"  => date("d"), "month" => date("m"), "year" => date("Y"));
        $out .= "<input type='submit' value='$go'/><div class=\"fireUI-dates-holder\">";
        foreach($ver as $vk=>$vv){$out .= "<span>{$label[$vk]}</span>"; foreach($date as $k=>$v) {
            if ($_GET["fireUI"]["datePicker"]["$vk"][$k] == "") $_GET["fireUI"]["datePicker"]["$vk"][$k] = $aux[$k];
            $out .= $this->arrayToSelect($v,"fireUI[datePicker][$vk][$k]",$_GET["fireUI"]["datePicker"]["$vk"][$k]);}}
        $out .= "</div>";
        $out .= "</form></div>";
        $out .= $this->styleDatePicker();
        $out .= $this->javascriptDatePicker();
        return $out;
    }
    
    function setLabel($label){
        $this->app["labels"] = $label;
    }
    
    private function arrayToSelect($array,$name,$current){
        $out = "\n<select name=\"$name\">";
        foreach($array as $k=>$v){
            $sel  = ($v==$current)?"selected":"";
            $out .= "\n<option {$sel}>$v</option>";
        }
        $out .= "\n</select>";
        return $out;
    }
    
    private function arrayToSelectAux($array,$name,$current){
        $out = "\n<select name=\"$name\">";
        foreach($array as $k=>$v){
            $sel  = ($k==$current)?"selected":"";
            $out .= "\n<option {$sel} value='$k'>$v</option>";
        }
        $out .= "\n</select>";
        return $out;
    }
    
    private function styleDatePicker(){
        $style = "<style>
                    .fireUI_datepicker {text-align: right;}
                    .fireUI-dates-holder{padding:10px 0px 10px 0px;}
                    .fireUI_datepicker input{ padding: 2px 7px;}
                    .fireUI_datepicker select {padding: 2px 0px; margin-right:5px;}
                    .fireUI_datepicker select:last-child{margin-right:0px;}
                    .fireUI_datepicker {margin-bottom:10px;}
                </style>";
        return $style;
    }
    
    private function javascriptDatePicker(){
        $out = '<script>
                $("select[name=\'fireUI[datePicker][relative]\']").change(function(){
                if($(this).val()!="custom"){var status = "disabled"; var display = "none" ;} else { var status = false; var display = "" ;}
                $("select[name^=\'fireUI[datePicker][start]\'],select[name^=\'fireUI[datePicker][end]\']").attr("disabled", status); $(".fireUI-dates-holder").css("display", display);
                $("select[name^=\'fireUI[datePicker][start][year]\'],select[name^=\'fireUI[datePicker][end][year]\'], select[name^=\'fireUI[datePicker][relative]\']");
        }).trigger("change");
              </script>';
        return $out;
    }
    
    private function getLabel($key){
        if ($this->app["labels"][$key] != "") $out = $this->app["labels"][$key];
        else $out = $key;
        return $out;
    }
    
    
   function buildPagination($currentPage, $total_pages, $includeCss=1, $adjacents=3) {
        $total_pages = ceil($total_pages);
        if ($total_pages > 1){
            if ($currentPage == "") $currentPage = 1;
            $labels = array("first" => "&lsaquo;&lsaquo;", "prev"=> "&lsaquo;", "next"=>"&rsaquo;", "last"=>"&rsaquo;&rsaquo;" );
            
            $out =  '<ul class="fireUI_pagination">';
            if ($currentPage == 1) $class = "class='fireUI_li_inactive'"; else $class="";
            $out.="<li $class><a href=\"".$_SERVER['PHP_SELF']."?".$this->buildUrl(1)."\">".$labels["first"]."</a>\n</li>";
            $out.="<li $class><a href=\"".$_SERVER['PHP_SELF']."?".$this->buildUrl($currentPage - 1)."\">".$labels["prev"]."</a>\n</li>"; 
            
            $pmin=($currentPage>$adjacents)?($currentPage - $adjacents):1;
            $pmax=($currentPage<($total_pages - $adjacents))?($currentPage + $adjacents):$total_pages;
            for ($i = $pmin; $i <= $pmax; $i++) {
                if ($i == $currentPage) $active = "class='fireUI_li_active'"; else $active = "";
                $out.= "<li $active><a href=\"".$_SERVER['PHP_SELF']."?".$this->buildUrl($i)."\">".$i."</a>\n</li>";
            }
            if ($currentPage == $total_pages) $class = "class='fireUI_li_inactive'"; else $class="";
            $out.= "<li $class><a href=\"".$_SERVER['PHP_SELF']."?".$this->buildUrl($currentPage + 1)."\">".$labels["next"]."</a>\n</li>";
            $out.= "<li $class><a href=\"".$_SERVER['PHP_SELF']."?".$this->buildUrl($total_pages)."\">".$labels["last"]."</a>\n</li>";
            $out .= "</ul>";
            if ($includeCss) $out .= $this->stylePagination();
            $out .= $this->javascriptPagination();
        }
        return $out;
    }  
    
    private function buildUrl($page){
        $params = $_GET;
        $params[fireUI][currentPage] = $page;
        $paramString = http_build_query($params);
        return $paramString;
    }  
    
    
    private function stylePagination(){
        $style = "<style>
                    .fireUI_pagination {margin:20px 0px 30px; text-align:right;}
                    .fireUI_pagination li {display: inline-block; padding: 0px 2px; }
                    .fireUI_pagination a {text-decoration: none; color: #404040; cursor: pointer; padding: 5px 10px;}
                    @media (min-width: 501px) {.fireUI_pagination a:hover{background:#F2F2F2; padding: 5px 10px;}}
                    .fireUI_li_active a {background: #EAEFFA}
                    .fireUI_li_inactive a {color: #e4e4e4;}
                    @media (min-width: 501px) {.fireUI_li_inactive a:hover{background: none; cursor: default;}}
                    </style>";
        return $style;
    }
    
    private function javascriptPagination(){
        $out = '<script>
                $(".fireUI_li_inactive").click(function(){  event.stopPropagation(); return false;})
              </script>';
        return $out;
    }
    
    function  buildEdit($array, $fieldsType=array(), $exclude=array(),  $id="", $optionsSelect=array()){
        $out = $this->buildForm($array, "Edit Entry.", "edit", $fieldsType, $exclude, $id, $optionsSelect);
        $out .= $this->styleForm();
        return $out;    
    }
    
    function  buildView($array, $fieldsType=array()){
        $out = $this->buildForm($array, "View Entry.", "view", "", $fieldsType);
        $out .= $this->styleForm();
        return $out;        
    }
    
    function  buildAdd($array, $fieldsType=array(), $exclude=array(), $optionsSelect=array()){
        $out = $this->buildForm($array, "Add Entry.", "add", $fieldsType, $exclude, "", $optionsSelect);
        $out .= $this->styleForm();
        return $out;        
    }
    
    private function buildForm($array, $title, $action, $fieldsType=array(), $exclude=array(), $id="", $optionSelect=array()){
       // print_r($exclude);
        $html  = '<h2 class="fireui-wborder">'.$title.'</h2>   
                    <form action="'.$_SERVER['HTTP_REFERER'].'" method="post" name="bid">
                    <input type="hidden" name="id" value="'.$id.'"/>
                    <div id="form_content" class="fireUI_table">
                        <table id="fireUI_table" class="fireUI_datatable">';
                         foreach ($array as $k => $v){
                            if (!in_array($k, (array)$exclude)){
                                if ($action != "add") $value = $v; 
                                $columnName = $k;
                                if ( $action == "view"){ $disable = "readOnly=true style='background: #F3F3F3; border: 1px solid rgb(188, 188, 188);'";}
                               
                                $type = $fieldsType[$k]; if ($type == "") $type = "text";
                                if ($this->app["labels"][$k] != "") $columnName = $this->app["labels"][$k];
                                $html .='<tr><td><label for="'.$columnName.'">'.$columnName.':</label></td>';
                                       
                                if ($type == "select"){
                                    $html .= '<td><select name="fireUI_'.$k.'" >';
                                    foreach ((array) $optionSelect as $l=>$m){  
                                        $seleted = ""; if ($value == $l) $seleted = "selected";
                                        $html .= '<option value="'.$l.'" '. $seleted . '>'.$m.'</option>';
                                    }
                                    $html .= '</select></td>';
                                }
                                else if ($type != "textarea") $html .='<td><input type="'.$type.'" name="fireUI_'.$k.'" value="'. $value.'" '.$disable.'/></td>';
                                else  $html .='<td><textarea name="fireUI_'.$k.'">'.$value.'</textarea></td>';
                                $html .='</tr>';
                            }
                         }
                        $html .= '<tr><td class="col_a">&nbsp;</td><td>';
                            if ($this->app["labels"]["Save"] != "")   $save = $this->app["labels"]["Save"]; else $save = "Save";
                            if ($this->app["labels"]["Delete"] != "") $delete = $this->app["labels"]["Delete"]; else $delete = "Delete";
                            if ($this->app["labels"]["Cancel"] != "") $cancel = $this->app["labels"]["Cancel"]; else $cancel = "Cancel";
                            
                            if ($action == "edit" ){
                                $html .= '<input name="fireUI_'.$action.'-entry" type="submit" value="'.$save.'" />';
                                $html .=  '<input id="sponsorjobs" name="fireUI_delete-entry" type="submit" class="fireui-important" value="'.$delete.'"/>';
                            } 
                            else if ($action == "add"){
                                $html .= '<input name="fireUI_'.$action.'-entry" type="submit" value="'.$save.'" />';
                            }
                            $html .= '<a href="'.$_SERVER['HTTP_REFERER'].'" ">'.$cancel.'</a>
                            </td></tr></table></div></form>';
        return $html;
    }
    
    function styleForm(){
        $style = "<style>
                    .fireUI_wborder { border-bottom: 1px solid #29ADE4; font-size: 20px; padding-bottom: 10px;margin-bottom: 25px;}
                    table#fireUI_table { width: 500px; }
                    #fireUI_table label { min-width: 200px; display: inline-block; }
                    #fireUI_table input[type=text], #fireUI_table input[type=password],  #fireUI_table select { width: 300px; padding: 5px !important;}
                    #fireUI_table textarea { width: 300px;height: 100px;padding: 5px;}
                    table.fireUI_datatable tr td { padding: 5px 0; vertical-align: top; }
                    .fireUI_important { background: #FFBEBE; border: 1px outset buttonface; }
                    #fireUI_table input { margin-right: 3px; padding: 2px 5px;}
                    .fireUI_datatable a {color: #404040;}
                    .fireui-important {background: #FFB9BB; }
                    
                </style>";
        return $style;
    }
    
    function getFilterDate(){
         if ($_GET["fireUI"]["datePicker"]["relative"] != ""){
           if($_GET["fireUI"]["datePicker"]["relative"] != "custom") {
            $dateStart = $_GET["fireUI"]["datePicker"]["relative"] . "00:00:01";
            $dateEnd   = $_GET["fireUI"]["datePicker"]["relative"] . "23:59:59";
            
            if ($_GET["fireUI"]["datePicker"]["relative"]      == "this week") {  $dateStart= "monday this week 00:00:01";                          $dateEnd="sunday this week 23:59:59";}
            else if ($_GET["fireUI"]["datePicker"]["relative"] == "last week") {  $dateStart= "monday this week 00:00:01 - 1 week";                 $dateEnd="sunday this week 23:59:59 - 1 week" ;}
            else if ($_GET["fireUI"]["datePicker"]["relative"] == "next week") {  $dateStart= "monday this week 00:00:01 + 1 week ";                $dateEnd="sunday this week 23:59:59 + 1 week ";}
            else if ($_GET["fireUI"]["datePicker"]["relative"] == "this month"){  $dateStart= "first day of this month 00:00:01";              $dateEnd="last day of this month 23:59:59";}
            else if ($_GET["fireUI"]["datePicker"]["relative"] == "last month"){  $dateStart= "first day of this month 00:00:01 - 1 month ";    $dateEnd="last day of this month 23:59:59 - 1 month ";}
            else if ($_GET["fireUI"]["datePicker"]["relative"] == "next month"){  $dateStart= "first day of this month 00:00:01 + 1 month ";    $dateEnd="last day of this month 23:59:59 + 1 month ";}
            else if ($_GET["fireUI"]["datePicker"]["relative"] == "this year") {  $dateStart= "first day of January this year 00:00:01";       $dateEnd="last day of December this year 23:59:59";}
            else if ($_GET["fireUI"]["datePicker"]["relative"] == "all time")  {   $dateStart= "";       $dateEnd="";}
            $out["start"] = strtotime($dateStart);
            $out["end"] = strtotime($dateEnd);
           }else{
               $day   = $_GET["fireUI"]["datePicker"]["start"]["year"]."-".$_GET["fireUI"]["datePicker"]["start"]["month"]."-".$_GET["fireUI"]["datePicker"]["start"]["day"];
               $dayE  = $_GET["fireUI"]["datePicker"]["end"]["year"]."-".$_GET["fireUI"]["datePicker"]["end"]["month"]."-".$_GET["fireUI"]["datePicker"]["end"]["day"];
               $out["start"] = strtotime($day);
               $out["end"] = strtotime($dayE . "23:59:59");
           }
       }
       return $out;
    }
    
}