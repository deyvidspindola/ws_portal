<option value="">Escolha</option>
<?php if (isset($this->view->cargos) && is_array($this->view->cargos)) : ?>
    <?php foreach ($this->view->cargos as $registro) : ?>
        <option value="<?php echo $registro->prhoid; ?>"><?php echo $registro->prhperfil; ?></option>
    <?php endforeach; ?>
<?php endif; ?>