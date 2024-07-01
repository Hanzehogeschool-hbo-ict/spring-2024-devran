<?php

namespace Hive;

class AIService
{
    /**
     * Send post request to the AI server.
     *
     * @param string $url Url to send move request to
     * @param int $moveNumber Total number of moves played so far by both players
     * @param array $hand Hands of white and black as dictionary
     * @param array $board Current board as dictionary
     */
    public function sendMove(string $url, int $moveNumber, array $hand, array $board)
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