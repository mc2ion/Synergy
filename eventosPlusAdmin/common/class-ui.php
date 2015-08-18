<?php
class ui  {
    
    var $app  = array();

    function buildTable($data){
       $row = 0;
       $tr  = "";
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
        $out =   "<table class=\"fireUI-table\">\n<thead>{$th}\n</thead>{$tr}</table>
                    <input style=\"display:none\" type=\"text\" name=\"fireUI[orderBy][field]\"     value=\"".htmlspecialchars($_GET["fireUI"]["orderBy"]["field"])."\"/>
                    <input style=\"display:none\" type=\"text\" name=\"fireUI[orderBy][direction]\" value=\"".htmlspecialchars($_GET["fireUI"]["orderBy"]["direction"])."\"/> ";
        return $out;
    }

    function setLabel($label){
        $this->app["labels"] = $label;
    }

    private function getLabel($key){
        if ($this->app["labels"][$key] != "") $out = $this->app["labels"][$key];
        else $out = $key;
        return $out;
    }
}