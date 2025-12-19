Le labyrinthe est visible pour pouvoir le tester et vérifier qu'il fonctionne.
Pour que le camouflage s'applique il faut modifier le foreach ligne 65 de index.php et remplacer par :
foreach ($_SESSION['maze'] as $y => $row) {
    foreach ($row as $x => $cell) {
        // labyrinthe entièrement visible si on gagne
        if ($_SESSION['won']):
            $visible = true;
        // sinon seules les cases autour du pion le sont
        else: 
            $visible = ($x === $px && $y === $py) ||
                ($x === $px + 1 && $y === $py) ||
                ($x === $px - 1 && $y === $py) ||
                ($x === $px && $y === $py + 1) ||
                ($x === $px && $y === $py - 1);
        endif;

        le else est passé à $visible = true dans la version uploadée et les vrais conditions sont en commentaire. 
