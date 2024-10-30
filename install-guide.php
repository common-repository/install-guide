<?php
	/*
	Plugin Name: Install Guide
	Plugin URI: http://www.fedmich.com/works/?src=wp_plugins
	Description: Install Guide
	Author: Fedmich
	Author URI: http://www.fedmich.com/works/?src=wp_plugins
	Version: 1.12
	*/
	
	define("INSTG_VERSION", "1.12");
	define("INSTG_CODERED", "red!");
	define("INSTG_CODEBLUE", "blue!");

	
	if($_SERVER['REQUEST_METHOD']=='POST'){
		if(isset($_POST['instg_options'])){
		
		$fkey = 'instg_root_menu';
		if(isset($_POST[$fkey])){$d = stripslashes($_POST[$fkey]);$d = $d=='on'?'1':0;}else{ $d=0; }
		update_option('instg_root_menu',$d);
		
		$uri = $_SERVER['REQUEST_URI'];
		header("Location: $uri&updated=true"); exit();
		}
	}
	
	
	/**
	 * Pre-2.6 compatibility
	 */
	if ( !defined( 'WP_CONTENT_URL' ) )
		define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	if ( !defined( 'INSTG_SITE_URL' ) )
		define( 'INSTG_SITE_URL', get_option( 'siteurl' ));
	if ( !defined( 'INSTG_WP_ADMIN' ) )
		define( 'INSTG_WP_ADMIN', INSTG_SITE_URL.'/wp-admin/');
	//	if ( !defined( 'INSTG_WP_PLUG' ) )
	
	if ( !defined( 'WP_CONTENT_DIR' ) )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );	
	
	define( 'INSTG_WP_PLUG', WP_CONTENT_URL.'/plugins/'.preg_replace('@.*(/|\\\)plugins(/|\\\)@i','', dirname(__FILE__)));

	
	
	
	function instg_add_admin(){
		$chkd= get_settings('instg_root_menu')?1:0;
		if(! $chkd){
		add_options_page('Install Guide', 'Install Guide', 1, 'INSTG', 'instg_options_page');
		}
		else{
		add_menu_page('INSTG', 'Install Guide', 'manage_options', 'INSTG', 'instg_options_page'
			,INSTG_WP_PLUG."/favicon_f.ico");
		add_submenu_page('INSTG', 'Plugin list', 'Plugin list', 1, 'INSTG#plugins_list', 'instg_options_page');
		add_options_page('Install Guide', 'Install Guide', 1, __FILE__, 'instg_options_page');
		}
	}
	
	function instg_options_page() {
		global $wpdb, $table_prefix;

		if(isset($_GET['htaccess'])){
			if($htaccess = trim(strip_tags($_GET['htaccess']))){
				$hdir = dirname(__FILE__)."/../../../$htaccess";
				if(is_dir($hdir)){
					$hfile = "$hdir/.htaccess";
					if(! file_exists($hfile)){
						$htext = 'IndexIgnore */*';
						file_put_contents($hfile,$htext);
					}
				}
			}
		}
		
		
		$todos = Array(); $todos2 = Array(); $todos3 = Array();
		
		$sql = "select option_value from ".DB_NAME.".$table_prefix"."options
			where option_name = 'blogname'
			";
		$v = $wpdb->get_var($sql);
		switch($v){
		case '':
			$todos[] = INSTG_CODERED.'<a target="_blank" href="'.INSTG_WP_ADMIN.'options-general.php">Change Blog name</a>';
			break;
		default:
			$todos[] = 'BlogName <b>OK</b>';
		}
		
		$sql = "select option_value from ".DB_NAME.".$table_prefix"."options
			where option_name = 'blogdescription'
			";
		$v = $wpdb->get_var($sql);
		switch(strtolower(trim($v))){
		case 'just another wordpress site':
		case 'just another wordpress weblog':
			$todos[] = INSTG_CODEBLUE.'<a target="_blank" href="'.INSTG_WP_ADMIN.'options-general.php">Change tagline</a> '. "(<i>$v</i>)";
			break;
		case '':
			$todos[] = INSTG_CODERED.'<a target="_blank" href="'.INSTG_WP_ADMIN.'options-general.php">Change tagline</a>';
			break;
		default:
			$todos[] = 'Tagline <b>OK</b>';
		}
		
		$sql = "select option_value from ".DB_NAME.".$table_prefix"."options
			where option_name = 'permalink_structure'
			";
		$v = $wpdb->get_var($sql);
		switch($v){
		case '':
			$todos[] = '<a target="_blank" href="'.INSTG_WP_ADMIN.'options-permalink.php">Set Permalinks</a> <br /> Recommended : <input type="text" value="/%postname%" onclick="this.select();" />';
			break;
		default:
			$todos[] = 'Permalinks Settings <b>OK</b>';
		}
		
		$code = '';
		$rec_upload_path = 'wp-content/uploads';
		$sql = "select option_value from ".DB_NAME.".$table_prefix"."options
			where option_name = 'upload_path'
			";
		$v = $wpdb->get_var($sql);
		switch($v){
		case '':
			$upload_path_ok = 0;
			$code = 'red!';
			break;
		default:
			if($v!=$rec_upload_path){
				$upload_path_ok = 0;
			}
			else{
				$upload_path_ok = 1;
			}
		}
		if(! $upload_path_ok){
			$todos[] = INSTG_CODERED.'<a target="_blank" href="'.INSTG_WP_ADMIN.'options-media.php">Set Upload folder</a> <br /> Recommended settings : <input type="text" value="'.$rec_upload_path.'" onclick="this.select();" />';
		}
		else{
			$todos[] = 'Upload Path <b>OK</b>';
		}
		
		$todos[] = INSTG_CODEBLUE.'Install Plugins from <a target="_blank" href="http://fedwp.com/install/?site='.urlencode(INSTG_WP_ADMIN).'">Fedmich\'s list</a>';
		
		$lng_nobrowse = 'Prevent directory browsing on /<b>plugins</b>/ using <b>.htaccess</b> <br />Add an htaccess file on with content <br /> IndexIgnore */* <br /><a href="?page=INSTG&htaccess=wp-content/plugins">Create this file</a>';
		$filecheck = WP_CONTENT_DIR.'/plugins/.htaccess';
		if(! file_exists($filecheck)){
			$todos2[] = INSTG_CODERED.$lng_nobrowse;
		}
		
		$lng_nobrowse = 'Prevent directory browsing on /<b>plugins</b>/ using <b>.htaccess</b> <br />Add an htaccess file on with content <br /> IndexIgnore */* <br /><a href="?page=INSTG&htaccess=wp-content/themes">Create this file</a>';
		$filecheck = WP_CONTENT_DIR.'/themes/.htaccess';
		if(! file_exists($filecheck)){
			$todos2[] = INSTG_CODERED.str_replace('plugins','themes',$lng_nobrowse);
		}
		$lng_nobrowse = 'Prevent directory browsing on /<b>plugins</b>/ using <b>.htaccess</b> <br />Add an htaccess file on with content <br /> IndexIgnore */* <br /><a href="?page=INSTG&htaccess=wp-includes">Create this file</a>';
		$filecheck = WP_CONTENT_DIR.'/../wp-includes/.htaccess';
		if(! file_exists($filecheck)){
			$todos2[] = INSTG_CODERED.str_replace('plugins','wp-includes',$lng_nobrowse);
		}
		
		$lng_nobrowse = 'Prevent directory browsing on /<b>plugins</b>/ using <b>.htaccess</b> <br />Add an htaccess file on with content <br /> IndexIgnore */* <br /><a href="?page=INSTG&htaccess=wp-admin">Create this file</a>';
		$filecheck = WP_CONTENT_DIR.'/../wp-admin/.htaccess';
		if(! file_exists($filecheck)){
			$todos2[] = INSTG_CODERED.str_replace('plugins','wp-admin',$lng_nobrowse);
		}
		$todos2[] = 'Check for securities. Configure/chmod files and folders';
		

		$todos3[] = 'Generate a sitemap.xml';
		$todos3[] = 'Install SEO Plugins';
		$todos3[] = 'Add any analytics/stats tracker like Google Analytics, Wordpress Stats, Woopra, etc';
		$todos3[] = 'Edit default page, <b>About</b>';
		$todos3[] = 'Change theme';
		$todos3[] = 'Plan for Automatic-backup';
		
		$todos3[] = 'Favicon of the site';
		
		$todos3[] = 'Install 404 Notifier';
		$todos3[] = 'Install Search Meter';
		$todos3[] = 'Install Search Terms Tagging';
		
		?>
		<div class="wrap inst_guide">
			<div class="icon32" id="icon-edit"><br></div>
			<h2>Install Guide v<?php echo INSTG_VERSION;?></h2>
		
		<table border="0" align="center">
			<tr>
			<td align="center" valign="top" width="700" >
		<p>
		<?php include dirname(__FILE__)."/do.php"; ?>
		</p>
		
		<div class="todos">
		
		<script type="text/javascript">
			function chked_t(o){
				if(o.checked){
				jQuery(o).parent('label').addClass('done');
				}
				else{
				jQuery(o).parent('label').removeClass('done');
				}
			}
		</script>
		<style type="text/css">
		<!--
		<?php echo file_get_contents(dirname(__FILE__)."/install-guide.css"); ?>
		-->
		</style>
		
		<?php
			if($todos){
			echo '<fieldset class="options"><legend>Common Installations</legend><ol>';
			foreach($todos as $todo){
			$todo = str_replace(INSTG_CODERED,'<img src="'.INSTG_WP_PLUG.'/i_red.png" width="16" height="16" alt="" title="" />&nbsp;',$todo);
			$todo = str_replace(INSTG_CODEBLUE,'<img src="'.INSTG_WP_PLUG.'/i_blue.png" width="16" height="16" alt="" title="" />&nbsp;',$todo);
		?>
			<li><?php echo $todo;?></li>
		<?php
			}
			echo '</ol></fieldset>';
			}
		?>
		
		<?php
			if($todos3){
			echo '<fieldset class="options"><legend>Security Settings</legend><ol>';
			foreach($todos2 as $todo){
			$todo = str_replace(INSTG_CODERED,'<img src="'.INSTG_WP_PLUG.'/i_red.png" width="16" height="16" alt="" title="" />&nbsp;',$todo);
			$todo = str_replace(INSTG_CODEBLUE,'<img src="'.INSTG_WP_PLUG.'/i_blue.png" width="16" height="16" alt="" title="" />&nbsp;',$todo);
		?>
			<li><?php echo $todo;?></li>
		<?php
			}
			echo '</ol></fieldset>';
			}
		?>
		
		<br />
		
		<fieldset class="options">
			<legend>Delete Sample Datas</legend>
			<form action="" method="GET">
				<input type="hidden" name="page" value="INSTG" />
				<input type="hidden" id="page" name="do" value="delete_sample_posts" />
				<label><input name="sample_posts" type="checkbox" checked />&nbsp;Sample Posts</label>
				<br />
				<label><input name="sample_links" type="checkbox" checked />&nbsp;Sample Links</label>
				<br /> <label><input name="sample_page" type="checkbox" checked />&nbsp;Sample Page</label>
				<br /> <label><input name="sample_comments" type="checkbox" checked />&nbsp;Sample Comments</label>
				<br /> <label><input name="sample_plugin" type="checkbox" checked />&nbsp;Sample Plugin <i>(Hello.php)</i></label>
				<br />
				
				<input type="submit" value="Delete Sample datas" />
			</form>
		</fieldset>
		<br />
		
		<fieldset class="options">
			<legend>Create basic pages</legend>
			<a href="?page=INSTG&do=addpage_blog">Blog page</a>
			
			<br />
			<a href="?page=INSTG&do=addpage_home">Home page</a>
			<br />
			
			&nbsp;&nbsp;
			<a target="_blank" href="<?php echo INSTG_WP_ADMIN ?>options-reading.php">Reading Settings</a>
		</fieldset>
		<br />
		
		<?php
			if($todos){
			echo '<fieldset class="options"><legend>More Checklist</legend><ol>';
			foreach($todos3 as $todo){
		?>
			<li>
				<label><input type="checkbox" onclick="chked_t(this);" />&nbsp;<?php echo $todo;?></label>
			</li>
		<?php
			}
			echo '</ol></fieldset>';
			}
		?>
		
		<form action="" method="POST">
			<input type="hidden" name="instg_options" value="" />
			
			<?php $chkd = get_settings('instg_root_menu')?'checked':''; ?>
			<label><input name="instg_root_menu" type="checkbox" <?php echo $chkd; ?> />&nbsp;Add on root menu</label>
			
			<input type="submit" value="Save" />
		</form>
		<br />
	
		
				</td>
				<td align="left" valign="top" width="300" >
					
				<p>
					Install Guide v<?php echo INSTG_VERSION;?>
					<br />
					
					Brought to you by <b><a target="_blank" href="http://www.fedmich.com/tools/wordpress-install-guide">Fedmich</a></b>
					
					<br />
					<br />
					<a target="_blank" href="http://www.fedmich.com/tools/donate" ><img src="http://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" width="122" height="47" alt="Donate" title="Donate" style="border:0px;" /></a>
					
					<small><br />
					<br />
					Send your suggestions and comments on <a target="_blank" href= "http://www.fedmich.com/tools/wordpress-install-guide">fedmich.com/tools/</a></small>
					
					<br />
					
					<h2>About</h2>
					<div style="padding:15px;">
					<small>Show your <b>support</b> by clicking <b>Like</b> below on facebook.</small>
					<div style="padding:5px;">
					<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2FFedmichWorks&amp;layout=standard&amp;show_faces=true&amp;width=240&amp;action=like&amp;font=lucida+grande&amp;colorscheme=light&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:240px; height:80px;" allowTransparency="true"></iframe>
					<br />
					<iframe src="http://fedwp.com/news/install-guide/?sidebar" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:240px; height:400px;" allowTransparency="true"></iframe>
					</div>
				
					
				</p>
		
				</td>
			</tr>
		</table>
		
		<?php instg_plugins_list(); ?>
		
		</div>
		</div>
		<?php
	}

	
	add_action('admin_menu', 'instg_add_admin');
	
	function instg_plugins_list(){
		?>
		<a name="plugins_list"></a>
		<b><caption>List of Plugins</caption></b>
		<table border="1" align="center" id="all-plugins-table" class="widefat">
		<thead>
			<tr>
			<th>#</th>
			<th>Name</th>
			<th>Version</th>
			<th>URL</th>
			<th>Active?</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$plugins = get_plugins();
		if($plugins){
		$ctr=0;
		foreach($plugins as $plugin_file => $pld) {
			++$ctr;
			$plg_active=is_plugin_active($plugin_file)?1:0;
			?>
			<tr class="<?php echo $plg_active?'plg_active':''?>">
				<td width="25"><?php echo $ctr;?></td>
				<td width="300" ><span class="nme"><?php echo $pld['Name']; ?></span></td>
				<td width="450" >
				<span class="url"><?php echo str_replace('http://','',$pld['PluginURI']); ?></span>
				</td>
				<td align="center">
					<?php echo $plg_active?'Yes':'-'; ?>
				</td>
				<td>
				v<?php echo $pld['Version']; ?>
				</td>
			</tr>
			<?php
			$txlist[] = join("\t",Array($pld['Name'],'v.'.$pld['Version'],$pld['PluginURI']));
		}
		}
		$txlist = join("\r\n",$txlist);
		?>
		</tbody>
		</table>
		
		<br />
		Copy/Paste the list of plugins: <small><a href="admin.php?page=INSTG&save_pluglist">save as CSV</a></small>
		<br />
		<textarea rows="3" cols="100" wrap="off" onclick="this.select();"><?=$txlist;?></textarea>
		<?php
	}
	
	
	function instg_set_plugin_meta($links, $file) {
		$plugin = plugin_basename(__FILE__);
		if ($file == $plugin) {
		return array_merge( $links, array( sprintf( '<a target="_blank" href="http://www.fedmich.com/tools/wordpress-install-guide">%s</a>', __('Help and FAQ') ) ));
		}
		return $links;
	}
	add_filter( 'plugin_row_meta', 'instg_set_plugin_meta', 10, 2 );
	
	if(isset($_GET['theme'])){
		function instg_theme_template($template){
			$theme = $_GET['theme'];
			$thd = get_theme($theme);
			if (!empty($thd)) {
				if (isset($thd['Status']) && $thd['Status'] != 'publish') { return $template;}
				return $thd['Template'];
			}
			
			$themes = get_themes();
			foreach ($themes as $thd) {
				if ($thd['Stylesheet'] == $theme) {
					if (isset($thd['Status']) && $thd['Status'] != 'publish') { return $template;}
					return $thd['Template'];
				}
			}
			return $template;
		}
		
		function instg_theme_css($stylesheet){
			$theme = $_GET['theme'];
			$thd = get_theme($theme);
			if (!empty($thd)) {
				  if (isset($thd['Status']) && $thd['Status'] != 'publish') { return $stylesheet;}
				  return $thd['Stylesheet'];
			}
			
			$themes = get_themes();
			foreach ($themes as $thd) {
				if ($thd['Stylesheet'] == $theme) {
				if (isset($thd['Status']) && $thd['Status'] != 'publish') { return $stylesheet;}
				return $thd['Stylesheet'];
				}
			}
			return $stylesheet;
		}
  	
	
		add_action('plugins_loaded','filters_changetheme');
		function filters_changetheme() {
			add_filter('template', 'instg_theme_template');
			add_filter('stylesheet', 'instg_theme_css');
		}
 
	}
	
	add_action('plugins_loaded','filters_checksave');
	
	function filters_checksave(){
	if(isset($_GET['save_pluglist'])){
	if('INSTG'==(isset($_GET['page'])?$_GET['page']:'')){
	if(strstr($_SERVER['REQUEST_URI'],'/wp-admin/')){
	include dirname(__FILE__)."/../../../wp-admin/includes/plugin.php";
	if(! headers_sent()){
		header("Content-type: application/csv");
		header("Content-Disposition:attachment;filename=".$_SERVER['HTTP_HOST']."_plugins.csv");
	} instg_plugins_list_save(); exit();
	}}}}
	
	function instg_plugins_list_save(){
		$plugins = get_plugins();
		if($plugins){
			$ctr=0;
			++$ctr;$txlist[] = join(",",Array('Name', 'ver','url','Active'));
			foreach($plugins as $plugin_file => $pld) {
				++$ctr;$txlist[] = join(",",
				Array($pld['Name'],'v.'.$pld['Version'],$pld['PluginURI']
				,(is_plugin_active($plugin_file)?'Yes':'')
				));
			}
		}
		$txlist = join("\r\n",$txlist);
		echo $txlist;
	}
	
	
?>