<?php

namespace Hive;

class App {

    protected static ?self $instance = null;
    protected Database $db;
    protected Session $session;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (!isset(static::$instance))
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function handle(): void {
        // get current route
        $path = explode('/', $_SERVER['PATH_INFO'] ?? '');
        $route = $path[1] ?? 'index';

        // find corresponding controller
        $controller = match ($route) {
            'index' => new IndexController($this->db, $this->session),
            'move' => new MoveController($this->db, $this->session),
            'pass' => new PassController($this->db, $this->session),
            'play' => new PlayController($this->db, $this->session),
            'restart' => new RestartController($this->db, $this->session),
            'undo' => new UndoController($this->db, $this->session),
        };

        // dispatch get or post request
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') == 'GET') {
            $controller->handleGet(...$_GET);
        } else {
            $controller->handlePost(...$_POST);
        }
    }

    // redirect to given url
    public static function redirect(string $url = '/') {
        header("Location: $url");
    }

    public function getDatabase(): Database
    {
        return $this->db;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function setDatabase(Database $db): void
    {
        $this->db = $db;
    }

    public function setSession(Session $session): void
    {
        $this->session = $session;
    }
}
