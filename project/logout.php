<?php
	$db = new SQLite3('users.db');
	$cookie = $_COOKIE['SessionToken'];
	if (hash('sha256', $cookie) == $_POST['token']) {
		$query = $db->prepare('UPDATE users SET session=null, sessionexpiry=null WHERE session=:session');
		$query->bindParam(':session', $cookie, SQLITE3_TEXT);
		$result = $query->execute();
	}
	setcookie("SessionToken", "");
	$host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\\\');
    header("Location: http://$host$uri/");
?>