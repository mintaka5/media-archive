<?php
class REST_Assets {
	private $model = false;
	public $asset;
	private $width = 0;
	private $height = 0;
	private $crop = false;
	private $api_key;
	private $username;
	
	const PUBLISHER_SUFFIX = "University of California, Irvine";
	
	public function __construct(DBO_Asset_Model $model) {
		$this->model = $model;
		
		$this->init();
	}
	
	private function init() {
		$this->asset = new stdClass();
		$this->public_id = $this->model->public_id;
		$this->title = $this->model->viewTitle();
		$this->dublin_core->title = $this->model->viewTitle();
		$this->created = date(DateTime::ISO8601, strtotime($this->model->created));
		$this->dublin_core->date = date(DateTime::ISO8601, strtotime($this->model->created));
		
		$this->caption = $this->model->viewFinalCaption();
		$this->dublin_core->description = $this->model->viewFinalCaption();
		$this->photographer = null;
		
		$this->credit = $this->model->viewCredit("");
		
		$this->dublin_core->rights = $this->model->rights();
		
		$this->dublin_core->type = $this->model->type()->mime_type;
		
		$orgs = $this->model->organizations();
		foreach($orgs as $org) {
			$this->dublin_core->publisher = $org->title . ", " . self::PUBLISHER_SUFFIX;
		}
		
		$this->dublin_core->subject = $this->model->viewKeywords();
		
		$photographer = $this->model->photographer();
		if(!empty($photographer)) {
			$this->photographer['firtsname'] = $this->model->photographer()->firstname;
			$this->photographer['lastname'] = $this->model->photographer()->lastname;
			
			$this->dublin_core->creator = $this->model->photographer()->fullname();
		}
		
		$this->shoot = null;
		
		$shoot = $this->model->shoot();
		if(!empty($shoot)) {
			$this->shoot['title'] = $this->model->shoot()->title;
			$this->shoot['shoot_date'] = date(DateTime::ISO8601, strtotime($this->model->shoot()->shoot_date));
		}
		
		$this->geo = null;
		
		if(!empty($this->model->lat) && !empty($this->model->lng)) {
			$this->geo['coordinates']['lat'] = $this->model->lat;
			$this->geo['coordinates']['lng'] = $this->model->lng;
			$this->geo['location'] = $this->model->location;
		}
		
		$srcUrl = new Net_URL2("");
		$srcUrl->setQueryVariables(array(
			'id' => $this->model->public_id,
			'w' => 1024/*,
			'api' => $this->getApiKey(),
			'user' => $this->getUsername()*/
		));
		
		/**
		 * Removed due to the fact that the servers do not have mcrypt installed
		 */
		//$this->src = BASE_URL . "imggen.php?hid=" . urlencode(Util::encrypt(APP_ENC_KEY, $srcUrl->getQuery()));
		$this->src = BASE_URL . "imggen.php?" . $srcUrl->getQuery();
		$this->dublin_core->source = BASE_URL . "imggen.php?" . $srcUrl->getQuery();
	}
	
	public function setApiKey($key) {
		$this->api_key = $key;
	}
	
	private function getApiKey() {
		return $this->api_key;
	}
	
	public function setUsername($username) {
		$this->username = $username;
	}
	
	private function getUsername() {
		return $this->username;
	}
}

class Assets {
	/**
	 * 
	 * @param string $id
	 * @throws RestException
	 * @class XmlFormat(root_name=asset)
	 * @protected
	 */
	public static function getOne($id = null) {
		if(is_null($id)) {
			throw new RestException(400);
		}
		
		$asset = DBO_Asset::getOneByPublicId($id);
		
		if($asset == false) {
			throw new RestException(412, 'The public ID # you provided does not exist.');
		}
		
		$response = new REST_Assets($asset);
		$response->setApiKey($_GET[API_Auth::API_KEY_NAME]);
		$response->setUsername($_GET[API_Auth::USERNAME_NAME]);
		
		return $response;
	}
	
	/**
	 * 
	 * @param string $terms
	 * @throws RestException
	 * @class XmlFormat(root_name=assets&default_tag_name=asset)
	 * @protected
	 */
	public static function search($q = null, $max_results = 25, $page = 1) {
		if(is_null($q)) {
			throw new RestException(400);
		}
		
		$query = trim($q);
		$query = "%" . preg_replace("/[\s\W\t\r\n]+/", "%", $query) . "%";
		$pageLimt = $page - 1;
		
		if($pageLimt < 0) {
			throw new RestException(412, 'You did not provide a valid page number. must be greater than 0');
		}
		
		if($max_results < 1) {
			throw new RestException(412, "Max result number must be greater than 0.");
		}
		
		$results = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Asset::COLUMNS . "
			FROM " . DBO_Asset::TABLE_NAME . " AS a
			LEFT JOIN " . DBO_Keyword_Asset_Cnx::TABLE_NAME . " AS b ON (b.asset_id = a.id)
			LEFT JOIN " . DBO_Keyword::TABLE_NAME . " AS c ON (c.id = b.keyword_id)
			WHERE a.is_active = 1
			AND a.is_deleted = 0
			AND c.keyword IS NOT NULL
			AND c.is_deleted = 0
			AND (
				c.keyword LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
				OR a.title LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
				OR a.caption LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
				OR a.description LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
				OR a.credit LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
			)
			GROUP BY a.id
			LIMIT " . $pageLimt . "," . $max_results . "
		")->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);
		
		$num_results = count($results);
		if(($num_results < $max_results) && $page > 1) { // there's only one page
			throw new RestException(412, "There is only one page of results.");
		}
		
		$assets = array();
		foreach($results as $asset) {
			$asst = new REST_Assets($asset);
			$asst->setApiKey($_GET[API_Auth::API_KEY_NAME]);
			$asst->setUsername($_GET[API_Auth::USERNAME_NAME]);
			
			$assets[] = $asst;
		}
		
		$resultObj = new stdClass();
		$resultObj = $assets;
		$resultObj->num_results = $num_results;
		
		return $resultObj;
	}
}
?>