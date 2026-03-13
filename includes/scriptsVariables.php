<script>
	var currentMonth = Number('<?php echo $_SESSION['wateringMaand'] ?>');
	var currentYear = Number('<?php echo $_SESSION['wateringJaar'] ?>');
	var currentWatering = Number('<?php echo $_SESSION['wateringId'] ?>');
	var enableBillit = <?php echo $wateringData['enableBillit'] ? 'true' : 'false'; ?>;
    if (enableBillit) {
        $("#billitEnabled").show();
    } else {
        $("#billitEnabled").hide();
    }
	$("#wateringOmschrijving").text('<?php echo $wateringData['omschrijving'] ?>');
</script>