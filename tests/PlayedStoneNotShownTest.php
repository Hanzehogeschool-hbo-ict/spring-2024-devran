<?php

use Hive\App;
use Hive\PlayController;
use PHPUnit\Framework\TestCase;
use Hive\Game;

class PlayedStoneNotShownTest extends TestCase
{
//    testGivenHandWhenStoneCountZeroThenNoShow
    public function testShouldNotShowStone()
    {
        $session = App::getInstance()->getSession();
        $game = new Game();
        $session->set('game', $game);
        $playController = new PlayController(null, $session);

        $playController->handlePost("Q", "0,0");

        $this->assertArrayNotHasKey("Q", $game->hand[0]);
    }
}