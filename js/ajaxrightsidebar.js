var cookieRightSideBar;
var $postscontainer;
var $container;
var $infocontainer;
var $rightside_collaps;
var refreshId;
var $oldrightsidepostsdiv;
var $newrightsidepostsdiv;
var $rightsideposts;
var $rightsidelastvisited;
var infoNotloaded;

var e = document.getElementById('onclick');
e.onclick = toggleRightside;
cookieRightSideBar = getCookie("rightside_container")
$postscontainer = document.getElementById('leftside');
$container = document.getElementById('rightside_container');
$infocontainer = document.getElementById('rightside');
$rightside_collaps = document.getElementById('rightside_collaps');
var $rightsideposts = $("#rightsideposts");	
var $rightsidelastvisited = $("#rightsidelastvisited");	

if (document.getElementById('leftside')) {
	var $myheight = document.getElementById ('leftside').offsetHeight - 56;
	document.getElementById('rightside_container').style.maxHeight = $myheight + 'px';
}

if (cookieRightSideBar == 'expanded') {
	$container.style.display = 'block';
	$infocontainer.style.width =  "19%";
	$postscontainer.style.width = "80%";
	$rightside_collaps.style.clip = "rect(auto auto auto 15px)";
	$rightside_collaps.style.right = "10px";
}

(function() {
	if (cookieRightSideBar == 'collapsed') {
	infoNotloaded = true;
	}
    ajaxupdate();
})();

/*FUNCTIONS*/
function ajaxupdate() {
	if (cookieRightSideBar == 'expanded') {
		(function($)
		{
			$(document).ready(function()
			{
				if (infoNotloaded) {
					$rightsidelastvisited.load('ajaxrightsidebar/includes/last_visited.php?&getlastvisited=1');
					$rightsideposts.load('ajaxrightsidebar/includes/last_posts.php?&getlastposts=1', function() {
						$("#rightside_container").animate({opacity: '1'}, 300);
						infoNotloaded = false;
					});
				} else {
					$rightsideposts.load('ajaxrightsidebar/includes/last_posts.php?&getlastposts=1', function() {
						$("#rightside_container").animate({opacity: '1'}, 150);
					});
				}
				writeContent();
				
				idleTimer = null;
				idleState = false;
				idleWait = 300000;
				
				$('body').bind('mousemove keydown scroll', function () {
					clearTimeout(idleTimer);
					if (idleState == true && cookieRightSideBar == 'expanded') {
						writeContent();				
					}

					idleState = false;
					idleTimer = setTimeout(function () {
					if(refreshId) {
						clearInterval(refreshId);
						refreshId = null;
					}
					idleState = true;
					}, idleWait);
				});
			});
		})(jQuery);
	} else {
		if(refreshId) {
			clearInterval(refreshId);
			refreshId = null;
		}
	}
};

function writeContent() {
	refreshId = setInterval(function()
	{
		$oldrightsidepostsdiv = $rightsideposts.html();
		$rightsideposts.load('ajaxrightsidebar/includes/last_posts.php?&getlastposts=1', function() {
			$newrightsidepostsdiv = $rightsideposts.html();
			if ($newrightsidepostsdiv != $oldrightsidepostsdiv) {
				$rightsideposts.addClass('rightside_highlight');
				removeHighlight = setTimeout(function () {
					$rightsideposts.removeClass('rightside_highlight');
				}, 1500);					
			}
		});
	}, 60000);
}

function toggleRightside() {
    cookieRightSideBar =  (!cookieRightSideBar || cookieRightSideBar == 'collapsed') ? 'expanded' : 'collapsed';
	createCookie("rightside_container", cookieRightSideBar, 180);
	ajaxupdate();
	$postscontainer.style.width = (!$postscontainer.style.width || $postscontainer.style.width == '99%') ? '80%' : '99%';
	$container.style.display = (!$container.style.display || $container.style.display == 'none') ? 'block' : 'none';
	$infocontainer.style.width = (!$infocontainer.style.width || $infocontainer.style.width == '0%') ? '19%' : '0%';
	$rightside_collaps.style.clip = (!$rightside_collaps.style.clip || $rightside_collaps.style.clip == 'rect(auto 15px auto auto)') ? 'rect(auto auto auto 15px)' : 'rect(auto 15px auto auto)';
	$rightside_collaps.style.right = (!$rightside_collaps.style.right || $rightside_collaps.style.right == '0px') ? '10px' : '0px';
	$("#rightside_container").stop(true, true);
	$("#rightside_container").animate({opacity: '0'}, 0);
}

function createCookie(name, value, days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(c_name) {
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf(c_name + "=");
		if (c_start != -1) {
			c_start = c_start + c_name.length + 1;
			c_end = document.cookie.indexOf(";", c_start);
			if (c_end == -1) {
				c_end = document.cookie.length;
			}
			return unescape(document.cookie.substring(c_start, c_end));
		}
	}
	return "";
}