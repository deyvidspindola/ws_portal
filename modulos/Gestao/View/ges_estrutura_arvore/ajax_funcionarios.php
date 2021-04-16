<option value="">Escolha</option>
<?php if (isset($this->view->funcionarios) && is_array($this->view->funcionarios)) : ?>
    <?php foreach ($this->view->funcionarios as $registro) : ?>
        <option value="<?php echo $registro->funoid; ?>"><?php echo $registro->funcionario; ?></option>
    <?php endforeach; ?>
<?php endif; ?>