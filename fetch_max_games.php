<?php
require 'vendor/autoload.php';
use Illuminate\Support\Facades\Http;

// Mocking minimal Laravel environment to use Http client if possible, 
// but easier to just use curl or a simple php script.

$agent_code = "ag_mm86la0rd2757ad9";
$agent_token = "d7b907188196289cf071c62a93d7c6fcf2e828901c211f246efbd6d85491460a";

$payload = [
    "method" => "game_list",
    "agent_code" => $agent_code,
    "agent_token" => $agent_token
];

$ch = curl_init("https://maxapigames.com/api/v2");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['status']) && $data['status'] == 1) {
    echo "ID | CODE | NAME | PROVIDER\n";
    foreach ($data['games'] as $game) {
        // In MAX API, we use game_code for both ID and CODE sometimes in panels, 
        // but let's show what we have.
        echo " - | " . $game['game_code'] . " | " . $game['game_name'] . " | " . $game['provider_code'] . "\n";
    }
} else {
    print_r($data);
}
