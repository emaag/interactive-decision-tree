<?php

define( 'XML_DIR_PATH', 'xml/' );
define( 'EDITOR_URL', 'editTree.php' );
define( 'VIEWER_URL', 'showTree.html' );
define( 'INDEX_URL', 'index.php' );

require_once 'config.php';

if( session_status() === PHP_SESSION_NONE ){
	session_start();
}

function csrf_token(){
	if( empty( $_SESSION['csrf_token'] ) ){
		$_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );
	}
	return $_SESSION['csrf_token'];
}

function csrf_input(){
	echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars( csrf_token() ) . '" />';
}

function csrf_verify(){
	$token = $_POST['csrf_token'] ?? '';
	if( !hash_equals( csrf_token(), $token ) ){
		http_response_code( 403 );
		die( 'Invalid request token.' );
	}
}

function require_auth(){
	if( EDITOR_PASSWORD_HASH !== '' && empty( $_SESSION['authenticated'] ) ){
		header( 'Location: login.php' );
		exit;
	}
}

// load classes "on demand"
spl_autoload_register(function($class_name) {
    require_once "class." . strtolower( $class_name ) . '.php';
});

// UTILITY FUNCTIONS
class Util{
	public static function makeVar($var, $type=""){
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


	public static function isAlpha( $str ){
		$alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for( $i = 0; $i < strlen( $str ); $i++ ){
			if( !strstr( $alpha, substr( $str, $i, 1 ) ) ){
				return false;
			}
		}
		return true;
	}


	public static function isAlphaNum( $str ){
		$alphaNum = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for( $i = 0; $i < strlen( $str ); $i++ ){
			if( !strstr( $alphaNum, substr( $str, $i, 1 ) ) ){
				return false;
			}
		}
		return true;
	}


	public static function getRandStr($numChars=10){
		$chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjklmnpqrstuvwxyz";
		for($i = 0; $i < $numChars; $i++){
			$char = substr($chars, rand(0,strlen($chars)), 1);
			$randStr .= $char;
		}
		return $randStr;
	}


	public static function sendEmail($mailTo, $mailSubj, $mailBody){
		$mailBody .= "\n---------------------------------------------------\n"
							.  "This is an automatically generated email message. Please do not respond to it.";
		$mailFromHeaders = "From: " . APP_EMAIL . "\r\nReply-To: " . APP_EMAIL . "\r\nReturn-Path: $appEmail";
		mail($mailTo, $mailSubj, $mailBody, $mailFromHeaders);
	}


	public static function linkEmails( $string ){
		$pattern = '/(\S+@\S+\.\S+)/i';
		$replacement = '<a href="mailto:$1">$1</a>';
		return preg_replace( $pattern, $replacement, $string );
	}


}

?>