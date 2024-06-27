<?php

use Hive\App;
use Hive\Game;
use Hive\Session;
use Hive\Util;

class CheckWinConditionTest extends \PHPUnit\Framework\TestCase
{
    public function testWinConditionCheck()
    {
        $app = App::getInstance();
        $app->setSession(new Session());
        $session = $app->getSession();
        $game = new Game();
        $game->board = [];
        $session->set('game', $game);

        // Check black win
        $game->board["0,0"] = [["Q", 0]]; // W
        $game->board["1,0"] = [["Q", 1]]; // B
        $game->board["-1,0"] = [["B", 0]]; // W
        $game->board["0,1"] = [["A", 1]]; // B
        $game->board["-1,1"] = [["A", 0]]; // W
        $game->board["1,-1"] = [["B", 1]]; // B
        $game->board["0,-1"] = [["A", 0]]; // W

        $blackWin = Util::checkWinCondition($game->board);
        $this->assertStringContainsString("black", $blackWin, "Check black win");

        // Check white win
        $game->board["0,0"] = [["Q", 1]]; // B
        $game->board["1,0"] = [["Q", 0]]; // W
        $game->board["-1,0"] = [["B", 1]]; // B
        $game->board["0,1"] = [["A", 0]]; // W
        $game->board["-1,1"] = [["A", 1]]; // B
        $game->board["1,-1"] = [["B", 0]]; // W
        $game->board["0,-1"] = [["A", 1]]; // B

        $whiteWin = Util::checkWinCondition($game->board);
        $this->assertStringContainsString("white", $whiteWin, "Check white win");

        // Check tie
        $game->board["0,0"] = [["Q", 0]]; // W
        $game->board["1,0"] = [["Q", 1]]; // B

        $game->board["1,-1"] = [["A", 0]]; // W
        $game->board["2,-1"] = [["A", 1]]; // B
        $game->board["2,0"] = [["A", 0]]; // W
        $game->board["1,1"] = [["A", 1]]; // B
        $game->board["0,1"] = [["A", 0]]; // W
        $game->board["-1,1"] = [["A", 1]]; // B
        $game->board["-1,0"] = [["B", 0]]; // W
        $game->board["0,-1"] = [["B", 1]]; // B

        $tie = Util::checkWinCondition($game->board);
        $this->assertStringContainsString("tie", $tie, "Check tie");

        // Check white win
        $game->board["0,0"] = [["Q", 1]]; // B
        $game->board["1,0"] = [["Q", 0]]; // W
        $game->board["-1,0"] = [["B", 1]]; // B
        $game->board["0,1"] = [["A", 0]]; // W

        $noWin = Util::checkWinCondition($game->board);
        $this->assertStringContainsString("", $noWin, "Check win condition not met");
    }
}