<?php

use Hive\MoveController;
use Hive\PlayController;
use PHPUnit\Framework\TestCase;
use Hive\Database;
use Hive\Game;
use Hive\Session;

class CannotMoveStoneToOpponentCellTest extends TestCase
{
    public function testCannotMoveStoneToOpponentCell()
    {
        $session = \Hive\App::getInstance()->getSession();
        $game = new Game();
        $game->board = [];
        $session->set('game', $game);
        $playController = new PlayController(null, $session);
        $moveController = new MoveController(null, $session);

        $playController->handlePost("A", "0,0"); // W
        $playController->handlePost("A", "0,1"); // B
        $playController->handlePost("A", "1,-1"); // W
        $playController->handlePost("A", "0,2"); // B
        $moveController->handlePost("1,-1", "1,0"); // Move W
        $moveController->handlePost("1,0", "0,1"); // Move W

        $this->assertArrayHasKey("0,1", $game->board);
    }
}