<?php
if ($_REQUEST['getlastposts'] == 1)
{
	chdir('../../');
	require_once('/global.php');
	$ajaxupdate = true;
}

if ($_COOKIE['rightside_container'] == 'expanded' OR $ajaxupdate)
{
	global $vbulletin;

	$query = $vbulletin->db->query_read("  
	SELECT *, post.dateline AS post_timestamp FROM post 
	INNER JOIN thread AS thread ON(thread.threadid = post.threadid)
	WHERE thread.visible = 1
	ORDER BY postid DESC LIMIT 0, 20 
	");  

	$postscounter = 1;
	while ($lastposts = $vbulletin->db->fetch_array($query) AND $postscounter <= 10)  
	{      
		$chekedthread = fetch_permissions($lastposts['forumid']);
		if (!($chekedthread & $vbulletin->bf_ugp_forumpermissions['canview']) OR !($chekedthread & $vbulletin->bf_ugp_forumpermissions['canviewthreads']))
		{
			continue;
		}
		if (!($chekedthread & $vbulletin->bf_ugp_forumpermissions['canviewothers']) AND ($thread['postuserid'] != $vbulletin->userinfo['userid'] OR $vbulletin->userinfo['userid'] == 0))
		{
			continue;
		} 
		
		if ($lastposts['post_timestamp'] < strtotime("-1 DAY")) 
		{ 
			$post_ts = vbdate($vbulletin->options['dateformat'], $lastposts['post_timestamp']); 
		} 
		if ($lastposts['post_timestamp'] > strtotime("yesterday")) 
		{ 
			$post_ts = vbdate($vbulletin->options['timeformat'], $lastposts['post_timestamp']) . ",&nbsp;" . strtolower($vbphrase['yesterday']); 
		} 
		if ($lastposts['post_timestamp'] > strtotime("today")) 
		{ 
			$post_ts = vbdate($vbulletin->options['timeformat'], $lastposts['post_timestamp']) . ",&nbsp;" . strtolower($vbphrase['today']); 
		} 
		 
		$lastpostsurl = "<a name=\"post" . $lastposts['postid'] . "\" href=\"showthread.php?t=" . $lastposts['threadid'] . "&p=" . $lastposts['postid'] . "&viewfull=1#post" . $lastposts['postid'] . "\">" . $lastposts['title'] . "</a>"; 
		$lastpostsinfo = "<div class=\"rightsideinfobit\"><div>" . $lastposts['username'] . "&nbsp;>&nbsp;<small>" . $post_ts . "</small></div>" . $lastpostsurl . "</div>"; 
		
		$templater = vB_Template::create('rightside_posts'); 
		$templater->register('lastpostsinfo', $lastpostsinfo); 
		$templatevalues['rightside_lastposts'] .= $templater->render(); 
		vB_Template::preRegister('SHOWTHREAD', $templatevalues);
		$postscounter++;
		if ($ajaxupdate)
		{
			echo $lastpostsinfo;
		}
	}
}
?>