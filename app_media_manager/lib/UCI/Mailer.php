<?php
class UCI_Mailer extends Mail_mime {
	private static $_instance;
	public $_headers = array();
	private $_tos = array();
	private $_from;
	private $_subject;
	
	const HDR_FROM = "From";
	const HDR_SUBJECT = "Subject";
	const HDR_REPLY_TO = "Reply-To";
	
	public function __construct($crlf = "\r\n") {
		parent::__construct();
		
		self::$_instance = $this;
	}
	
	public function addHeader($name, $value) {
		$this->_headers[$name] = $value;
	}
	
	public function getHeaders() {
		return $this->_headers;
	}
	
	public function addTo($email, $name = false) {
		$this->_tos[] = array("email" => $email, "name" => $name);
	}
	
	public function setFromHeader($email, $name = false) {
		$str = "";
		if($name != false) {
			$str .= $name . " <";
		}
		
		$str .= $email;
		
		if($name != false) {
			$str .= ">";
		}
		
		$this->addHeader(self::HDR_FROM, $str);
	}
	
	public function getTos() {
		return $this->_tos;
	}
	
	public function getTosString() {
		$ary = array();
		$tos = $this->getTos();
		
		foreach($tos as $to) {
			$ary[] = $to['email'];
		}
		
		return implode(",", $ary);
	}
	
	public function setSubject($subj) {
		$this->_subject = $subj;
		
		$this->addHeader(self::HDR_SUBJECT, $this->getSubject());
	}
	
	public function getSubject() {
		return $this->_subject;
	}
	
	public function setFrom($email, $name = false) {
		$this->_from = array("email" => $email, "name" => $name);
		
		$this->setFromHeader($email, $name);
	}
	
	public function getFrom() {
		return $this->_from;
	}
	
	public function send() {
		$mail = Mail::factory("mail");
		$msg = $this->get();
		$headers = $this->headers($this->getHeaders());
		
		try {
			$mail->send($this->getTosString(), $headers, $msg);
		} catch(Exception $e) {
			//exit($e->getMessage());
            error_log($e->getMessage(), 0);
		}
	}
}
?>