<?php 
    class json2html {
 
        public $filepath = "";
 
        public function __construct () {
            //Iz uvoza datoteke določi pot do datoteke
            $this->filepath = (isset($_POST['filepath'])) ? $_POST['filepath'] : "";
        }
 
        public function convert() {
            $fileArr = $this->get_file_data_in_array();
            //Oblikovanje JSON datoteke (displayana na stran)
            echo "JSON struktura malo lepše:";
            echo "<pre>";var_dump($fileArr);echo "</pre>";
            $htmlResult = "";
            foreach ($fileArr as $key => $singleElement) {
                if ($key == "doctype") {
                    $htmlResult .= "<!DOCTYPE " . $singleElement . ">\r";        
                } 
                if ($key == "language") {
                    $htmlResult .= "<html lang='" . $singleElement . "'>\r";
                } 
 
                //HEAD HTML datoteke
                if ($key == "head") {
                    $htmlResult .= "\t<head>\r";
                    foreach ($singleElement as $headKey => $headElement) {
                        if ($headKey == "meta") {
                            foreach ($headElement as $metaKey => $metaElement) {
                                if ($metaKey == "charset") {
                                    $htmlResult .= "\t\t<meta charset='" . $metaElement . "'>\r";
                                }
                                if ($metaKey == "keywords") {
                                    $htmlResult .= "\t\t<meta name='keywords' content='" . $metaElement . "'>\r";
                                }
                                if($metaKey =="viewport"){
                                    $htmlResult .= "\t\t<meta name='viewport' content='";
                                    foreach($metaElement as $attributeKey => $attributeElement){
                                        if($attributeKey == "width"){
                                            $htmlResult .= "width=" . $attributeElement . ",";
                                        }
                                        if($attributeKey == "initial-scale"){
                                            $htmlResult .= "initial-scale=" . $attributeElement;
                                        }
                                    }
                                    $htmlResult .= "'>\r";
                                }
                            }
                        }
                        elseif($headKey == "link"){
                            $htmlLinkAdd = "";
                            foreach($headElement as $linkKey => $linkElement){
                                $htmlLinkAdd .= "\t\t<link";
                                foreach($linkElement as $linkKeyAtt => $linkAttribute){
                                    if($linkKeyAtt == "href"){
                                        $htmlLinkAdd .= " href='" . $linkAttribute . "'";
                                    }
                                    elseif($linkKeyAtt == "rel"){
                                        $htmlLinkAdd .= " rel='" . $linkAttribute . "'";
                                    }
                                    elseif($linkKeyAtt == "type"){
                                        $htmlLinkAdd .= " type='" . $linkAttribute . "'";
                                    } 
                                }
                                $htmlLinkAdd .= ">\r";                                
                            }
                            $htmlResult .= $htmlLinkAdd;
                             
                        }
                        elseif($headKey == "title"){
                            $htmlResult .= "\t\t<title>" . $headElement . "</title>\r";
                        }
                    }
                    $htmlResult .= "\t</head>\r";
                } 
 
                //BODY HTML datoteke
                if($key == "body"){
                    $atResult = $datResult = "";
                    $htmlResult .= "\t<body [[AT]]>\r";
                    foreach ($singleElement as $bodyKey => $bodyElement){
                        if($bodyKey == "attributes"){
                            foreach($bodyElement as $attributesKey => $attributesElement){
                                if($attributesKey == "id"){
                                    $atResult .= " id='" . $attributesElement . "'";
                                }
                                if($attributesKey == "style"){
                                    $atResult .= " style='";
                                    foreach($attributesElement as $styleKey => $styleElement){
                                        if($styleKey == "width"){
                                            $atResult .= "width:" . $styleElement . ";";
                                        }
                                        if($styleKey == "height"){
                                            $atResult .= "height:" . $styleElement . ";";
                                        }
                                        if($styleKey == "text-align"){
                                            $atResult .= "text-align:" . $styleElement . ";";
                                        }
                                    }
                                    $atResult .= "'";
                                }
                                                               
                            }
                            //$htmlResult .= ">\r";
                        }
 
                        if($bodyKey =="h1"){
                            $htmlResult .= "\t\t<h1>" . $bodyElement . "</h1>\r";
                        }
                        if($bodyKey =="p"){
                            $htmlResult .= "\t\t<p>" . $bodyElement . "</p>\r";
                        }
                        if($bodyKey =="h2"){
                            $htmlResult .= "\t\t<h2>" . $bodyElement . "</h2>\r";
                        }
                        if($bodyKey == "div"){
                            $htmlResult .= "\t\t<div [[DAT]]>\r";
                            foreach($bodyElement as $divKey => $divElement){
                                if($divKey == "attributes"){
                                    foreach($divElement as $attributeKey => $attributeElement){
                                        if($attributeKey == "class"){
                                            $datResult .= "class='" . $attributeElement . "'"; 
                                        }
                                    }
                                    //$htmlResult .= ">\r";
                                }
                                
                                if($divKey == "h3"){
                                    $htmlResult .= "\t\t\t<h3>" . $divElement . "</h3>\r";
                                }
                                if($divKey == "myTag"){
                                    $htmlResult .= "\t\t\t<myTag>\r";
                                    foreach($divElement as $myTagKey => $myTagElement){
                                        if($myTagKey == "p"){
                                            $htmlResult .= "\t\t\t\t<p>" . $myTagElement . "</p>\r";
                                        }
                                    }
                                    $htmlResult .= "\t\t\t</myTag>\r";
                                }
                            }
                            $htmlResult .= "\t\t</div>\r";
                        }
                    }
                    $htmlResult .= "\t</body>\r";
                    $htmlResult .= "</html>\r";
                    //Naknadno dodajanje atributov v tag
                    $htmlResult = str_replace("[[AT]]", trim($atResult), $htmlResult);
                    $htmlResult = str_replace("[[DAT]]", trim($datResult), $htmlResult);
                }
            }
            //Izpis html vsebine na 
            echo "<hr>Sestava HTML datoteke:";
            echo "<br><pre>" . htmlspecialchars($htmlResult);
 
            //Ustvari datoteko in v njo zapiše HTML kodo
            file_put_contents("ConvertedFile.html", $htmlResult);
 
        }
 
        //Prebere, decoda in zapiše v array JSON file
        public function get_file_data_in_array() {
            $file = file_get_contents($this->filepath);
            return json_decode($file, true);
        }
    }
?>
<html>
    <head>
        <title>JSON to HTML converter</title>
        <style>
            html {
                height: 100%;
                background-color: #f7f2e7;
            }
            body {
                background-color: #f7f2e7;
                font-family: "Trebuchet MS", Helvetica, sans-serif;
                text-align: left;
            }
            table {
                border: 1px solid #aaa;
                margin: 20px;
            }
            tr:nth-child(even) {
                background: #ccc;
            }
            tr:nth-child(odd) {
                background: #eee;
            }
            td {
                padding: 3px 15px;
                vertical-align: top;
            }
            .btnsubmit {
                width: 200px;
                height: 30px;
                background-color: #ccc;
                border: 1px solid #aaa;
                font-size: 17px;
                color: #333;
            }
            .btnsubmit:hover {
                cursor: pointer;
                background-color: #aaa;
                border: 1px solid #888;
                color: #eee;
                text-shadow: 0 0 15px black;
            }
        </style>
    </head>
    <body>
        <h1>JSON to HTML converter</h1>
        <div class="col-sm-6">
        <form action="#" method="post">
            <input type="hidden" name="action" value="importcsv">
            <table>
                <tr>
                    <td colspan="2" align=center><h3>JSON datoteka</h3> Ime datoteke: <?=(isset($_POST['filepath'])) ? $_POST['filepath'] : ""?></td>
                </tr>
                <tr>
                    <td>Pot datoteke</td>
                    <td><input type="file" name="filepath" id="filepath" value="<?= (isset($_POST['filepath'])) ? $_POST['filepath'] : "" ?>" required></td>
                </tr>
                <tr>
                    <td colspan="2" align=center>
                        <br>
                        <input type="submit" value="Uvozi" class="btnsubmit">
                        <input type="button" value="Novo" class="btnsubmit" onclick="window.location=''">
                        <br><br>
                    </td>
                </tr>
            </table>
        </form>
        </div>
        <div class="col-sm-6">
        <?php
            //Ustvarjen nov objekt in klicanje celotne funkcije
            $newObj = new json2html;
            $newObj->convert(); 
        ?>
        </div>
    </body>
</html>    