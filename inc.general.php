<?php

define( 'XML_DIR_PATH', 'xml/' );
define( 'EDITOR_URL', 'editTree.php' );
define( 'VIEWER_URL', 'showTree.html' );

// load classes "on demand"
function __autoload($class_name) {
    require_once "class." . strtolower( $class_name ) . '.php';
}

// UTILITY FUNCTIONS
class Util{
	function makeVar($var, $type=""){
		global $_GET, $_POST;
		$temp = false;
		if(isset($_GET[$var])){ $temp = $_GET[$var]; }
		if(isset($_POST[$var])){ $temp = $_POST[$var]; }
		switch( $type ){
			case 'int':
				$temp = (int)$temp;
				if( !is_int( $temp ) ){
					trigger_error( "Variable: $var is not an integer", E_USER_WARNING );
					return 0;
				}
				break;
			case 'alpha':
				if( !Util::isAlpha( $temp ) ){
					trigger_error( "Variable: $var is not pure alpha", E_USER_WARNING );
					return 0;
				}
				break;
			case 'alphanum':
				if( !Util::isAlphaNum( $temp ) ){
					trigger_error( "Variable: $var is not alphanumeric", E_USER_WARNING );
					return 0;
				}
				break;
			case 'float':
				if( !is_numeric( $temp ) ){
					trigger_error( "Variable: $var is not a number", E_USER_WARNING );
					return 0;
				}
				break;
		}
		return $temp;
	}
	
	function isAlpha( $str ){
		$alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for( $i = 0; $i < strlen( $str ); $i++ ){
			if( !strstr( $alpha, substr( $str, $i, 1 ) ) ){
				return false;
			}
		}
		return true;
	}
	
	function isAlphaNum( $str ){
		$alphaNum = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for( $i = 0; $i < strlen( $str ); $i++ ){
			if( !strstr( $alphaNum, substr( $str, $i, 1 ) ) ){
				return false;
			}
		}
		return true;
	}
		
	function getRandStr($numChars=10){
		$chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjklmnpqrstuvwxyz";
		for($i = 0; $i < $numChars; $i++){
			$char = substr($chars, rand(0,strlen($chars)), 1);
			$randStr .= $char;
		}
		return $randStr;
	}
	
	function sendEmail($mailTo, $mailSubj, $mailBody){
		$mailBody .= "\n---------------------------------------------------\n"
							.  "This is an automatically generated email message. Please do not respond to it.";
		$mailFromHeaders = "From: " . APP_EMAIL . "\r\nReply-To: " . APP_EMAIL . "\r\nReturn-Path: $appEmail";
		mail($mailTo, $mailSubj, $mailBody, $mailFromHeaders);
	}
			
	function linkEmails( $string ){
		$pattern = '/(\S+@\S+\.\S+)/i';
		$replacement = '<a href="mailto:$1">$1</a>';
		return preg_replace( $pattern, $replacement, $string );
	}


}

?>