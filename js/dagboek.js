// ---   Watering, jaar en maand dropdowns   ---
// ---------------------------------------------
		$("#selectWatering").change(function() {
			var wateringId = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeWatering.php", { wateringId: wateringId }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});	

		$("#selectJaar").change(function() {
			var jaar = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeJaar.php", { jaar: jaar }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});	

		$("#selectMaand").change(function() {
			var maand = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeMaand.php", { maand: maand }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});	

		function currentMonth() {
			var maand = <?php echo date("m"); ?>;
			$.post("<?php echo($prefix);?>bin/selects/changeMaand.php", { maand: maand }, function(response) {
				// Log the response to the console
				});
			location.reload();
		}	

		function opslaan() {
			location.reload();
		}


// ---   Search Dropdowns   ---
// ----------------------------
	$(document).ready(function(){
		$("div#addSubpost").hide();
		$('#postSearch').hierarchySelect({
			hierarchy: false,
			width: '100%'
		   });
		});

	$(document).ready(function(){
		$("div#addSubpostChange").hide();
		$('#postSearchChange').hierarchySelect({
			hierarchy: false,
			width: '100%'
		   });
		});	


// ---   Modals leegmaken   ---
// ----------------------------
	$('#addBoekingModal').on('shown.bs.modal', function (e) {
		$('#inputBoekingHoofdpost').val("").change();
		$('select[name="boekingPost"]').empty();
		$('select[name="boekingSubpost"]').empty();
		});


// ---   Datepicker   ---
// ----------------------
	$('.datepicker').datepicker({
			format: "dd/mm",
			todayBtn: "linked",
			language: "nl-BE",
			autoclose: true,
			todayHighlight: true
		});	


// Wijzigen post - Data vullen
	function vulWijzigingsModal(boekId, datum, hoofdpostId, postId, subpostId) {
		$('#inputBoekingHoofdpost').val("").change();
		$('select[name="boekingPost"]').empty();
		$('select[name="boekingSubpost"]').empty();
		$("div#addSubpostChange").hide();
		$('#inputBoekingIdChange').val(boekId);

		$('#inputBoekingHoofdpostChange').val(hoofdpostId).change();
			document.getElementById('inputBoekingDatumChange').value = datum;
			const d = new Date(datum);
			$('#inputBoekingDatumChange').datepicker('update', d);
			
			if(hoofdpostId && hoofdpostId !="") {
				var hoofdpostEl = document.getElementById('inputBoekingHoofdpostChange');
				hoofdpostEl.value = hoofdpostId;
				
				if(hoofdpostId !== '') {
					$.ajax({
							url: "bin/selects/getPosten.php",
							dataType: 'Json',
							data: {'id':hoofdpostId},
							success: function(data) {
								$('select[name="boekingPost"]').empty();
								$('select[name="boekingPost"]').append('<option value=""></option>');
								$.each(data, function(key, value) {
									$('select[name="boekingPost"]').append('<option value="'+ key +'">'+ value +'</option>');
								});
							var postIdEl = document.getElementById('inputBoekingPostChange')
							postIdEl.value = postId;

							if(postId !== '') {
								$.ajax({
										url: "bin/selects/getSubposten.php",
										dataType: 'Json',
										data: {'id':postId},
										success: function(data) {
											$('select[name="boekingSubpost"]').empty();
											$('select[name="boekingSubpost"]').append('<option value=""></option>');
											if(data && data !="") {
												$("div#addSubpostChange").show();
												document.getElementById("inputBoekingSubpostChange").required = true;
												$.each(data, function(key, value) {
													$('select[name="boekingSubpost"]').append('<option value="'+ key +'">'+ value +'</option>');
												});
											var subpostIdEl = document.getElementById('inputBoekingSubpostChange')
											subpostIdEl.value = subpostId;												
											}
										}
									});
								} else { 
									$('select[name="boekingSubpost"]').empty();
									$("div#addSubpostChange").hide();
								}							
							}
						});
					} else { 			
						$('select[name="boekingPost"]').empty();
						$('select[name="boekingSubpost"]').empty();
					}
				}		
	}

// ---   Posten - DropDowns   ---
// ------------------------------

// Wijziging Hoofdpost
	$( "select[name='boekingHoofdpost']" ).change(function () {
			var hoofdpostId = $(this).val();
			$("div#addSubpost").hide();
			document.getElementById("inputBoekingSubpost").required = false;

			if(hoofdpostId !== '') {
				$.ajax({
						url: "bin/selects/getPosten.php",
						dataType: 'Json',
						data: {'id':hoofdpostId},
						success: function(data) {
							$('select[name="boekingPost"]').empty();
							$('select[name="boekingPost"]').append('<option value=""></option>');
							$.each(data, function(key, value) {
								$('select[name="boekingPost"]').append('<option value="'+ key +'">'+ value +'</option>');
							});
						}
					});
			} else { 			
				$('select[name="boekingPost"]').empty();
				$('select[name="boekingSubpost"]').empty();
			}
		});

// Wijziging Post
		$( "select[name='boekingPost']" ).change(function () {
			var postId = $(this).val();
			$("div#addSubpost").hide();
			document.getElementById("inputBoekingSubpost").required = false;
			
			if(postId !== '') {
				$.ajax({
						url: "bin/selects/getSubposten.php",
						dataType: 'Json',
						data: {'id':postId},
						success: function(data) {
							$('select[name="boekingSubpost"]').empty();
							$('select[name="boekingSubpost"]').append('<option value=""></option>');
							if(data && data !="") {
								$("div#addSubpost").show();
								document.getElementById("inputBoekingSubpost").required = true;
								$.each(data, function(key, value) {
									$('select[name="boekingSubpost"]').append('<option value="'+ key +'">'+ value +'</option>');
								});
							}
						}
					});
			} else { 
				$('select[name="boekingSubpost"]').empty();
				$("div#addSubpost").hide();
			}
		});


// ---   Search Dropdown - Toevoegen   ---
// ---------------------------------------
		$(function(){
		  $(".dropdown-action-add").on("click",function(e){
			e.preventDefault(); 
			var referentie = $(this).data("value");
			const refArray = referentie.split('|');
			var hoofdpostId = refArray[0];
			var postId = refArray[1];
			var subpostId = refArray[2];
			
			$('select[name="boekingPost"]').empty();
			$('select[name="boekingSubpost"]').empty();
			document.getElementById("inputBoekingSubpost").required = false;
			$("div#addSubpost").hide();
			
			if(hoofdpostId && hoofdpostId !="") {
				var hoofdpostEl = document.getElementById('inputBoekingHoofdpost')
				hoofdpostEl.value = hoofdpostId;
				
				if(hoofdpostId !== '') {
					$.ajax({
							url: "bin/selects/getPosten.php",
							dataType: 'Json',
							data: {'id':hoofdpostId},
							success: function(data) {
								$('select[name="boekingPost"]').empty();
								$('select[name="boekingPost"]').append('<option value=""></option>');
								$.each(data, function(key, value) {
									$('select[name="boekingPost"]').append('<option value="'+ key +'">'+ value +'</option>');
								});
							var postIdEl = document.getElementById('inputBoekingPost')
							postIdEl.value = postId;

							if(postId !== '') {
								$.ajax({
										url: "bin/selects/getSubposten.php",
										dataType: 'Json',
										data: {'id':postId},
										success: function(data) {
											$('select[name="boekingSubpost"]').empty();
											$('select[name="boekingSubpost"]').append('<option value=""></option>');
											if(data && data !="") {
												$("div#addSubpost").show();
												document.getElementById("inputBoekingSubpost").required = true;
												$.each(data, function(key, value) {
													$('select[name="boekingSubpost"]').append('<option value="'+ key +'">'+ value +'</option>');
												});
											var subpostIdEl = document.getElementById('inputBoekingSubpost')
											subpostIdEl.value = subpostId;												
											}
										}
									});
								} else { 
									$('select[name="boekingSubpost"]').empty();
									$("div#addSubpost").hide();
								}							
							}
						});
					} else { 			
						$('select[name="boekingPost"]').empty();
						$('select[name="boekingSubpost"]').empty();
					}
				}
		    });
		});
		
// ---   Search Dropdown - Wijzigen   ---
// ---------------------------------------
		$(function(){
		  $(".dropdown-action-change").on("click",function(e){
			e.preventDefault(); 
			var referentie = $(this).data("value");
			const refArray = referentie.split('|');
			var hoofdpostId = refArray[0];
			var postId = refArray[1];
			var subpostId = refArray[2];
			
			$('select[name="boekingPost"]').empty();
			$('select[name="boekingSubpost"]').empty();
			document.getElementById("inputBoekingSubpostChange").required = false;
			$("div#addSubpostChange").hide();
			
			if(hoofdpostId && hoofdpostId !="") {
				var hoofdpostEl = document.getElementById('inputBoekingHoofdpostChange')
				hoofdpostEl.value = hoofdpostId;
				
				if(hoofdpostId !== '') {
					$.ajax({
							url: "bin/selects/getPosten.php",
							dataType: 'Json',
							data: {'id':hoofdpostId},
							success: function(data) {
								$('select[name="boekingPost"]').empty();
								$('select[name="boekingPost"]').append('<option value=""></option>');
								$.each(data, function(key, value) {
									$('select[name="boekingPost"]').append('<option value="'+ key +'">'+ value +'</option>');
								});
							var postIdEl = document.getElementById('inputBoekingPostChange')
							postIdEl.value = postId;

							if(postId !== '') {
								$.ajax({
										url: "bin/selects/getSubposten.php",
										dataType: 'Json',
										data: {'id':postId},
										success: function(data) {
											$('select[name="boekingSubpost"]').empty();
											$('select[name="boekingSubpost"]').append('<option value=""></option>');
											if(data && data !="") {
												$("div#addSubpostChange").show();
												document.getElementById("inputBoekingSubpostChange").required = true;
												$.each(data, function(key, value) {
													$('select[name="boekingSubpost"]').append('<option value="'+ key +'">'+ value +'</option>');
												});
											var subpostIdEl = document.getElementById('inputBoekingSubpostChange')
											subpostIdEl.value = subpostId;												
											}
										}
									});
								} else { 
									$('select[name="boekingSubpost"]').empty();
									$("div#addSubpostChange").hide();
								}							
							}
						});
					} else { 			
						$('select[name="boekingPost"]').empty();
						$('select[name="boekingSubpost"]').empty();
					}
				}
		    });
		});

// ---   Editeerbare tabel boekingen   ---
// ---------------------------------------
	$(document).ready(function(){
	  $('td.editable-col').on('focusout', function() {
		if($(this).attr('col-calc') !== '999999' && $(this).text().trim() != '') {
			if (!isValid($(this).text().trim())) {
				$(this).addClass('cellError'); // Add error 
				return false;
			  } else {
				$(this).removeClass('cellError'); // Remove if it became valid
			  }
			}

		data = {};
		data['val'] = $(this).text();
		data['id'] = $(this).parent('tr').attr('data-row-id');
		data['index'] = $(this).attr('col-index');
		  if($(this).attr('oldVal') === data['val'])
		return false;
		
		var tdElem = $(this);
		var oldValue = data['val'];
		
		if(data['val'] === '')
			data['val'] = 0;
								
		$.ajax({
			  type: "POST",  
			  url: "bin/pages/changeBoekingTD.php",  
			  cache:false,  
			  data: data,
			  dataType: "json",       
			  success: function(response)  
			  {
				//$("#loading").hide();
				if(response.status) {
					tdElem.attr('oldVal', oldValue);
				  // $("#msg").removeClass('alert-danger');
				  // $("#msg").addClass('alert-success').html(response.msg);
				} else {
					console.log(response.msg);
				  // $("#msg").removeClass('alert-success');
				  // $("#msg").addClass('alert-danger').html(response.msg);
				}
			  }   
			});

		   var calcId = $(this).attr('col-calc');
		   var calcKey = $(this).attr('col-key');
		   var calculated_total_sum = 0;

// Bereken de totalen na een wijziging
		   $("#addBoekingenTable .txtCal").each(function () {
			   if($(this).attr('col-calc') == calcId && $(this).attr('col-key') == calcKey) {;
			   var get_textbox_value = $(this).text();
			   if ($.isNumeric(get_textbox_value)) {
				  calculated_total_sum += parseFloat(get_textbox_value);
				  } 
			    }                 
				});
				
				if($(this).attr('col-calc') !== '999999') {
					var myVar;
					myVar = 'totalValue' + calcId + calcKey;
					
					let USDollar = new Intl.NumberFormat('nl-BE', { style: 'currency', currency: 'EUR', });
					document.getElementById(myVar).innerHTML = USDollar.format(calculated_total_sum);
				}
		});
	});	



// ---   Rekeningen overdracht   ---
// ---------------------------------
// Rekeningen overdracht toggle
	function rekeningVanChange(id, amount, rekId) {
		document.getElementById('rekeningNaarHidden').value = '';
		
		for(let i=1; i<=amount; i++) {
			var element = 'labelRekeningNaar' + i;
			document.getElementById(element).classList.remove("btn-secondary");
			document.getElementById(element).classList.remove("active");
			document.getElementById(element).classList.add("btn-primary");

			var element = 'rekeningNaar' + i;
			document.getElementById(element).disabled = false;
			}
		
		var element = 'labelRekeningNaar' + id;
		document.getElementById(element).classList.remove("btn-primary");
		document.getElementById(element).classList.add("btn-secondary");

		var element = 'rekeningNaar' + id;
		document.getElementById(element).disabled = true;
		
		document.getElementById('rekeningVanHidden').value = rekId;
	}	

	function rekeningNaarChange(rekId) {
		document.getElementById('rekeningNaarHidden').value = rekId;
	}	
	
// Rekeningen overdracht check bedrag	
		$(document).ready(function(){
		  $('#rekeningNaarBedrag').on('focusout', function() {
			if (!isValid($(this).val().trim())) {
				$(this).addClass('cellError'); // Add error 
				$("#rekeningOverdrachtSubmit"). attr("disabled", true);
				return false;
			  } else {
				$(this).removeClass('cellError'); // Remove if it became valid
				$("#rekeningOverdrachtSubmit"). attr("disabled", false);
			  }
			});
		});		