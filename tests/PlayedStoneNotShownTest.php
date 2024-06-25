<?php

use Hive\PlayController;
use PHPUnit\Framework\TestCase;
use Hive\Database;
use Hive\Game;
use Hive\Session;

class PlayedStoneNotShownTest extends TestCase
{
//    testGivenHandWhenStoneCountZeroThenNoShow
    public function testShouldNotShowStone()
    {
        $session = Session::inst();
        $game = new Game();
        $session->set('game', $game);
        $playController = new PlayController(null, $session);

        $playController->handlePost("Q", "0,0");

        $this->assertArrayNotHasKey("Q", $game->hand[0]);
    }
}