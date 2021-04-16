<style type="text/css">

div.campo_ajustado_fieldset, select.campo_ajustado_fieldset{
	width: 279px !important;
	width: 257px !important \9;
}

div.campo_ajustado_fieldset_formula, select.campo_ajustado_fieldset_formula{	
    width: 355px !important;    
    margin: 0;
}


#campo_formula{
	width: 359px;
	width: 356px !important \9;
	height: 86px;
	margin: 0px !important;
	margin-right: -10px !important \9;
}

button.botao_calculo{
	margin-bottom: 7px;
    margin-left: 0;
    margin-top: 9px;
}

#adicionar_responsavel{
	margin-top: 19px;
	margin-left: 0px;
	margin-right: 0px;
    *padding-right: 2px !important;
}

.box-compartilhamento{
	border: 1px solid #999999;
    height: 86px;
    overflow-y: scroll;
    width: 356px;
}

.box-compartilhamento div {
	background: none repeat scroll 0 0 #E7EBEE;
    margin-top: 1px;
    padding-bottom: 1px;
    padding-top: 5px;
    position: relative;
}

.box-compartilhamento .exclui_compartilhamento{
	background: url("images/icones/excluir-funcionario.png") no-repeat scroll center top transparent;
    height: 16px;
    position: absolute;
    right: 0;
    top: -4px;
    width: 16px;
    cursor: pointer;
}

</style>

<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo maior">
            <label id="lbl_gmenome" for="gmenome">Nome da Meta *</label>
            <input id="gmenome" class="campo" type="text" value="<?php echo $this->view->parametros->gmenome ?>" name="gmenome">
        </div>
        

        <div class="clear"></div>

        <div class="campo menor">
            <label id="lbl_gmecodigo" for="gmecodigo">Código *</label>
            <input id="gmecodigo" <?php echo isset($this->view->parametros->somenteLeitura) && $this->view->parametros->somenteLeitura ? 'disabled="disabled"' : '' ?> class="campo" type="text" value="<?php echo $this->view->parametros->gmecodigo ?>" name="gmecodigo">
        </div>

        <div class="campo menor">
            <label id="lbl_gmeano" for="gmeano">Ano de Referência *</label>
            <select <?php echo isset($this->view->parametros->somenteLeitura) && $this->view->parametros->somenteLeitura ? 'disabled="disabled"' : '' ?> id="gmeano" name="gmeano">
            	<?php $ano = 2014; ?>
            	<?php 
            		$anoReferencia = intval(date('Y'));
            		if ($anoReferencia < '2014') {
            			$anoReferencia = 2014;
            		}
            	 ?>
    			<?php $anoMax = $anoReferencia + 1; ?>
    			<?php for ($ano; $ano <= $anoMax; $ano++ ) : ?>
    				<option <?php echo $this->view->parametros->gmeano == $ano ? 'selected="selected"' : '' ?> value="<?php echo $ano ?>"><?php echo $ano ?></option>
    			<?php endfor; ?>
            </select>
        </div>

        <div class="campo menor">
            <label id="lbl_gmetipo" for="gmetipo">Tipo *</label>
            <select <?php echo isset($this->view->parametros->somenteLeitura) && $this->view->parametros->somenteLeitura ? 'disabled="disabled"' : '' ?> id="gmetipo" name="gmetipo">
            	<?php if (isset($this->view->parametros->listarTipos) && !empty($this->view->parametros->listarTipos)) : ?>
    				<?php foreach ($this->view->parametros->listarTipos AS $key => $meta) : ?>
    					<option <?php echo $this->view->parametros->gmetipo == $key ? 'selected="selected"' : '' ?> value="<?php echo $key ?>"><?php echo $meta ?></option>
    				<?php endforeach; ?>
    			<?php endif; ?>
            </select>
        </div>

        <div class="clear"></div>



        <div class="campo maior">
            <label id="lbl_gmefunoid_responsavel" for="gmefunoid_responsavel">Responsável *</label>
            <select id="gmefunoid_responsavel" name="gmefunoid_responsavel">
            	<option value="">-- Escolha --</option>
				<?php if (isset($this->view->parametros->listarFuncionariosCadastro) && !empty($this->view->parametros->listarFuncionariosCadastro)) : ?>
					<?php foreach ($this->view->parametros->listarFuncionariosCadastro AS $funcionario) : ?>
						<option <?php echo $this->view->parametros->gmefunoid_responsavel == $funcionario['id'] ? 'selected="selected"' : '' ?> value="<?php echo $funcionario['id'] ?>"><?php echo $funcionario['label'] ?></option>
					<?php endforeach; ?>    
				<?php endif; ?>
            </select>
            <img class="carregando invisivel" src="images/ajax-loader-circle.gif">
        </div>

        <div class="clear"></div>

        <div class="campo menor">
            <label id="lbl_gmemetrica" for="gmemetrica">Métrica *</label>
            <select id="gmemetrica" name="gmemetrica">
            	<?php if (isset($this->view->parametros->listarMetricas) && !empty($this->view->parametros->listarMetricas)) : ?>
    				<?php foreach ($this->view->parametros->listarMetricas AS $key => $metrica) : ?>
    					<option <?php echo $this->view->parametros->gmemetrica == $key ? 'selected="selected"' : '' ?> value="<?php echo $key ?>"><?php echo $metrica ?></option>
    				<?php endforeach; ?>
    			<?php endif; ?>
            </select>
        </div>
        

        <div class="campo menor">
            <label id="lbl_gmepeso" for="gmepeso">Peso *</label>
            <select id="gmepeso" name="gmepeso">
            	<?php for ($i=0; $i <= 100; $i++) : ?>
            		<option <?php echo $this->view->parametros->gmepeso == $i ? 'selected="selected"' : '' ?> value="<?php echo $i ?>"><?php echo $i ?></option>
            	<?php endfor; ?>
            </select>
        </div>

        <div class="campo menor">
            <label id="lbl_gmeprecisao" for="gmeprecisao">Precisão *</label>
            <select id="gmeprecisao" name="gmeprecisao">
            	<?php for ($i=0; $i <= 4; $i++) : ?>
            		<option <?php echo $this->view->parametros->gmeprecisao == $i ? 'selected="selected"' : '' ?> value="<?php echo $i ?>"><?php echo $i ?></option>
            	<?php endfor; ?>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_gmedirecao" for="gmedirecao">Direção *</label>
            <select id="gmedirecao" name="gmedirecao">
            	<?php if (isset($this->view->parametros->listarDirecoes) && !empty($this->view->parametros->listarDirecoes)) : ?>
    				<?php foreach ($this->view->parametros->listarDirecoes AS $key => $direcao) : ?>
    					<option <?php echo $this->view->parametros->gmedirecao == $key ? 'selected="selected"' : '' ?> value="<?php echo $key ?>"><?php echo $direcao ?></option>
    				<?php endforeach; ?>
    			<?php endif; ?>
            </select>
        </div>        

        <div class="clear"></div>

        <div class="campo menor">
            <label id="lbl_gmelimite" for="gmelimite">Limite *</label>
            <input id="gmelimite" class="campo percentual percentual_fix" maxlength="10" type="text" value="<?php echo $this->view->parametros->gmelimite ?>" name="gmelimite">
        </div>

        <div class="campo menor">
            <label id="lbl_gmelimite_superior" for="gmelimite_superior">Limite Superior *</label>
            <input id="gmelimite_superior" class="campo percentual percentual_fix" maxlength="10" type="text" value="<?php echo $this->view->parametros->gmelimite_superior ?>" name="gmelimite_superior">
        </div>

        <div class="campo menor">
            <label id="lbl_gmelimite_inferior" for="gmelimite_inferior">Limite Inferior *</label>
            <input id="gmelimite_inferior" class="campo percentual percentual_fix" maxlength="10" type="text" value="<?php echo $this->view->parametros->gmelimite_inferior ?>" name="gmelimite_inferior">
        </div>

        <div class="clear"></div>

        <fieldset class="maior">
        	<legend>Compartilhar Metas</legend>

        	<div class="campo campo_ajustado_fieldset">
	            <label id="lbl_compartilhar_metas" for="compartilhar_metas">Responsável</label>
	            <select class="campo_ajustado_fieldset" id="compartilhar_metas" name="compartilhar_metas">
	            	<option value="">Escolha</option>
					<?php if (isset($this->view->parametros->listarFuncionariosCadastroCompartilhamento) && !empty($this->view->parametros->listarFuncionariosCadastroCompartilhamento) && trim($this->view->parametros->gmeoid) != '') : ?>
						<?php foreach ($this->view->parametros->listarFuncionariosCadastroCompartilhamento AS $funcionario) : ?>
							<option value="<?php echo $funcionario['id'] ?>"><?php echo $funcionario['label'] ?></option>
						<?php endforeach; ?>    
					<?php endif; ?>
	            </select>
	            <img class="carregando invisivel" src="images/ajax-loader-circle.gif">
        	</div>

        	<button id="adicionar_responsavel">Adicionar</button>

        	<div class="clear"></div>

        	<div class="box-compartilhamento">
        		<?php $meta_compartilhamento = ""; ?>
        		<?php if (isset($this->view->parametros->compartilhamento)  && !empty($this->view->parametros->compartilhamento)) : ?>
        			<?php foreach ($this->view->parametros->compartilhamento AS $compartilhado) : ?>
        				<?php $meta_compartilhamento .= $compartilhado->gmcfunoid .','; ?>
        				<div data-funcionario='<?php echo $compartilhado->nm_usuario ?>' ><?php echo $compartilhado->nm_usuario ?><span class='exclui_compartilhamento' data-funcionarioid='<?php echo $compartilhado->gmcfunoid ?>'></span></div>
        			<?php endforeach; ?>
        		<?php endif; ?>
        	</div>



        	<input type="hidden" id="meta_compartilhamento" name="meta_compartilhamento" value="<?php echo $meta_compartilhamento ?>" />

        </fieldset>

        <div class="clear"></div>

        <fieldset class="maior maior_fieldset_ajustado">
        	<legend>Cadastrar Fórmula de Cálculo da Meta</legend>

        	<div class="campo campo_ajustado_fieldset_formula">
	            <label id="lbl_compartilhar_metas" for="compartilhar_metas">Indicadores</label>
	            <select class="campo_ajustado_fieldset_formula" id="combo_indicadores" name="compartilhar_metas">
	            	<option value="">Escolha</option>
	            	<?php if (isset($this->view->parametros->listarIndicadores) && !empty($this->view->parametros->listarIndicadores)) : ?>
						<?php foreach ($this->view->parametros->listarIndicadores AS $key => $value) : ?>
							<option value="<?php echo $value['id'] ?>"><?php echo $value['label'] ?></option>
						<?php endforeach; ?>    
					<?php endif; ?>
	            </select>
	            <img class="carregando invisivel" src="images/ajax-loader-circle.gif">
        	</div>

        	<div class="clear"></div>

        	<button class="botao_calculo <?php echo isset($this->view->parametros->formulaSomenteLeitura) && $this->view->parametros->formulaSomenteLeitura ? 'somenteLeitura' : '' ?>" id="adicionar_indicador">Adicionar</button>
        	<button class="botao_calculo <?php echo isset($this->view->parametros->formulaSomenteLeitura) && $this->view->parametros->formulaSomenteLeitura ? 'somenteLeitura' : '' ?>" id="limpar_formula">Limpar</button>

        	<div class="clear"></div>

        	<textarea <?php echo isset($this->view->parametros->formulaSomenteLeitura) && $this->view->parametros->formulaSomenteLeitura ? 'readonly="readonly"' : '' ?> name="gmeformula" id="campo_formula"><?php echo trim($this->view->parametros->gmeformula) ?></textarea>

        </fieldset>

        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_gravar" name="bt_gravar" value="gravar">Confirmar</button>
    <button type="button" id="bt_voltar">Retornar</button>
</div>

<?php if (isset($this->view->parametros->gridIndicadores) && count($this->view->parametros->gridIndicadores) > 0) : ?>

<div class="separador"></div>

<!-- abaixo somente na edição -->

<div class="resultado bloco_titulo">Valores dos indicadores</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Data</th>
                    <th>Código</th>
                    <th>Nome</th>
                    <th class="medio">Valor Previsto</th>
                    <th>Valor Realizado</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php              

                if (count($this->view->parametros->gridIndicadores) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->parametros->gridIndicadores as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">
                                <td class="centro"><?php echo $resultado->data ?></td>
                                <td class="centro"><?php echo $resultado->codigo ?></td>
                                <td class="esquerda"><?php echo $resultado->nome ?></td>
                                <td class="direita"><?php echo $resultado->valor_previsto ?></td>
                                <td class="direita"><?php echo $resultado->valor_realizado ?></td>
                                <td class="centro"><?php echo ($resultado->tipo == 'M') ? 'Meta' : 'Indicador'; ?></td>                                
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="15" class="centro">
                        <?php
                        $totalRegistros = count($this->view->parametros->gridIndicadores);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php endif; ?>

