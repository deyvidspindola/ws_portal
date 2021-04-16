<form id="form_pesquisa"  method="post" action="rel_seriais_lancados.php">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>

    <div class="bloco_titulo">Dados da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            
            <div class="campo data periodo">
                <div class="inicial">
                    <label for="data_inicial">Período *</label>
                    <input id="data_inicial" type="text" name="data_inicial" maxlength="10" value="<?php echo $this->view->parametros->data_inicial;?>" class="campo">
                </div>

                <div class="campo label-periodo">a</div>

                <div class="final">
                    <label for="data_final">&nbsp;</label>
                    <input id="data_final" type="text" name="data_final" maxlength="10" value="<?php echo $this->view->parametros->data_final;?>" class="campo">
                </div>
            </div>

            <div class="campo medio">
                <label for="estado">UF</label>
                <select id="estado" name="estado">
                    <option value="">Escolha</option>
                    <?php $estados = $this->retornaEstados();?>
                    <?php if(isset($estados)): ?>
                        <?php foreach ($estados as $row) : ?>
                            <option value="<?=$row["ufuf"];?>"
                                <?php echo ($this->view->parametros->estado == $row['ufuf']) ? 'selected="true"' : ''; ?>><?=$row['ufuf'];?>
                            </option>
                        <?php endforeach;?>
                    <?php endif;?>
                </select>
            </div>

            <div class="clear"></div>

            <div class="campo medio">
                <label for="representante">Representante </label>
                <select id="representante" name="representante">
                    <option value="">Escolha</option>
                    <?php $representantes = $this->retornaRepresentantes(); ?>
                    <?php if(isset($representantes)):?>
                        <?php foreach ($representantes as $row): ?>
                            <option value="<?php echo $row['repoid']; ?>" <?php echo ($this->view->parametros->representante == $row['repoid']) ? 'selected="true"' : '' ; ?>>
                                <?=$row['repnome'];?></option>
                        <?php endforeach;?>     
                    <?php endif;?>                                                               
                </select>
            </div>


            <div class="campo medio">
                <label for="cidade">Cidade</label>
                <select id="cidade" name="cidade">
                    <option value="">Escolha</option>
                    <?php if(isset($this->view->parametros->estado) && $this->view->parametros->estado != ''): ?>
                        <?php $cidades = $this->dao->buscaCidadesSiglaEstado($this->view->parametros->estado); ?>
                        <?php foreach ($cidades as $row): ?>
                            <option value="<?php echo $row['ciddescricao']; ?>" <?php echo ($this->view->parametros->cidade == $row['ciddescricao']) ? 'selected="true"' : '' ; ?>>
                                <?php echo $row['ciddescricao'];?></option>
                        <?php endforeach;?>
                    <?php endif;?>                                                                     
                </select>
            </div>

            <div class="clear"></div>

            <div class="campo medio">
                <label for="tipo_relatorio">Tipo de Relatório *</label>
                <select id="tipo_relatorio" name="tipo_relatorio">
                    <option value="geral" <?php echo ($this->view->parametros->tipo_relatorio == 'geral') ? 'selected="true"' : '' ; ?>>Geral</option>
                    <option value="duplicado" <?php echo ($this->view->parametros->tipo_relatorio == 'duplicado') ? 'selected="true"' : '' ; ?>>Duplicado</option>
                </select>
            </div>

            <div class="clear"></div>
        </div>
    </div>

    <div class="bloco_acoes">
        <button type="submit" id="bt_pesquisar">Pesquisar</button>
        <button id="bt_voltar">Voltar</button>
    </div>
</form>
<?php if (count($this->view->campos) > 0): ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->campos); ?>); 
    });
    </script>
<?php endif; ?>
