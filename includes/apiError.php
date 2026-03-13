<script>
<?php if (!empty($apiError)): ?>
	Swal.fire({
	  icon: 'error',
	  html: '<b>Billit API probleem:</b> <?php echo $apiError ?><br><br>Contacteer het Wapobel team als het probleem blijft aanhouden.',
	  background: '#fff6f6',
	  confirmButtonText: 'Sluiten',
	  confirmButtonColor: '#d33',
	  showClass: { popup: 'animate__animated animate__fadeInDown' },
	  hideClass: { popup: 'animate__animated animate__fadeOutUp' }
	});
<?php endif; ?>
</script>