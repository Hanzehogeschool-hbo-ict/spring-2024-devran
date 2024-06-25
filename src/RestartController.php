<?php

namespace Hive;

// restart the game
class RestartController extends Controller {
    public function handleGet() {
        if (!isset($this->session))
            return;

        // create new game
        $this->session->set('game', new Game());

        if (!isset($this->db))
            return;

        // get new game id from database
        $this->db->Execute('INSERT INTO games VALUES ()');
        $this->session->set('game_id', $this->db->Get_Insert_Id());

        // redirect back to index
        App::redirect();
    }
}
