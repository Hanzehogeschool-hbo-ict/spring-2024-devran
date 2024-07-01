<?php

namespace Hive;

// restart the game
class RestartController extends Controller {
    public function handleGet(): void
    {
        if (!isset($this->session))
            return;

        // create new game
        $this->session->set('game', new Game());
        $this->session->set("error", '');

        if (!isset($this->db))
            return;

        // get new game id from database
        $this->db->execute('INSERT INTO games VALUES ()', []);
        $this->session->set('game_id', $this->db->getInsertId());

        // redirect back to index
        App::redirect();
    }
}
