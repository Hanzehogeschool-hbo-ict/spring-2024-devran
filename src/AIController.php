<?php

namespace Hive;

class AIController extends Controller
{
    public function handlePost(Game $game): void
    {
        $url = "spring-2024-devran-ai-1:5000";

        $resData = $this->sendMove($url, count($game->board), $game->hand, $game->board);
        // De data die de AI terugstuurt bij de eerste zet is soms leeg om de een of andere reden
        if (!$resData) {
            App::redirect();
            return;
        }

        $type = $resData[0];
        $piece = $resData[1];
        $to = $resData[2];

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

        // redirect back to index
        App::redirect();
    }

    /**
     * Send post request to the AI server.
     *
     * @param string $url Url to send move request to
     * @param int $moveNumber Total number of moves played so far by both players
     * @param array $hand Hands of white and black as dictionary
     * @param array $board Current board as dictionary
    */
    protected function sendMove(string $url, int $moveNumber, array $hand, array $board)
    {
        $curl = curl_init();
        $data = array(
            "move_number" => $moveNumber,
            "hand" => $hand,
            "board" => $board
        );
        $newData = json_encode($data);

        $arrayOptions = array(
            CURLOPT_URL=>$url,
            CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>$newData,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_HTTPHEADER=>array('Content-Type:application/json')
        );
        curl_setopt_array($curl,$arrayOptions);

        $res = curl_exec($curl);
        $resData = json_decode($res);
        curl_close($curl);

        return $resData;
    }
}
