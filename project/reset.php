<?php
	$host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $username = $_POST['username'];
    $questionid = $_POST['questionid'];
    $secretanswer = $_POST['secretanswer'];
    # Check password is entered twice correctly
    if ($_POST["password"] != $_POST["verifypassword"]) {
        header("Location: http://$host$uri/forgot.php?error=2");
    # Secret question answer to be at least 8 characters
    } else {
        # Check that the password is complex enough
        if (ispwcomplex($_POST['password'])) {
            $db = new SQLite3('users.db');
            $query = $db->prepare('SELECT username, questionid, answerhash, questionattempts, questionlockeduntil FROM users where username=:username');
            $query->bindParam(':username', $username, SQLITE3_TEXT);
            $username = $_POST['username'];
            $result = $query->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);
            if (!$row) {
                header("Location: http://$host$uri/forgot.php?error=1");
                return;
            }
            if ($row['questionid']==$_POST['questionid'] && password_verify($secretanswer, $row['answerhash']) && (empty($row['questionlockeduntil']) || $row['questionlockeduntil'] < time())) {
            	$resetquery = $db->prepare('UPDATE users SET pwhash=:pwhash, loginattempts=0, lockeduntil=NULL, questionattempts=0, questionlockeduntil=NULL WHERE username=:username AND questionid=:questionid');
            	$resetquery->bindParam(':pwhash', $pwhash, SQLITE3_TEXT);
            	$resetquery->bindParam(':username', $username, SQLITE3_TEXT);
            	$resetquery->bindParam(':questionid', $questionid, SQLITE3_INTEGER);
            	$pwhash = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost" => 10]);
            	$resetresult = $resetquery->execute();
            	if ($resetresult) {
            		header("Location: http://$host$uri/");
            	} else {
            		header("Location: http://$host$uri/forgot.php?error=1");
            	}
            } else {
            	if ($row['questionattempts'] > 3) {
            		$resetquery = $db->prepare('UPDATE users SET questionattempts=:questionattempts, questionlockeduntil=:questionlockeduntil WHERE username=:username');
            		$resetquery->bindParam(':questionattempts', $questionattempts, SQLITE3_TEXT);
            		$resetquery->bindParam(':questionlockeduntil', $questionlockeduntil, SQLITE3_TEXT);
            		$resetquery->bindParam(':username', $username, SQLITE3_INTEGER);
            		$questionattempts = $row['questionattempts'] + 1;
            		$questionlockeduntil = time() + 1800;
            		$resetresult = $resetquery->execute();
            	} else {
            		$resetquery = $db->prepare('UPDATE users SET questionattempts=:questionattempts WHERE username=:username');
            		$resetquery->bindParam(':questionattempts', $questionattempts, SQLITE3_TEXT);
            		$resetquery->bindParam(':username', $username, SQLITE3_TEXT);
            		$questionattempts = $row['questionattempts'] + 1;
            		$resetresult = $resetquery->execute();
            	}
            	$dummyhash = password_hash('DummyAnswer', PASSWORD_DEFAULT, ["cost" => 10]);
            	header("Location: http://$host$uri/forgot.php?error=1");
            }

        } else {
            header("Location: http://$host$uri/forgot.php?error=3");
        }
        
    }
    # Require the password to be at least 8 characters long, 1 number, 1 capital, 1 lowercase
    function ispwcomplex($pw): bool {
        if (strlen($pw) >= 8 && preg_match("/[0-9]/", $pw) && preg_match("/[A-Z]/", $pw) && preg_match("/[a-z]/", $pw)) {
            return true;
        }     
        return false;
    }
?>