<?php

namespace Hive;

use mysqli_result;

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

        $moveHistory = $this->getMoveHistory();
        $to = $this->getAdjacentPositions($game->board);
        $tilesToRender = $this->getTilesToRender($game, $to);

        $sessionError = "";
        if (isset($_SESSION['error'])) {
            $sessionError = $_SESSION['error'];
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
                $to[] = ($qr[0] + $qr2[0]).','.($qr[1] + $qr2[1]);
                // $qr en $qr2 zijn strings maar worden opgeteld als nummers.
                //Dit maakt niet uit want php veranderd het intern voor je.
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

    protected function getMoveHistory(): mysqli_result | null
    {
        return $this->db->query("SELECT * FROM moves WHERE game_id = ?", [$this->session->get('game_id')]);
    }

    protected function getTilesToRender(Game $game, array $to): array
    {
        $width = 35;
        $height = 30;

        // find minimum values for q and r to render board
        $minQ = 1000;
        $minR = 1000;

        foreach ($game->board as $pos => $tile) {
            $qr = explode(',', $pos);
            if ($qr[0] < $minQ) $minQ = $qr[0];
            if ($qr[1] < $minR) $minR = $qr[1];
        }

        // reduce minimum values for q and r to make room for empty spaces adjacent to tiles
        $minQ--;
        $minR--;

        // store rendered tiles so they can later be rendered in the proper order
        $renderedTiles = [];

        // render tiles in play
        foreach (array_filter($game->board) as $pos => $tile) {
            $qr = explode(',', $pos);
            $h = count($tile);
            $str = '<div class="tile player';
            $str .= $tile[$h-1][0];
            if ($h > 1) $str .= ' stacked';
            $str .= '" style="left: ';
            $str .= $width * (($qr[0] - $minQ) + ($qr[1] - $minR) / 2);
            $str .= 'px; top: ';
            $str .= $height * ($qr[1] - $minR);
            $str .= "px;\">$qr[0],$qr[1]<span>";
            $str .= $tile[$h-1][1];
            $str .= '</span></div>';
            $renderedTiles[$pos] = $str;
        }

        // render empty tiles adjacent to existing tiles
        foreach ($to as $pos) {
            if (!array_key_exists($pos, $game->board)) {
                $qr = explode(',', $pos);
                $str = '<div class="tile empty" style="left: ';
                $str .= $width * (($qr[0] - $minQ) + ($qr[1] - $minR) / 2);
                $str .= 'px; top: ';
                $str .= $height * ($qr[1] - $minR);
                $str .= "px;\">$qr[0],$qr[1]<span>";
                $str .= "&nbsp;";
                $str .= '</span></div>';
                $renderedTiles[$pos] = $str;
            }
        }

        // sort in display order
        uksort($renderedTiles, function($a, $b) {
            // split coordinates
            $a = explode(',', $a);
            $b = explode(',', $b);

            // compare second (vertical) coordinate first
            return $a[1] == $b[1] ? $a[0] <=> $b[0] : $a[1] <=> $b[1];
        });

        return $renderedTiles;
    }
}