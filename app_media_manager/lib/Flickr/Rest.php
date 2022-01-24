<?php
class Flickr_Rest {
	const BASE_API_URL = "https://api.flickr.com/services/rest/";
	const REQUEST_TOKEN_URL = 'https://www.flickr.com/services/oauth/request_token';
	const ACCESS_TOKEN_URL = 'https://www.flickr.com/services/oauth/access_token';
	const AUTHORIZE_URL = 'https://www.flickr.com/services/oauth/authorize';
	const AUTH_URL = 'https://www.flickr.com/services/oauth';
	
	const METHOD_PHOTOS_SEARCH = "flickr.photos.search";
	
	const PHOTO_EXTRAS = "description, license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o";
	
	const TAG_MODE_ANY = "any";
        
        const SIZE_SQUARE = 'square';
        const SIZE_LARGE_SQUARE = 'large square';
        const SIZE_THUMBNAIL = 'thumbnail';
        const SIZE_SMALL = 'small';
        const SIZE_MEDIUM = 'medium';
        const SIZE_ORIGINAL = 'original';
        const SIZE_MEDIUM_640 = 'medium 640';


        private $api_key = null;
	private $nsid = null;
	private $app_secret = null;
	private $access_token = false;
	private $redirect = false;
	
	public function __construct($api_key = false, $nsid = false, $app_secret = false, $redirect = false) {
		if($api_key != false) {
			$this->api_key = $api_key;
		}
		
		if($nsid != false) {
			$this->nsid = $nsid;
		}
		
		if($app_secret != false) {
			$this->app_secret = $app_secret;
		}
		
		if($redirect != false) {
			$this->redirect = $redirect;
		}
	}
	
	public function setAccessToken(Zend_Oauth_Token_Access $access_token) {
		$this->access_token = $access_token;
	}
	
	public function getAccessToken() {
		return $this->access_token;
	}
	
	public function getPhotoset($photoset_id, $format = "json") {
		$client = $this->getAccessToken()->getHttpClient(array(
				'callbackUrl' => $this->redirect,
				'siteUrl' => self::AUTH_URL,
				'consumerKey' => $this->api_key,
				'consumerSecret' => $this->app_secret,
				'requestTokenUrl' => self::REQUEST_TOKEN_URL,
				'accessTokenUrl' => self::ACCESS_TOKEN_URL,
				'authorizeUrl' => self::AUTHORIZE_URL
		));
		
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$client->setAdapter($adapter);
		
		$client->setUri(self::BASE_API_URL);
		$client->setMethod(Zend_Http_Client::GET);
		$client->setParameterGet('method', 'flickr.photosets.getPhotos');
		//$client->setParameterGet('user_id', "me");
		$client->setParameterGet('api_key', $this->api_key);
		$client->setParameterGet('format', $format);
		//$client->setParameterGet('content_type', 1);
		$client->setParameterGet('photoset_id', $photoset_id);
		$client->setParameterGet('extras', self::PHOTO_EXTRAS);
		if($format == 'json') {
                    $client->setParameterGet('nojsoncallback', 1);
		}
		
		$response = $client->request();
		return $response->getBody();
	}
        
        public function getOne($id) {
            $info = $this->getInfo($id, 'php_serial');
            $info = unserialize($info);
            $sizes = $this->getSizes($id, 'php_serial');
            $sizes = unserialize($sizes);
            
            $info = $info['photo'];
            $sizes = $sizes['sizes']['size'];
            
            $flickr = new Flickr_Model();
            $flickr->setId($id);
            $flickr->setDescription($info['description']);
            $flickr->setLargeSquareUrl($this->getSizeUrl(self::SIZE_LARGE_SQUARE, $sizes));
            $flickr->setOriginalUrl($this->getSizeUrl(self::SIZE_ORIGINAL, $sizes));
            $flickr->setPublished($info['dateuploaded']);
            $flickr->setSmallSquareUrl($this->getSizeUrl(self::SIZE_SQUARE, $sizes));
            $flickr->setThumbnailUrl($this->getSizeUrl(self::SIZE_THUMBNAIL, $sizes));
            $flickr->setTitle($info['title']);
            $flickr->setMedium640Url($this->getSizeUrl(self::SIZE_MEDIUM_640, $sizes));
            $flickr->setMediumUrl($this->getSizeUrl(self::SIZE_MEDIUM, $sizes));
            
            return $flickr;
        }
        
        private function getSizeUrl($label, $sizes) {
            foreach($sizes as $size) {
                $labelName = strtolower($size['label']);
                if($labelName == $label) {
                    return $size['source'];
                }
            }
            
            return false;
        }
        
        private function getInfo($id, $format='json') {
            $client = $this->getAccessToken()->getHttpClient(array(
                'callbackUrl' => $this->redirect,
                'siteUrl' => self::AUTH_URL,
                'consumerKey' => $this->api_key,
                'consumerSecret' => $this->app_secret,
                'requestTokenUrl' => self::REQUEST_TOKEN_URL,
                'accessTokenUrl' => self::ACCESS_TOKEN_URL,
                'authorizeUrl' => self::AUTHORIZE_URL
            ));

            $adapter = new Zend_Http_Client_Adapter_Curl();
            $client->setAdapter($adapter);
            
            $client->setUri(self::BASE_API_URL);
            $client->setMethod(Zend_Http_Client::GET);
            $client->setParameterGet('method', 'flickr.photos.getInfo');
            $client->setParameterGet('api_key', $this->api_key);
            $client->setParameterGet('photo_id', $id);
            $client->setParameterGet('format', $format);
            
            if($format == 'json') {
                $client->setParameterGet('nojsoncallback', 1);
            }

            $response = $client->request();
            return $response->getBody();
        }
        
        private function getSizes($id, $format='json') {
            $client = $this->getAccessToken()->getHttpClient(array(
                'callbackUrl' => $this->redirect,
                'siteUrl' => self::AUTH_URL,
                'consumerKey' => $this->api_key,
                'consumerSecret' => $this->app_secret,
                'requestTokenUrl' => self::REQUEST_TOKEN_URL,
                'accessTokenUrl' => self::ACCESS_TOKEN_URL,
                'authorizeUrl' => self::AUTHORIZE_URL
            ));

            $adapter = new Zend_Http_Client_Adapter_Curl();
            $client->setAdapter($adapter);
            
            $client->setUri(self::BASE_API_URL);
            $client->setMethod(Zend_Http_Client::GET);
            $client->setParameterGet('method', 'flickr.photos.getSizes');
            $client->setParameterGet('api_key', $this->api_key);
            $client->setParameterGet('photo_id', $id);
            $client->setParameterGet('format', $format);
            
            if($format == 'json') {
                $client->setParameterGet('nojsoncallback', 1);
            }

            $response = $client->request();
            return $response->getBody();
        }
	
	public function search($search_terms, $format = "json") {		
            $client = $this->getAccessToken()->getHttpClient(array(
                'callbackUrl' => $this->redirect,
                'siteUrl' => self::AUTH_URL,
                'consumerKey' => $this->api_key,
                'consumerSecret' => $this->app_secret,
                'requestTokenUrl' => self::REQUEST_TOKEN_URL,
                'accessTokenUrl' => self::ACCESS_TOKEN_URL,
                'authorizeUrl' => self::AUTHORIZE_URL
            ));

            $adapter = new Zend_Http_Client_Adapter_Curl();
            $client->setAdapter($adapter);

            $client->setUri(self::BASE_API_URL);
            $client->setMethod(Zend_Http_Client::GET);
            $client->setParameterGet('method', 'flickr.photos.search');
            $client->setParameterGet('user_id', "me");
            $client->setParameterGet('api_key', $this->api_key);
            $client->setParameterGet('format', $format);
            $client->setParameterGet('content_type', 1);
            $client->setParameterGet('extras', self::PHOTO_EXTRAS);

            $tags = preg_replace("/[\W]+/", " ", $search_terms);
            $tags = preg_split("/[\s\t\r\n]+/i", $tags);
            $tags = implode(",", $tags);
            $client->setParameterGet('tags', $tags);

            $client->setParameterGet('tag_mode', 'any');
            $client->setParameterGet('text', $search_terms);
            if($format == 'json') {
                    $client->setParameterGet('nojsoncallback', 1);
            }

            $response = $client->request();
            return $response->getBody();
	}
	
        /**
         * 
         * @param string $format 'rest', 'php_serial', 'xmlrpc', 'soap', or 'json' (default)
         * @return mixed
         */
	public function getAll($format = "json") {
		$client = $this->getAccessToken()->getHttpClient(array(
		 	'callbackUrl' => $this->redirect,
			'siteUrl' => self::AUTH_URL,
			'consumerKey' => $this->api_key,
			'consumerSecret' => $this->app_secret,
			'requestTokenUrl' => self::REQUEST_TOKEN_URL,
			'accessTokenUrl' => self::ACCESS_TOKEN_URL,
			'authorizeUrl' => self::AUTHORIZE_URL
		));
		
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$client->setAdapter($adapter);
		
		$client->setUri(self::BASE_API_URL);
		$client->setMethod(Zend_Http_Client::GET);
		$client->setParameterGet('method', 'flickr.photos.search');
		$client->setParameterGet('user_id', "me");
		$client->setParameterGet('api_key', $this->api_key);
		$client->setParameterGet('format', $format);
		$client->setParameterGet('content_type', 1);
		$client->setParameterGet('extras', self::PHOTO_EXTRAS);
		if($format == 'json') {
                    $client->setParameterGet('nojsoncallback', 1);
		}
		
		$response = $client->request();
		return $response->getBody();
	}
}
?>