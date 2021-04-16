<option value="">Escolha</option>
<?php foreach($defeito as $indice => $registro) : ?>
	<option value="<?php echo $registro->otdoid; ?>"><?php echo utf8_encode($registro->otddescricao); ?></option>
<?php endforeach;?>