<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/main.css">
</head>

<body>
    <div class="login-form">
        <form action="create.php" method="post">
            <h2 class="text-center">Register User</h2>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Username" name="username" required="required">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" name="password" required="required">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Verify Password" name="verifypassword" required="required">
            </div>
            <div class="form-group">
                <select name="questionid" id="questionid" type="questionid" name="questionid" class="form-control" required="required">
                    <?php $db = new SQLite3('users.db');
                        $list=$db->query("select * from questions order by questionid asc");
                        while($row_list=$list->fetchArray()){
                    ?>
                    <option value="<?php echo $row_list['questionid']; ?>">  
                        <?php echo $row_list['questiontext']; ?>  
                    </option>  
                    <?php  
                        }  
                    ?> 
                </select>
            </div>
            <div class="form-group">
                <input type="secretanswer" class="form-control" placeholder="Answer" name="secretanswer" required="required" autocomplete="off" >
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>
            <?php
                if($_GET['error'] == 1)
                    echo "<b>Error in creating account</b>";
                else if ($_GET['error'] == 2)
                    echo "<b>Passwords don't match</b>";
                else if ($_GET['error'] == 3)
                    echo "<b>Password needs to be at least 8 character long, and have at least one capital, one lowercase, one number</b>";
                else if ($_GET['error'] == 4)
                    echo "<b>Secret question answer must be at least 8 characters long</b>";
                else if ($_GET['error'] == 5)
                    echo "<b>Usernames must only contian alphanumeric characters</b>";
            ?>
            <div class="clearfix">
            </div>
        </form>
    </div>

</body>

</html>
