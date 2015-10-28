<?php
if ($_REQUEST['getlastvisited'] == 1)
{
	chdir('../../');
	require_once('/global.php');
	$ajaxupdate = true;
}

if ($_COOKIE['rightside_container'] == 'expanded' OR $ajaxupdate)
{
	global $vbulletin;
	
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
			if ($ajaxupdate)
			{
				echo $lastvisitedinfo;
			}
		}
	}
}
?>