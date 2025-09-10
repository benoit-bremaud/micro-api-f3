<?php
namespace Controllers;
use Base;
use Models\UserModel;
use Services\AuthService;
use Services\Http;
use Services\Json;

class AuthController {
  private UserModel $users;
  public function __construct() {
    $this->users = new UserModel(Base::instance()->get('DB'));
  }

  public function register() {
    $input = Json::readBody();
      if ($input === null) {
          Http::badRequest('Invalid JSON payload');
          return;
      }
    $email = strtolower(trim($input['email'] ?? ''));
    $pass = (string)($input['password'] ?? '');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
      Http::error(422, 'validation failed', 'Invalid email or weak password');
      return;
    }
    if ($this->users->findByEmail($email)) {
      Http::error(409, 'conflict', 'Email already registered');
      return;
    }
    $user = $this->users->create($email, password_hash($pass, PASSWORD_DEFAULT));
    $token = AuthService::tokenFor((int)$user['id']);
    Http::created(['token'=>$token, 'user'=>$user]);
  }

  public function login() {
    $input = Json::readBody();
    if ($input === null) {
        Http::badRequest('Invalid JSON payload');
        return;
    }
    $email = strtolower(trim($input['email'] ?? ''));
    $pass = (string)($input['password'] ?? '');
    $row = (new UserModel(Base::instance()->get('DB')))->findByEmail($email);

    if (!$row || !password_verify($pass, $row['password_hash'])) {
      Http::error(401, 'unauthorized', 'Invalid credentials');
      return;
    }
    $user = ['id'=>$row['id'],'email'=>$row['email'],'created_at'=>$row['created_at']];
    $token = AuthService::tokenFor((int)$row['id']);
    Http::ok(['token'=>$token,'user'=>$user]);
  }
}