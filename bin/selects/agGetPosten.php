<?php
include '../init.php';

$hoofdpostId = $_GET['hoofdpostId'];
$hoofdpostData = getHoofdPostData($hoofdpostId);
$posten = getPosten($wateringData['wateringId'], $wateringJaar, $hoofdpostId);

$data = [];

foreach ($posten as $post) {

    $subPosten = getSubPosten($wateringData['wateringId'], $wateringJaar, $post['postId']);

    $hasSub = $subPosten->num_rows > 0;

    $bedrag = getBoekingBedragPost(
        $wateringData['wateringId'],
        $wateringJaar,
        $post['postId'],
        ''
    );

    $deleteAllowed =
        ($bedrag == '' || $bedrag == '0.00') &&
        !$hasSub && $hoofdpostData['andere'] == '1';

    $data[] = [
        "id" => "P".$post['postId'],
        "type" => "post",
        "postId" => $post['postId'],
        "referentie" => $post['referentie'],
        "omschrijving" => $post['omschrijving'],
        "raming" => $post['raming'],
        "actief" => $post['actief'] === 'X',
        "hasSub" => $hasSub,
        "verwijderbaar" => $deleteAllowed,
        "parent" => null
    ];

    foreach ($subPosten as $subPost) {

        $bedrag = getBoekingBedragPost(
            $wateringData['wateringId'],
            $wateringJaar,
            $post['postId'],
            $subPost['subpostId']
        );

        $deleteAllowed =
            ($bedrag == '' || $bedrag == '0.00');

        $data[] = [
            "id" => "S".$subPost['subpostId'],
            "type" => "subpost",
            "postId" => $post['postId'],
            "subpostId" => $subPost['subpostId'],
            "referentie" => $subPost['referentie'],
            "omschrijving" => $subPost['omschrijving'],
            "raming" => $subPost['raming'],
            "actief" => $subPost['actief'] === 'X',
            "verwijderbaar" => $deleteAllowed,
            "parent" => "P".$post['postId']
        ];
    }
}

echo json_encode($data);