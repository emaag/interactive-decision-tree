<?php
class DecisionTree{

	var $treeID;
	var $title;
	var $description;
	var $branches = array();
	var $revisions = array();
	var $xmlDirPath = XML_DIR_PATH;
	
	function __construct( $treeID = '' ){
		if( !empty( $treeID ) ){
			$this->treeID = $treeID;
			$this->loadData();
		}
	}
	
	function createNewTree(){
		$newFileName = 'tree' . time() . '.xml';
		while( file_exists( XML_DIR_PATH . $newFileName ) ){
			$newFileName = 'tree' . time() . '.xml';
		}
		$xmlData = new SimpleXMLElement("<tree></tree>");
		$xmlData->asXML( $newFileName );
		$this->treeID = str_replace( XML_DIR_PATH, '', $newFileName );
	}
	
	function loadRevision( $revision ){
		$revisionFile = $this->xmlDirPath . $this->treeID . '.' . $revision;
		if( !file_exists( $revisionFile ) ){
			die( "Cannot load XML data: " . $revision );
		}
		if( $xmlData = simplexml_load_file( $revisionFile ) ){
			$this->title = (string)$xmlData->title;
			$this->description = (string)$xmlData->description;
			$this->branches = array();
			foreach( $xmlData->branch as $branch ){
				$thisBranch = new Branch();
				$thisBranch->ID = (string)$branch['id'];
				$thisBranch->content = (string)$branch->content;
				foreach( $branch->fork as $fork ){
					$thisBranch->forks[(string)$fork['target']] = (string)$fork;
				}
				array_push( $this->branches, $thisBranch );
			}
		}else{
			die( "Cannot parse XML data: " . $this->xmlDirPath . $treeID );
		}
	}
	
	function loadData(){
		if( !file_exists( $this->xmlDirPath . $this->treeID ) ){
			die( "Cannot load XML data: " . $this->xmlDirPath . $this->treeID );
		}
		if( $xmlData = simplexml_load_file( $this->xmlDirPath . $this->treeID ) ){
			$this->title = (string)$xmlData->title;
			$this->description = (string)$xmlData->description;
			foreach( $xmlData->branch as $branch ){
				$thisBranch = new Branch();
				$thisBranch->ID = (string)$branch['id'];
				$thisBranch->content = (string)$branch->content;
				foreach( $branch->fork as $fork ){
					$thisBranch->forks[(string)$fork['target']] = (string)$fork;
				}
				array_push( $this->branches, $thisBranch );
			}
			foreach( $xmlData->revision as $revision ){
				array_push( $this->revisions, (string)$revision );
			}

		}else{
			die( "Cannot parse XML data: " . $this->xmlDirPath . $treeID );
		}
	}
	
	function getBranch( $branchID ){
		foreach( $this->branches as $branch ){
			if( strval( $branch->ID ) === strval( $branchID ) ){
				return $branch;
			}
		}
		return false;
	}
	
	function overview(){
		if( empty( $this->branches ) ){
			?>
      Alas. This decision tree is currently void of any questions or decisions.<br />
      <a href="<?php echo EDITOR_URL; ?>?cmd=new-branch&treeID=<?php echo $this->treeID; ?>&branchID=1">Add the first question/decision</a>
      <?php
		}else{
			echo '<ul class="tree-overview">';
			$this->getTargetBranches( 1 );
			echo '</ul>';
		}
	}
	
	function saveBranch( $branch ){
		// look for existing branch to replace
		foreach( $this->branches as $branchIndex => $treeBranch ){
			if( strval( $branch->ID ) === strval( $treeBranch->ID ) ){
				$this->branches[$branchIndex] = $branch;
				return true;
			}
		}
		// must be a new branch, so we'll add it
		array_push( $this->branches, $branch );
	}
	
	function removeBranch( $branchID ){
		// look for existing branch to replace
		foreach( $this->branches as $branchIndex => $treeBranch ){
			if( strval( $branchID ) === strval( $treeBranch->ID ) ){
				unset( $this->branches[$branchIndex] );
				return true;
			}
		}
	}
	
	function saveData(){
		$xmlData = new SimpleXMLElement("<tree></tree>");
		$xmlData->addChild( 'title', $this->title );
		$xmlData->addChild( 'description', $this->description );
		foreach( $this->branches as $branch ){
			$branchXML = $xmlData->addChild( 'branch' );
			$branchXML->addAttribute( 'id', $branch->ID );
			$branchXML->addChild( 'content', htmlspecialchars( $branch->content ) );
			foreach( $branch->forks as $forkTarget => $forkLabel ){
				$forkXML = $branchXML->addChild( 'fork', $forkLabel );
				$forkXML->addAttribute( 'target', $forkTarget );
			}			
		}
		if( file_exists( $this->xmlDirPath . $this->treeID ) ){
			$destPath = $this->xmlDirPath . str_replace( '.xml', '.xml.' . time(), $this->treeID );
			rename( $this->xmlDirPath . $this->treeID, $destPath );
		}
		foreach( $this->revisions as $revision ){
			if( !empty( $revision ) ){
				$xmlData->addChild( 'revision', $revision );
			}
		}
		$xmlData->addChild( 'revision', $destPath );
		$xmlData->asXML( $this->xmlDirPath . $this->treeID );
	}
	
	function getTargetBranches( $branchID, $forkLabel = '' ){
		foreach( $this->branches as $branch ){
			if( strval( $branch->ID ) === strval( $branchID ) ){
        $class = '';
				if( empty( $branch->content ) ){
					$class = 'class="missing-content"';
				}
				echo '<li ' . $class . '>' . $forkLabel . $branch->content . '<br /><a href="' . EDITOR_URL . '?cmd=edit-branch&treeID=' . $this->treeID . '&branchID=' . $branchID . '">Edit</a>';
				if( !empty( $branch->forks ) ){
					echo '<ul>';
					foreach( $branch->forks as $forkID => $forkLabel ){
						$this->getTargetBranches( $forkID, '<em>' . $forkLabel . '</em>: <br />' );
					}
					echo '</ul>';
				}
				echo '</li>';
			}
		}
	}
	
	function listAll(){
		if( !is_dir( $this->xmlDirPath ) ){
			die("Cannot access XML directory: $xmlDirPath");
		}
		?>
    <dl>
    <?php
		if( $dh = opendir( $this->xmlDirPath ) ){
			while( $file = readdir( $dh ) ){
				if( strtolower( substr( $file, -4, 4 ) ) == '.xml' ){
					if( $xmlData = simplexml_load_file( $this->xmlDirPath . $file ) ){
						$treeID = str_replace( '.xml', '', substr( $file, 4 ) );
						?>
						<dt><?php echo $xmlData->title; ?> 
            	<span class="action-links"><a target="_blank" href="<?php echo VIEWER_URL; ?>?<?php echo $treeID ; ?>">View</a> | 
              	<a href="<?php echo EDITOR_URL . '?cmd=edit-tree&treeID=' . $file; ?>">Edit</a></span></dt>
						<dd><?php echo $xmlData->description; ?></dd>
						<?php
					}
				}
			}
		}
		?>
    </dl>
    <?php
	}
	

}

class Branch{

	var $ID;
	var $content;
	var $forks = array();
	
	function __construct(){
	
	}
	
	function saveData(){
	
	}
	

}

?>