<?php
require_once 'inc.general.php';

// No password configured — skip login
if( EDITOR_PASSWORD_HASH === '' ){
	header( 'Location: ' . EDITOR_URL );
	exit;
}

// Already logged in
if( !empty( $_SESSION['authenticated'] ) ){
	header( 'Location: ' . EDITOR_URL );
	exit;
}

$error      = '';
$lockout    = 5;          // max attempts before lockout
$lockout_s  = 30;         // lockout duration in seconds

if( !isset( $_SESSION['login_attempts'] ) )  $_SESSION['login_attempts'] = 0;
if( !isset( $_SESSION['login_locked_until'] ) ) $_SESSION['login_locked_until'] = 0;

$locked = ( time() < $_SESSION['login_locked_until'] );

if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
	if( $locked ){
		$wait  = $_SESSION['login_locked_until'] - time();
		$error = 'Too many attempts. Try again in ' . $wait . ' second' . ( $wait === 1 ? '' : 's' ) . '.';
	} else {
		$password = $_POST['password'] ?? '';
		if( password_verify( $password, EDITOR_PASSWORD_HASH ) ){
			$_SESSION['authenticated']    = true;
			$_SESSION['login_attempts']   = 0;
			$_SESSION['login_locked_until'] = 0;
			header( 'Location: ' . EDITOR_URL );
			exit;
		}
		$_SESSION['login_attempts']++;
		if( $_SESSION['login_attempts'] >= $lockout ){
			$_SESSION['login_locked_until'] = time() + $lockout_s;
			$_SESSION['login_attempts']     = 0;
			$error = 'Too many attempts. Try again in ' . $lockout_s . ' seconds.';
		} else {
			$remaining = $lockout - $_SESSION['login_attempts'];
			$error     = 'Invalid password. ' . $remaining . ' attempt' . ( $remaining === 1 ? '' : 's' ) . ' remaining.';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Editor Login – Interactive Decision Tree</title>
<link href="css/editor.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Interactive Decision Tree – Editor</h1>
<?php if( $error ): ?>
<p style="color:red"><?php echo htmlspecialchars( $error ); ?></p>
<?php endif; ?>
<form method="post" action="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] ); ?>">
<?php csrf_input(); ?>
<p><label for="password">Password:</label><br />
<input type="password" id="password" name="password" autofocus /></p>
<p><input type="submit" value="Log In" /></p>
</form>
</body>
</html>
