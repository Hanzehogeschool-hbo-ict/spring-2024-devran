<?php

use Hive\App;
use Hive\PlayController;
use Hive\Session;
use PHPUnit\Framework\TestCase;
use Hive\Game;

class PlayedStoneNotShownTest extends TestCase
{
//    testGivenHandWhenStoneCountZeroThenNoShow
    public function testShouldNotShowStone()
    {
        $app = App::getInstance();
        $app->setSession(new Session());
        $session = $app->getSession();
        $game = new Game();
        $session->set('game', $game);
        $playController = new PlayController(null, $session);

        $playController->handlePost("Q", "0,0");

        $this->assertTrue($game->hand[0]["Q"] === 0);
    }
}