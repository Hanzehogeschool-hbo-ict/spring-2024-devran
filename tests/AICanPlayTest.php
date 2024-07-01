<?php


use Hive\AIController;
use Hive\App;
use Hive\Game;
use Hive\PlayController;
use Hive\Session;

class AICanPlayTest extends \PHPUnit\Framework\TestCase
{
    public function testAICanPlay(): void
    {
        $app = App::getInstance();
        $app->setSession(new Session());
        $session = $app->getSession();
        $game = new Game();
        $game->board = [];
        $session->set('game', $game);

        $playController = new PlayController(null, $session);
        $playController->handlePost("G", "0,0");

        $aiService = Mockery::mock("aiService");
        $aiService->shouldReceive("sendMove")
            ->withArgs(["spring-2024-devran-ai-1:5000", count($game->board), $game->hand, $game->board])
            ->andReturnValues(["play", "B", "0,0"]);

        $aiController = new AIController(null, $session, $aiService);
        $aiController->doMove("play", "B", "0,1");

        $this->assertArrayHasKey("0,1", $game->board);
    }
}