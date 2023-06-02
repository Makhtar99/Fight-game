<!DOCTYPE html>
<html>
<head>
    <title>Jeu de combat</title>
</head>
<body>

<h1>Jeu de combat</h1>

<?php

// Classe de personnage
class Personnage {
    public $nom;
    public $points_de_vie;
    public $attaque_min;
    public $attaque_max;
    public $defense;
    public $endormi;

    public function __construct($nom, $points_de_vie, $attaque_min, $attaque_max, $defense) {
        $this->nom = $nom;
        $this->points_de_vie = $points_de_vie;
        $this->attaque_min = $attaque_min;
        $this->attaque_max = $attaque_max;
        $this->defense = $defense;
        $this->endormi = false;
    }

    public function attaquer($cible) {
        if ($this->endormi) {
            echo "<p>{$this->nom} est endormi et ne peut pas attaquer.</p>";
            return;
        }

        $degats = rand($this->attaque_min, $this->attaque_max) - $cible->defense;
        if ($degats < 0) {
            $degats = 0;
        }
        $cible->points_de_vie -= $degats;

        echo "<p>{$this->nom} attaque {$cible->nom} et lui inflige {$degats} points de dégâts.</p>";

        if ($cible->points_de_vie <= 0) {
            echo "<p>{$cible->nom} a été vaincu !</p>";
        }
    }

    public function endormir($cible) {
        if ($this->endormi) {
            echo "<p>{$this->nom} est déjà endormi et ne peut pas utiliser la compétence d'endormissement.</p>";
            return;
        }

        $this->endormi = true;
        echo "<p>{$this->nom} endort {$cible->nom} pendant 15 secondes.</p>";
        sleep(15);
        $this->endormi = false;
        echo "<p>{$cible->nom} se réveille.</p>";
    }
}

// Classe de l'IA
class AI {
    public function jouerTour($personnage, $adversaire) {
        $action = rand(1, 2); // Choix aléatoire d'une action : 1 pour attaquer, 2 pour endormir

        if ($action === 1) {
            $personnage->attaquer($adversaire);
        } elseif ($action === 2) {
            $personnage->endormir($adversaire);
        }
    }
}

// Vérification du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $type = $_POST["type"];

    if (empty($nom) || empty($type)) {
        echo "<p>Veuillez saisir un nom et sélectionner un type de personnage.</p>";
    } else {
        if ($type === "guerrier") {
            $personnage = new Personnage($nom, 100, 20, 40, rand(10, 19));
        } elseif ($type === "magicien") {
            $personnage = new Personnage($nom, 100, 5, 10, 0);
        } else {
            echo "<p>Type de personnage invalide.</p>";
        }

        $adversaire = new Personnage("AI", 100, rand(20, 40), rand(20, 40), rand(10, 19));
        
        // Gestion des actions
        if ($_POST["action"] === "Attaquer") {
            $personnage->attaquer($adversaire);
        } elseif ($_POST["action"] === "Endormir") {
            $personnage->endormir($adversaire);
        }

        // Tour de l'adversaire
        if ($adversaire->points_de_vie > 0) {
            $ai = new AI();
            $ai->jouerTour($adversaire, $personnage);
        }
    }
}

?>

<?php if (isset($personnage) && isset($adversaire) && $personnage->points_de_vie > 0 && $adversaire->points_de_vie > 0) : ?>
    <h2><?php echo $personnage->nom; ?></h2>
    <p>Points de vie : <?php echo $personnage->points_de_vie; ?></p>
    <p>Attaque : <?php echo $personnage->attaque_min; ?> - <?php echo $personnage->attaque_max; ?></p>
    <p>Défense : <?php echo $personnage->defense; ?></p>
    <br>

    <h2><?php echo $adversaire->nom; ?></h2>
    <p>Points de vie : <?php echo $adversaire->points_de_vie; ?></p>
    <p>Attaque : <?php echo $adversaire->attaque_min; ?> - <?php echo $adversaire->attaque_max; ?></p>
    <p>Défense : <?php echo $adversaire->defense; ?></p>
    <br>

    <?php if (!$personnage->endormi) : ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="nom" value="<?php echo $personnage->nom; ?>">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input type="submit" name="action" value="Attaquer">
        </form>
    <?php endif; ?>
    <?php if ($type === "magicien" && !$personnage->endormi) : ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="nom" value="<?php echo $personnage->nom; ?>">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input type="submit" name="action" value="Endormir">
        </form>
    <?php endif; ?>
<?php elseif (isset($personnage) && isset($adversaire) && $personnage->points_de_vie <= 0) : ?>
    <p><?php echo $personnage->nom; ?> a été vaincu !</p>
<?php elseif (isset($personnage) && isset($adversaire) && $adversaire->points_de_vie <= 0) : ?>
    <p><?php echo $adversaire->nom; ?> a été vaincu !</p>
<?php else : ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required>
        <br>
        <label for="type">Type :</label>
        <select name="type" id="type" required>
            <option value="guerrier">Guerrier</option>
            <option value="magicien">Magicien</option>
        </select>
        <br>
        <input type="submit" value="Commencer le combat">
    </form>
<?php endif; ?>

</body>
</html>




