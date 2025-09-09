<?php
namespace Controllers;
use Base;
use Models\UserModel;
use Services\AuthService;

class AuthController {
    private UserModel $users;
    public function __construct() {
        $this->users = new UserModel(Base::instance()->get('DB'));
    }

    public function register() {
        $in = json_decode(file_get_contents('php://input'), true) ?? [];
        $email = strtolower(trim($in['email'] ?? ''));
        $pass  = (string)($in['password'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
            http_response_code(422);
            echo json_encode(['error'=>'email/password invalid']); return;
        }
        if ($this->users->findByEmail($email)) {
            http_response_code(409);
            echo json_encode(['error'=>'email exists']); return;
        }
        $user  = $this->users->create($email, password_hash($pass, PASSWORD_DEFAULT));
        $token = AuthService::tokenFor((int)$user['id']);
        echo json_encode(['token'=>$token,'user'=>$user]);
    }

    public function login() {
        $in = json_decode(file_get_contents('php://input'), true) ?? [];
        $email = strtolower(trim($in['email'] ?? ''));
        $pass  = (string)($in['password'] ?? '');
        $row = (new UserModel(Base::instance()->get('DB')))->findByEmail($email);
        if (!$row || !password_verify($pass, $row['password_hash'])) {
            http_response_code(401);
            echo json_encode(['error'=>'invalid credentials']); return;
        }
        $user  = ['id'=>$row['id'],'email'=>$row['email'],'created_at'=>$row['created_at']];
        $token = AuthService::tokenFor((int)$row['id']);
        echo json_encode(['token'=>$token,'user'=>$user]);
    }
}
