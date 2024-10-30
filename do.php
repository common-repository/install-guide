<?php
	if($do=isset($_GET['do'])?$_GET['do']:''){
		switch($do){
		case 'addpage_blog':
			$sql = "select ID from ".DB_NAME.".$table_prefix"."posts
			where post_type = 'page' and post_title = 'Blog' and post_status <> 'trash'
			";
			$ret = $wpdb->get_var($sql);
			if(! $ret){
				$sql = "insert into ".DB_NAME.".$table_prefix"."posts
				(post_type, post_content,post_title,post_name,post_status)
				values('page','Blog listing','Blog','blog','publish') ";
				$ret = $wpdb->query($sql);
			}
			
			if($ret){
			echo '<br />';
			echo '<b>Added blog page</b>';
			}
			break;
		case 'addpage_home':
			$sql = "select ID from ".DB_NAME.".$table_prefix"."posts
			where post_type = 'page' and post_title = 'Home' and post_status <> 'trash'
			";
			$ret = $wpdb->get_var($sql);
			if(! $ret){
				$sql = "insert into ".DB_NAME.".$table_prefix"."posts
				(post_type, post_content,post_title,post_name,post_status)
				values('page','Welcome','Home','home','publish') ";
				$ret = $wpdb->query($sql);
			}
			
			if($ret){
			echo '<br />';
			echo '<b>Added home page</b>';
			}
			break;
		case 'delete_sample_posts':
		?>
		<div align="left">
		<h4>Deleting Sample datas...</h4>
		
		<?php
		if('on'==($inp_check=isset($_GET['sample_page'])?$_GET['sample_page']:'')){
			$sql = "delete from ".DB_NAME.".$table_prefix"."posts
			where post_content = 
			'This is an example of a WordPress page, you could edit this to put information about yourself or your site so readers know where you are coming from. You can create as many pages like this one or sub-pages as you like and manage all of your content inside of WordPress.'
			and post_title = 'About'
			";
		$ret = $wpdb->query($sql);
		if($ret){	?>
		<b>Deleted About page</b>
		<br />
		<?php	}}	?>
		
		<?php
		if('on'==($inp_check=isset($_GET['sample_comments'])?$_GET['sample_comments']:'')){
			$sql = "delete from ".DB_NAME.".$table_prefix"."comments
			where comment_content = 
			'Hi, this is a comment.<br />To delete a comment, just log in and view the post&#039;s comments. There you will have the option to edit or delete them.'
			and comment_author = 'Mr WordPress'
			";
		
		$ret = $wpdb->query($sql);
		if($ret){	?>
		<b>Deleted Sample Comment</b>
		<br />
		<?php	}}	?>
		
		<?php
		if('on'==($inp_check=isset($_GET['sample_posts'])?$_GET['sample_posts']:'')){
		$sql = "delete from ".DB_NAME.".$table_prefix"."posts
			where
			post_content = 
			'Welcome to WordPress. This is your first post. Edit or delete it, then start blogging!'
			and post_title = 'Hello world!'
			";
		$ret = $wpdb->query($sql);
		if($ret){	?>
		<b>Deleted Hello world! posts</b>
		<br />
		<?php	}}	?>
		
		<?php
		if('on'==($inp_check=isset($_GET['sample_links'])?$_GET['sample_links']:'')){
		$sql = "delete from ".DB_NAME.".$table_prefix"."links
			where link_url
			in('http://codex.wordpress.org/'
			,'http://wordpress.org/news/'
			,'http://wordpress.org/extend/ideas/'
			,'http://wordpress.org/support/'
			,'http://wordpress.org/extend/plugins/'
			,'http://wordpress.org/extend/themes/'
			,'http://planet.wordpress.org/'
			)
			";
		$ret = $wpdb->query($sql);
		if($ret){	?>
		<b>Deleted sample links</b>
		<br />
		<?php	}}	?>
		<?php
		if('on'==($inp_check=isset($_GET['sample_plugin'])?$_GET['sample_plugin']:'')){
		$filecheck = WP_CONTENT_DIR.'/plugins/hello.php';
		$ret = 0;
		if(file_exists($filecheck)){
			$ret = unlink($filecheck)?1:0;
		}
		if($ret){	?>
		<b>Deleted hello.php</b>
		<br />
		<?php	}}	?>
		
		<?php
			if($ret){
				echo '<br />';
			}
		?>
		<br />
		Done
		</div>
		<?php
			break;
		default:
			break;
		}

	}
?>