<?php

use Hive\App;
use Hive\IndexController;
use Hive\Session;
use PHPUnit\Framework\TestCase;
use Hive\Game;

class PlaceDropdownContainsInvalidCellsTest extends TestCase
{
    public function testPlaceDropdownContainsInvalidCells()
    {
        $app = App::getInstance();
        $app->setSession(new Session());
        $session = $app->getSession();
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