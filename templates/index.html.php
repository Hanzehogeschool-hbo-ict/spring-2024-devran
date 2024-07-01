<!DOCTYPE html>
<html>
<head>
    <title>Hive</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="board">
    <?php

    use Hive\Game;

    // display tiles
    /** @var array $tilesToRender */
    foreach ($tilesToRender as $str) {
        echo($str);
    }
    ?>
</div>
<div class="hand">
    White:
    <?php
    // render tiles in white's hand
    /** @var Game $game */
    foreach ($game->hand[0] as $tile => $ct) {
        for ($i = 0; $i < $ct; $i++) {
            echo '<div class="tile player0"><span>'.$tile."</span></div>";
        }
    }
    ?>
</div>
<div class="hand">
    Black:
    <?php
    // render tiles in black's hand
    foreach ($game->hand[1] as $tile => $ct) {
        for ($i = 0; $i < $ct; $i++) {
            echo '<div class="tile player1"><span>'.$tile."</span></div>";
        }
    }
    ?>
</div>
<div class="turn">
    Turn: <?php
        // render active player
        if ($game->player == 0) echo "White"; else echo "Black";
    ?>
</div>
<form method="post" action="/play">
    <select name="piece">
        <?php
        // render list of tile types
        foreach ($game->hand[$game->player] as $tile => $ct) {
            if ($ct > 0)
                echo "<option value=\"$tile\">$tile</option>";
        }
        ?>
    </select>
    <select name="to">
        <?php
        // render list of possible moves
        /** @var array $to */
        foreach ($to as $pos) {
            echo "<option value=\"$pos\">$pos</option>";
        }
        ?>
    </select>
    <input type="submit" value="Play">
</form>
<form method="post" action="/move">
    <select name="from">
        <?php
        // render list of positions in board
        foreach ($game->board as $key => $val) {
            if ($val[0][0] == $game->player)
                echo "<option value=\"$key\">$key</option>";
        }
        ?>
    </select>
    <select name="to">
        <?php
        // render list of possible moves
        foreach ($to as $pos) {
            echo "<option value=\"$pos\">$pos</option>";
        }
        ?>
    </select>
    <input type="submit" value="Move">
</form>
<form method="post" action="/pass">
    <input type="submit" value="Pass">
</form>
<form method="get" action="/restart">
    <input type="submit" value="Restart">
</form>
<strong><?php
    // render error message
    if (isset($sessionError)) echo($sessionError); unset($sessionError);
?></strong>
<ol>
    <?php
    // render list of moves
    /** @var mysqli_result | null $moveHistory */
    while ($row = $moveHistory->fetch_array()) {
        echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
    }
    ?>
</ol>
<form method="post" action="/undo">
    <input type="submit" value="Undo">
</form>
</body>
</html>

