<?php
require_once 'inc.general.php';
$tree = new DecisionTree();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Interactive Decision Tree</title>
<link href="css/decisionTree.css" rel="stylesheet" type="text/css" />
<style>
body { font-family: sans-serif; max-width: 700px; margin: 2em auto; padding: 0 1em; }
h1 { border-bottom: 2px solid #ccc; padding-bottom: .4em; }
dl { margin: 0; }
dt { margin-top: 1.2em; font-size: 1.1em; font-weight: bold; }
dd { margin: .2em 0 0 0; color: #555; }
.view-link { margin-left: .8em; font-size: .85em; font-weight: normal; }
.empty { color: #999; font-style: italic; margin-top: 1em; }
</style>
</head>
<body>
<h1>Decision Trees</h1>
<?php
if( !is_dir( XML_DIR_PATH ) || !($dh = opendir( XML_DIR_PATH )) ){
	echo '<p class="empty">No trees found.</p>';
}else{
	$found = false;
	echo '<dl>';
	while( $file = readdir( $dh ) ){
		if( strtolower( substr( $file, -4, 4 ) ) !== '.xml' ) continue;
		if( !($xmlData = simplexml_load_file( XML_DIR_PATH . $file )) ) continue;
		$found = true;
		$treeID = str_replace( '.xml', '', substr( $file, 4 ) );
		$title = (string)$xmlData->title;
		$description = (string)$xmlData->description;
		?>
		<dt><?php echo htmlspecialchars( $title ?: $file ); ?>
			<a class="view-link" href="<?php echo VIEWER_URL . '?' . urlencode( $treeID ); ?>">View &raquo;</a>
		</dt>
		<?php if( $description ): ?>
		<dd><?php echo htmlspecialchars( $description ); ?></dd>
		<?php endif; ?>
		<?php
	}
	echo '</dl>';
	closedir( $dh );
	if( !$found ){
		echo '<p class="empty">No decision trees yet. <a href="' . EDITOR_URL . '">Create one &raquo;</a></p>';
	}
}
?>
</body>
</html>
