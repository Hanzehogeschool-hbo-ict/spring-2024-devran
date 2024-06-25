<?php

namespace Hive;

// encapsulate session superglobal
class Session {
    public function __construct() {
        session_start();
    }

    // get session variable
    public function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    // set session variable
    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }
}
