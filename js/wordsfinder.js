/**
 * @author Daniel Infante
 *@version 1.4
 */
 
 function tags_flush_to_text(id, a) {
      a = a || false;
      var taxbox, text, tags, newtags;
  
      taxbox = jQuery('#'+id);
      text = a ? jQuery(a).text() : taxbox.find('input.newtag').val();
  
      // is the input box empty (i.e. showing the 'Add new tag' tip)?
      if ( taxbox.find('input.newtag').hasClass('form-input-tip') && ! a )
          return false;
  
     tags = taxbox.find('.the-tags').val();
      newtags = tags ? tags + ',' + text : text;
  
      // massage
      newtags = newtags.replace(/\s+,+\s*/g, ',').replace(/,+/g, ',').replace(/,+\s+,+/g, ',').replace(/,+\s*$/g, '').replace(/^\s*,+/g, '');
      newtags = array_unique_noempty(newtags.split(',')).join(',');
      taxbox.find('.the-tags').val(newtags);
      tag_update_quickclicks(taxbox);
  
      if ( ! a )
          taxbox.find('input.newtag').val('').focus();
  
      return false;
 }

/**
  * Apply the changes done by the user to the tags
  */
 function wordsfinder( ) {
	
	code = '<input type="button" tabindex="4" value="Generate" id="taggen" class="button"/>';
	code += '<div id="ajax-loader"><img src="../wp-content/plugins/wordsfinder/ajax-loader.gif" /></div>';
	element = $$('input.tagadd')[0];
	element.insert( { 'after' : code } );
	
	init( );

}

/**
  * Initializes the box and its elements
  */
function init( ) {

	$('ajax-loader').hide();
	$( 'taggen' ).observe( 'click', generate );
}

function parse( responseText ) {
	
	
	var keywords = responseText.evalJSON();
		
	tags = '';
	
	if(keywords.length > 0) {
		if( wf.limit != 0 & wf.limit < keywords.length)
			limit = wf.limit;
		else 
			limit = keywords.length;
	
		for( i = 0; i < limit; i++ ) {
			$('new-tag-post_tag').value = keywords[i];
			flag = jQuery('#post_tag').find('input.newtag').removeClass('form-input-tip');
			tags_flush_to_text('post_tag');
		}
		
		$('new-tag-post_tag').value = "";
	}
	
	else {
		
		alert('No keywords were generated');
		
	}
	
}

/**
  * Calls WordsFinder API to generate keywords
  */
function generate( ) {

	$( 'ajax-loader' ).show();
	
	if( typeof tinyMCE != "undefined" ) {
		var editorcontainer = tinyMCE.getInstanceById( 'content' );
	
		postContent = editorcontainer.getContent( );
	} else
	postContent = $( 'content' ).value;
	
	
	new Ajax.Request( '../wp-content/plugins/' + wf.folder + '/proxy.php', {
		parameters: { text: postContent, userid: wf.key, url: 'http://www.wordsfinder.com/extraction/includes/server_ext.php'  },
		onSuccess: function( transport ) { parse( transport.responseText ); $( 'ajax-loader' ).hide(); }
	});

}

/**
  * Gets the API status from wordsfinder and displays it in the options page
  */
function getApiStatus() {
	
	new Ajax.Updater( 'statusdiv', '../wp-content/plugins/' + wf.folder + '/proxy_services.php', {
		parameters: { userid: key, userurl: user_url, url: 'http://www.wordsfinder.com/getapistatus.php' },
		onSuccess: function( transport ) { $( 'ajax-loader' ).hide(); }
	});
	
}

/**
  * Gets and displays the message from wordsfinder
  */
function getMessage() {
	
	$('wordsfinder-message').hide();
	
	new Ajax.Updater( 'wordsfinder-message', '../wp-content/plugins/' + wf.folder + '/proxy_services.php', {
		parameters: { userid: key, userurl: user_url, url: 'http://www.wordsfinder.com/getmessage.php' },
		onSuccess: function( transport ) { if(transport.responseText != "no-message")	{ $( 'admin-menu' ).setStyle({marginBottom: '5em'}); $( 'wordsfinder-message' ).show(); }}
	});
	
}