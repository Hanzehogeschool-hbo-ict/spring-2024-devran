<?php

namespace Hive;

// pass, which should only be allowed if there are no other valid moves
class PassController extends Controller {
    public function handlePost(string $from, string $to): void {
        if (!isset($this->session))
            return;

        // get state from session
        $game = $this->session->get('game');

        // TODO: pass is not implemented yet
        // switch players
        $game->player = 1 - $game->player;

        if (!isset($this->db))
            return;

        // store move in database
        $state = $this->db->escape($game);
        $last = $this->session->get('last_move') ?? 'null';
        $this->db->query("
                insert into moves (game_id, type, move_from, move_to, previous_id, state)
                values (?, ?, ?, ?, ?, ?);
            ", [$this->session->get('game_id'), "pass", null, null, $last, $state]);
        $this->session->set('last_move', $this->db->getInsertId());

        // redirect back to index
        App::redirect();
    }
}