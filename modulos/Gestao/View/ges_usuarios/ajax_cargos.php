<option value="">Escolha</option>
<option value="todos">Todos</option>
<?php if (isset($this->view->dados->cargos) && is_array($this->view->dados->cargos)) : ?>
    <?php foreach ($this->view->dados->cargos as $registro) : ?>
        <option value="<?php echo $registro->prhoid; ?>"><?php echo $registro->prhperfil; ?></option>
    <?php endforeach; ?>
<?php endif; ?>