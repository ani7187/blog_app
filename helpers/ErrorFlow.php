<?php

namespace helpers;
class ErrorFlow {
    // Adds an error to the session
    public static function addError(string $key, string $error) {
        $_SESSION['errorFlow'][$key] = $error;
    }

    public static function fetch(string $key): mixed {
        $error = "";
        if (!empty($_SESSION['errorFlow']) && array_key_exists($key, $_SESSION['errorFlow'])) {
            $error = $_SESSION['errorFlow'][$key];
            unset($_SESSION['errorFlow'][$key]);
        }
        return  $error;
    }

    // Retrieves errors from the session
    public static function getErrors(): array {
        return $_SESSION['errorFlow'] ?? [];
    }

    // Clears errors after displaying them
    public static function clearErrors() {
        unset($_SESSION['errorFlow']);
    }
}

