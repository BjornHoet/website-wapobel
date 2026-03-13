<script>
		$("#selectWatering").change(function() {
			var wateringId = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeWatering.php", { wateringId: wateringId }, function(response) {
				// Log the response to the console
				});
			currentWatering = wateringId;
			location.reload();
		});			

		$("#selectMaand").change(function() {
			var maand = $(this).val();
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
				type: "post",
				data: { maand: maand }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				currentMonth = maand;
			});
		});

		$("#selectJaar").change(function() {
			var jaar = $(this).val();
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeJaar.php",
				type: "post",
				data: { jaar: jaar }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				currentYear = jaar;
				window.location.href = window.location.href;
				location.reload(true);
			});
		});

		$('#verversBillit').click(function () {
			$.showLoader({ message: 'De gegevens worden geladen…' });
					request = $.ajax({
						url: "../bin/selects/refreshBillit.php",
						type: "post"
					});
					// callback handler that will be called on success
					request.done(function (response, textStatus, jqXHR){
						// log a message to the console
						window.location.href = window.location.href;
						location.reload(true);
					});
			});	
</script>