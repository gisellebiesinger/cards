<?php
session_start();
require 'vendor/autoload.php';
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://deckofcardsapi.com/api/deck/new/shuffle/?dec_count=1');
$response_json = json_decode($response->getBody(), TRUE);
$response2 = $client->request('GET', 'https://deckofcardsapi.com/api/deck/'. $response_json['deck_id'] . '/draw/?count=2');
$response_json2 = json_decode($response2->getBody(), TRUE);
$cards_array = $response_json2["cards"];

$_SESSION['cards_array'] = $cards_array;
$_SESSION['deck_id'] = $response_json['deck_id'];

$card_total = calc_card_total($cards_array);

function calc_card_total($card_array){
    $card_total1 = 0;
    $card_total2 = 0;
    $card_value1 = ["KING"=>10, "QUEEN"=>10, "JACK"=>10, "ACE"=>1, "2"=>2, "3"=>3, "4"=>4, "5"=>5,"6"=>6, "7"=>7, "8"=>8, "9"=>9, "10"=>10];
    $card_value2 = ["KING"=>10, "QUEEN"=>10, "JACK"=>10, "ACE"=>11, "2"=>2, "3"=>3, "4"=>4, "5"=>5,
                        "6"=>6, "7"=>7, "8"=>8, "9"=>9, "10"=>10];
    $card_value = '';
    foreach($card_array as $card){
        $card_value = $card['value'];
        $card_total1 = $card_total1 + $card_value1[$card_value];
        $card_total2 += $card_value2[$card_value];
    }
    if($card_total1 < 21 && $card_total2 < 21){
        return $card_total2;
    } elseif($card_total1 === 21){
        return $card_total1;
    } elseif($card_total2 === 21){
        return $card_total2;
    } elseif($card_total1 < 21){
        return $card_total1;
    } else{
        return $card_total2;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php foreach($cards_array as $card): ?>
        <img src="<?=$card["image"] ?>">
    <?php endforeach ?>
    <h1>Your card total is <?=$card_total?></h1>
    <form action="drawagain.php" method="get">
        <?php if($card_total > 21) : ?>
            Sorry your total is above 21 <br/>
            <a href="index.php">Play again</a>
        <?php elseif($card_total === 21): ?>
            You win, take a trip to Atlantic City <br/>
            <a href="index.php">Play again</a>
        <?php else: ?>
            <input type="submit" value="Draw Again">
        <?php endif ?>
    </form>
</body>
</html>