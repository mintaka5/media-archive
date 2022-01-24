<?php
class Metadata_XMP extends Metadata {
	public function __construct($filename) {
		parent::__construct($filename);
	}
	
	public function toHtml() {
		$output = shell_exec($this->getExifTool() . " -xmp:all -h -filename " . $this->getFilename());
		
		return $output;
	}
	
	public function keywords() {
		$output = shell_exec($this->getExifTool() . " -Keywords -j " . $this->getFilename());
		$json = new Services_JSON();
		$output = $json->decode($output);
		
		if(!empty($output)) {
			if(isset($output[0]->Keywords)) {
				return $output[0]->Keywords;
			}
		} else {
			$ouptut = shell_exec($this->getExifTool() . " -Subject -j " . $this->getFilename());
			$output = $json->decode($output);
			
			if(!empty($output)) {
				if(isset($output[0]->Subject)) {
					return $output[0]->Subject;
				}
			}
		}
		
		return false;
	}
	
	public function creator() {
		$output = shell_exec($this->getExifTool() . " -Creator -j " . $this->getFilename());
		$json = new Services_JSON();
		$output = $json->decode($output);
		
		if(!empty($output)) {
			if(isset($output[0]->Creator)) {
				return Util::stringify($output[0]->Creator);
			}
		} else {
			$output = shell_exec($this->getExifTool() . " -Artist -j " . $this->getFilename());
			$output = $json->decode($output);
			
			if(!empty($output)) {
				if(isset($output[0]->Artist)) {
					return Util::stringify($output[0]->Artist);
				}
			}
		}
		
		return false;
	}
	
	public function description() {
		$output = shell_exec($this->getExifTool() . " -Description -j " . $this->getFilename());
                //echo $this->getExifTool()." -xmp:Description -j ".$this->getFilename();
		$json = new Services_JSON();
		$output = $json->decode($output);
                
		if(!empty($output)) {
			if(isset($output[0]->Description)) {
				return $output[0]->Description;
			}
		} else {
			$output = shell_exec($this->getExifTool() . " -ImageDescription -j " . $this->getFilename());
			$output = $json->decode($output);
			
			if(!empty($output)) {
				if(isset($output[0]->ImageDescription)) {
					return $output[0]->ImageDescription;
				}
			}
		}
                
	
		return false;
	}
	
	public function copyright() {
		$output = shell_exec($this->getExifTool() . " -Copyright -j " . $this->getFilename());
		$json = new Services_JSON();
		$output = $json->decode($output);
	
		if(!empty($output)) {
			if(isset($output[0]->Copyright)) {
				return $output[0]->Copyright;
			}
		}
	
		return false;
	}
	
	public function coordinates() {
		$latOutput = shell_exec($this->getExifTool() . " -exif:GPSLatitude -j " . $this->getFilename());
		$lngOutput = shell_exec($this->getExifTool() . " -exif:GPSLongitude -j " . $this->getFilename());
		
		$json = new Services_JSON();
		$latOutput = $json->decode($latOutput);
		$lngOutput = $json->decode($lngOutput);
		
		if(!empty($latOutput) || !empty($lngOutput)) {
			if(isset($latOutput[0]->GPSLatitude) && isset($lngOutput[0]->GPSLongitude)) {
				$coordObj = new stdClass();
				$coordObj->lat = $latOutput[0]->GPSLatitude;
				$coordObj->lng = $lngOutput[0]->GPSLongitude;
				
				return $coordObj;
			}
		}
		
		return false;
	}
	
	public function dpi() {
		$units = array("inches", "cm");
		$desig = "";
		
		$output = shell_exec($this->getExifTool() . " -xmp:XResolution -xmp:ResolutionUnit -j " . $this->getFilename());
		$json = new Services_JSON();
		$output = $json->decode($output);
		
		if(!empty($output)) {
			if(isset($output[0]->XResolution) && isset($output[0]->ResolutionUnit)) {
				$obj = new stdClass();
				$obj->resolution = $output[0]->XResolution;
				$obj->units = $output[0]->ResolutionUnits;
				
				return $obj;
			}
		}
		
		return false;
	}
        
        /**
         *
         * @return Date 
         */
        public function created() {
            $output = shell_exec($this->getExifTool()." -exif:CreateDate -exif:DateTimeOriginal -j ".$this->getFilename());
            $json = new Services_JSON();
            $output = $json->decode($output);
            
            if(!empty ($output)) {
                if(isset ($output[0]->CreateDate)) {
                    return $this->makeEXIFDate($output[0]->CreateDate);
                } else if(isset ($output[0]->DateTimeOriginal)) {
                    return $this->makeEXIFDate($output[0]->DateTimeOriginal);
                }
            }
            
            return false;
        }
        
        /**
         *
         * @return string
         */
        public function title() {
            $output = shell_exec($this->getExifTool()." -xmp:Title -j ".$this->getFilename());
            $json = new Services_JSON();
            $output = $json->decode($output);
            
            if(!empty ($output)) {
                if(isset ($output[0]->Title)) {
                    return trim($output[0]->Title);
                }
            }
            
            return false;
        }
        
        /**
         * EXIF stores a date/time string with colons instead of dashes in the date (ANNOYING!)
         * Replace these
         * 
         * @param string $date_string 
         * @return Date $dateObj
         */
        private function makeEXIFDate($date_string) {
            $date_string = trim($date_string);
            preg_match("/(\d{4})\:(\d{2})\:(\d{2})\s(\d{2})\:(\d{2})\:(\d{2})/", $date_string, $matches);
            
            $dateObj = new Date();
            $dateObj->setDay((int)$matches[3]);
            $dateObj->setHour((int)$matches[4]);
            $dateObj->setMinute((int)$matches[5]);
            $dateObj->setMonth((int)$matches[2]);
            $dateObj->setSecond((int)$matches[6]);
            $dateObj->setYear((int)$matches[1]);
            
            return $dateObj;
        }
        
        public function write(DBO_Asset_Model $asset) {
        	/**
        	* If final caption is avaialable embed the asset's Caption
        	* 
        	*/
        	if($asset->hasCaption(DBO_Caption_Type::FINAL_NAME) == true) {
        		$caption = escapeshellarg($asset->finalCaption());
        		
        		$execParams[] = "-exif:ImageDescription=" . $caption . "";
        		$execParams[] = "-iptc:Caption-Abstract=" . $caption . "";
        		$execParams[] = "-xmp:Caption=" . $caption . "";
        		$execParams[] = "-xmp:Description=" . $caption . "";
        	}
        	
        	/**
        	 * If description is available embed that bad boy!
        	 * 
        	 * exif:ImageDescription; xmp:Description
        	 */
        	if(!empty($asset->description)) {
        		//$execParams[] = "-ImageDescription='".escapeshellarg($asset->description)."'";
        	}
        	
        	/**
        	 * Embed dates into asset
        	 */
        	$execParams[] = "-iptc:DateCreated=" . escapeshellarg(date("Y:m:d", strtotime($asset->created))) . "";
        	$execParams[] = "-iptc:TimeCreated=" . escapeshellarg(date("H:i:s", strtotime($asset->created))) . "";
        	$execParams[] = "-exif:ModifyDate=". escapeshellarg(date("Y:m:d H:i:s", strtotime($asset->modified))) ."";
        	$execParams[] = "-exif:DateTimeOriginal=" . escapeshellarg(date(DATE_ISO8601, strtotime($asset->created))) . "";
        	$execParams[] = "-exif:CreateDate=" . escapeshellarg(date(DATE_ISO8601, strtotime($asset->created))) . "";
        	$execParams[] = "-xmp:Datetime=" . escapeshellarg(date(DATE_ISO8601, strtotime($asset->created))) . "";
        	$execParams[] = "-xmp:Date=" . escapeshellarg(date(DATE_ISO8601, strtotime($asset->created))) . "";
        	$execParams[] = "-xmp:DateTimeOriginal=" . escapeshellarg(date(DATE_ISO8601, strtotime($asset->created))) . "";
        	$execParams[] = "-xmp:DateCreated=" . escapeshellarg(date("Y:m:d", strtotime($asset->created))) . "";
        	
        	/**
        	 * Embed creator information
        	 */
        	if($asset->photographer() != false) {
        		$execParams[] = "-exif:Artist=".escapeshellarg($asset->photographer()->fullname())."";
        		$execParams[] = "-xmp:Creator=".escapeshellarg($asset->photographer()->fullname())."";
        		$execParams[] = "-xmp:Author=".escapeshellarg($asset->photographer()->fullname())."";
        	}
        	
        	/**
        	 * Embed the copyright info
        	 */
        	/*if(!empty($asset->credit)) {
        		$execParams[] = "-exif:Copyright=".escapeshellarg($asset->credit)."";
        		$execParams[] = "-iptc:CopyrightNotice=".escapeshellarg($asset->credit)."";
        		$execParams[] = "-xmp:Copyright=".escapeshellarg($asset->credit)."";
        	}*/
        	if($asset->copyright() != false) {
        		$execParams[] = "-exif:Copyright=".escapeshellarg($asset->copyright()->metadata_value)."";
        		$execParams[] = "-iptc:CopyrightNotice=".escapeshellarg($asset->copyright()->metadata_value)."";
        		$execParams[] = "-xmp:Copyright=".escapeshellarg($asset->copyright()->metadata_value)."";
        	}
        	
        	/**
        	 * Let's do the keywords dance. If we got 'em,
        	 * embed them.
        	 */
        	if($asset->hasKeywords() == true) {
        		$kwords = $asset->keywords();
        		foreach($kwords as $num => $keyword) {
        			$execParams[] = "-keywords=".escapeshellarg($keyword->keyword)."";
        		}
        	}
        	
        	/**
        	 * Embed the asset's title
        	 */
        	$execParams[] = "-Title=".escapeshellarg($asset->title)."";
        	
        	/**
        	 * Embed the author of the caption
        	 */
        	$execParams[] = "-CaptionWriter=".escapeshellarg($asset->user()->fullname())."";
        	
        	/**
        	 * If the photo is published, embed the publisher
        	 */
        	if($asset->isPublished() == true) {
        		$execParams[] = "-Publisher=".escapeshellarg($asset->published()->publication()->title)."";
        	}
        	
        	$execStr = $this->getExifTool() . " " . implode(" ", $execParams) . " " . $this->getFilename();
        	//echo $execStr; die();
        	try {
        		exec($execStr);
        	} catch(Exception $e) {
        		//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
        		
        		return false;
        	}
        	
        	return true;
        }
        
        public function getMake() {
        	$output = shell_exec($this->getExifTool()." -make -j ".$this->getFilename());
        	$json = new Services_JSON();
        	$output = $json->decode($output);
        	
        	if(!empty ($output)) {
        		if(isset ($output[0]->Make)) {
        			return trim($output[0]->Make);
        		}
        	}
        	
        	return false;
        }
        
        public function getShutterSpeed() {
        	$output = shell_exec($this->getExifTool()." -shutterspeed -j ".$this->getFilename());
        	$json = new Services_JSON();
        	$output = $json->decode($output);
        	 
        	if(!empty ($output)) {
        		if(isset ($output[0]->ShutterSpeed)) {
        			return trim($output[0]->ShutterSpeed);
        		}
        	}
        	 
        	return false;
        }
        
        public function getModel() {
        	$output = shell_exec($this->getExifTool()." -model -j ".$this->getFilename());
        	$json = new Services_JSON();
        	$output = $json->decode($output);
        
        	if(!empty ($output)) {
        		if(isset ($output[0]->Model)) {
        			return trim($output[0]->Model);
        		}
        	}
        
        	return false;
        }
}