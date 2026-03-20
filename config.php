<?php

// Password hash protecting the editor (editTree.php).
// Generate your own: php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
// Leave empty to disable authentication (not recommended on public servers).
define( 'EDITOR_PASSWORD_HASH', '' );
