<?php

namespace Hive;

class Controller
{

    public function __construct(protected ?Database $db, protected ?Session $session)
    {
    }

    protected function saveToDatabase(string $type, string $piece, string $to): void
    {
        $game = $this->session->get('game');
        $state = $this->db->escape($game);
        $last = $this->session->get('last_move') ?? 'null';
        $this->db->execute("
                insert into moves (game_id, type, move_from, move_to, previous_id, state)
                values (?, ?, ?, ?, ?, ?);
            ", [$this->session->get('game_id'), $type, $piece, $to, (int)$last, $state]);
    }
}