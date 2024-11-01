<?php
/*
Plugin Name: WordsFinder
Plugin URI: http://wordsfinder.com/
Description: Allows to add keywords.
Version: 1.4
License: GPL
Author: Daniel Infante
Author URI: http://www.wordsfinder.com/
*/

// {{{ wordsfinder()

/*
  * Echoes the Javascript to add the wordsfinder functionality
  *
  * @return bool returns true 
  */
  
$folder = get_option( 'wf_folder' );

if(empty($folder))
	$folder = 'wordsfinder-keywordtag-generator';

function wordsfinder( )
{
	
	global $post_ID;
	global $folder;
		 /*
		  * Display the box only if tags are supported (Version 2.3 or higher)
		  */
	if(function_exists(get_tags_to_edit)) {
	 	 /*
		  * Display the box only when writing or editing a post
		  */
		if ( preg_match( '/post/', $_SERVER['SCRIPT_NAME'] ) ) 	{
	
			echo '<script type="text/javascript" src="../wp-content/plugins/'.$folder.'/js/wordsfinder.js"></script>
'.			'<script type="text/javascript">
'.			'//<![CDATA[
';
	
			$wf = "	var wf = {
".				"		key : '".get_option('wf_api_key')."',
".				"		limit : ".((get_option( 'wf_limit' )) ? get_option( 'wf_limit' ) : 0).",
".				"		folder : '".$folder."'
".				"};
";
		
			echo $wf;	
			echo '//]]>
'.				'</script>
';	
			echo  '<script type="text/javascript">addLoadEvent(wordsfinder)</script>
'.			'<link rel="stylesheet" type="text/css" href="../wp-content/plugins/'.$folder.'/wordsfinder.css" />
';

		}
	} else {
		
		add_action('admin_footer', 'warning');
		
	}
	

	
	return true;
}

// }}}


// {{{ addOptions()

/*
  * Adds a page in the admin menu to configure WordsFinder
  *
  * @return bool returns true 
  */
function addOptions( )
{
	add_menu_page( 'Wordsfinder Configuration', 'Wordsfinder', 8, 'testoptions', 'mt_options_page' );
	
	return true;
}

// }}}


// {{{ warning()

/*
  * Displays a warning message if wordpress version is incompatible with wordsfinder
  *
  * @return bool returns true 
  */
function warning() {
	
		echo "
			<div id='wordsfinder-warning' class='updated fade-ff0000'><p><strong>".__('WordsFinder requires version 2.3 or higher')."</strong></p></div>
			<style type='text/css'>
				#adminmenu { margin-bottom: 5em; }
				#wordsfinder-warning { position: absolute; top: 7em; right: 1em}
			</style>
		";
		
		return true;
	
}
// }}}


// {{{ message()

/*
  * Displays a message from wordsfinder
  *
  * @return bool returns true 
  */
function message() {

	global $folder;

?>
	<script type="text/javascript" src="../wp-includes/js/prototype.js"></script>
	<script type="text/javascript" src="../wp-content/plugins/<?php echo $folder ?>/js/wordsfinder.js"></script>
	<script type="text/javascript">
	//<![CDATA[
		var key = "<?php echo get_option( 'wf_api_key' ) ?>";
		var user_url = document.location.toString();
		addLoadEvent(getMessage);
	//]]>
	</script>
<?php

	echo "
			<div id='wordsfinder-message' class='updated fade-C3D9FF'></div>
			<style type='text/css'>
				#wordsfinder-message { position: absolute; top: 7em; right: 1em; z-index: 100; background: #CDEB8B}
			</style>
		";
	
	return true;
	
}
// }}}


// {{{ mt_options_page()

/*
  * Displays the wordsfinder configuration page
  *
  * @return bool returns true 
  */
function mt_options_page( ) {
	
	global $folder;
	
?>
	<script type="text/javascript" src="../wp-includes/js/prototype.js"></script>
	<script type="text/javascript" src="../wp-content/plugins/<?php echo $folder ?>/js/wordsfinder.js"></script>
	<script type="text/javascript">
	//<![CDATA[
		var key = "<?php echo get_option( 'wf_api_key' ) ?>";
		var wf = { folder: '<?php echo $folder ?>' };
		var user_url = document.location.toString();
		addLoadEvent(getApiStatus);
	//]]>
	</script>
	<style>
		#statusdiv {
			background:#F4F4F4 none repeat scroll 0% 50%;
			border:1px solid #CCCCCC;
			font-size:11px;
			height:200px;
			overflow:auto;
			width:300px;
		}
		
		#ajax-loader {
			width: 16px;
			height: 16px;
			position: relative;
			top: -110px;
			left: 130px;
			display: inline;
		}
	</style>
	<div class="wrap">
    	<h2>Configure Wordsfinder</h2>
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options') ?>
			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e( 'Update Options &raquo;' ) ?>" />
			</p>
			<fieldset class="options">
				<table class="editform optiontable">
				<tbody>
					<tr valign="top">
						<th scope="row">WordsFinder API Key:</th>
						<td><input type="text" size="40" name="wf_api_key" value="<?php echo get_option( 'wf_api_key' ) ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Maximum number of results:</th>
						<td><input type="text" size="3" name="wf_limit" value="<?php echo ((get_option( 'wf_limit' )) ? get_option( 'wf_limit' ) : 0) ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">WordsFinder Plugin Folder:</th>
						<td><input type="text" size="40" name="wf_folder" value="<?php echo ((get_option( 'wf_folder' )) ? get_option( 'wf_folder' ) : 'wordsfinder-keywordtag-generator') ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Account Status:</th>
						<td><div id="statusdiv"></div><div id="ajax-loader"><img src="../wp-content/plugins/<?php echo $folder ?>/ajax-loader.gif" /></div></td>
					</tr>
				</table>
			</fieldset>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="wf_api_key,wf_limit,wf_folder" />
			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e( 'Update Options &raquo;' ) ?>" />
			</p>
			</form>
	</div>
	
<?php	
	return true;
}

 /*
  * Add a hook to call the main function in the head of the document
  */
add_action( 'admin_head', 'wordsfinder' );

 /*
  * Add a hook to add the options page
  */
add_action( 'admin_menu', 'addOptions' );

 /*
  * Add a hook to add the WordsFinder options to the DB
  */
add_action( 'admin_menu', 'addOptions' );

 /*
  * Add a hook to display the message
  */
add_action( 'admin_footer', 'message' );

?>