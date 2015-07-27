<?php
/*
    Author: Marion Carambula
    Sección de eventos
*/
include ("./common/common-include.php");
//Verificar que el usuario tiene  permisos
if ($_SESSION["app-user"]["user"][1]["type"] == "client" && $_SESSION["app-user"]["permission"]["9"]["read"] == "0"){ header("Location: ./index.php"); exit();}


$out = "";
$surveys = $backend->getSurveyReport($_SESSION["data"]["evento"]);
$event   = $backend->getEvent($_SESSION["data"]["evento"]);
$title   = $event["name"];


if (isset($_POST["pdf"])&& $surveys){
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

if (isset($_POST["cvs"])&& $surveys){
    include_once('./backend/csv/simple_html_dom.php');
    $out = '<table class="ttq">';
            foreach($surveys as $k=>$v){
                $out .='<tr>
                    <td class="title" colspan="2"><h1>'.$v["question"].'</h1></td>
                </tr>';
                foreach($v["option"] as $sk=>$sv){
                $out .='<tr>
                            <td>'.$sv["option"].'</td><td>'.$sv["result"].'</td>
                        </tr>';
                }
            }
            $out .= '</table>';
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

function getReport($title){
        global $surveys, $label;
        $out = '
                <style>
                    h1{font-size:16px; font-weight:normal;}
                    .ttq td.title { background-color: #4CC554;    padding: 5px;    border: 1px solid;}
                    table.ttq {width: 100%;border: 1px solid gray;  border-collapse: collapse;  text-align: center;  table-layout: fixed; margin-top:30px;}
                    .ttq td   {border: 1px solid;  padding: 4px 0px;}
                    .title    {margin-bottom:10px;}
                </style>';
        if ($surveys){
            $out .= '<div class="title"> Reporte -'. $title.'</div>';
            $out .= '<table class="ttq">';
            foreach($surveys as $k=>$v){
                $out .='<tr>
                            <td class="title" colspan="2"><h1>'.$v["question"].'</h1></td>
                        </tr>';
                foreach($v["option"] as $sk=>$sv){
                $out .='<tr>
                            <td>'.$sv["option"].'</td><td>'.$sv["result"].'</td>
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
    <?= menu("encuestas"); ?>
    <div class="content">
        <?= $globalMessage ?>
        <?php if ($surveys) { ?>
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
        <a href="./surveys.php"><?= $label["Volver"]?></a>
       </div>
    </div>
  </body>
</html>                                                                                                                                                                