<?php
session_start();

// bouton reset
if (isset($_POST['reset'])) {
    session_destroy(); 
    header("Location: index.php"); 
    exit;
}

// initialisation du paramètre de la session win à false
if (!isset($_SESSION['won'])) {
    $_SESSION['won'] = false;
}

//Vérification qu'un chemin existe entre le joueur et le trésor en visitant toutes les cases accessibles depuis le pion du joueur. On stocke chaque case possible dans un tableau, on vérifie si cela correspond au trésor si oui le chemin existe. On stocke le booléen true (visited) dans le tableau de suivi aux index des coordonnées de la case testée. Si toutes les cases possibles ne mènent pas au trésor on retourne false.
function isPath($maze, $player, $bounty) {
    $queue = [[$player['x'], $player['y']]];
    $visited = [];
    $visited[$player['y']][$player['x']] = true; // on stocke la valeur booléenne pour ces coordonnées ici la case du joueur 

    $dirs = [[1,0], [-1,0], [0,1], [0,-1]];

    while (!empty($queue)) {
        [$x, $y] = array_shift($queue);

        if ($x === $bounty['x'] && $y === $bounty['y']) {
            return true;
        }

        foreach ($dirs as [$dirX,$dirY]) {
            $testedX = $x + $dirX;
            $testedY = $y + $dirY;

            if (
                isset($maze[$testedY][$testedX]) &&
                $maze[$testedY][$testedX] === ' ' &&
                !isset($visited[$testedY][$testedX])
            ) {
                $visited[$testedY][$testedX] = true; // on marque la case comme visitée 
                $queue[] = [$testedX,$testedY]; // on stocke ses index dans le tableau des cases à tester
            }
        }
    }
    return false; // si le trésor n'est jamais atteint dans les cases visitables on retourne faux
}

// Vérification de la distance (distance de Manhattan) : vérifier que le trésor n'est pas trop près, que le joueur a gagné etc.
function ManhatanDistance($pPlayerX, $pPlayerY, $pBountyX, $pBountyY){
            return abs($pBountyX - $pPlayerX) + abs($pBountyY - $pPlayerY);
}

//Génération de placement aléatoires pour les éléments du labyrinthe


// Génération d'un labyrinthe viable avec un chemin allant du joueur au trésor. Boucle do while tant qu'un chemin vers le trésor n'existe pas. 

if (!isset($_SESSION['maze'])) {

    unset($_SESSION['maze'], $_SESSION['player'], $_SESSION['bounty'], $_SESSION['item1']);

    do {

        $maze_rows = random_int(5, 9);
        $maze_columns = random_int(5,9);

        $_SESSION['maze'] = [];

        // génération aléatoire du labyrinthe avec 30% de "murs" 
        for($y = 0; $y < $maze_rows ; $y ++){
            for($x = 0; $x < $maze_columns ; $x++){
                $isWall = random_int(0,100);
                if ($isWall < 30) {
                    $_SESSION['maze'][$y][$x] = "#";
                }else{
                    $_SESSION['maze'][$y][$x] = " ";
                }
            }
        }

        // génération de la position du joueur jusqu'à tomber sur une case libre
        do {
        $randY_player = random_int(0, $maze_rows-1);
        $randX_player = random_int(0, $maze_columns-1);
        } while 
        ($_SESSION['maze'][$randY_player][$randX_player] === "#");

        $_SESSION['player'] = [
            'x' => $randX_player,
            'y' => $randY_player,
        ];

        // génération de la position du trésor à une distance minimale du pion du joueur et dans une case libre (distance = 5) 

        do {
            $randY_bounty = random_int(0, $maze_rows-1);
            $randX_bounty = random_int(0, $maze_columns-1);

        }while (ManhatanDistance($_SESSION['player']['x'], $_SESSION['player']['y'],$randX_bounty, $randY_bounty) <= 5 || $_SESSION['maze'][$randY_bounty][$randX_bounty] === '#');

        $_SESSION['bounty'] = ['x' => $randX_bounty, 'y' => $randY_bounty];

        do {
            $randY_item = random_int(0, $maze_rows-1);
            $randX_item = random_int(0, $maze_columns-1);
        } while ($_SESSION['maze'][$randY_item][$randX_item] === "#" || ($_SESSION['player']['y'] === $randY_item && $_SESSION['player']['x'] === $randX_item) || ($_SESSION['bounty']['y'] === $randY_item && $_SESSION['bounty']['x'] === $randX_item)
        );

        $_SESSION['item1'] = [
        'x' => $randX_item,
        'y' => $randY_item,
        ];

    } while (!isPath($_SESSION['maze'], $_SESSION['player'], $_SESSION['bounty']));
}

//Génération de la possession de l'item1 à false
if(!isset($_SESSION['item1_hold'])){
    $_SESSION['item1_hold'] = false;
}

if (isset($_POST['move'])) {
    // Récupération des valeurs des bouttons de navigation du jeu et affectation des directions sur les coordonnées avec un switch
    $x = $_SESSION['player']['x'];
    $y = $_SESSION['player']['y'];

    $dx = 0;
    $dy = 0;

    switch ($_POST['move']) {
        case 'up':    $dy = -1; break;
        case 'down':  $dy = 1; break;
        case 'left':  $dx = -1; break;
        case 'right': $dx = 1; break;
    }

    $newX = $x + $dx;
    $newY = $y + $dy;

    // vérification si on dépasse la limite du labyrinthe ou si on tombe sur un mur avec les nouvelles coordonnées sinon on affecte les nouvelles coordonnées au pion du joueur.
    if (
        $newX >= 0 &&
        $newY >= 0 &&
        $newY < count($_SESSION['maze']) &&
        $newX < count($_SESSION['maze'][0]) &&
        $_SESSION['maze'][$newY][$newX] !== '#'
    ) {
        $_SESSION['player']['x'] = $newX;
        $_SESSION['player']['y'] = $newY;
    }

    if(!$_SESSION['won']){

        //Déplacement du trésor le plus loin possible du joueur, identification des cases possibles, calcul distance de manhattan, récupération de la valeur la plus élevée, déplacement

            $xBounty = $_SESSION['bounty']['x'];
            $yBounty = $_SESSION['bounty']['y'];

            $dirs = [[1,0], [-1,0], [0,1], [0,-1]];

            $bestDistance = ManhatanDistance($_SESSION['player']['x'], $_SESSION['player']['y'], $_SESSION['bounty']['x'], $_SESSION['bounty']['y']);

            foreach ($dirs as [$dirX,$dirY]) {
                $newBountyX = $xBounty + $dirX;
                $newBountyY = $yBounty + $dirY;

                if (
                    $newBountyX < 0 ||
                    $newBountyY < 0 ||
                    $newBountyY >= count($_SESSION['maze']) ||
                    $newBountyX >= count($_SESSION['maze'][0])
                    ){
                    continue;
                }
                
                if ($_SESSION['maze'][$newBountyY][$newBountyX] === "#"){
                    continue;
                }

                $distance = ManhatanDistance($_SESSION['player']['x'], $_SESSION['player']['y'], $newBountyX, $newBountyY);

                if($distance >= $bestDistance){
                        $bestDistance = $distance;
                        $_SESSION['bounty']['x'] = $newBountyX;
                        $_SESSION['bounty']['y'] = $newBountyY;
                }
            }

            // Récupération de l'item 1 et destruction du mur rencontré ensuite
            //vérifier si la position du joueur est identique à celle de l'item et changer la valeur d'item1_hold.
            if(isset($_SESSION['item1']) && $_SESSION['player']['x'] === $_SESSION['item1']['x'] && $_SESSION['player']['y'] === $_SESSION['item1']['y']){

            $_SESSION['item1_hold'] = true;
            unset($_SESSION['item1']);
            }

            // utilisation de l'item destruction du mur qui entoure le joueur le plus proche du trésor.
            if($_SESSION['item1_hold'] === true){
                $xPlayer = $_SESSION['player']['x'];
                $yPlayer = $_SESSION['player']['y'];

                $dirs = [[1,0], [-1,0], [0,1], [0,-1]];

                // initialisation de la meilleure distance à une valeur élevée
                $bestDistance = 10000;
                $bestWall = null;

                // test de chaque case, si c'est un mur et que la distance est inférieure à la référence elle devient le "best wall"
                foreach ($dirs as [$dirX,$dirY]) {
                        $targetWallX = $xPlayer + $dirX;
                        $targetWallY = $yPlayer + $dirY;

                    if (!isset($_SESSION['maze'][$targetWallY][$targetWallX])) {
                    continue;
                    }

                    if($_SESSION['maze'][$targetWallY][$targetWallX] !== "#"){
                        continue;
                    }

                    $distance = ManhatanDistance($targetWallX, $targetWallY, $_SESSION['bounty']['x'], $_SESSION['bounty']['y']);

                    if($distance < $bestDistance){
                                $bestDistance = $distance;
                                $bestWall = [$targetWallY, $targetWallX];
                        }
                }

            //Une fois le meilleur mur "trouvé" s'il existe on modifie le labyrinthe pour en faire une case libre et on réinitialise l'item à false 
                if ($bestWall !== null) {
                    $_SESSION['maze'][$bestWall[0]][$bestWall[1]] = ' ';
                    $_SESSION['item1_hold'] = false;
                } 
            }
    }

        // victoire distance = 1
        if (ManhatanDistance($_SESSION['player']['x'],$_SESSION['player']['y'], $_SESSION['bounty']['x'], $_SESSION['bounty']['y']) === 1)  {
            $_SESSION['won'] = true;
    }
}
?>
