<?php

use Hive\App;
use Hive\Game;
use Hive\PlayController;
use Hive\Session;

class CannotPlaceBeeWhenThreeStonesPlacedTest extends \PHPUnit\Framework\TestCase
{
    public function testCannotPlaceBeeWhenThreeStonesPlaced()
    {
        $app = App::getInstance();
        $app->setSession(new Session());
        $session = $app->getSession();
        $game = new Game();
        $game->board = [];
        $session->set('game', $game);
        $playController = new PlayController(null, $session);

        $playController->handlePost("G", "0,0"); // W
        $playController->handlePost("B", "0,1"); // B
        $playController->handlePost("A", "0,-1"); // W
        $playController->handlePost("A", "0,2"); // B
        $playController->handlePost("G", "1,-1"); // W
        $playController->handlePost("A", "-1,2"); // B

        $playController->handlePost("G", "1,-2"); // W, can't be stone other than queen
        $this->assertArrayHasKey("G", $game->hand[0]); // Ensure last grass hopper can't be placed as 4th move

        $playController->handlePost("Q", "1,-2"); // W, must be queen
        $this->assertTrue($game->hand[0]["Q"] === 0);
    }
}