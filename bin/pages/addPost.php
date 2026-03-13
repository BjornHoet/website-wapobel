<?php
include '../init.php';

header('Content-Type: application/json');

$wateringId = $wateringData['wateringId'];
$hoofdpostId = $_POST['hoofdPostId'];
$postType = $_POST['postType'];
$postId = $_POST['postId'] ?? null;
$omschrijving = $_POST['postOmschrijving'];

$newRow = [];

if($postType === 'P') {
    $nextPostId = getNextPostId($wateringId, $wateringJaar, $hoofdpostId);
    $nextPostId = $nextPostId + 1;

    $hoofdpostData = getHoofdPostData($hoofdpostId);
    $hoofdpostUseKey = $hoofdpostData['useKey'];

    addPost($wateringId, $wateringJaar, $hoofdpostId, $nextPostId, $omschrijving);

    // Update referenties
    $posten = getPostenUseKey($wateringId, $wateringJaar, $hoofdpostUseKey);
    $referentie = 1;
    foreach ($posten as $post) {
        $postIdTmp = $post['postId'];
        changePostRef($postIdTmp, $referentie);

        if($postIdTmp == $nextPostId) {
            $newRow = [
                'id' => "P".$nextPostId,          // uniek ID voor AG Grid
                'postId' => $nextPostId,
                'type' => 'post',
                'referentie' => $referentie,
                'omschrijving' => $omschrijving,
                'raming' => 0,
                'actief' => true,
                'verwijderbaar' => true,
                'hasSub' => false,
                'parent' => null
            ];
        }
        $referentie++;
    }
}

if($postType === 'S') {
    $nextSubPostId = getNextSubPostId($wateringId, $wateringJaar, $postId);
    if(empty($nextSubPostId)) {
        $nextSubPostId = 'a';
    } else {
        $nextSubPostId = ++$nextSubPostId;
    }

    $hoofdpostData = getHoofdPostData($hoofdpostId);
    $postData = getPostData($postId);

    addSubPost($wateringId, $wateringJaar, $postId, $nextSubPostId, $omschrijving);
    changePost($postId, $postData['referentie'], $postData['omschrijving'], '0.00', 'X');

    $newRow = [
        'id' => "S".$nextSubPostId,        // uniek ID voor AG Grid
        'subpostId' => $nextSubPostId,
        'postId' => $postId,
        'type' => 'subpost',
        'referentie' => $nextSubPostId,
        'omschrijving' => $omschrijving,
        'raming' => 0,
        'actief' => true,
        'verwijderbaar' => true,
        'parent' => "P".$postId         // parent = ID van de hoofdpost
    ];
}

// JSON terugsturen naar frontend
echo json_encode($newRow);
exit();