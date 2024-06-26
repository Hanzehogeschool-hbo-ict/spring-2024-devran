<?php

namespace Hive;

// undo last move
class UndoController extends Controller
{
    public function handlePost(): void
    {
        if (!isset($this->session) || !isset($this->db))
            return;

        // restore last move from database
        $last_move = $this->session->get('last_move') ?? 0;
        $result = $this->db->query("SELECT previous_id, state FROM moves WHERE id = ?;", [$last_move])->fetch_array();
        $this->session->set('last_move', $result[0]);
        $this->session->set('game', Game::fromString($result[1]));

        // redirect back to index
        App::redirect();
    }
}
