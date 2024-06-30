<?php

namespace Hive;

// move an existing tile
class MoveController extends Controller
{
    public function handlePost(string $from, string $to): void
    {
        if (!isset($this->session))
            return;

        // get state from session
        $game = $this->session->get('game');
        $hand = $game->hand[$game->player];

        if (!isset($game->board[$from])) {
            // cannot move tile from empty position
            $this->session->set('error', 'Board position is empty');
        } else if ($game->board[$from][count($game->board[$from])-1][0] != $game->player)
            // can only move top of stack and only if owned by current player
            $this->session->set("error", "Tile is not owned by player");
        else if ($hand["Q"] > 0)
            // cannot move unless queen bee has previously been played
            $this->session->set('error', "Queen bee is not played");
        else if ($from === $to) {
            // a tile cannot return to its original position
            $this->session->set('error', 'Tile must move to a different position');
        } else {
            // temporarily remove tile from board
            $tile = array_pop($game->board[$from]);
            if (!Util::hasNeighBour($to, $game->board)) {
                // target position is not connected to hive so move is invalid
                $this->session->set("error", "Move would split hive");
            } else if (Util::hasMultipleHives($game->board)) {
                // the move would split the hive in two so it is invalid
                $this->session->set("error", "Move would split hive");
            } else if (isset($game->board[$to]) && $tile[1] != "B") {
                // only beetles are allowed to stack on top of other tiles
                $this->session->set("error", 'Tile not empty');
            } else if ($tile[1] == "Q" || $tile[1] == "B") {
                // queen bees and beetles must move a single hex using the sliding rules
                if (!Util::slide($game->board, $from, $to))
                    $this->session->set("error", 'Tile must slide');
            }

            // TODO: rules for other tiles aren't implemented yet

            if ($this->session->get('error')) {
                // illegal move so reset tile that was temporarily removed
                if (isset($game->board[$from])) $game->board[$from][] = $tile;
                else $game->board[$from] = [$tile];
            } else {
                // move tile to new position and switch players
                if (isset($game->board[$to])) $game->board[$to][] = $tile;
                else $game->board[$to] = [$tile];

                // Switch current player
                $game->player = 1 - $game->player;

                if (!isset($this->db))
                    return;
                // Store move in database
                $this->saveToDatabase("move", $from, $to);

                $this->session->set('last_move', $this->db->getInsertId());

                // Let AI play
                $ai = new AIController($this->db, $this->session);
                $ai->handlePost($game);
            }
        }

        // redirect back to index
        App::redirect();
    }
}
