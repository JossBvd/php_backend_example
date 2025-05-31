<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Identifiants en dur
$validUsername = 'admin';
$validPassword = 'password123';

// Traitement du login
if (isset($_POST['login'])) {
    if ($_POST['username'] === $validUsername && $_POST['password'] === $validPassword) {
        $_SESSION['logged_in'] = true;
    } else {
        $loginError = "Identifiants incorrects.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?')); // Nettoie l'URL
    exit;
}

// Si pas connecté, afficher le formulaire de login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Connexion</title>
    </head>

    <body>
        <h1>Connexion</h1>
        <?php if (!empty($loginError)) echo "<p style='color:red;'>$loginError</p>"; ?>
        <form method="POST">
            <label>Nom d'utilisateur : <input type="text" name="username" required></label><br>
            <label>Mot de passe : <input type="password" name="password" required></label><br>
            <button type="submit" name="login">Se connecter</button>
        </form>
    </body>

    </html>
<?php
    exit;
endif;

// --- CRUD LOGIQUE ICI (accessible uniquement si connecté) ---
$host = $_ENV['HOST'];
$dbname = $_ENV['DBNAME'];
$username = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // CREATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
        $stmt = $pdo->prepare("INSERT INTO movie (title, release_year, genre, duration) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['release_year'], $_POST['genre'], $_POST['duration']]);
    }

    // UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $stmt = $pdo->prepare("UPDATE movie SET title = ?, release_year = ?, genre = ?, duration = ? WHERE id = ?");
        $stmt->execute([$_POST['title'], $_POST['release_year'], $_POST['genre'], $_POST['duration'], $_POST['id']]);
    }

    // DELETE
    if (isset($_GET['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM movie WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
    }

    // GET MOVIE FOR EDITING
    $editMovie = null;
    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare("SELECT * FROM movie WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $editMovie = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ
    $stmt = $pdo->query("SELECT * FROM movie ORDER BY id DESC");
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>CRUD Films</title>
</head>

<body>
    <h1>Liste des films</h1>
    <p><a href="?logout=1">Déconnexion</a></p>
    <ul>
        <?php foreach ($films as $film): ?>
            <li>
                <strong><?= htmlspecialchars($film['title']) ?></strong>
                (<?= $film['release_year'] ?>) – <?= $film['genre'] ?> – <?= $film['duration'] ?> min
                [<a href="?edit=<?= $film['id'] ?>">Modifier</a>]
                [<a href="?delete=<?= $film['id'] ?>" onclick="return confirm('Supprimer ce film ?')">Supprimer</a>]
            </li>
        <?php endforeach; ?>
    </ul>

    <h2><?= $editMovie ? 'Modifier' : 'Ajouter' ?> un film</h2>
    <form method="POST">
        <input type="hidden" name="<?= $editMovie ? 'update' : 'create' ?>" value="1">
        <?php if ($editMovie): ?>
            <input type="hidden" name="id" value="<?= $editMovie['id'] ?>">
        <?php endif; ?>
        <label>Titre : <input type="text" name="title" value="<?= $editMovie['title'] ?? '' ?>" required></label><br>
        <label>Année de sortie : <input type="number" name="release_year" value="<?= $editMovie['release_year'] ?? '' ?>" required></label><br>
        <label>Genre : <input type="text" name="genre" value="<?= $editMovie['genre'] ?? '' ?>" required></label><br>
        <label>Durée (min) : <input type="number" name="duration" value="<?= $editMovie['duration'] ?? '' ?>" required></label><br>
        <button type="submit"><?= $editMovie ? 'Mettre à jour' : 'Ajouter' ?></button>
    </form>
</body>

</html>