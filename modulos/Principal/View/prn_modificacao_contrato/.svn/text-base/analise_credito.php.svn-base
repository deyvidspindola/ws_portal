 <div class="bloco_titulo"></div>
    <div class="bloco_conteudo">

        <div class="separador"></div>
        <div class="mensagem alerta <?php echo (count($this->view->parametros->dadosAnalise) > 0) ? 'invisivel' : '' ;?>">
             Nenhum registro encontrado.
        </div>
        <div class="bloco_titulo">Análise de Créditos Pendentes</div>
        <form id="form_analise_credito" method="post" action="">
            <input type="hidden" id="acao" name="acao" value="atualizarAnaliseCredito">
        <div class="bloco_conteudo">

            <div class="separador"></div>

                <div class="<?php echo (count($this->view->parametros->dadosAnalise) > 0) ? '' : 'invisivel' ?>">
                    <div class="bloco_titulo">Crédito Pendentes</div>
                    <div class="bloco_conteudo">
                    	<div class="listagem">

                                <table>
                                    <thead>
                                        <tr>
                                            <th class="selecao"><input id="selecao_todos_analise" type="checkbox" data-bloco="analise" /></th>
                                            <th class="maior centro">Cliente</th>
                                            <th class="menor centro">Data</th>
                                            <th class="maior centro">Observação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $totalRegistros = 0;
                                            foreach ($this->view->parametros->dadosAnalise as $dados):
                                                $cor = ($cor=="par") ? "impar" : "par";
                                        ?>
                                        <tr class="<?php echo $cor; ?>">
                                            <td class="centro"><input id="<?php echo $dados->cmacoid ?>" type="checkbox" value="<?php echo $dados->cmacoid ?>" data-bloco="analise" name="opcao[]" /></td>
                                            <td class="esquerda"><?php echo $dados->clinome ?></a></td>
                                            <td class="centro"><?php echo date('d/m/Y',strtotime($dados->cmacdt_cadastro)) ?></td>
                                            <td class="esquerda"><?php echo $dados->cmacobservacao ?></td>
                                        </tr>
                                        <?php
                                                $totalRegistros++;
                                            endForeach;
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <?php
                                                    if($totalRegistros == "1") {
                                                        echo "1 registro encontrado.";
                                                    } else {
                                                        echo $totalRegistros . " registros encontrados.";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                        </div>
                    </div>
                    <div class="separador"></div>
                </div>

                <div class="bloco_titulo">Dados da Aprovação</div>
                <div class="bloco_conteudo">
                	<div class="formulario">
                		 <div class="campo medio">
    	                    <label for="combo_status">Status *</label>
    	                    <select id="combo_status" name="combo_status">
    	                        <option value="">Escolha</option>
    	                        <option value="A" <?php echo ($this->view->parametros->combo_status == 'A') ?  'selected="true"' : ''?>>Crédito Aprovado</option>
    	                        <option value="N"  <?php echo ($this->view->parametros->combo_status == 'N') ?  'selected="true"' : ''?>>Crédito Não Aprovado</option>
    	                    </select>
                   		 </div>
                   		 <div class="campo data">
                            <label for="data">Liberado até</label>
                           	<input id="campo_liberacao" name="campo_liberacao" class="campo" type="text"
                            <?php echo ($this->view->parametros->combo_status == 'N') ?  'disabled="true"' : ''?>
                            value="<?php echo $this->view->parametros->campo_liberacao; ?>">
                    	 </div>
                    	 <div class="campo">
                            <label>&nbsp;</label>
                            <input id="check_periodo" name="check_periodo" value="t"
                             <?php echo ($this->view->parametros->combo_status == 'N') ?  'disabled="true"' : ''?>
                            <?php echo ($this->view->parametros->check_periodo == 't') ?  'checked="true"' : ''?> class="campo" type="checkbox">
                            <label for="texto" class="campo">&nbsp; Período Indeterminado</label>
                    	 </div>
                   		 <div class="clear"></div>
                   		 <div class="campo maior">
                            <label for="motivo">Motivo *</label>
                            <textarea id="motivo" name="motivo"><?php echo $this->view->parametros->motivo?></textarea>
                        </div>
                        <div class="clear"></div>
                	</div>
                </div>

            <div class="separador"></div>
        </div>
        </form>
         <div class="bloco_acoes">
	        <button type="button" id="btn_confirmar_analise"  disabled="true" class="desabilitado">Confirmar</button>
	    </div>
	    <div class="separador"></div>
    </div>
 <div class="bloco_acoes">
            <button type="button" id="btn_voltar">Voltar</button>
    </div>