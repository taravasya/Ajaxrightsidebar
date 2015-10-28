<?php
if ($_REQUEST['getlastposts'] == 1)
{
	chdir('../../');
	require_once('/global.php');
	$ajaxupdate = true;
}

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
    $lastpostsinfo = "<div class=\"rightsideinfobit\"><div>" . $lastposts['username'] . "&nbsp;&#187;&nbsp;<small>" . $post_ts . "</small></div>" . $lastpostsurl . "</div>"; 
    
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

$lvptid = 0;
for ($i=5; $i>=1; $i--)
{
	$cookiename = 'lvp'.$i;
	$cookielvp = $vbulletin->input->clean_gpc('c', $cookiename, TYPE_UNIT);

	if (!empty($cookielvp)) {
		$lvptid .= ','.$cookielvp;
	}

}

if (!empty($lvptid))
{
	$lvps = $vbulletin->db->query_read("
	SELECT thread.threadid, thread.title AS threadtitle, forum.forumid, forum.title_clean AS forumtitle
	FROM " . TABLE_PREFIX . "thread AS thread
	INNER JOIN " . TABLE_PREFIX . "forum AS forum ON(forum.forumid = thread.forumid)
	WHERE thread.threadid IN (".$lvptid.") ORDER BY FIELD(thread.threadid, ".$lvptid.")
	");

	while ($lvp = $vbulletin->db->fetch_array($lvps))
	{
		$visitedthreadurl = "<div class=\"rightsideinfobit\"><div>" . $vbphrase['thread'] . ":&nbsp;<a href=\"showthread.php?t=" . $lvp['threadid'] . "&goto=newpost\">" . $lvp['threadtitle'] . "</a>";
		$fromforumurl = $vbphrase['forum'] . ":&nbsp;<a href=\"forumdisplay.php?f=" . $lvp['forumid'] . "\">" . $lvp['forumtitle'] . "</a></div></div>";
		$lastvisitedinfo = $visitedthreadurl . "<br />" . $fromforumurl;

		$templater = vB_Template::create('rightside_lastvisited');
		$templater->register('lastvisitedinfo', $lastvisitedinfo);
		$templatevalues['rightside_lastvisited'] .= $templater->render();
		vB_Template::preRegister('SHOWTHREAD', $templatevalues);
	}
}

?>