<?php
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM weather WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch());
        } else {
            $stmt = $pdo->query("SELECT * FROM weather");
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("INSERT INTO weather (city, temperature, condition_text, last_updated) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$data['city'], $data['temperature'], $data['condition_text']]);

        echo json_encode(["status" => "created"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("UPDATE weather SET city=?, temperature=?, condition_text=? WHERE id=?");
        $stmt->execute([$data['city'], $data['temperature'], $data['condition_text'], $data['id']]);

        echo json_encode(["status" => "updated"]);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "ID is required"]);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM weather WHERE id = ?");
        $stmt->execute([$_GET['id']]);

        echo json_encode(["status" => "deleted"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method Not Allowed"]);
}
