
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Successful</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/main.css">
</head>

<body>
    <div class="success_div">
    	<?php
            header('Cache-Control: no-cache, no-store');
            header('Expires: 0');
            header('Pragma: no-cache');
            $db = new SQLite3('users.db');
            $cookie = $_COOKIE["SessionToken"];
            $query = $db->prepare('SELECT username, session, sessionexpiry FROM users WHERE session=:session');
            $query->bindParam(':session', $cookie, SQLITE3_TEXT);
            # Hash the password with the PHP default algo (bcrypt) with a work factor of 10
            $result = $query->execute();
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\\\');
            if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                if ($row['sessionexpiry'] > time()) {
                    echo "You are logged in";
                } else {
                    header("Location: http://$host$uri/");
                }
            } else {
                header("Location: http://$host$uri/");
            }
		?>
		 <form action="/logout.php" method="post">
            <div class="form-group">
                <input type="hidden" name="token" value="<?php echo hash('sha256', $cookie) ?>" />
                <button type="logout" class="btn btn-primary btn-block">Log out</button>
            </div>
        </form>
    </div>
</body>
</html>
