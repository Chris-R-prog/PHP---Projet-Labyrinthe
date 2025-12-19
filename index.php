<?php require 'partials/_game.php'; ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="./css/main.css">
        <title>PHP : Labyrinthe of the Dead</title>
    </head>

    <body>
    <!--Section bouttons de navigation-->
        <section class="header">
            <div class="header__nav-button">
    <?php
        if (!$_SESSION['won']): ?>
            <form class="nav-button" action="#" method="POST">
                <button class="btn btn--gallery" id="up" type="submit" name="move" value="up"><img class="arrow" src="./assets/img/arrows_square_up.svg" alt="arrow-up"></button>

                <button class="btn btn--gallery" id="down" type="submit" name="move" value="down"><img class="arrow" src="./assets/img/arrows_square_down.svg" alt="arrow-down"></button>

                <button class="btn btn--gallery" id="left" type="submit" name="move" value="left"><img class="arrow" src="./assets/img/arrows_square_left.svg" alt="arrow-left"></button>

                <button class="btn btn--gallery" id="right" type="submit" name="move" value="right"><img class="arrow" src="./assets/img/arrows_square_right.svg" alt="arrow-right"></button>
            </form>
            <div class="u-margin-bottom-medium"></div>
            <div class="instructions">
                <p>Fuyez le piège que vous a tendu l'empire en vous déplaçant à l'aide des flèches ci-dessus.</p>
                <div class="u-margin-bottom-small"></div>
                <ul>
                    <li>LASER : vous permet d'éliminer le stormtrooper le plus proche de votre objectif</li>
                </ul>
            </div>
        <?php else: 

        ?>
            <p class="win">Vous avez gagné ! Cliquez sur Reset pour rejouer.</p>
        <?php endif; ?>
            </div>
        <form class="header__reset" action="#" method="POST">
            <button class="btn btn--gallery" type="submit"name="reset">Reset</button>
        </form>
        </section>

        <main>
        <!--Section de la grille du jeu-->
        <section class="game-board">
                <h1 class="heading-primary">
                    <span class="heading-primary--main-name">Labyrinthe of the dead</span>
                    <div class="u-margin-bottom-medium"></div>
                </h1>
<?php

// génération de la grille du jeu en fonction du nombre de colonnes du tirage aléatoire
$cols = count($_SESSION['maze'][0]);
echo '<div class="maze" style="grid-template-columns: repeat(' . $cols . ', 80px);">';

$px = $_SESSION['player']['x'];
$py = $_SESSION['player']['y'];

foreach ($_SESSION['maze'] as $y => $row) {
    foreach ($row as $x => $cell) {
        // labyrinthe entièrement visible si on gagne
        if ($_SESSION['won']):
            $visible = true;
        // sinon seules les cases autour du pion le sont
        else: 
            $visible = true;
                /*($x === $px && $y === $py) ||
                ($x === $px + 1 && $y === $py) ||
                ($x === $px - 1 && $y === $py) ||
                ($x === $px && $y === $py + 1) ||
                ($x === $px && $y === $py - 1);*/
        endif;

// génération du rendu en fonction de l'état visible et des informations stochées pour le labyrinthe en cession.
if(!$visible) {
        echo '<div class="cell cloud"></div>';
        continue;
}elseif ($x === $px && $y === $py) {
        if ($_SESSION['item1_hold']){
            echo '<div class="cell player itemP1">LASER</div>';
        }else{
            echo '<div class="cell player"></div>';
        }

} elseif ($x === $_SESSION['bounty']['x'] && $y === $_SESSION['bounty']['y']) {
            echo '<div class="cell bounty"></div>';

} elseif (isset($_SESSION['item1']) && !$_SESSION['item1_hold'] && $x === $_SESSION['item1']['x'] &&
    $y === $_SESSION['item1']['y']){
        echo '<div class="cell item1">LASER</div>';      

} elseif (isset($_SESSION['item1']) && $x === $_SESSION['player']['x'] &&
    $y === $_SESSION['player']['y']){
        echo '<div class="cell player itemP1 item1">LASER</div>';     

} elseif ($cell === '#') {
    echo '<div class="cell wall"></div>';

} else {
    echo '<div class="cell floor"></div>';
        }

    }
}

    echo '</div>';

?>
<p class="thanks">Remerciement pour les illustration : <a href="https://www.uidownload.com/en/clipart-huggb">Chibi Princess Leia</a> - <a href="https://wallpapers-clan.com/sticker-png/star-wars-chibi-stormtrooper/">Chibi Stormtrooper</a></p>
</section>


