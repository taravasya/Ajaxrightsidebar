<?php
for ($i=1; $i<=5; $i++)
	{
		$name = 'lvp'.$i;

		if (empty($_COOKIE[$name])) {
			setcookie($name, $threadinfo[threadid], TIMENOW + 365*86400, "/");
			break;
		}

		if ($_COOKIE[$name] == $threadinfo[threadid]) {
			break;
		} else {
			if ($i == 5) {
			$exist = 1;
			}	
		}	

	}

	if ($exist) {

		for ($i=1; $i<=5; $i++)
		{
			$no = $i + 1;
			$names = 'lvp'.$no;
			if ($i == 5) { 
				setcookie('lvp'.$i, $threadinfo[threadid], TIMENOW + 365*86400, "/");
				break;
			} else {
				$lvp_re = 'lvp'.$i;
				setcookie($lvp_re, $_COOKIE[$names], TIMENOW + 365*86400, "/");
			}

		}

	} 
?>