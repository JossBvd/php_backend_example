<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header("Content-Type: application/json");

// Autoriser les requêtes depuis n'importe quelle origine (ou remplace * par ton domaine précis)
header("Access-Control-Allow-Origin: *");

// Autoriser certains en-têtes (important pour les requêtes POST/PUT/DELETE avec JSON)
header("Access-Control-Allow-Headers: Content-Type");

// Autoriser les méthodes (optionnel mais utile)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Pour répondre aux pré-requêtes (CORS preflight) OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = $_ENV['HOST'];
$dbname = $_ENV['DBNAME'];
$username = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents("php://input"), true);
    $id = $_GET['id'] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM movie WHERE id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($data ?: ["error" => "Film non trouvé"]);
            } else {
                $stmt = $pdo->query("SELECT * FROM movie");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            break;

        case 'POST':
            if (!isset($input['title'], $input['release_year'], $input['genre'], $input['duration'])) {
                http_response_code(400);
                echo json_encode(["error" => "Champs requis manquants"]);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO movie (title, release_year, genre, duration) VALUES (?, ?, ?, ?)");
            $stmt->execute([$input['title'], $input['release_year'], $input['genre'], $input['duration']]);
            echo json_encode(["success" => true, "id" => $pdo->lastInsertId()]);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "ID manquant"]);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE movie SET title = ?, release_year = ?, genre = ?, duration = ? WHERE id = ?");
            $stmt->execute([
                $input['title'],
                $input['release_year'],
                $input['genre'],
                $input['duration'],
                $id
            ]);
            echo json_encode(["success" => true]);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "ID manquant"]);
                exit;
            }
            $stmt = $pdo->prepare("DELETE FROM movie WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["success" => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur : " . $e->getMessage()]);
}
