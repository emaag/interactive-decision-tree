var treeData;
$(document).ready( function(){
	
	windowWidth = $('#tree-window').outerWidth( false );
	sliderWidth = 0;
	slideTime = 300;
	branches = new Array();
	var thisURL = new String(document.location);
	var urlParts = thisURL.split('?');
	loadData( urlParts[1] );
		
});

function debug( str ){
	$('#debug').append( str + '<br />' );
}

function loadData( id ){
	$.ajax({
		type: "GET", 
		url: "xml/tree" + id + ".xml", 
		dataType: "xml", 
		success: function( xml ){
			buildNodes( xml );
		}
	});
}

function TreeBranch(){
	this.id = '';
	this.content = '';
	this.forkIDs = new Array();
	this.forkLabels = new Array();
}

function buildNodes( xmlData ){
	var maxDepth = 0;
	treeData = xmlData;
	$(xmlData).find('branch').each(
		function(){
			var branch = new TreeBranch();
			branch.id = $(this).attr('id');
			branch.content = $(this).find('content').text();
			$(this).find('fork').each(
				function(){
					branch.forkIDs.push( $(this).attr('target') );
					branch.forkLabels.push( $(this).text() );
				}
			);
			branches.push( branch );
			var branchDepthParts = branch.id.split('.');
			if( branchDepthParts.length > maxDepth ){
				maxDepth = branchDepthParts.length;
			}
	});
	sliderWidth = windowWidth * maxDepth;
	$('#tree-slider').width( sliderWidth );
	showBranch( 1 );
}

function resetActionLinks(){
	$('.decision-links a').unbind( 'click' );
	$('a.back-link').unbind( 'click' );
	
	$('.decision-links a').click( function(e){
		if( !$(this).attr('href') ){
			showBranch( $(this).attr('id') );
		}
	});
	$('a.back-link').click( function(){
		$('#tree-window').scrollTo( '-=' + windowWidth + 'px', { axis:'x', duration:slideTime, easing:'easeInOutExpo' } );
		$(this).parent().fadeOut( slideTime, function(){
			$(this).remove();
		});
	});
}

function showBranch( id ){
	for(i = 0; i < branches.length; i++ ){
		if( branches[i].id == id ){
			var currentBranch = branches[i];
			break;
		}
	}
	var decisionLinksHTML = '<div class="decision-links">';
	for( d = 0; d < currentBranch.forkIDs.length; d++ ){
		var link = '';
		var forkContent = $(treeData).find('branch[id="' + currentBranch.forkIDs[d] + '"]').find('content').text();
		if( forkContent.indexOf('http://') == 0 || forkContent.indexOf('https://') == 0 ){
			link = 'href="' + forkContent + '"'
		}
		decisionLinksHTML += '<a ' + link + ' id="' + currentBranch.forkIDs[d] + '">' + currentBranch.forkLabels[d] + '</a>';
	}
	decisionLinksHTML += '</div>';
	var branchHTML = '<div id="branch-' + currentBranch.id + '" class="tree-content-box"><div class="content">' + currentBranch.content + '</div>' + decisionLinksHTML;
	if( currentBranch.id != 1 ){
		branchHTML += '<a class="back-link">&laquo; Back</a>';
	}
	branchHTML += '</div>';
	$('#tree-slider').append( branchHTML );
	resetActionLinks();
	if( currentBranch.id != 1 ){
		$('#tree-window').scrollTo( '+=' + windowWidth + 'px', { axis:'x', duration:slideTime, easing:'easeInOutExpo' } );
	}
	// add last-child class for IE
	$('.decision-links a:last').addClass( 'last-child' );
}
