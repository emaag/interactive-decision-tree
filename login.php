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

$error = '';
if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
	$password = $_POST['password'] ?? '';
	if( password_verify( $password, EDITOR_PASSWORD_HASH ) ){
		$_SESSION['authenticated'] = true;
		header( 'Location: ' . EDITOR_URL );
		exit;
	}
	$error = 'Invalid password.';
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
