<?php

use Hive\App;
use Hive\IndexController;
use PHPUnit\Framework\TestCase;
use Hive\Game;

class PlaceDropdownContainsInvalidCellsTest extends TestCase
{
    public function testPlaceDropdownContainsInvalidCells()
    {
        $session = App::getInstance()->getSession();
        $game = new Game();
        $game->board = [];
        $session->set('game', $game);
        $indexController = new IndexController(null, $session);

        $game->board["0,0"] = [["A", 0]];
        $game->board["0,1"] = [["Q", 1]];

        $to = $indexController->getAdjacentPositions($game->board);

        foreach ($to as $nb) {
            $this->assertArrayNotHasKey($nb, $game->board);
        }
    }
}