											<h4 class="h4 mb-4 text-primary font-weight-bold"><?php echo $titelGU ?> (<?php echo $wateringJaar ?>)</h4>
											<?php $totaalRamingU = 0;
												  $totaalBedragU = 0;
												foreach ($hoofdPostenUitgaven as $hoofdPostUitgaven) { 
												$totaalRamingHoofdPost = 0;
												$totaalBedragHoofdPost = 0;
												
												$posten = getPosten($wateringData['wateringId'], $wateringJaar, $hoofdPostUitgaven['hoofdpostId']); ?>
												<div class="row">
													<div class="col-md-12 mb-4">
														<table class="h5 text-gray-900">
															<tr>
																<td width="40px" class="font-weight-bold"><?php echo $hoofdPostUitgaven['referentie']; ?></td>
																<td>
																	<div id="headerButtons">
																		<div class="childButtons"><span class="mr-3"><?php echo $hoofdPostUitgaven['omschrijving']; ?></span></div>
																		<?php if($boekjaarOpen === true) { ?>
																		<div class="childButtons">
																			<a href="#" class="mr-2" onclick="toevoegenPost('<?php echo $hoofdPostUitgaven['hoofdpostId']; ?>', '<?php echo $hoofdPostUitgaven['referentie'] ?>', '<?php echo $hoofdPostUitgaven['omschrijving'] ?>')" title="Post toevoegen" data-toggle="modal" data-target="#postModal">
																				<i class="fas fa-file fa-sm fa-fw mr-2 text-success"></i>
																			</a>
																		</div>
																		<div class="childButtons">
																			<a href="wijzigPost.php?hoofdpostId=<?php echo $hoofdPostUitgaven['hoofdpostId'] ?>" title="Posten wijzigen">
																				<i class="fas fa-pencil-alt fa-sm fa-fw mr-2 text-warning"></i>
																			</a>
																		</div>
																		<?php } ?>
																	</div>
																</td>
															</tr>
														</table>
														<table style="width: 100%" class="ml-2 table-striped">
															<thead class="text-s text-gray-900">
																<th>Ref</th>
																<th>Omschrijving</th>
																<th class="text-right"><span class="mr-2">Begroting</span></th>
																<th class="text-right"><span class="mr-2">Bedrag</span></th>
															</thead>
															<tbody>														
														<?php foreach ($posten as $post) {
															if($post['actief'] === 'X') {
																	$postStyle = 'text-gray-800'; 
																} else {
																	$postStyle = 'text-gray-400'; 
																}

															$bedrag = 0;
															$bedrag = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], '');

															if($post['actief'] === 'X') {
																$totaalRamingU = $totaalRamingU + $post['raming'];
																$totaalBedragU = $totaalBedragU + $bedrag;
																$totaalRamingHoofdPost = $totaalRamingHoofdPost + $post['raming'];
																$totaalBedragHoofdPost = $totaalBedragHoofdPost + $bedrag;
																}
																
															$subPosten = getSubPosten($wateringData['wateringId'], $wateringJaar, $post['postId']);
															$subPostThere = "";
															foreach ($subPosten as $subPost) {
																$subPostThere = "X";
																break;
																} ?>
															<tr class="text-s <?php echo $postStyle ?>">
																<td width="5%" class="font-weight-bold"><span class="ml-2"><?php echo $post['referentie']; ?></span></td>
																<td width="65%"><?php echo $post['omschrijving']; ?></td>
																<td width="15%" class="text-right"><?php if($subPostThere !== 'X') { ?><span class="mr-2"><?php echo currencyConv($post['raming']); ?></span><?php } ?></td>
																<?php if($post['raming'] >= $bedrag) {
																	$bedragStyle = 'text-success';
																	} else {
																	$bedragStyle = 'text-danger text-bold';
																	} ?>																
																<td width="15%" class="text-right"><?php if($subPostThere !== 'X') { ?><span class="<?php echo $bedragStyle ?> mr-2"><?php echo currencyConv($bedrag); ?></span><?php } ?></td>
															</tr>
															<?php foreach ($subPosten as $subPost) {
																$bedrag = 0;
																$bedrag = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], $subPost['subpostId']);

																if($post['actief'] === 'X') {
																	$totaalRamingU = $totaalRamingU + $subPost['raming'];
																	$totaalBedragU = $totaalBedragU + $bedrag;
																	$totaalRamingHoofdPost = $totaalRamingHoofdPost + $subPost['raming'];
																	$totaalBedragHoofdPost = $totaalBedragHoofdPost + $bedrag;
																	}
																
																if($subPost['actief'] === 'X') {
																	$subpostStyle = 'text-gray-700'; 
																} else {
																	$subpostStyle = 'text-gray-400'; 
																}?>
																<tr class="ml-3 text-xs <?php echo $subpostStyle ?>">
																	<td width="5%" class="font-weight-bold"><span class="ml-4"><?php echo $subPost['referentie']; ?></span></td>
																	<td width="65%"><span class="ml-4"><?php echo $subPost['omschrijving']; ?></span></td>
																	<td width="15%" class="text-right"><span class="mr-2"><?php echo currencyConv($subPost['raming']); ?></span></td>
																<?php if($subPost['raming'] >= $bedrag) {
																	$bedragStyle = 'text-success';
																	} else {
																	$bedragStyle = 'text-danger text-bold';
																	} ?>																	
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
												
											<?php $ramingReserve = 0;
												$reserveExist = checkReserveExists($wateringData['wateringId'], $wateringJaar);
												$ramingReserve = getReserve($wateringData['wateringId'], $wateringJaar);
												if ($reserveExist === true) { ?>
												<div class="row ">
													<div class="col-md-12 mb-4 h5 text-gray-900 text-bold">
														Reserve
														<?php if($boekjaarOpen === true) { ?>
														<div class="ml-3 childButtons">
															<a href="#" title="Reserve wijzigen" data-toggle="modal" data-target="#reserveModal">
																<i class="fas fa-pencil-alt fa-sm fa-fw mr-2 text-warning"></i>
															</a>
														</div>
														<?php } ?>
														
														<table style="width: 100%" class="ml-2 mt-2 table-striped">
															<thead class="text-s text-gray-900">
																<th width="70%">Reservefonds</th>
																<th width="15%" class="text-right"><span class="mr-2"><?php echo currencyConv($ramingReserve); ?></span></th>
																<th width="15%" class="text-right"><span class="mr-2"></span></th>
															</thead>
														</table>
													</div>
												</div>
											<?php } ?>											