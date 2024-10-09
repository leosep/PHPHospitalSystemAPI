<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use OpenApi\Annotations as OA;

class AuthController {
    private $authService;

    public function __construct($authService) {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="User login",
     *     description="Logs user into the system",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successful login"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login($request) {
        // Get the parsed body (assuming the request is in JSON format)
        $data = json_decode($request->getBody()->getContents(), true);
        
        // Validate that username and password are provided
        if (!isset($data['username']) || !isset($data['password'])) {
            return json_encode(['error' => 'Username and password are required']);
        }
        
        // Call the login method in AuthService with the username and password
        return $this->authService->login($data['username'], $data['password']);
    }
}
