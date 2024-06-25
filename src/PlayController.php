<?php

namespace Hive;

// play a new tile
class PlayController extends Controller
{
    public function handlePost(string $piece, string $to): void
    {
        if (!isset($this->session))
            return;

        // get state from session
        $game = $this->session->get('game');
        $hand = $game->hand[$game->player];

        if (!$hand[$piece]) {
            // must still have tile in hand to be able to play it
            $this->session->set('error', "Player does not have tile");
        } elseif (isset($game->board[$to])) {
            // can only play on empty positions (even beetles)
            $this->session->set('error', 'Board position is not empty');
        } elseif (count($game->board) && !Util::has_NeighBour($to, $game->board)) {
            // every tile except the very first one of the game must be played adjacent to the hive
            $this->session->set('error', "board position has no neighbour");
        } elseif (array_sum($hand) < 11 && !Util::neighboursAreSameColor($game->player, $to, $game->board)) {
            // every tile after the first one a player plays may not be adjacent to enemy tiles
            $this->session->set("error", "Board position has opposing neighbour");
        } elseif (array_sum($hand) <= 8 && $hand['Q']) {
            // must play the queen bee in one of the first four turns
            $this->session->set('error', 'Must play queen bee');
        } else {
            // add the new tile to the board, remove it from its owners hand and switch players
            $game->board[$to] = [[$game->player, $piece]];
            $game->hand[$game->player][$piece]--;

            // remove stone type from hand when all stones of this type have been played
            if ($game->hand[$game->player][$piece] === 0)
                unset($game->hand[$game->player][$piece]);

            $game->player = 1 - $game->player;

            if (!isset($this->db))
                return;

            // store move in database
            $state = $this->db->Escape($game);
            $last = $this->session->get('last_move') ?? 'null';
            $this->db->Execute("
                insert into moves (game_id, type, move_from, move_to, previous_id, state)
                values ({$this->session->get('game_id')}, \"play\", \"$piece\", \"$to\", $last, \"$state\")
            ");
            $this->session->set('last_move', $this->db->Get_Insert_Id());
        }

        // redirect back to index
        App::redirect();
    }
}