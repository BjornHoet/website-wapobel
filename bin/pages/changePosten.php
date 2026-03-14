<?php
include '../init.php';

// Handle JSON data from AG Grid
if (isset($_POST['rows'])) {
    $rows = json_decode($_POST['rows'], true);
    
    if (is_array($rows)) {
        foreach ($rows as $row) {
            if (isset($row['type']) && isset($row['postId'])) {
                if ($row['type'] === 'post') {
                    // Handle post
                    $postId = $row['postId'];
                    $referentie = $row['referentie'] ?? '';
                    $omschrijving = $row['omschrijving'] ?? '';
                    $raming = $row['raming'] ?? 0;
                    $actief = $row['actief'] ? 'X' : '';
                    
                    changePost($postId, $referentie, $omschrijving, $raming, $actief);
                } elseif ($row['type'] === 'subpost') {
                    // Handle subpost
                    $subpostId = $row['subpostId'];
                    $referentie = $row['referentie'] ?? '';
                    $omschrijving = $row['omschrijving'] ?? '';
                    $raming = $row['raming'] ?? 0;
                    $actief = $row['actief'] ? 'X' : '';
                    
                    changeSubPost($subpostId, $referentie, $omschrijving, $raming, $actief);
                }
            }
        }
    }
} else {
    // Fallback to old form data format for backward compatibility
    $category = '';
    $referentie = '';
    $omschrijving = '';
    $raming = '';
    $actief = '';

    foreach ($_POST as $key => $value) {
        $category = substr($key, 0, 1);
        $postId = substr($key, 6);
        $fieldCat = substr($key, 2, 3);
        
        switch ($fieldCat) {
            case 'ref':
                $referentie = $value;
                break;
            case 'oms':
                $omschrijving = $value;
                break;
            case 'ram':
                $raming = $value;
                break;
            case 'act':
                $actief = $value;
                break;
            case 'end':
                $doPost = 'X';
            }
        
        if($doPost === 'X') {
            if($category === 'p') {
                changePost($postId, $referentie, $omschrijving, $raming, $actief);
            } else {
                changeSubPost($postId, $referentie, $omschrijving, $raming, $actief);
            }
            
            $category = '';
            $referentie = '';
            $omschrijving = '';
            $raming = '';
            $actief = '';
            $doPost = '';
        }
    }
}

header("Location: ../../posten/");
exit();
?>
