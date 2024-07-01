<?php

use Hive\App;
use Hive\Game;
use Hive\MoveController;
use Hive\PlayController;
use Hive\Session;
use PHPUnit\Framework\TestCase;

class MovedStoneCellBecomesEmptyTest extends TestCase
{
    public function testMovedStoneCellBecomesEmpty()
    {
        $app = App::getInstance();
        $session = new Session();
        $app->setSession($session);
        $game = new Game();
        $game->board = [];
        $session->set('game', $game);
        $playController = new PlayController(null, $session);
        $moveController = new MoveController(null, $session);

        $playController->handlePost("Q", "0,0"); // W
        $playController->handlePost("Q", "0,-1"); // B
        $playController->handlePost("A", "0,1"); // W
        $playController->handlePost("B", "0,-2"); // B
        $moveController->handlePost("0,1", "1,0"); // W
        $playController->handlePost("S", "0,-3"); // B
        $playController->handlePost("S", "0,1"); // W
        $playController->handlePost("A", "0,-4"); // B

        $this->assertArrayHasKey("1,0", $game->board, "Stone not moved to new cell");
        $this->assertTrue($game->board["0,1"][0][1] === "S", "New stone not placed in old cell");
    }
}