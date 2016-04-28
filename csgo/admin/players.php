<html>
    <head>
        <?php include_once "navadmin.php"; ?>
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
        <title>Administrate players</title>
    </head>
    <body>
            <div class="wrap">
                <h1>Administrate players</h1>
                <ul>
                <li><a href="player/addplayer.php">Add player to database</a>
                <li><a href="player/playerteam.php">Assign a player to a team</a>
                <li><a href="player/selectplayer.php">Edit playerinfo</a>
            </div>
    </body>
</html>