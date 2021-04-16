<option value="">Escolha</option>
<?php if (isset($this->view->dados->cidades) && is_array($this->view->dados->cidades)) : ?>
    <?php foreach ($this->view->dados->cidades as $registro) : ?>
        <option value="<?php echo $registro->descricao; ?>"><?php echo $registro->descricao; ?></option>
    <?php endforeach; ?>
<?php endif; ?>