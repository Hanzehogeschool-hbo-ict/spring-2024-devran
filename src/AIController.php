<?php

namespace Hive;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(?Database $db, ?Session $session, $aiService)
    {
        parent::__construct($db, $session);
        $this->aiService = $aiService;
    }

    public function handlePost(): void
    {
        $url = "spring-2024-devran-ai-1:5000";
        $game = $this->session->get('game');

        $numMoves = $this->db->query("
                SELECT COUNT(*) AS num_moves FROM moves 
                WHERE game_id = ?;
            ", [$this->session->get('game_id')]);
        $resData = $this->aiService->sendMove($url, $numMoves->field_count, $game->hand, $game->board);
        // De data die de AI terugstuurt bij de eerste zet is soms leeg om de een of andere reden
        if (!$resData) {
            App::redirect();
            return;
        }

        $type = $resData[0];
        $piece = $resData[1];
        $to = $resData[2];

        $this->doMove($type, $piece, $to);

        // redirect back to index
        App::redirect();
    }

    public function doMove(string $type, string $piece, string $to): void
    {
        $game = $this->session->get('game');

        // add the new tile to the board, remove it from its owners hand and switch players
        $game->board[$to] = [[$game->player, $piece]];
        $game->hand[$game->player][$piece]--;

        // Switch current player
        $game->player = 1 - $game->player;

        if (!isset($this->db))
            return;
        // Store move in db
        $this->saveToDatabase($type, $piece, $to);

        $this->session->set('last_move', $this->db->getInsertId());
    }
}
