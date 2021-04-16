<div id="alerta_acao_confirmar" class="mensagem alerta invisivel"></div>
<div class="bloco_titulo">Cadastrar Ação</div>
<div class="bloco_conteudo">
	<div class="formulario">
		<input type="hidden" name="id_acao" id="id_acao" value="<? echo (isset($this->param->id_acao)) ? $this->param->id_acao : '';?>" />
        <input type="hidden" name="ano" id="ano" value="<? echo (isset($this->param->ano)) ? $this->param->ano : '';?>"/>
		<div class="campo maior">
			<label for="meta">Título Plano de Ação *</label>

			<?php if ($this->layout->superUsuario) { ?>
				<select id="plano" name="plano">
					<option value="">Escolha</option>

					<?php
					foreach ($this->view->planos as $plano) :
						$selected = ($plano->gploid == $this->param->plano) ? 'selected="selected"' : ''; ?>
						<option value="<?php echo $plano->gploid ?>" <?php echo $selected ?>><?php echo $plano->gplnome ?></option>
					<?php endforeach; ?>

				</select>
			<?php } else {  ?>
				<select disabled="disabled">
					<?php
					foreach ($this->view->planos as $plano) :
						$selected = ($plano->gploid == $this->param->plano) ? 'selected="selected"' : ''; ?>
						<option value="<?php echo $meta->gploid ?>" <?php echo $selected ?>><?php echo $plano->gplnome ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" id="plano" name="plano" value="<?php echo $this->param->plano; ?>" />
			<?php } ?>

		</div>

		<div class="clear"></div>

		<div class="campo maior">
			<label for="nome_acao">Nome Ação *</label>
			<input id="nome_acao" name="nome_acao" value="<?php echo $this->param->nome_acao ?>" class="campo" type="text" maxlength="100" />
		</div>

		<div class="clear"></div>

		<div class="campo maior">
			<label for="combo">Responsável *</label>
			<select id="responsavel" name="responsavel">
				<option value="">Escolha</option>
                <?php if(!empty($this->param->plano)) :?>
				<?php
                    foreach ($this->view->responsaveis as $responsavel) :
                        if (intval($this->param->meta) > 0) {
                            $selected = ($responsavel->funoid == $responsavel->gmefunoid_responsavel) ? 'selected="selected"' : '';
                        }
                        if ($this->param->acao == 'editar' || $this->param->responsavel != '') {

                            $selected = ($responsavel->funoid == $this->param->responsavel) ? 'selected="selected"' : '';

                        } ?>
                        <option value="<?php echo $responsavel->funoid ?>" <?php echo $selected ?>><?php echo $responsavel->funnome ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
			</select>
		</div>

		<div class="clear"></div>

		<div class="campo medio">
			<label for="tipo">Tipo *</label>
			<select id="tipo" name="tipo">
				<option value="">Escolha</option>
				<?php
					foreach ($this->view->arrComboTipo as $chave => $tipo) :

						$selected = ($chave == 'P') ? 'selected="selected"' : '';

						if ($this->param->tipo != '') {
							$selected = ($chave == $this->param->tipo) ? 'selected="selected"' : '';
						}

						if ($this->param->acao == 'editar') {
							$selected = ($chave == $this->param->tipo) ? 'selected="selected"' : '';
						}  ?>
						<option value="<?php echo $chave ?>" <?php echo $selected ?>><?php echo $tipo ?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="clear"></div>

		<div class="campo maior">
			<label for="fato_causa" class="fato_causa">Fato/Causa</label>
			<input id="fato_causa" name="fato_causa" value="<?php echo $this->param->fato_causa ?>" class="campo" type="text" maxlength="200" />
		</div>

		<div class="clear"></div>

		<div class="campo data periodo" style="width: 267px !important;">
	        <div class="inicial">
	            <label for="inicio_previsto">Data Início Previsto *</label>
	            <input type="text" id="inicio_previsto" name="inicio_previsto" value="<?php echo ($this->param->inicio_previsto) ? $this->param->inicio_previsto : '';?>" class="campo" />
	        </div>
	        <div class="final">
	            <label for="fim_previsto">Data Fim Previsto *</label>
	            <input type="text" id="fim_previsto" name="fim_previsto" value="<?php echo ($this->param->fim_previsto) ? $this->param->fim_previsto : '';?>" class="campo"/>
	        </div>
	    </div>

		<div class="clear"></div>

		<div class="campo data periodo" style="width: 267px !important;">
	        <div class="inicial">
	            <label for="inicio_realizado">Data Início Realizado</label>
	            <input type="text" id="inicio_realizado" name="inicio_realizado" value="<?php echo ($this->param->inicio_realizado) ? $this->param->inicio_realizado : '';?>" class="campo" />
	        </div>
	        <div class="final">
                <label for="fim_realizado">Data Fim Realizado</label>
                <input type="text" id="fim_realizado" name="fim_realizado"
                       value="<?php echo ($this->param->fim_realizado) ? $this->param->fim_realizado : '';?>"
                       class="campo"
                       />
	        </div>
	    </div>

		<div class="campo menor">
			<label for="percentual">Percentual *</label>
			<input id="percentual" name="percentual" value="<?php echo $this->param->percentual ?>" class="campo" type="text" />
		</div>

		<div class="clear"></div>

		<div class="campo medio">
			<label for="andamento">Andamento</label>
			<select id="andamento" name="andamento" <?php echo ($this->param->bloqueio) ? 'disabled' : '' ; ?>>
				<option value="">Escolha</option>
				<?php
					foreach ($this->view->arrComboAndamento as $chave => $andamento) :

						$selected = ($chave == $this->param->andamento) ? 'selected="selected"' : '';
						 ?>
						<option value="<?php echo $chave ?>" <?php echo $selected ?>><?php echo $andamento ?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="campo medio">
			<label for="motivo_cancelamento" class="motivo_cancelamento">Motivo Cancelamento</label>
			<input id="motivo_cancelamento" name="motivo_cancelamento" value="<?php echo $this->param->motivo_cancelamento ?>" class="campo" type="text" maxlength="200" />
		</div>

		<div class="clear"></div>

		<fieldset class="medio opcoes-inline">
			<legend>Compartilhar</legend>
			<?php
				$checked = (intval($this->param->compartilhar) == 1) ? 'checked="checked"' : '';
			?>
			<input id="compartilhar" name="compartilhar" value="1" type="checkbox" <?php echo $checked ?>>
			<label for="opcao">Compartilhar</label>
		</fieldset>

		<div class="clear"></div>

		<div class="campo medio">
			<label for="status">Status *</label>
			<?php
			$nomeStatus = "";
			if (empty($this->param->status)) {
				$this->param->status = "I";
			}

			foreach ($this->view->arrComboStatus as $chave => $status) :

				if ($this->param->status == $chave) {
					$nomeStatus = $status;
				} 

			endforeach;
			?>
			<input disabled="disabled" name="status_nome" value="<?php echo $nomeStatus ?>">
			<select id="status" class="invisivel" name="status" <?php /*($this->param->bloqueio) ? 'disabled' : '' ;*/ ?>>
				<option value="">Escolha</option>
				<?php
					foreach ($this->view->arrComboStatus as $chave => $status) :

						$selected = ($chave == 'I') ? 'selected="selected"' : '';

						if ($this->param->status != '') {
							$selected = ($chave == $this->param->status) ? 'selected="selected"' : '';
						}

						if ($this->param->acao == 'editar') {
							$selected = ($chave == $this->param->status) ? 'selected="selected"' : '';
						}  ?>
						<option value="<?php echo $chave ?>" <?php echo $selected ?>><?php echo $status ?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="clear"></div>
	</div>
</div>
<div class="bloco_acoes">
	<button type="button" id="bt_confirmar" class="<?php echo ($this->param->bloqueio) ? 'invisivel' : '' ; ?>">
        Confirmar
    </button>
    <?php if (!empty($this->param->plano)): ?>
	<button type="button" id="bt_retornar">
        Retornar
    </button>
    <?php endIf; ?>
</div>

<div class="separador"></div>

<?php if ($this->param->id_acao) :?>
<div class="mensagem alerta invisivel alerta_item_acao"></div>
<div class="mensagem erro invisivel erro_item_acao"></div>

<div class="bloco_titulo">Item da Ação</div>
<div class="bloco_conteudo">
	<div class="formulario ui-sortable">

		<div class="campo maior" style="cursor: default;">
			<label for="descricao_item" style="cursor: default;">Descrição do Item *</label>
			<textarea id="descricao_item" name="descricao_item"></textarea>
		</div>

		<div class="campo menor">
			<label>&nbsp;</label>
			<button type="button" id="bt_adicionar" style="margin-top: 37px !important;">Adicionar</button>
		</div>

		<div class="clear"></div>

		<br />

		<div class="bloco_titulo resultados_tabela invisivel" style="margin: 0px !important; cursor: default;">&nbsp;</div>
		<div class="bloco_conteudo resultados_tabela invisivel" style="margin: 0px !important;">
        <div class="listagem">

            <table>
                <thead>
                    <tr>
                        <th class="menor centro">Data</th>
                    	<th class="maior centro">Descrição</th>
                    	<th class="medio centro">Usuário</th>
                    </tr>
                </thead>
                <tbody class="resultado_item_acao">
                	<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="cursor: default;" class="resultado_encontrado"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="carregando loading_itens invisivel"></div>

	</div>
</div>
<div class="bloco_acoes">&nbsp;</div>
<?php endif; ?>
