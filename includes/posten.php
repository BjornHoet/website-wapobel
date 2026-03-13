											<h4 class="h4 mb-4 text-primary font-weight-bold"><?php echo $titleHeader ?> (<?php echo $wateringJaar ?>)</h4>
											<?php 
												foreach ($hoofdPosten as $hoofdPost) { 
												$totaalRamingHoofdPost = 0;
												$totaalBedragHoofdPost = 0;

												$posten = getPosten($wateringData['wateringId'], $wateringJaar, $hoofdPost['hoofdpostId']); ?>
												<div class="row">
													<div class="col-md-12 mb-4">
														<table class="h5 text-gray-900">
															<tr>
																<td width="40px" class="font-weight-bold"><?php echo $hoofdPost['referentie']; ?></td>
																<td>
																	<div id="headerButtons">
																		<div class="childButtons"><span class="mr-3"><?php echo $hoofdPost['omschrijving']; ?></span></div>
																		<?php if($boekjaarOpen === true && $hoofdPost['reserve'] == false) { ?>
																		<div class="childButtons">
																			<a href="#" class="btn-wijzig-posten" data-hoofdpostid="<?php echo $hoofdPost['hoofdpostId'] ?>" data-referentie="<?php echo $hoofdPost['referentie'] ?>" data-omschrijving="<?php echo $hoofdPost['omschrijving'] ?>" title="Posten wijzigen">
																				<button type="button" class="btn btn-warning zbtn-xs">Wijzig</button>
																			</a>
																		</div>
																		<?php } ?>
																	</div>
																</td>
															</tr>
														</table>
														<table style="width: 100%" class="ml-2 table-striped">
															<thead class="text-s text-gray-900">
																<th width="5%">Ref</th>
																<th width="65%">Omschrijving</th>
																<th width="15%" class="text-right"><span class="mr-2">Begroting</span></th>
																<th width="15%" class="text-right"><span class="mr-2">Bedrag</span></th>
															</thead>
															<tbody>
														<?php foreach ($posten as $post) {
															if($post['actief'] === 'X') {
																	$postStyle = 'text-gray-800'; 
																} else {
																	$postStyle = 'text-inactive'; 
																}
															
															$bedrag = 0;
															/* if($type == 'GO' && $hoofdPost['referentie'] === 'I' && $post['referentie'] === '1')
                                                                $bedrag = getOverdrachtTotaal($wateringData['wateringId'], $wateringJaar);
                                                            else */
															$bedrag = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], '');
															
															if($post['actief'] === 'X') {
																if($type == 'GO' || $type == 'BO') {
																	$totaalRamingO = $totaalRamingO + $post['raming'];
																	$totaalBedragO = $totaalBedragO + $bedrag;
																	}
																if($type == 'GU' || $type == 'BU') {
																	$totaalRamingU = $totaalRamingU + $post['raming'];
																	$totaalBedragU = $totaalBedragU + $bedrag;
																	}
																
																$totaalRamingHoofdPost = $totaalRamingHoofdPost + $post['raming'];
																$totaalBedragHoofdPost = $totaalBedragHoofdPost + $bedrag;
																}
															
															$subPosten = getSubPosten($wateringData['wateringId'], $wateringJaar, $post['postId']);
															$subPostThere = "";
															foreach ($subPosten as $subPost) {
																$subPostThere = "X";
																break;
																}
															?>
															<tr class="text-s <?php echo $postStyle ?>">
																<td width="5%" class="font-weight-bold"><span class="ml-2"><?php echo $post['referentie']; ?></span></td>
																<td width="65%"><?php echo $post['omschrijving']; ?></td>
																<td width="15%" class="text-right"><?php if($subPostThere !== 'X') { ?><span class="mr-2"><?php echo currencyConv($post['raming']); ?></span><?php } ?></td>
																<?php if($post['raming'] >= $bedrag) {
																	$bedragStyle = 'text-success';
																	} else {
																	$bedragStyle = 'text-danger text-bold';
																	} 
																	if($post['actief'] != 'X') {
																		$bedragStyle = 'text-inactive'; 
																	}?>
																<td width="15%" class="text-right"><?php if($subPostThere !== 'X') { ?><span class="<?php echo $bedragStyle ?> mr-2"><?php echo currencyConv($bedrag); ?></span><?php } ?></td>
															</tr>
															<?php foreach ($subPosten as $subPost) {
																$bedrag = 0;
																$bedrag = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], $subPost['subpostId']);
																
																if($post['actief'] === 'X') {
																	if($type == 'GO' || $type == 'BO') {
																		$totaalRamingO = $totaalRamingO + $subPost['raming'];
																		$totaalBedragO = $totaalBedragO + $bedrag;
																		}
																	if($type == 'GU' || $type == 'BU') {
																		$totaalRamingU = $totaalRamingU + $subPost['raming'];
																		$totaalBedragU = $totaalBedragU + $bedrag;
																		}
																	$totaalRamingHoofdPost = $totaalRamingHoofdPost + $subPost['raming'];
																	$totaalBedragHoofdPost = $totaalBedragHoofdPost + $bedrag;
																	}
																
																if($subPost['actief'] === 'X') {
																	$subpostStyle = 'text-gray-700'; 
																} else {
																	$subpostStyle = 'text-inactive'; 
																}?>
																<tr class="ml-3 text-xs <?php echo $subpostStyle ?>">
																	<td width="5%" class="font-weight-bold"><span class="ml-4"><?php echo $subPost['referentie']; ?></span></td>
																	<td width="65%"><span class="ml-4"><?php echo $subPost['omschrijving']; ?></span></td>
																	<td width="15%" class="text-right"><span class="mr-2"><?php echo currencyConv($subPost['raming']); ?></span></td>
																<?php if($subPost['raming'] >= $bedrag) {
																	$bedragStyle = 'text-success';
																	} else {
																	$bedragStyle = 'text-danger text-bold';
																	}
																	if($subPost['actief'] != 'X') {
																		$bedragStyle = 'text-inactive'; 
																	}
																	?>
																	<td width="15%" class="text-right"><span class="<?php echo $bedragStyle ?> mr-2"><?php echo currencyConv($bedrag); ?></span></td>
																</tr>
															<?php } ?>
														<?php } ?>
															</tbody>
															<tfoot class="postTotaal">
																<th colspan="2" class="pl-3">TOTAAL</th>
																<th class="text-right"><span class="mr-2"><?php echo currencyConv($totaalRamingHoofdPost); ?></span></th>
																<th class="text-right"><span class="mr-2"><?php echo currencyConv($totaalBedragHoofdPost); ?></span></th>															
															</tfoot>
														</table>
													</div>
												</div>
											<?php } ?>