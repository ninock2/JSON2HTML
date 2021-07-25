<?php 
	class json2html {

		private $filepath = "";
		private $tmpResult = "";

		public function __construct () {
	        $this->filepath = (isset($_POST['filepath'])) ? $_POST['filepath'] : "";
	    }

	    public function convert() {
            if (!$this->filepath) {
                echo "Import JSON file.";
                exit();
            }
            $fileArr = $this->convertFileJsonToArray();

            //Displays the array filled with JSON file
            //echo "<pre>";var_dump($fileArr);echo "</pre>";

            $doctypeResult = $headResult = $bodyResult = $htmlResult = "";
            foreach ($fileArr as $key => $singleValue) {
                if ($key == "doctype") {
                    $doctypeResult .= "<!DOCTYPE " . $singleValue . ">\r";        
                } 
                if ($key == "language") {
                    $htmlResult .= "<html lang='" . $singleValue . "'>\r";
                } 

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
            //Displays HTML sturusture
            echo "<br><pre>" . htmlspecialchars($result);

            file_put_contents("ConvertedFile.html", $result);
        }

        private function generateTags($content, $tagKey, $level = 1) {
        	foreach ($content as $tagKey => $tagValue) {
        		$closeLevel = 0;
				if (isset($tagValue['attributes'])) { 
    				$this->tmpResult .= $this->multiplyText("\t", $level + 1) . "<" . $tagKey . $this->handleAttributes($tagValue['attributes']) . ">\r";
    				$this->generateTags($tagValue, $tagKey, $level + 1);
				}
				else {
					if (is_array($tagValue)) {
						if ($tagKey != "attributes") { 
							$contentValue = (is_array($tagValue)) ? "" : $tagValue;
							$this->tmpResult .= "\r" . $this->multiplyText("\t", $level + 1) . "<" . $tagKey . ">" . $contentValue; 
							$this->generateTags($tagValue, $tagKey, $closeLevel = $level + 1); 
							$this->tmpResult .= $this->multiplyText("\t", $level + 1); 
						}						
					} 
					else { 
						$this->tmpResult .= "\r" . $this->multiplyText("\t", $level + 1) . "<" . $tagKey . ">" . $tagValue; // 
					}
				}
				$this->tmpResult .= ($tagKey != "attributes") ? "</" . $tagKey . ">\r" : "";
			}
		}

		private function handleAttributes($content) {
			if (isset($content) && is_array($content)) {
				$result = "";
				foreach($content as $attributesKey => $attributesValue) {
            		$result .= " " . $attributesKey . "='" . $this->prepareAttributes($attributesValue, ";", "", ":", false) . "'";
              	}
              	return $result;
			}	
		}

        private function prepareAttributes($content, $delimeter = ',', $txtmark = "", $equals = '=', $cutlast = true) {
	    	if (is_array($content)) {
	    		$content_result = "";
	    		foreach($content as $content_key => $content_value){
                    $content_result .= $content_key . $equals . $txtmark . $content_value . $txtmark . $delimeter;
                }
                $content_result = ($cutlast) ? rtrim($content_result, $delimeter) : $content_result;
                return $content_result;
	    	}
	    	return $content;
	    }

	    private function convertFileJsonToArray() {
	    	$file = file_get_contents($this->filepath);
	    	return json_decode($file, true);
	    }

	    private function multiplyText($text, $numReps) {
			$result = "";
			for($i = 0; $i < $numReps; $i++) {
				$result .= $text;
			}
			return $result;
		}
	}
require_once "view.php";
?>
