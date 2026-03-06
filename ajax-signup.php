<?php
require_once 'includes/auth.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['username']) && isset($data['email']) && isset($data['password']) && isset($data['full_name'])) {
    $result = registerUser(
        $data['username'],
        $data['email'],
        $data['password'],
        $data['full_name']
    );
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
}
?>