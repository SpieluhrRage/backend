<?php
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch());
        } else {
            $stmt = $pdo->query("SELECT id, username FROM users");
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$data['username'], $data['password']]);

        echo json_encode(["status" => "created"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("UPDATE users SET username=?, password=? WHERE id=?");
        $stmt->execute([$data['username'], $data['password'], $data['id']]);

        echo json_encode(["status" => "updated"]);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "ID is required"]);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$_GET['id']]);

        echo json_encode(["status" => "deleted"]);
        break;
}
