<?php
/**
 * Plugin Name: Netrox SC
 * Author: Netrox SC
 * Author URI: www.netroxsc.com
 * Plugin URI: http://netroxsc.com/
 * Description: Netrox allows for conversing with website visitors directly.
 * Version: 1.1
 * Text Domain: netroxsc
 * Domain Path: /
 */


defined('ABSPATH') or die("Nothing to look at here...");

load_plugin_textdomain('netroxsc', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));

$aLocale		= explode("-", get_bloginfo("language"));
$sLanguage		= $aLocale[0];
$sWebsiteURL	= "https://www.netroxsc.".($sLanguage == "ru" ? "ru" : "com");

define("NETROX_LNG",		$sLanguage);
define("NETROX_URL_WEB",	$sWebsiteURL);
define("NETROX_URL_SYS",	"https://sys.netrox.sc/");
define("NETROX_URL_API",	NETROX_URL_SYS."API/");
define("NETROX_URL_PLUGIN",	plugin_dir_url(__FILE__));
define("NETROX_URL_IMG",	NETROX_URL_PLUGIN."/images/");

function netroxsc_admin_menu(){
    load_plugin_textdomain('netroxsc', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
    add_menu_page(
		__('Netrox SC Live Chat', 'netroxsc'), 
		__('Netrox SC', 'netroxsc'), 
		8, 
		basename(__FILE__), 
		'netroxsc_show_options',
		NETROX_URL_IMG."icon.png"
	);
}

function netroxsc_activation_hook(){
    return NetroxSC::instance()->install();
}

function netroxsc_deactivation_hook(){
    return NetroxSC::instance()->delete();
}

function netroxsc_admin_init(){
    register_setting('netroxsc_valid',		'netroxsc_valid');
    register_setting('netroxsc_auth_token', 'netroxsc_auth_token');
    register_setting('netroxsc_enabled',	'netroxsc_enabled');
    register_setting('netroxsc_site_id', 	'netroxsc_site_id');
    register_setting('netroxsc_widget_id', 	'netroxsc_widget_id');
    register_setting('netroxsc_user_id', 	'netroxsc_user_id');
}

function netroxsc_wp_footer() {
    NetroxSC::instance()->code();
}

register_activation_hook	(__FILE__,	'netroxsc_activation_hook');
register_deactivation_hook	(__FILE__,	'netroxsc_deactivation_hook');

add_action('admin_init',	'netroxsc_admin_init');
add_action('wp_footer',		'netroxsc_wp_footer', 100000);
add_action('admin_menu',	'netroxsc_admin_menu');


function netroxsc_show_options(){
    load_plugin_textdomain('netroxsc', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
    echo NetroxSC::instance()->show();
}

function netroxsc_sort_sites($site1, $site2) {
	if (!is_array($site1)) return 0;
	if (!is_array($site2)) return 0;
	if (!isset($site1["name"])) return 0;
	if (!isset($site2["name"])) return 0;
	
	if ($site1["name"] == $site2["name"]) return 0;
	return $site1["name"] > $site2["name"] ? 1 : -1;
}

class NetroxSC {

	// PROPS

    protected static $_instance, $_language;

	private $valid;
	private $enabled;
	private $siteID;
    private $widgetID;
    private $authToken;
	private $userID;
	
	private $showSignup = true;
	
	private $auth;				// Is user currently authenticated
	private $errors = array();	// Data exchange errors 

    private function __construct(){
        $this->authToken	= get_option( 'netroxsc_auth_token');
        $this->siteID		= get_option( 'netroxsc_site_id');
        $this->widgetID		= get_option( 'netroxsc_widget_id');
		$this->userID		= get_option( 'netroxsc_user_id');
		$this->valid		= get_option( 'netroxsc_valid');
		$this->enabled		= get_option( 'netroxsc_enabled');
    }
	
    private function __clone()    {}
    private function __wakeup()   {}
	
	// METHODS

	private function error($sCode, $sMessage = "") {
		$this->errors[$sCode] = $sMessage;
	}

    public static function instance() {
		self::$_instance instanceof NetroxSC || self::$_instance = new NetroxSC();
        
		self::$_language     = NETROX_LNG == "ru" ? "ru" : "en";

        return self::$_instance;
    }

    public function setSiteID($sID) {
        $this->siteID = $sID;
		return $this;
    }

    public function setWidgetID($iID) {
        $this->widgetID = $iID;
		return $this;
    }

    public function setAuthToken($sToken) {
        $this->authToken = $sToken;
		return $this;
    }
	
    /**
     * Install
     */
    public function install() {
		$this->authToken	= null;
		$this->valid		= true;
		$this->enabled		= true;
		$this->save();
    }

    public function processPost() {
		$sAction = self::postField("action");
		switch($sAction) {
			case "signup":
				//	Sign up form
				if (!is_email($sEmail = self::postField('email'))) return false;
				if (!strlen($sWebsite	= self::postField('website'))) return false;
				if (!strlen($sPassword	= self::postField('password'))) return false;
				if (!strlen($sConfirm	= self::postField('confirm'))) return false;
				if (!strlen($sLastName	= self::postField('lname'))) return false;
				if (!strlen($sFirstName	= self::postField('fname'))) return false;
				
				$aResponse = self::sendRequest(
					array(
						"email"				=> $sEmail,
						"website"			=> $sWebsite,
						"company"			=> $sWebsite,
						"lname"				=> $sLastName,
						"fname"				=> $sFirstName,
						"confirm_password"	=> $sConfirm,
						"password"			=> $sPassword,
						"partnerID"			=> "WORDPRESS_PLUGIN"
					), 
					"signUp?lang=".self::$_language
				);
				
				if ($aResponse["success"]) {
					$this->authToken	= $aResponse["user"]["token"];
					$this->valid		= false;
					$this->save();
				}
			break;
			case "login":
				//	User filled the login form

				$this->showSignup = false;
				
				$sLogin		= self::postField('login');
				$sPassword	= self::postField('password');

				if (!is_email($sLogin)) 	return "login";
				if (!strlen($sPassword)) 	return "login";
				
				$aResponse = self::sendRequest(
					array(
						"login"		=> $sLogin,
						"password"	=> $sPassword,
						"remember"	=> 1			// To receive an auth token
					), 
					"login"
				);
				
				if 		(!is_array($aResponse))												$this->error("CONNECT");
				else if (!isset($aResponse["u_id"]) || strlen($aResponse["u_id"]) != 36)	{
					$this->error("AUTH");
				}

				if (count($this->errors)) return "login";
				
				// Store received data
				$this->authToken	= $aResponse["token"];
				$this->userID		= $aResponse["u_id"];
				$this->save();
				
				$this->auth			= true;				
			break;
			case "validate":
				if (isset($_POST["cancel"])) {
					$this->valid = true;
					$this->authToken  = null;
					$this->save();
				}
			break;
			case "set":
				if (isset($_POST["unbind"])) {
					$this->authToken = null;
					$this->userID = null;
					$this->save();
					$this->showSignup = false;
				}
				$this->enabled	= (bool) self::postField('enable', false);
				$this->siteID	= self::postField('site_id');
				$this->widgetID	= intval(self::postField("widget_id_{$this->siteID}", 1));
				$this->save();
			break;
		}
    }

    /**
     * delete plugin
     */
    public function delete(){
        delete_option('netroxsc_site_id');
        delete_option('netroxsc_widget_id');
        delete_option('netroxsc_user_id');
        delete_option('netroxsc_auth_token');
        delete_option('netroxsc_valid');
        delete_option('netroxsc_enabled');
    }


    public function getId(){
        return $this->widget_id;
    }

    /**
     * render admin page
     */
    public function show(){
        $sAction	= $this->processPost();
		// Reset auth if invalid userID
		if (!is_string($this->userID) || strlen($this->userID) != 36) $this->auth = false;
		
		if (!$this->auth) { 
			// If not authenticated - try to login with token
			if (is_string($this->authToken) && strlen($this->authToken) > 30) {
				$this->showSignup = false;
				$aResponse = self::sendRequest(
					array(
						"token" => $this->authToken
					), 
					"login"
				);

				if 		(!is_array($aResponse))												$this->error("CONNECT");
				else if (!isset($aResponse["u_id"]) || strlen($aResponse["u_id"]) != 36)	$this->error("AUTH");
				else {
					// Authentication success
					$this->userID	= $aResponse["u_id"];
					$this->auth		= true;
				}
			}
		}
		
		if ($this->auth) {
			// Get user sites
			$aResponse = self::sendRequest(
				array("token" => $this->authToken), 
				"User/GetSites"
			);
			
			if 		(!is_array($aResponse))			$this->error("CONNECT");
			else if (isset($aResponse["error"]))	$this->error("AUTH");
			else {

				/* 	Expected response
				 *	-----------------
				 *
				 *		{
				 *			"SITE_ID_1" : {
				 *				"id"			: "SITE_ID_1",
				 *				"name"			: "Website #1",
				 *				"chatWndThemes"	: [
				 *					{ "id" : 1, "name" : "Theme #1 name" },
				 *					...
				 *				]
				 *			},
				 *			...
				 *		}
				 *
				 *	chatWndThemes present only for sites with admin rights
				 */
				
				$aAdminSites = array();
				// Site array cleanup
				foreach ($aResponse as $sSiteID => $aSite) {
					if (!isset($aSite["chatWndThemes"])) continue;
					
					$aAdminSites []= $aSite;
				}
				
				// Sort by site name
				usort($aAdminSites, "netroxsc_sort_sites");

				
				// Force defaults if no values set
				if ((!is_string($this->siteID) || !strlen($this->siteID)) && count($aAdminSites)) {
					$this->siteID		= $aAdminSites[0]["id"];
					$this->widgetID		= 1;
				}
				
				// Show settings page
				include "inc/settings_form.php";
			}
		} else if (!$this->valid) {
			include "inc/invalid.php";
			return;
		}
		
		if (isset($this->errors["AUTH"])) $this->auth = false;
		else if (isset($this->errors["CONNECT"])) _e("Error occured while trying to connect to Netrox SC servers", 'netroxsc');
		
		if (!$this->auth) $this->showLoginForm();
    }
	
	private function showLoginForm() {
		// Show signup and login form if no auth token 
		$sLogin		= get_option('admin_email');
		$sWebsite	= get_site_url();
		// Trim protocol
		if (substr_count($sWebsite, "//")) $sWebsite = substr($sWebsite, strpos($sWebsite, "//") + 2);
		
		// nickname
		$iUserID = get_current_user_id();
		$oUser = get_userdata($iUserID);
		$sName = $oUser->display_name;
		
		include "inc/login_form.php";
	}

    public function save() {
        do_settings_sections( __FILE__ ); //?

        update_option('netroxsc_site_id',		$this->siteID);
        update_option('netroxsc_widget_id', 	$this->widgetID);
        update_option('netroxsc_user_id', 		$this->userID);
        update_option('netroxsc_auth_token',	$this->authToken);
        update_option('netroxsc_valid',			$this->valid);
        update_option('netroxsc_enabled',		$this->enabled);
    }
	
	/**
	 *	Inserts system code
	 */
    public function code() {
		if (!$this->enabled) return;
		if (!$this->siteID || strlen($this->siteID) < 36) return;
		if (!is_numeric($this->widgetID) || $this->widgetID < 1) return;
		
        include "inc/code.php";
    }

	/*
	 *	Sends requests to Netrox API
	 */
	private static function sendRequest($aFields, $sHandlerName) {
		$sUrl		= NETROX_URL_API . $sHandlerName;

		$aResponse = wp_remote_post($sUrl, array("body" => $aFields));
		
		if (is_wp_error($aResponse)) {
			_e("Error occured while trying to connect to Netrox SC servers", 'netroxsc');
			return false;
		}
		$sResponse = $aResponse["body"];
		
		// Just a workaround until API is fixed
		if (substr_count($sResponse, "{")) $sResponse = substr($sResponse, strpos($sResponse, "{"));
		
		return json_decode($sResponse, true);
	}
		
	public static function getField($key, $subst = "") {
		if (isset($_GET[$key])) return trim($_GET[$key]);
		return $subst;
	}
		
	public static function postField($key, $subst = "") {
		if (isset($_POST[$key])) return trim($_POST[$key]);
		return $subst;
	}
}