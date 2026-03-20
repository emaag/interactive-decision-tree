<?php
include('inc.general.php');
require_auth();

if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
	csrf_verify();
}

$cmd = Util::makeVar( 'cmd' );
$treeID = Util::makeVar( 'treeID' );
$branchID = Util::makeVar( 'branchID' );
$branchContent = Util::makeVar( 'branchContent' );
$forks = Util::makeVar( 'forks' );
$revision = Util::makeVar( 'revision' );

$tree = new DecisionTree( $treeID );
if( !empty( $revision ) ){
	$tree->loadRevision( $revision );
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Interactive Decision Tree - Editor</title>
<link href="css/editor.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
<script type="text/javascript" src="js/editor.js"></script>
</head>

<body>
<h1>Interactive Decision Tree - Editor</h1>
<?php if( EDITOR_PASSWORD_HASH === '' ): ?>
<p style="background:#fff3cd;border:1px solid #ffc107;padding:.5em 1em;border-radius:4px">
	<strong>Warning:</strong> No editor password is set. Anyone who can reach this page can edit your trees.
	Set <code>EDITOR_PASSWORD_HASH</code> in <code>config.php</code> to enable authentication.
</p>
<?php endif; ?>
<?php if( EDITOR_PASSWORD_HASH !== '' ): ?>
<p style="text-align:right"><a href="logout.php">Log out</a></p>
<?php endif; ?>
<div id="debug"></div>
<?php
switch( $cmd ){
	case 'edit-tree':
	case 'new-tree':
		showTreeForm( $tree, $revision );
		if( !empty( $tree->treeID ) ){
			$tree->overview();
		}
		break;
	case 'save-tree':
		saveTree( $tree, $revision );
		showTreeForm( $tree, $revision );
		$tree->overview();
		break;
	case 'save-branch':
		saveBranch( $tree );
		$tree->saveData();
		showTreeForm( $tree, $revision );
		$tree->overview();
		break;
	case 'delete-tree':
		$tree->deleteTree();
		header( 'Location: ' . EDITOR_URL );
		exit;
	case 'edit-branch':
	case 'new-branch':
		showBranchForm( $tree, $branchID );
		break;
	default:
		showList( $tree );
}
?>
</body>
</html>
<?php

function showList( $tree ){
	?>
  <a class="btnCreateNewTree">Create a new decision tree &raquo;</a>
  <?php
	$tree->listAll();

}

function showTreeForm( $tree, $selectedRevision ){
	$treeViewerID = substr( substr( $tree->treeID, 0, strlen( $tree->treeID ) - 4 ), 4 );
	?>
  <p><a href="<?php echo EDITOR_URL; ?>">&laquo; Return to list</a>&nbsp;&nbsp;&nbsp;
  	<a target="_blank" href="<?php echo VIEWER_URL; ?>?<?php echo $treeViewerID; ?>">View tree in new window &raquo;</a></p>
  <form id="tree-editor" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
  <?php csrf_input(); ?>
  <?php
	if( !empty( $tree->revisions ) ){
	?>
  <select id="revisions" name="revision">
  	<option value="">View Revisions</option>
    <?php
		foreach( $tree->revisions as $revision ){
			$selectedHTML = '';
			$revisionParts = explode( '.', $revision );
			$revisionTS = array_pop( $revisionParts );
			$revisionTime = date( 'D M jS, Y g:i a T', $revisionTS );
			if( $revisionTS == $selectedRevision ){
				$selectedHTML = 'selected="true"';
			}
			echo '<option ' . $selectedHTML . ' value="' . $revisionTS . '">' . $revisionTime . '</option>';
		}
		?>
  </select>
  <?php
	}
	?>
  <p><label for="title">Title:</label><br />
  	<input type="text" id="title" name="treeTitle" value="<?php echo htmlspecialchars($tree->title); ?>" /></p>
  <p><label for="description">Description:</label><br />
  	<textarea id="description" name="treeDescription"><?php echo htmlspecialchars($tree->description); ?></textarea></p>
  <p><input type="submit" value="Save" />
		<input type="hidden" id="treeID" name="treeID" value="<?php echo htmlspecialchars($tree->treeID); ?>" />
    <input type="hidden" name="cmd" id="cmd" value="save-tree" /></p>
  </form>
  <?php
}

function saveTree( $tree, $selectedRevision ){
	if( empty( $tree->treeID ) ){
		$tree->createNewTree();
	}
	if( !empty( $selectedRevision ) ){
		$tree->loadRevision( $selectedRevision );
	}
	$tree->title = Util::makeVar( 'treeTitle' );
	$tree->description = Util::makeVar( 'treeDescription' );
	$tree->saveData();
}

function showBranchForm( $tree, $branchID ){
	$branch = $tree->getBranch( $branchID );
	if( empty( $branch ) ){
		// This is a new branch. Add some default data.
		$branch = new Branch();
		$branch->ID = $branchID;
		$branch->forks[$branchID . '.1'] = 'Yes';
		$branch->forks[$branchID . '.2'] = 'No';
	}
	?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="branch-editor">
  <?php csrf_input(); ?>
  <p><label for="content">Question/Decision Text:</label><br />
  	<textarea id="content" name="branchContent"><?php echo htmlspecialchars($branch->content); ?></textarea>
		<div class="note">Enter a URL above to route users there instead of displaying text (e.g. http://hungry-media.com)</div></p>
  <p id="forks"><label for="fork">Options:</label><br />
  <?php
	foreach( $branch->forks as $forkID => $forkLabel ){
	?>
    <span><input class="fork" type="text" id="fork-<?php echo htmlspecialchars($forkID); ?>" name="fork-<?php echo htmlspecialchars($forkID); ?>" value="<?php echo htmlspecialchars($forkLabel); ?>" /> <a href="#" class="btnRemoveFork">&laquo; Remove</a><br /></span>
  <?php
	}
	?></p>
  <a href="#" id="btnAddFork">Add Another</a>
  <p><input type="submit" value="Save" /> 
  	<input type="hidden" name="cmd" id="cmd" value="save-branch" />
  	<input class="btnCancel" type="button" value="cancel" />
    <input type="hidden" id="branchID" name="branchID" value="<?php echo htmlspecialchars($branchID); ?>" />
    <input type="hidden" id="treeID" name="treeID" value="<?php echo htmlspecialchars($tree->treeID); ?>" />
    </p>
  </form>
	<?php

}

function saveBranch( $tree ){
	$branchID = Util::makeVar( 'branchID' );
	$branch = $tree->getBranch( $branchID );
	if( empty( $branch ) ){
		// This is a new branch. Add some default data.
		$branch = new Branch();
		$branch->ID = $branchID;
	}
	$branch->content = Util::makeVar( 'branchContent' );
	// Add forks and branches
	$passedForks = array();
	foreach( $_POST as $formField => $formValue ){
		if( substr( $formField, 0, 5 ) == 'fork-' ){
			// get forkID from field name
			$fieldParts = explode( '-', $formField );
			$forkID = str_replace( '_', '.', $fieldParts[1]);
			array_push( $passedForks, $forkID );
			$branch->forks[$forkID] = $formValue;
			$forkBranch = $tree->getBranch( $forkID );
			if( empty( $forkBranch ) ){
				$forkBranch = new Branch();
				$forkBranch->ID = $forkID;
				$tree->saveBranch( $forkBranch );
			}
			unset( $forkBranch );
		}
	}
	// remove any forks from the branch that weren't passed
	foreach( $branch->forks as $forkID => $forkLabel ){
		if( !in_array( $forkID, $passedForks ) ){
			unset( $branch->forks[$forkID] );
			// remove the branches as well
			$tree->removeBranch( $forkID );
		}
	}
	$tree->saveBranch( $branch );	
}

?>

