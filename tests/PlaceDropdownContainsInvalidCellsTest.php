<?php

use Hive\IndexController;
use Hive\Util;
use PHPUnit\Framework\TestCase;
use Hive\Database;
use Hive\Game;
use Hive\Session;

class PlaceDropdownContainsInvalidCellsTest extends TestCase
{
    public function testPlaceDropdownContainsInvalidCells()
    {
        $session = Session::inst();
        $game = new Game();
        $game->board = [];
        $session->set('game', $game);
        $db = Database::inst();
        $indexController = new IndexController();

        $game->board["0,0"] = [["A", 0]];
        $game->board["0,1"] = [["Q", 1]];

        $to = $indexController->getAdjacentPositions($game->board);

        foreach ($to as $nb) {
            $this->assertArrayNotHasKey($nb, $game->board);
        }
    }
}