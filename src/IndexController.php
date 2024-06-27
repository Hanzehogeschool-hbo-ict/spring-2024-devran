<?php

namespace Hive;

/**
 * Handle index page.
 */
class IndexController extends Controller
{
    public function handleGet(): void {
        if (!isset($this->session))
            return;

        // ensure session contains a game
        $game = $this->session->get('game');
        if (!$game) {
            App::redirect('/restart');
            return;
        }

        $to = $this->getAdjacentPositions($game->board);

        $sessionError = "";
        if (isset($_SESSION['error'])) {
            $sessionError = $_SESSION['error'];
           echo gettype($_SESSION['error']);
        }

        // render view
        require_once TEMPLATE_DIR.'/index.html.php';
    }

    // find all positions that are adjacent to one of the tiles in the hive as candidates for a new tile
    public function getAdjacentPositions($board)
    {
        $to = [];
        foreach (Util::OFFSETS as $qr) {
            foreach (array_keys($board) as $pos) {
                $qr2 = explode(',', $pos);
                $to[] = ($qr[0] + $qr2[0]).','.($qr[1] + $qr2[1]); // $qr en $qr2 zijn strings maar worden opgeteld als nummers. Dit maakt niet uit want php veranderd het intern voor je.
            }
        }
        $to = array_unique($to);
        if (!count($to)) $to[] = '0,0';

        foreach (array_keys($board) as $pos) {
            if (($key = array_search($pos, $to)) !== false) {
                unset($to[$key]);
            }
        }

        return $to;
    }
}