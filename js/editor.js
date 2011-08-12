$(document).ready( function(){
																												
	$('#branch-editor .btnCancel').click( function(){
		var treeID = $('#treeID').attr( 'value' );
		var formAction = $('#branch-editor').attr( 'action' );
		document.location = formAction = '?cmd=edit-tree&treeID=' + treeID;
	});
	
	$('#tree-editor .btnCancel').click( function(){
		document.location = $('#tree-editor').attr( 'action' );
	});
	
	$('.btnCreateNewTree').click( function(){
		var newURL = document.location + '?cmd=new-tree';
		document.location = newURL;
	});
	
	$('#btnViewTree').click( function(){
		var treeID = $('#treeID').attr( 'value' );
		viewer = window.open( 'showTree.html?' + treeID, 'viewerWindow' );
	});
	
	$('#revisions').change( function(){
		var treeID = $('#treeID').attr( 'value' );
		var revision = $('#revisions option:selected').attr( 'value' );
		var newURL = document.location + '?cmd=edit-tree&treeID=' + treeID + '&revision=' + revision;
		document.location = newURL;
	});
	
	$('#btnAddFork').click( function(){
		var branchID = $('#branchID').attr( 'value' );
		if( $('.fork').length > 0 ){
			var lastForkIDParts = $('.fork:last').attr('id').split('.');
			var lastForkNum = lastForkIDParts[lastForkIDParts.length - 1];
			var nextFork = parseInt(lastForkNum) + 1;
		}else{
			var nextFork = 1;
		}
		var forkID = branchID + '.' + nextFork;
		var newForkHTML = '<span><input class="fork" type="text" id="fork-' + forkID + '" name="fork-' + forkID + '" /> <a href="#" class="btnRemoveFork">&laquo; Remove</a><br /></span>';
		$('p#forks').append( newForkHTML );
//		alert( forkID );
	});
	
	$('.btnRemoveFork').live( 'click',function(){
		$(this).parent().remove();
	});
	
});