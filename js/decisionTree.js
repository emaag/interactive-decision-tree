var treeData;
var windowWidth;
var sliderWidth;
var slideTime;
var branches;
var path;
var maxDepth;
var isMobile;

$(document).ready( function(){

	isMobile = window.innerWidth < 640;
	windowWidth = isMobile ? window.innerWidth : $('#tree-window').outerWidth( false );
	sliderWidth  = 0;
	slideTime    = 300;
	branches     = [];
	path         = [];
	maxDepth     = 0;

	// Restore theme preference
	var savedTheme = localStorage.getItem('dt-theme');
	if( savedTheme === 'dark' ){
		$('body').addClass('dark');
		$('#dark-toggle').attr('title', 'Switch to light mode');
	} else if( savedTheme === 'light' ){
		$('body').addClass('light');
	}

	var thisURL  = document.location.href;
	var urlParts = thisURL.split('?');
	loadData( urlParts[1] );

	$('#start-over').click( function(){ startOver(); } );

	$('#dark-toggle').click( function(){
		if( $('body').hasClass('dark') ){
			$('body').removeClass('dark').addClass('light');
			localStorage.setItem('dt-theme', 'light');
		} else {
			$('body').removeClass('light').addClass('dark');
			localStorage.setItem('dt-theme', 'dark');
		}
	});

	// Keyboard navigation
	$(document).keydown( function(e){
		if( $(e.target).is('input, textarea, select') ){ return; }

		// 1–9: select that fork option
		var num = e.which - 48;
		if( num >= 1 && num <= 9 ){
			var $links = $('.tree-content-box').last().find('.decision-links a');
			if( $links.length >= num ){
				$links.eq( num - 1 ).trigger('click');
				return false;
			}
		}

		// Backspace: go back
		if( e.which === 8 ){
			var $back = $('.back-link').last();
			if( $back.length ){
				$back.trigger('click');
				return false;
			}
		}
	});

	$(window).resize( function(){
		isMobile = window.innerWidth < 640;
		if( !isMobile ){
			windowWidth = $('#tree-window').outerWidth( false );
		}
	});

});

function escapeHTML( str ){
	return $('<div>').text( str ).html();
}

function debug( str ){
	$('#debug').append( escapeHTML(str) + '<br />' );
}

function loadData( id ){
	if( !id ){
		showError( 'No tree specified. Add a tree ID to the URL, e.g. <code>showTree.html?0001</code>' );
		return;
	}
	$.ajax({
		type: "GET",
		url: "xml/tree" + id + ".xml",
		dataType: "xml",
		success: function( xml ){
			$('#loader').addClass('hidden');
			buildNodes( xml );
		},
		error: function( xhr ){
			$('#loader').addClass('hidden');
			var msg = xhr.status === 404
				? 'Tree <strong>' + $('<div>').text(id).html() + '</strong> not found.'
				: 'Could not load tree (HTTP ' + xhr.status + ').';
			showError( msg );
		}
	});
}

function showError( msg ){
	$('#tree-window').html(
		'<div style="padding:40px;color:var(--text-muted);font-size:1rem;line-height:1.6">'
		+ msg
		+ '</div>'
	);
}

function TreeBranch(){
	this.id         = '';
	this.content    = '';
	this.forkIDs    = [];
	this.forkLabels = [];
}

function buildNodes( xmlData ){
	maxDepth = 0;
	treeData = xmlData;

	var title = $(xmlData).find('title').first().text();
	if( title ){
		$('#tree-title').text( title );
		document.title = title;
	}

	$(xmlData).find('branch').each( function(){
		var branch     = new TreeBranch();
		branch.id      = $(this).attr('id');
		branch.content = $(this).find('content').text();
		$(this).find('fork').each( function(){
			branch.forkIDs.push( $(this).attr('target') );
			branch.forkLabels.push( $(this).text() );
		});
		branches.push( branch );
		var parts = branch.id.split('.');
		if( parts.length > maxDepth ){ maxDepth = parts.length; }
	});

	if( !isMobile ){
		sliderWidth = windowWidth * maxDepth;
		$('#tree-slider').width( sliderWidth );
	}

	showBranch( 1, null );
	var hash = window.location.hash.slice(1);
	if (hash && hash !== '1') {
		replayToId(hash);
	}
}

function updateBreadcrumb(){
	if( path.length === 0 ){
		$('#breadcrumb').html('');
		$('#progress').text('');
		$('#start-over').addClass('hidden');
		return;
	}

	var html = '';
	for( var i = 0; i < path.length; i++ ){
		if( i > 0 ){ html += '<span class="bc-sep">›</span>'; }
		html += '<span class="bc-item">' + escapeHTML( path[i] ) + '</span>';
	}
	$('#breadcrumb').html( html );
	$('#start-over').removeClass('hidden');

	// Progress indicator: step N of ~M
	if( maxDepth > 1 ){
		$('#progress').text( 'Step ' + path.length + ' of ~' + (maxDepth - 1) );
	}
}

function startOver(){
	path = [];
	updateBreadcrumb();
	$('#tree-slider').empty();
	if( !isMobile ){
		$('#tree-window').scrollLeft(0);
	} else {
		$('html, body').animate({ scrollTop: 0 }, slideTime);
	}
	sliderWidth = 0;
	showBranch( 1, null );
	if (history.replaceState) {
		history.replaceState(null, '', window.location.pathname + window.location.search);
	}
}

function resetActionLinks(){
	$('.decision-links a').off('click');
	$('a.back-link').off('click');
	$('.result-start-over').off('click');

	$('.decision-links a').click( function(e){
		if( !$(this).attr('href') ){
			// Read label from data attribute (avoids capturing kbd hint text)
			var label = $(this).data('label');
			path.push( label );
			updateBreadcrumb();
			showBranch( $(this).attr('id'), label );
		}
	});

	$('a.back-link').click( function(){
		path.pop();
		updateBreadcrumb();
		var $box = $(this).closest('.tree-content-box');
		var $prev = $box.prevAll('.tree-content-box').first();
		var prevId = $prev.length ? $prev.attr('id').replace('branch-', '') : '1';
		updateHash(prevId);
		if( isMobile ){
			$box.slideUp( slideTime, function(){ $(this).remove(); } );
			if( $prev.length ){
				$('html, body').animate({
					scrollTop: $prev.offset().top - 60
				}, slideTime );
			}
		} else {
			$('#tree-window').scrollTo(
				'-=' + windowWidth + 'px',
				{ axis: 'x', duration: slideTime, easing: 'easeInOutExpo' }
			);
			$box.fadeOut( slideTime, function(){ $(this).remove(); } );
		}
	});

	$('.result-start-over').click( function(){ startOver(); } );

	$('.copy-link-btn').off('click').click(function() {
		var $btn = $(this);
		var url = window.location.href;
		function onCopied() {
			$btn.text('Copied!').addClass('copied');
			setTimeout(function() { $btn.text('Copy link').removeClass('copied'); }, 2000);
		}
		if (navigator.clipboard) {
			navigator.clipboard.writeText(url).then(onCopied);
		} else {
			var $ta = $('<textarea>').val(url).css({position:'fixed',opacity:0}).appendTo('body');
			$ta[0].select();
			document.execCommand('copy');
			$ta.remove();
			onCopied();
		}
	});
}

function findBranch(id) {
	for (var i = 0; i < branches.length; i++) {
		if (branches[i].id == id) return branches[i];
	}
	return null;
}

function updateHash(id) {
	if (!history.replaceState) return;
	var base = window.location.pathname + window.location.search;
	if (id == 1) {
		history.replaceState(null, '', base);
	} else {
		history.replaceState(null, '', base + '#' + id);
	}
}

function replayToId(targetId) {
	if (!findBranch(targetId)) return;
	var parts = targetId.split('.');
	for (var depth = 2; depth <= parts.length; depth++) {
		var branchId = parts.slice(0, depth).join('.');
		var parentId = parts.slice(0, depth - 1).join('.');
		var parent = findBranch(parentId);
		var label = '';
		if (parent) {
			for (var f = 0; f < parent.forkIDs.length; f++) {
				if (parent.forkIDs[f] === branchId) {
					label = parent.forkLabels[f];
					break;
				}
			}
		}
		path.push(label);
		showBranch(branchId, label, true); // silent=true
	}
	updateBreadcrumb();
	updateHash(targetId);
	if (!isMobile) {
		$('#tree-window').scrollLeft(windowWidth * (parts.length - 1));
	} else {
		var $panel = $('#branch-' + targetId);
		if ($panel.length) {
			$('html, body').scrollTop($panel.offset().top - 60);
		}
	}
}

function showBranch( id, chosenLabel, silent ){
	var currentBranch;
	for( var i = 0; i < branches.length; i++ ){
		if( branches[i].id == id ){
			currentBranch = branches[i];
			break;
		}
	}
	if( !currentBranch ){ return; }

	var isLeaf     = currentBranch.forkIDs.length === 0;
	var boxClass   = 'tree-content-box' + ( isLeaf ? ' result-node' : '' );
	var widthStyle = isMobile ? 'width:100%' : 'width:' + windowWidth + 'px';

	var innerHTML = '';

	if( isLeaf ){
		innerHTML += '<span class="result-label">&#10003; Result</span>';
	}

	innerHTML += '<div class="content">' + escapeHTML( currentBranch.content ) + '</div>';

	if( !isLeaf ){
		innerHTML += '<div class="decision-links">';
		for( var d = 0; d < currentBranch.forkIDs.length; d++ ){
			var link = '';
			var forkContent = $(treeData)
				.find('branch[id="' + currentBranch.forkIDs[d] + '"]')
				.find('content').text();
			if( forkContent.indexOf('http://') === 0 || forkContent.indexOf('https://') === 0 ){
				link = 'href="' + escapeHTML( forkContent ) + '"';
			}
			var kbdHint = ( d < 9 )
				? '<span class="kbd-hint">' + (d + 1) + '</span>'
				: '';
			var label = currentBranch.forkLabels[d];
			innerHTML += '<a ' + link
				+ ' id="'         + escapeHTML( currentBranch.forkIDs[d] ) + '"'
				+ ' data-label="' + escapeHTML( label ) + '">'
				+ kbdHint
				+ escapeHTML( label )
				+ '</a>';
		}
		innerHTML += '</div>';
	}

	if( isLeaf ){
		innerHTML += '<div class="result-actions">';
		innerHTML += '<a class="result-start-over">Start over</a>';
		innerHTML += '<button class="copy-link-btn">Copy link</button>';
		innerHTML += '</div>';
	}

	if( currentBranch.id != 1 ){
		innerHTML += '<a class="back-link">&#8592; Back</a>';
	}

	var branchHTML = '<div id="branch-' + escapeHTML( currentBranch.id ) + '"'
		+ ' class="' + boxClass + '"'
		+ ' style="'  + widthStyle + '">'
		+ innerHTML
		+ '</div>';

	$('#tree-slider').append( branchHTML );
	resetActionLinks();

	if( !silent && currentBranch.id != 1 ){
		if( isMobile ){
			var $panel = $( '#branch-' + currentBranch.id );
			$('html, body').animate({
				scrollTop: $panel.offset().top - 60
			}, slideTime );
		} else {
			$('#tree-window').scrollTo(
				'+=' + windowWidth + 'px',
				{ axis: 'x', duration: slideTime, easing: 'easeInOutExpo' }
			);
		}
	}

	if( !silent ){
		updateHash( currentBranch.id );
	}
}
