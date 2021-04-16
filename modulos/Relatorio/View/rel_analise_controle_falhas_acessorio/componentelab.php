<option value="">Escolha</option>
<?php foreach($componenteLab as $indice => $registro) : ?>
	<option value="<?php echo $registro->ifcoid; ?>"><?php echo utf8_encode($registro->ifcdescricao); ?></option>
<?php endforeach;?>