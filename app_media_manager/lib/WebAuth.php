<?php
/***
* UC Irvine - Web Authentication
* For more info see: 
*   http://www.nacs.uci.edu/help/webauth/
*   http://www.nacs.uci.edu/~ecarter/webauth-php/
* Contact: ecarter@uci.edu
*
* How do I use this?
* Start by creating a new file, call it "auth-test.php" for
* example and add the following:
<?php

    // Require this web authentication class file
    require_once 'WebAuth.php';

    // Create a new authentication object
    $auth_object = new WebAuth();

    // Both of these commands make it possible
    // to go to http://mypage.uci.edu/auth-test.php?login=1
    // or http://mypage.uci.edu/auth-test.php?logout=1
    // so people can login or logout.
    if (!empty($_GET['login'])) { $auth_object->login(); }
    if (!empty($_GET['logout'])) { $auth_object->logout(); }

    // Next, we can check whether or not you're logged
    // in by checking the $auth->isLoggedIn()  method
    if ($auth_object->isLoggedIn()) {
        // do stuff, you can check the ucinetid of
        // the person by looking at $auth->ucinetid
    }
    else {
        // you're not logged in, sorry...
    }

    // Also, you can look at all the values within
    // the auth object by using the code:
    print "<pre>";
    print_r ($auth_object);
    print "</pre>";

    // As always, feel free to contact me with questions.
    // Eric Carter, ecarter@uci.edu
?>
*/
 
class WebAuth
{
    // The URLs to the web authentication at login.uci.edu
    public $login_url    = 'https://login.uci.edu/ucinetid/webauth';
    public $logout_url   = 'https://login.uci.edu/ucinetid/webauth_logout';
    public $check_url    = 'http://login.uci.edu/ucinetid/webauth_check';

    // The cookie - the name of the cookie is 'ucinetid_auth'
    public $cookie;

    // The user's URL - indicates where to goes upon authentication
    public $url;

    // The user's remote address - matched against the auth_host
    public $remote_addr;

    // The various errors that might crop up are stored in this array
    public $errors = array();

    // These are the defined vars from login.uci.edu
    public $time_created = 0;
    public $ucinetid = '';
    public $age_in_seconds = 0;
    public $max_idle_time = 0;
    public $auth_fail = '';
    public $seconds_since_checked = 0;
    public $last_checked = 0;
    public $auth_host = '';

    public function __construct() {
    	$this->WebAuth();
    }
    
    // Constructor for the web authentication
    function WebAuth() {
        
        // First, let's check the PHP version
        $php_version = phpversion();
        if ($php_version < 5) {
            $this->errors[1] = "Warning, designed to work with PHP 5.x";
        }

        // Let's get the client's ip address
        $this->remote_addr = $_SERVER['REMOTE_ADDR'];

        // Time to construct the client's URL
        // Check the server port first
        switch ($_SERVER['SERVER_PORT']) {
            case "443":
                $prefix = "https://";
                break;
            default:
                $prefix = "http://";
                break;
        }

        // Now, we'll add the HTTP_HOST name
		$this->url = $prefix . $_SERVER['HTTP_HOST'];

       	// Let's add the script name
       	$this->url .= $_SERVER['SCRIPT_NAME'];

        // Reconstruct the GET variables
        if (is_array($_GET) && sizeof($_GET) > 0) {
            $i = 0;
            $get_string = '';
            while (list($k, $v) = each($_GET)) {
                if ($k != 'login' && $k != 'logout') {
                    $get_string .= (($i++ == 0) ? '?' : '&') 
                        . urlencode($k) . '=' . urlencode($v);
                }
            }
            $this->url .= $get_string;
        }
        // Done with URL construction

        // Modify the various login.uci.edu URLs with our return URL
        //$this->login_url .= '?return_url=' . urlencode($this->url);
        $this->login_url .= '?return_url=' . urlencode($this->url);
        $this->logout_url .= '?return_url=' . urlencode($this->url);

        // Let's add the cookie called 'ucinetid_auth'
        if ($_COOKIE['ucinetid_auth']) {
            $this->cookie = $_COOKIE['ucinetid_auth'];
            $this->check_url .= '?ucinetid_auth=' . $this->cookie;
        }

        // Now, let's check authentication
        $this->checkAuth();

    } // end Constructor

    // Check the authentication based on cookie
    function checkAuth() {

        // First, we'll check that we even have a cookie
        if (empty($this->cookie) || $this->cookie == 'no_key') {
            return false;
        }
        
        // Check that we can connect to login.uci.edu
        $this->read();
        //if (!$auth_array = @file($this->check_url)) {
        if(!$this->isAuthAvailable()) {
            $this->errors[2] = "Unable to connect to login.uci.edu";
            return false;
        }

        // Make sure we have an array, and build the auth values
        //if (is_array($auth_array)) {
        $auth_array = $this->read();
        
        if(is_array($auth_array)) {
            while (list($k,$v) = each($auth_array)) {
                if (!empty($v)) {
                    $v = trim($v);
                    $auth_values = explode("=", $v);
                    if (!empty($auth_values[0]) && !empty($auth_values[1])) 
                        $this->$auth_values[0] = $auth_values[1];
                }
            }

            // Check to ensure auth_host is verified
            if ($this->auth_host != $this->remote_addr) {
                $this->errors[3] = "Warning, the auth host doesn't match.";
                return false;
            }
            return true;
        }
    } // end check_auth

    // Boolean, determines if someone's logged in
    function isLoggedIn() {
        if ($this->time_created) return true;
        else return false;
    }

    // The login function
    function login() {
        header('Location: ' . $this->login_url);
        exit();
    }

    // The logout function
    function logout() {
    	
        header('Location: ' . $this->logout_url);
        echo $this->logout_url;
        exit();
    }
    
    /**
     * 
     * Getter for UCNetID
     */
    public function getUCINetID() {
    	return $this->ucinetid;
    }
    
    /**
     * 
     * Added this to use CURL instead of file
     */
    private function isAuthAvailable() {
    	$handle = curl_init($this->check_url);
    	curl_setopt_array($handle, array(CURLOPT_NOBODY => true));
    	$res = curl_exec($handle);
    	$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    	curl_close($handle);
    	
    	if($code == 200) {
    		return true;
    	}
    	
    	return false;
    }
    
    /**
     * 
     * Uses CURL to read back contents of check_url page,
     * @return array
     */
    private function read() {
    	$handle = curl_init($this->check_url);
    	curl_setopt_array($handle, array(
    		CURLOPT_RETURNTRANSFER => true
    	));
    	$res = curl_exec($handle);
    	
    	return preg_split("/[\n\r]{1}/", $res);
    }
}
?>
