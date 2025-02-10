<?php

namespace src\Controllers;

use helpers\ErrorFlow;
use helpers\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use core\Controller;
use src\Models\User;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function loginForm(Request $request, Response $response, $args): Response
    {
        return $this->render($response, '/auth/login.php');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function registerForm(Request $request, Response $response, $args): Response
    {
        return $this->render($response, '/auth/register.php');
    }

    /**
     * Handles user registration
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function register(Request $request, Response $response, array $args): Response
    {
        $parsedBody = $request->getParsedBody();
        $isValid = $this->checkIsValid($parsedBody);
        if (!$isValid) {
            return $response->withHeader('Location', '/register')->withStatus(302);
        }

        try {
            $user = new User();
            $user->setUsername($parsedBody['username'])
                ->setPassword($parsedBody['password'])
                ->setEmail($parsedBody['email']);

            if ($user->create()) {
                ErrorFlow::addError("login_error", "User registered successfully.");
                return $response->withHeader('Location', '/login')->withStatus(302);
            }

            ErrorFlow::addError("register_error", "Registration failed.");
        } catch (\Exception $e) {
            Logger::error("Error on registration:" . $e->getMessage());
            ErrorFlow::addError("register_error", "An error occurred during registration.");
        }

        return $response->withHeader('Location', '/register')->withStatus(302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function login(Request $request, Response $response, array $args): Response
    {
        $parsedBody = $request->getParsedBody();
        $email = $parsedBody['email'] ?? '';
        $password = $parsedBody['password'] ?? '';

        if (empty($email) || empty($password)) {
            ErrorFlow::addError("login_error", "Email and password are required.");
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $user = (new User())->getByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;

            return $response->withHeader('Location', '/posts')->withStatus(302);
        }

        ErrorFlow::addError("login_error", "Invalid email or password.");
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    /**
     * Handles user logout
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function logout(Request $request, Response $response): Response
    {
        session_unset();
        session_destroy();

        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    /**
     * @param $userData
     * @return bool
     */
    private function checkIsValid($userData): bool
    {
        if (empty($userData['username']) || empty($userData['password']) || empty($userData['email'])
            || empty($userData['password_confirmation'])) {
            ErrorFlow::addError("register_error", "All fields are required.");
            return false;
        }

        //check exist user with given credentials
        $user = (new User())->getByEmailOrUsername($userData['email'], $userData['username']);
        if (!empty($user)) {
            if ($user['username'] === $userData['username']) {
                $log = "User with username already exists.";
            } else {
                $log = "User with email already exists.";
            }
            ErrorFlow::addError("register_error", $log);
            return false;
        }

        if ($userData['password'] != $userData['password_confirmation']) {
            ErrorFlow::addError("register_error", "Password dont match.");
            return false;
        }

        return true;
    }
}