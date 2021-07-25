<?php 
	class json2html {

		private $filepath = "";
		private $tmpResult = "";

		public function __construct () {
            //Iz uvoza datoteke določi pot do datoteke
	        $this->filepath = (isset($_POST['filepath'])) ? $_POST['filepath'] : "";
	    }

	    public function convert() {
            if (!$this->filepath) {
                echo "Naloži Json datoteko.";
                exit();
            }
            //Ustvari array iz decodanega JSON fila
            $fileArr = $this->convertFileJsonToArray();

            //Display arraya iz JSON datoteke na stran (uporaba po želji)
            //echo "<pre>";var_dump($fileArr);echo "</pre>";

            $doctypeResult = $headResult = $bodyResult = $htmlResult = "";
            foreach ($fileArr as $key => $singleValue) {
                if ($key == "doctype") {
                    $doctypeResult .= "<!DOCTYPE " . $singleValue . ">\r";        
                } 
                if ($key == "language") {
                    $htmlResult .= "<html lang='" . $singleValue . "'>\r";
                } 

                //HEAD HTML datoteke -> nastavljanje meta podatkov, linkov in naslova.
                if ($key == "head") {
                    $headResult .= "\t<head>\r";
                    foreach ($singleValue as $headKey => $headValue) {
                        if ($headKey == "meta") {
                            foreach ($headValue as $metaKey => $metaValue) {
                                if ($metaKey == "charset") {
                                    $headResult .= "\t\t<meta charset='" . $metaValue . "'>\r";
                                } else {
                                	$headResult .= "\t\t<meta name='" . $metaKey . "' content='" . $this->prepareAttributes($metaValue) . "'>\r";
                                }
                            }
                        }
                        elseif ($headKey == "link"){
                            foreach ($headValue as $linkKey => $linkValue){
                                $headResult .= "\t\t<link " . $this->prepareAttributes($linkValue, " ", "'") . ">\r"; 
                            }
                        }
                        elseif ($headKey == "title"){
                            $headResult .= "\t\t<title>" . $headValue . "</title>\r";
                        }
                    }
                    $headResult .= "\t</head>\r";
                } 

                //BODY HTML datoteke
                if($key == "body") {
                	$bodyResult .= "\t<body";
					if (isset($singleValue['attributes'])) { 
						$bodyResult .= $this->handleAttributes($singleValue['attributes']);
					}
					$bodyResult .= ">\r";
					$this->generateTags($singleValue, "body", 1);
					$bodyResult .= $this->tmpResult;
                    $bodyResult .= "\t</body>\r";
                }

            }
            $result = $doctypeResult . $htmlResult . $headResult . $bodyResult . "</html>\r";
            //Izpis html vsebine na UI
            echo "<br><pre>" . htmlspecialchars($result);

            //Ustvari datoteko in v njo zapiše HTML kodo
            file_put_contents("ConvertedFile.html", $result);
        }

        //Generetor vseh tagov <> -> rekurzivna funkcija
        //TO-DO -> Identacija pri nested tags. Postavi jih z 0 zamika.
        private function generateTags($content, $tagKey, $level = 1) {
        	foreach ($content as $tagKey => $tagValue) {
        		$closeLevel = 0;
        		//Če ima tag atribute -> torej, če <body class="...">
				if (isset($tagValue['attributes'])) { 
    				$this->tmpResult .= $this->multiplyText("\t", $level + 1) . "<" . $tagKey . $this->handleAttributes($tagValue['attributes']) . ">\r";
    				$this->generateTags($tagValue, $tagKey, $level + 1);
				}
				else {
					//Če je tag nested -> če <body> vsebuje <h1> ali katere druge tagge
					if (is_array($tagValue)) {
						//Preveri, nadaljuje če to "tagi" poimenovani drugeče kot attributes
						if ($tagKey != "attributes") { 
							$contentValue = (is_array($tagValue)) ? "" : $tagValue; //Če je $tagValue polje se content pusti prazen, če je pripiše vrednost $tagValue
							$this->tmpResult .= "\r" . $this->multiplyText("\t", $level + 1) . "<" . $tagKey . ">" . $contentValue; //stopnja indentacija -> doda odprti tag -> pripiše vrednost
							$this->generateTags($tagValue, $tagKey, $closeLevel = $level + 1); //Funkcija rekurzivno kliče sama sebe
							$this->tmpResult .= $this->multiplyText("\t", $level + 1); //nastavi stopnjo indentacije
						}						
					} 
					//Če tagValue ni array pomeni da ni nested -> torej znotraj taggov samo napiše vrednost
					else { 
						$this->tmpResult .= "\r" . $this->multiplyText("\t", $level + 1) . "<" . $tagKey . ">" . $tagValue; // 
					}
				}
				//Tag zapre če ne spada pod atribute
				$this->tmpResult .= ($tagKey != "attributes") ? "</" . $tagKey . ">\r" : "";
			}
		}

		//Pomožna funkcija za prepareAttributes
		private function handleAttributes($content) {
			//Funkcija je izvede, če je content array -> ponavadi se to zgodi pri atributih tagov, meta podatkov in linkov
			if (isset($content) && is_array($content)) {
				$result = "";
				foreach($content as $attributesKey => $attributesValue) {
					//Nastavi key oz. tip atributa in temu tipo pripiše vrednost
            		$result .= " " . $attributesKey . "='" . $this->prepareAttributes($attributesValue, ";", "", ":", false) . "'";
              	}
              	return $result;
			}	
		}

		//Priprava atributov za meta podatke, linke in tage
        private function prepareAttributes($content, $delimeter = ',', $txtmark = "", $equals = '=', $cutlast = true) {
	    	if (is_array($content)) {
	    		$content_result = "";
	    		foreach($content as $content_key => $content_value){
	    			//Uredi vsak atribut posebej v tagu. Npr. pri linku uredi href, rel in type
                    $content_result .= $content_key . $equals . $txtmark . $content_value . $txtmark . $delimeter;
                }
                //Če je zadnji tag ga obreže
                $content_result = ($cutlast) ? rtrim($content_result, $delimeter) : $content_result;
                return $content_result;
	    	}
	    	return $content;
	    }

        //Prebere in decoda JSON file ter ga zapiše v array
	    private function convertFileJsonToArray() {
	    	$file = file_get_contents($this->filepath);
	    	return json_decode($file, true);
	    }

	    //Zamik texta (indentacija)
	    private function multiplyText($text, $numReps) {
			$result = "";
			for($i = 0; $i < $numReps; $i++) {
				$result .= $text;
			}
			return $result;
		}
	}
//Klick UI
require_once "view.php";
?>
