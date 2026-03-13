<?php
// includes/posten.php
// Verwacht: $titleHeader, $hoofdPosten, $type, $wateringData, $wateringJaar, $boekjaarOpen
?>

<div class="mb-3">
    <h5 class="h5 mb-2 text-primary font-weight-bold">
        <?php echo $titleHeader ?> (<?php echo $wateringJaar ?>)
    </h5>
</div>

<?php foreach ($hoofdPosten as $hoofdPost): ?>

    <?php
        $totaalRamingHoofdPost = 0;
        $totaalBedragHoofdPost = 0;
        $posten = getPosten($wateringData['wateringId'], $wateringJaar, $hoofdPost['hoofdpostId']);
    ?>

    <div class="card mb-3 shadow-sm mb-5">
        <div class="card-header bg-darker d-flex justify-content-between align-items-center">
            <div>
                <span class="font-weight-bold text-muted mr-2"><?php echo $hoofdPost['referentie']; ?></span>
                <span class="text-dark"><?php echo $hoofdPost['omschrijving']; ?></span>
            </div>

            <?php if ($boekjaarOpen && !$hoofdPost['reserve']): ?>
                <a href="wijzigPost.php?hoofdpostId=<?php echo $hoofdPost['hoofdpostId'] ?>"
                   class="btn btn-sm btn-warning">
                    Wijzig
                </a>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="pl-3" style="width:8%;">Ref</th>
                        <th>Omschrijving</th>
                        <th class="text-right" style="width:15%;">Begroting</th>
                        <th class="text-right" style="width:15%;">Bedrag</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($posten as $post):
                    $isActive = ($post['actief'] === 'X');
                    $postClass = $isActive ? '' : 'table-row-inactive';
                    $bedrag = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], '');

                    if ($isActive) {
                        if ($type == 'GO' || $type == 'BO') {
                            $totaalRamingO += $post['raming'];
                            $totaalBedragO += $bedrag;
                        }
                        if ($type == 'GU' || $type == 'BU') {
                            $totaalRamingU += $post['raming'];
                            $totaalBedragU += $bedrag;
                        }
                        $totaalRamingHoofdPost += $post['raming'];
                        $totaalBedragHoofdPost += $bedrag;
                    }

                    $subPosten = getSubPosten($wateringData['wateringId'], $wateringJaar, $post['postId']);
                    $hasSubPosts = !empty($subPosten);

                    $bedragStyle = (!$isActive) ? 'text-muted' : (($post['raming'] >= $bedrag) ? 'text-success' : 'text-danger font-weight-bold');
                ?>

                    <tr class="<?php echo $postClass; ?>">
                        <td class="pl-3 font-weight-bold"><?php echo $post['referentie']; ?></td>
                        <td><?php echo $post['omschrijving']; ?></td>
                        <td class="text-right"><?php if (!$hasSubPosts) echo currencyConv($post['raming']); ?></td>
                        <td class="text-right"><?php if (!$hasSubPosts) { ?><span class="<?php echo $bedragStyle; ?>"><?php echo currencyConv($bedrag); ?></span><?php } ?></td>
                    </tr>

                    <?php foreach ($subPosten as $subPost):
                        $subActive = ($subPost['actief'] === 'X');
                        $subClass = $subActive ? '' : 'table-row-inactive';
                        $subBedrag = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], $subPost['subpostId']);

                        if ($post['actief'] === 'X') {
                            if ($type == 'GO' || $type == 'BO') {
                                $totaalRamingO += $subPost['raming'];
                                $totaalBedragO += $subBedrag;
                            }
                            if ($type == 'GU' || $type == 'BU') {
                                $totaalRamingU += $subPost['raming'];
                                $totaalBedragU += $subBedrag;
                            }
                            $totaalRamingHoofdPost += $subPost['raming'];
                            $totaalBedragHoofdPost += $subBedrag;
                        }

                        $subBedragStyle = (!$subActive) ? 'text-muted' : (($subPost['raming'] >= $subBedrag) ? 'text-success' : 'text-danger font-weight-bold');
                    ?>

                        <tr class="small <?php echo $subClass; ?>">
                            <td class="pl-4"><?php echo $subPost['referentie']; ?></td>
                            <td class="pl-4"><?php echo $subPost['omschrijving']; ?></td>
                            <td class="text-right"><?php echo currencyConv($subPost['raming']); ?></td>
                            <td class="text-right"><span class="<?php echo $subBedragStyle; ?>"><?php echo currencyConv($subBedrag); ?></span></td>
                        </tr>
                    <?php endforeach; ?>

                <?php endforeach; ?>
                </tbody>

				<tfoot class="table-footer">
					<tr>
						<th colspan="2" class="pl-3">TOTAAL</th>
						<th class="text-right font-weight-bold"><?php echo currencyConv($totaalRamingHoofdPost); ?></th>
						<th class="text-right font-weight-bold"><?php echo currencyConv($totaalBedragHoofdPost); ?></th>
					</tr>
				</tfoot>
            </table>
        </div>
    </div>

<?php endforeach; ?>
