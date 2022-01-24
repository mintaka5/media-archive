<?php
class UCI_LDAP {
	const LDAP_CONNECT = "ldap.service.uci.edu";
	
	private $_connection;
	private $_campusId;
	private $_binding;
	private $_results = false;
	
	public function __construct() {
		$this->_connection = ldap_connect(self::LDAP_CONNECT);
		
		$this->bind();
	}
	
	private function bind() {
		$this->_binding = ldap_bind($this->getConnection());
	}
	
	private function getConnection() {
		return $this->_connection;
	}
	
	private function getBinding() {
		return $this->_binding;
	}
	
	public function cSearch($campusId) {
		$this->_results = ldap_search($this->getConnection(), "ou=University of California,o=University of California,c=US", "campusid=" . $campusId);
		
		//Utils::debug($this->getResults());
		return ldap_get_entries($this->getConnection(), $this->getResults());
	}
	
	/**
	 * Search for LDAP information by UCINetID
	 * @param string $uid UCINetID
	 * @return multitype: boolean|array
	 */
	public function search($uid) {
		$this->_results = ldap_search($this->getConnection(), "ou=University of California Irvine,o=University of California, c=US", "ucinetid=" . $uid);
	
		return ldap_get_entries($this->getConnection(), $this->getResults());
	}
	
	public function searchName($nameQry, $affiliation = false) {
		$qry = "(displayName=*" . $nameQry . "*)";
		//$qry = "(|(displayName=*".$nameQry."*)(cn=*".$nameQry."*))";
		if($affiliation != false) {
			$affs = explode(",", $affiliation);
				
			$qry = "(&(displayName=*" . $nameQry . "*) ";
			if(count($affs) > 1) {
				$qry .= "(|";
				foreach($affs as $v) {
					$qry .= "(uciAffiliation=" . $v . ")";
				}
				$qry .= ")";
			} else {
				$qry .= "(uciAffiliation=" . $affiliation . ")";
			}
			$qry .= ")";
		}
	
		$this->_results = ldap_search($this->getConnection(), "ou=University of California Irvine,o=University of California, c=US", $qry);
	
		return ldap_get_entries($this->getConnection(), $this->getResults());
	}
	
	public function getDepartment() {
		$result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "department");
	
		return $result[0];
	}
	
	public function getDepartmentNum() {
		$result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "departmentNumber");
	
		return $result[0];
	}
	
	public function getLastFirstName() {
		$result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "lastfirstname");
	
		return $result[0];
	}
	
	public function getCampusId() {
		$result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "campusid");
		
		return $result[0];
	} 
	
	public function getPhone() {
		$result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "telephonenumber");
	
		return $result[0];
	}
	
	public function getEmail() {
		$result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "mail");
	
		return $result[0];
	}
	
	public function getDisplayName() {
		$result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "displayname");
		
		return $result[0];
	}
	
	public function getUCINetId() {
		$result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "uid");
		
		return $result[0];
	}
	
	private function getResults() {
		return $this->_results;
	}
	
	public function close() {
		ldap_close($this->getConnection());
	}
        
        public function getAddress() {
            $result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "postaladdress");
            
            return $result[0];
        }
        
        public function getZotCode() {
            $result = ldap_get_values($this->getConnection(), ldap_first_entry($this->getConnection(), $this->getResults()), "mailcode");
            
            return $result[0];
        }
	
	/* private function __destruct() {
		ldap_close($this->getConnection());
	} */
}
?>