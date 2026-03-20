var treeData;
var windowWidth;
var sliderWidth;
var slideTime;
var branches;
var path;

$(document).ready( function(){

	windowWidth = $('#tree-window').outerWidth( false );
	sliderWidth = 0;
	slideTime = 300;
	branches = [];
	path = [];
	var thisURL = document.location.href;
	var urlParts = thisURL.split('?');
	loadData( urlParts[1] );

	$('#start-over').click( function(){
		startOver();
	});

});

function escapeHTML( str ){
	return $('<div>').text( str ).html();
}

function debug( str ){
	$('#debug').append( escapeHTML(str) + '<br />' );
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
	this.forkIDs = [];
	this.forkLabels = [];
}

function buildNodes( xmlData ){
	var maxDepth = 0;
	treeData = xmlData;

	var title = $(xmlData).find('title').first().text();
	if( title ){
		$('#tree-title').text( title );
		document.title = title;
	}

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
	showBranch( 1, null );
}

function updateBreadcrumb(){
	if( path.length === 0 ){
		$('#breadcrumb').html('');
		$('#start-over').addClass('hidden');
		return;
	}
	var html = '';
	for( var i = 0; i < path.length; i++ ){
		if( i > 0 ){
			html += '<span class="bc-sep">›</span>';
		}
		html += '<span class="bc-item">' + escapeHTML( path[i] ) + '</span>';
	}
	$('#breadcrumb').html( html );
	$('#start-over').removeClass('hidden');
}

function startOver(){
	path = [];
	updateBreadcrumb();
	$('#tree-slider').empty();
	$('#tree-window').scrollLeft(0);
	sliderWidth = 0;
	showBranch( 1, null );
}

function resetActionLinks(){
	$('.decision-links a').off( 'click' );
	$('a.back-link').off( 'click' );
	$('.result-start-over').off( 'click' );

	$('.decision-links a').click( function(e){
		if( !$(this).attr('href') ){
			var label = $(this).text();
			path.push( label );
			updateBreadcrumb();
			showBranch( $(this).attr('id'), label );
		}
	});

	$('a.back-link').click( function(){
		path.pop();
		updateBreadcrumb();
		$('#tree-window').scrollTo( '-=' + windowWidth + 'px', { axis:'x', duration:slideTime, easing:'easeInOutExpo' } );
		$(this).closest('.tree-content-box').fadeOut( slideTime, function(){
			$(this).remove();
		});
	});

	$('.result-start-over').click( function(){
		startOver();
	});
}

function showBranch( id, chosenLabel ){
	var currentBranch;
	for( var i = 0; i < branches.length; i++ ){
		if( branches[i].id == id ){
			currentBranch = branches[i];
			break;
		}
	}
	if( !currentBranch ){ return; }

	var isLeaf = currentBranch.forkIDs.length === 0;
	var boxClass = 'tree-content-box' + ( isLeaf ? ' result-node' : '' );

	var innerHTML = '';

	if( isLeaf ){
		innerHTML += '<span class="result-label">&#10003; Result</span>';
	}

	innerHTML += '<div class="content">' + escapeHTML( currentBranch.content ) + '</div>';

	if( !isLeaf ){
		innerHTML += '<div class="decision-links">';
		for( var d = 0; d < currentBranch.forkIDs.length; d++ ){
			var link = '';
			var forkContent = $(treeData).find('branch[id="' + currentBranch.forkIDs[d] + '"]').find('content').text();
			if( forkContent.indexOf('http://') === 0 || forkContent.indexOf('https://') === 0 ){
				link = 'href="' + escapeHTML( forkContent ) + '"';
			}
			innerHTML += '<a ' + link + ' id="' + escapeHTML( currentBranch.forkIDs[d] ) + '">' + escapeHTML( currentBranch.forkLabels[d] ) + '</a>';
		}
		innerHTML += '</div>';
	}

	if( isLeaf ){
		innerHTML += '<a class="result-start-over">Start over</a>';
	}

	if( currentBranch.id != 1 && !isLeaf ){
		innerHTML += '<a class="back-link">&#8592; Back</a>';
	} else if( currentBranch.id != 1 && isLeaf ){
		innerHTML += '<a class="back-link" style="margin-top:12px">&#8592; Back</a>';
	}

	var branchHTML = '<div id="branch-' + escapeHTML( currentBranch.id ) + '" class="' + boxClass + '" style="width:' + windowWidth + 'px">' + innerHTML + '</div>';

	$('#tree-slider').append( branchHTML );
	resetActionLinks();

	if( currentBranch.id != 1 ){
		$('#tree-window').scrollTo( '+=' + windowWidth + 'px', { axis:'x', duration:slideTime, easing:'easeInOutExpo' } );
	}
}
