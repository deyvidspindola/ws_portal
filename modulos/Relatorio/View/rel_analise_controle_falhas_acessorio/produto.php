<option value="">Escolha</option>
<?php foreach($produto as $indice => $registro) : ?>
	<option value="<?php echo $registro->prdoid; ?>"><?php echo utf8_encode($registro->prdproduto); ?></option>
<?php endforeach;?>