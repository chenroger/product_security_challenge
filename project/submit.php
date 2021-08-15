<?php
	$db = new SQLite3('users.db');
	$query = $db->prepare('SELECT username, pwhash, session, loginattempts, lockeduntil FROM users WHERE username=:username');
	$query->bindParam(':username', $username, SQLITE3_TEXT);
	$username = $_POST['username'];
	$password = $_POST['password'];
	$result = $query->execute();
	$host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $cookie = $_COOKIE['SessionToken'];
    # check CSRF token
    if (hash('sha256', $cookie) != $_POST['token']) {
    	header("Location: http://$host$uri/");
    	return;
    }
    # If an existing user that matches the entered username
	if ($row = $result->fetchArray(SQLITE3_ASSOC)){
		if (empty($row['lockeduntil']) || $row['lockeduntil'] < time()) {
			if (password_verify($password, $row['pwhash'])) {
				$srow = 1;
				$newsession = "";
				# if the session token exists in the DB get another one and replace it
				while ($srow) {
					$checksessions = $db->prepare('SELECT session FROM users WHERE session=:session');
					$checksessions->bindParam(':session', $session, SQLITE3_TEXT);
					# 128 bit session token
					$session = bin2hex(random_bytes(16));
					$sresult = $checksessions->execute();
					$srow = $sresult->fetchArray(SQLITE3_ASSOC);
					$newsession = $session;
				}
				$goodloginquery = $db->prepare('UPDATE users SET session=:session, sessionexpiry=:sessionexpiry, loginattempts=0 WHERE username=:username');
				$goodloginquery->bindParam('session', $newsession, SQLITE3_TEXT);
				$goodloginquery->bindParam(':sessionexpiry', $sessionexpiry, SQLITE3_INTEGER);
				$goodloginquery->bindParam(':username', $username, SQLITE3_TEXT);
				# 30 minute session validity
				$sessionexpiry = time() + 1800;
				$gquery = $goodloginquery->execute();
				setcookie("SessionToken", $newsession, NULL, NULL, NULL, NULL, TRUE);
				header("Location: http://$host$uri/success.php");
			} else { # password failure case
				# lockout for 30 minutes if there's 5 bad password attempts
				if ($row['loginattempts'] > 3) {
					$badpwquery = $db->prepare('UPDATE users SET loginattempts=:loginattempts, lockeduntil=:lockeduntil WHERE username=:username');
					$badpwquery->bindParam(':loginattempts', $newloginattempts, SQLITE3_INTEGER);
					$badpwquery->bindParam(':lockeduntil', $lockoutuntil, SQLITE3_INTEGER);
					$badpwquery->bindParam(':username', $username, SQLITE3_TEXT);
					$lockoutuntil = time() + 1800;
					$newloginattempts = $row['loginattempts'] + 1;
				} else {
					$badpwquery = $db->prepare('UPDATE users SET loginattempts=:loginattempts WHERE username=:username');
					$badpwquery->bindParam(':loginattempts', $newloginattempts, SQLITE3_INTEGER);
					$badpwquery->bindParam(':username', $username, SQLITE3_TEXT);
					$newloginattempts = $row['loginattempts'] + 1;
				}
				$badpwresult = $badpwquery->execute();
				header("Location: http://$host$uri/?error=1");
			}
		} else {
			header("Location: http://$host$uri/?error=2");
		}

    } else {
    	$dummy = password_verify("DummyPass", $row['pwhash']);
        header("Location: http://$host$uri/?error=1");
    }
?>