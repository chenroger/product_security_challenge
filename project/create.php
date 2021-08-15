<?php
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    # Check password is entered twice correctly
    if ($_POST["password"] != $_POST["verifypassword"]) {
        header("Location: http://$host$uri/register.php?error=2");
    # Secret question answer to be at least 8 characters
    } else if (strlen($_POST["secretanswer"]) < 8) {
        header("Location: http://$host$uri/register.php?error=4");
    #Enforce only alphanumeric usernames are used
    } else if (!preg_match("/^[0-9a-zA-Z]+$/", $_POST['username'])) {
        header("Location: http://$host$uri/register.php?error=5");
    } else {
        # Check that the password is complex enough
        if (ispwcomplex($_POST['password'])) {
            $db = new SQLite3('users.db');
            $cookie = random_bytes(16);
            $query = $db->prepare('INSERT INTO users (username, pwhash, questionid, answerhash) VALUES (:username, :pwhash, :questionid, :answerhash)');
            $query->bindParam(':username', $username, SQLITE3_TEXT);
            $query->bindParam(':pwhash', $pwhash, SQLITE3_TEXT);
            $query->bindParam(':questionid', $questionid, SQLITE3_INTEGER);
            $query->bindParam(':answerhash', $answerhash, SQLITE3_TEXT);
            $username = $_POST['username'];
            $questionid = (int) $_POST['questionid'];
            # Hash the password with the PHP default algo (bcrypt) with a work factor of 10
            $pwhash = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost" => 10]);
            $answerhash = password_hash($_POST["secretanswer"], PASSWORD_DEFAULT, ["cost" => 10]);
            $result = $query->execute();
            if ($result){
                header("Location: http://$host$uri/");
            } else {
                header("Location: http://$host$uri/register.php?error=1");
            }
        } else {
            header("Location: http://$host$uri/register.php?error=3");
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