<?php
/*
    Author: Marion Carambula
    Sección de eventos
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"]["9"]["read"] == "0"){ header("Location: ./index.php"); exit();}


$out        = "";
$review     = $backend->getReviewReport($_GET["id"]);
$title      = $review["details"]["1"]["session_title"];


if (isset($_POST["pdf"])&& $review){
        require_once("./backend/dompdf/dompdf_config.inc.php");
        $htmlString = '';
        $htmlString = utf8_decode(getReport($title));
        $dompdf = new DOMPDF();
        $dompdf->set_paper("letter", "portrait");
        $dompdf->load_html($htmlString);
        $dompdf->render();
        $dompdf->stream($title.".pdf");
        exit(0);   
}

if (isset($_POST["cvs"])&& $review){
    include_once('./backend/csv/simple_html_dom.php');
    $out  = getReport($title, "1");
    $html = str_get_html(utf8_decode($out));
    header('Content-type: application/ms-excel');
    header("Content-Disposition: attachment; filename={$title}.csv");
    $fp = fopen("php://output", "w");
    foreach($html->find('tr') as $element)
    {
        $td = array();
        foreach( $element->find('th') as $row)  
        {
            $td [] = $row->plaintext;
        }
        fputcsv($fp, $td);
    
        $td = array();
        foreach( $element->find('td') as $row)  
        {
            $td [] = $row->plaintext;
        }
        fputcsv($fp, $td);
    }
    fclose($fp);
    exit();
}

function getReport($title, $excel="0"){
        global $review, $label;
        $out = '
                <style>
                    h1{font-size:16px; font-weight:normal;}
                    .title{font-size:20xp;}
                    .ttq td.title { background-color: #4CC554;    padding: 5px;    border: 1px solid;}
                    table.ttq {width: 100%;border: 1px solid gray;  border-collapse: collapse;  text-align: center;  table-layout: fixed; margin-top:15px;}
                    .ttq td   {border: 1px solid;  padding: 4px 0px;}
                    .title    {margin-bottom:10px;}
                    table.ranking { width: 400px; margin-top:20px;}
                    table.dtails{width: 450px; margin-top:30px;}
                    table.dtails td{padding-bottom: 5px;}
                    table.comments {width: 100%;margin-top: 25px;}
                    .comments td.cmments {background-color: #F7F7F7 ; padding: 10px; margin-top: 10px;}
                    .rnk {display: block;text-align: right;}
                    img.ssm {width: 12px; margin-left:1px;}
                </style>';
        if ($review){
            $out .= '<div class="title">'. $label["Resultado evaluacion"].' - '. $title.'</div>';
            $out .= '<table class="dtails">';
            foreach ($review["details"][1] as $k=>$v){
                if ($k != "reviewers" && $k != "ranking" ){
                    $out .= "<tr>";
                    $out .= "<td><b>".$label[$k] ."</b>:</td><td>" .$v . "</td>";
                    $out .= "</tr>";
                }
            }
             $out .= '</table>';
            
            $out .= "<table class='ranking'>    
                        <tr>
                        <td><b>{$label["Cantidad de votaciones"]}</b>: {$review["details"][1]["reviewers"]}</td>
                        <td><b>{$label["Valoracion Total"]}</b>: {$review["details"][1]["ranking"]} </td>
                        </tr>
                        </table>
                        <div style='clear:both'></div>";
           
            // Raking
            $out .= '<table class="ttq">';
            $out .='<tr>
                        <td class="title" colspan="1"><h1>'. $label["Ranking"].'</h1></td>
                        <td class="title" colspan="1"><h1>'.$label["Cantidad"].'</h1></td>
                    </tr>';
            if ($excel == 0){
                for ($j=1; $j < 6; $j++){
                    $stars[$j] = '<img src="./images/star_empty.png" class="bgst">';
                }
            }
            for($i = 1; $i < 6; $i++){
                if ($excel == 0){
                    $stars[$i] = '<img src="./images/star_filled.png" class="bgst">';
                    $st        = implode($stars);
                }else{
                    $st         = $i;
                }
                $r = 0;
                if (isset($review["reviews"]["$i"])) $r = $review["reviews"]["$i"]["reviewers"];
                $v               = $label["Votaciones"];
                if ($r <= 1 ) $v = $label["Votacion"];
                $out .='<tr>
                        <td>'.$st.'</td>
                        <td>'. $r. ' ' . $v.'</td>
                    </tr>';
            }
            $out .= '</table>';
           
            //Comentarios
            if ($review["comments"]){
                $out .= '<table class="comments">';
                $out .= '<tr><td colspan="2"><h1>'. $label["Comentarios"].'</h1></td></tr>';
                foreach($review["comments"] as $k=>$v){
                        if ($excel == "0"){
                            $stars = array('<img src="./images/star_empty.png" class="ssm">', '<img src="./images/star_empty.png" class="ssm">',
                               '<img src="./images/star_empty.png" class="ssm">', '<img src="./images/star_empty.png" class="ssm">', '<img src="./images/star_empty.png" class="ssm">');
                             if($v["ranking"] == "1")  {$stars[0] = '<img src="./images/star_filled.png" class="ssm">';}
                             if($v["ranking"] == "2")  {$stars[0] = $stars[1] = '<img src="./images/star_filled.png" class="ssm">';}
                             if($v["ranking"] == "3")  {$stars[0] = $stars[1] = $stars[2] = '<img src="./images/star_filled.png" class="ssm">';}
                             if($v["ranking"] == "4")  {$stars[0] = $stars[1] = $stars[2] = $stars[3] = '<img src="./images/star_filled.png" class="ssm">';}
                             if($v["ranking"] == "5")  {$stars[0] = $stars[1] = $stars[2] = $stars[3] = $stars[4] = '<img src="./images/star_filled.png" class="ssm">';}
                             $st = implode($stars);
                        }else{
                            $st = $label["Valoracion"] . ": ". $v["ranking"] . "/5";
                        }
                         $out .='<tr style="height:5px;"><td></td></tr>
                                <tr>
                                <td colspan="2" class="cmments">
                                <span class="rnk">'.$st .'</span>'.$v["comment"].'</td>
                            </tr>';
                }
                
            }         
            $out .= '</table>'; 
            
        }else{
            $out .= '-- '. $label["Esta encuesta no posee resultados aún"]. ' --';
        }
        return $out;
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <?= my_header()?>
     <link rel="stylesheet" href="./css/surveyprint.css" type="text/css" media="print" />
  </head>
  <body>
    <?= menu("evaluaciones"); ?>
    <div class="content">
        <?= $globalMessage ?>
        <?php if ($review) { ?>
            <div class="actions">
            <form method="post">
                <input type="submit" class='add' name="cvs"     value="<?= $label["Exportar Excel"]?>"/>
                <input type="submit" class='add' name="pdf"     value="<?= $label["Exportar PDF"]?>"/>
                <a href="javascript:window.print()" class="add"><?= $label["Imprimir"]?></a>
            </form>
            </div>
        <?php } ?>
    
       <?= getReport($title)?>
       <div style="margin-top: 15px; text-align: right;" class="action">
        <a href="./reviews.php"><?= $label["Volver"]?></a>
       </div>
    </div>
  </body>
</html>                                                                                                                                                                