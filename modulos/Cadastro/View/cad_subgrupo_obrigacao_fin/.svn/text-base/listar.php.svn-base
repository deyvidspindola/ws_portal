<!-- Form Pesquisa/Novo -->
<div class="bloco_titulo">Dados para pesquisa</div>
<div class="bloco_conteudo">
	<div class="formulario">
		<div class="campo maior">
			<label>Descrição</label>
			<input type="text" maxlength="50" class="campo descricao-pesquisa">
		</div>
		<div class="clear"></div>
	</div>
</div>
<div class="bloco_acoes">
	<button type="submit" class="pesquisar">Pesquisar</button>
	<a href="cad_subgrupo_obrigacao_fin.php?acao=cadastrar">
		<button>Novo</button>
	</a>
</div>
<br>
<!-- Pesquisa -->
<div class="bloco_titulo">Resultados da pesquisa</div>
<div class="bloco_conteudo" style="overflow-x:hidden;">
<div class="listagem">
	<table id="table-lista-subgrupo">
		<?php if(!empty($this->view->subgrupos)): ?>
		<thead>
			<tr>
				<th class="menor">Código</th>
				<th>Descrição</th>
				<th class="menor">Status</th>
				<th class="acao">Ação</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->view->subgrupos as $key => $subgrupo): ?>
			<tr class="<?php echo ($key + 1) % 2 ? 'impar' : 'par'; ?>">
				<td class="centro"><?php echo $subgrupo->ofsgoid; ?></td>
				<td><?php echo $subgrupo->ofsgdescricao; ?></td>
				<td class="centro"><?php echo $subgrupo->ofsgstatus == 't' ? 'Ativo' : 'Inativo'; ?></td>
				<td class="centro">
					<span>
						<a href="cad_subgrupo_obrigacao_fin.php?acao=editar&id=<?php echo $subgrupo->ofsgoid; ?>">
							<img title="Editar" src="images/edit.png" class="icone">
						</a>
					</span>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4"><?php echo count($this->view->subgrupos); ?> registros encontrados.</td>
			</tr>
		</tfoot>
		<?php else: ?>
		<tfoot>
			<tr>
				<td colspan="4">Nenhum registro encontrado.</td>
			</tr>
		</tfoot>
		<?php endif; ?>
	</table>
</div>
</div>
<div class="separador"></div>

<div class="bloco_titulo" id="bloco_titulo_1" style="border:1px solid #94adc2 !important;" >
	<a href="javascript:void(0)"; onclick="$('#bloco_conteudo_hist_subgrupo').toggle();" >Histórico de Modificações dos Subgrupos</a>
</div>
<div class="bloco_conteudo" id="bloco_conteudo_hist_subgrupo" style="overflow-x:hidden; display: none;" >
	<?php
	if ($this->view->historico){
		?>
		<div class="separador"></div>
		<div class="resultado bloco_conteudo">
			<div id="bloco_itens" class="listagem">
				<table id="tbl_historico" style="margin-top: 0px !important">
					<thead>
						<tr>
							<th class="menor">Código</th>
							<th class="menor">Data / Hora</th>
							<th class="menor">Ação</th>
							<th class="maior">Descrição Antiga</th>
							<th class="maior">Descrição Nova</th>
							<th class="menor">Login</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($this->view->historico as $historico) : ?>
						<?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
						<tr class="<?php echo $classeLinha; ?>" id="<?=$historico->ofsghofsgoid ?>" >
							<td class="centro"><?=$historico->ofsghofsgoid ?></td>
							<td class="centro"><?=date_format(date_create($historico->ofsghdt_alteracao), 'd/m/Y H:i:s'); ?></td>
							<td class="centro"><?=$historico->ofsghacao ?></td>
							<td class="centro"><?=$historico->ofsghdescricao_antiga ?></td>
							<td class="centro"><?=$historico->ofsghdescricao_nova ?></td>
							<td class="centro"><?=$historico->ds_login ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" align="center"><?=count($this->view->historico)?> registro(s) encontrado(s)</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="separador"></div>
	<?php }else{ ?>
		<div class="mensagem info">Nenhuma modificação encontrada!</div>
	<?php } ?>
	<div class="separador"></div>
</div>