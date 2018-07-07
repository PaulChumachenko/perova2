<h2><?php echo 'Оплата наличными.' ?></h2>
<div class="well well-sm">
  <p><?php echo $payable; ?></p>
    <p><?php echo 'Режим работы:
Пн-Вс с 9:00 до 17:30' ?></p>

<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1269.360370659435!2d30.590206368871165!3d50.48354235115606!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTDCsDI5JzAwLjgiTiAzMMKwMzUnMjguNiJF!5e0!3m2!1sru!2sua!4v1528558964598" width="100%" height="300px" frameborder="1" style="border:0" allowfullscreen=""></iframe>





</div>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').on('click', function() {
	$.ajax({
		type: 'get',
		url: 'index.php?route=payment/cheque/confirm',
		cache: false,
		beforeSend: function() {
			$('#button-confirm').button('loading');
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},
		success: function() {
			location = '<?php echo $continue; ?>';
		}
	});
});
//--></script>