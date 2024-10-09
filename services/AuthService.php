<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService {
    private $authRepository;
    private $secretKey = 'your-secret-key'; // Replace this with a secure key

    public function __construct($authRepository) {
        $this->authRepository = $authRepository;
    }

    public function login($username, $password) {
        // Validate user credentials
        $user = $this->authRepository->getUserByUsernameAndPassword($username, $password);
        if ($user) {
            // Generate JWT token
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // jwt valid for 1 hour
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'user_id' => $user->id,
                'username' => $user->username
            ];

            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

            // Return the JWT token
            return json_encode(['token' => $jwt]);
        } else {
            return json_encode(['error' => 'Invalid username or password']);
        }
    }

    public function validateToken($token) {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (Exception $e) {
            return false;
        }
    }
}
