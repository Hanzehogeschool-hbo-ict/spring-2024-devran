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
        } elseif (count($game->board) && !Util::hasNeighBour($to, $game->board)) {
            // every tile except the very first one of the game must be played adjacent to the hive
            $this->session->set('error', "board position has no neighbour");
        } elseif (array_sum($hand) < 11 && !Util::neighboursAreSameColor($game->player, $to, $game->board)) {
            // every tile after the first one a player plays may not be adjacent to enemy tiles
            $this->session->set("error", "Board position has opposing neighbour");
        } elseif (array_sum($hand) <= 8 && $hand['Q'] > 0 && $piece !== "Q") {
            // must play the queen bee in one of the first four turns
            $this->session->set('error', 'Must play queen bee');
        } else {
            // add the new tile to the board, remove it from its owners hand and switch players
            $game->board[$to] = [[$game->player, $piece]];
            $game->hand[$game->player][$piece]--;

            // Switch current player
            $game->player = 1 - $game->player;

            if (!isset($this->db))
                return;
            // Store move in database
            $this->saveToDatabase("play", $piece, $to);

            $this->session->set('last_move', $this->db->getInsertId());

            // Let AI play. Deze 2 lijnen uitcommenten om AI uit te doen
            $ai = new AIController($this->db, $this->session, new AIService());
            $ai->handlePost();
        }

        // redirect back to index
        App::redirect();
    }
}