<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/main.css">
</head>

<body>
    <div class="login-form">
        <form action="submit.php" method="post">
            <h2 class="text-center">Log in</h2>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Username" name="username" required="required">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" name="password" required="required">
            </div>
            <div class="form-group">
                <input type="hidden" name="token" value="<?php
                    if (empty($_COOKIE['SessionToken'])) {
                    $db = new SQLite3('users.db');
                    $srow = 1;
                    $newsession = '';
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
                    setcookie('SessionToken', $newsession, NULL, NULL, NULL, NULL, TRUE);
                    echo hash('sha256', $newsession);
                    } else {
                        echo hash('sha256', $_COOKIE['SessionToken']);
                    }
                    ?>" />
                <button type="submit" class="btn btn-primary btn-block">Log in</button>
            </div>
            <?php
                if ($_GET['error'] == 1) {
                    echo "<b>Login Failed</b>";
                } else if ($_GET['error'] == 2) {
                    echo "<b>Account locked out</b>";
                }
            ?>
            <div class="clearfix">
                <!--<label class="pull-left checkbox-inline"><input type="checkbox"> Remember me</label>-->
                <a href="/forgot.php" class="pull-right">Forgot Password?</a>
            </div>
        </form>
        <p class="text-center"><a href="/register.php">Create an Account</a></p>
    </div>
</body>
</html>
