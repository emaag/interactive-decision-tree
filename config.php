<?php

// Password hash protecting the editor (editTree.php).
// Generate your own: php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
// Leave empty to disable authentication (not recommended on public servers).
//
// To avoid committing your password hash, create config.local.php (gitignored)
// and redefine EDITOR_PASSWORD_HASH there instead of editing this file.
if( !defined('EDITOR_PASSWORD_HASH') ){
	define( 'EDITOR_PASSWORD_HASH', '' );
}
