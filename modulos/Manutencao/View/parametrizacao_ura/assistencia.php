<?php require_once '_header.php' ?>
<script type="text/javascript" src="modulos/web/js/man_parametrizacao_ura_assistencia.js"></script>
<form method="post" id="assistencia_form" action="">
	<input type="hidden" name="acao" id="acao" value="" />
	<div class="bloco_titulo">Desconsiderar - Ordem de Serviço</div>
	<div class="bloco_conteudo" style="height: 300px;">
		<div class="conteudo">
			<div class="left">
            	<div class="campo medio">
	                <label>Item:</label>
	                <div class="combo-check" style="height:85px;">
	                    <ul>
	                      <li>
                            <label for="itens_os_a">Acessório</label>
                            <input type="checkbox" id="itens_os_a"
	                                name="itens_os[]"  <?=(in_array("A", $form['itemOS']) ? 'checked="checked"' : "")?>
	                                value="A" />
	                      </li>
		                  <li>
                            <label for="itens_os_e">Equipamento</label>
                            <input type="checkbox" id="itens_os_e"
		                                name="itens_os[]" <?=(in_array("E", $form['itemOS']) ? 'checked="checked"' : "")?>
		                                value="E" />
                          </li>
	                    </ul>
	                </div>
	                <br /><br />
	                <label>Tipo:</label>
	                <div class="combo-check" style="height:85px;">
	                    <ul>
	                    	<?php while($tipo = pg_fetch_array($resOsTipo)) : ?>
	                        <li>
	                            <label for="os_tipo_id_<?php echo $tipo[0]; ?>"><?php echo $tipo[1]; ?></label>
	                            <input type="checkbox" 	id="os_tipo_id_<?php echo $tipo[0]; ?>"
						        name="os_tipo_id[]" <?=(in_array($tipo[0], $form['tipoOS']) ? 'checked="checked"' : "")?>
						        value="<?php echo $tipo[0]; ?>" />
	                        </li>
	                        <?php endwhile; ?>
	                    </ul>
	                </div>
            	</div>
				<div class="campo medio">
 					<label>Defeito Alegado:</label>
	                <div class="combo-check">
	                	<input type="hidden" name="defeitos_carregados_assistencia" id="defeitos_carregados_assistencia" />
	                	<input type="hidden" name="defeitos_marcados" id="defeitos_marcados" value="<?=implode(",", $form['tipoDefeitoOS'])?>"/>
	                    <ul id="ulDefeitos">
                                <? foreach ($tiposDefeitoAlegado as $item): ?>
                                <li>
                                    <label for="pupotdoid_<?= $item['otdoid'] ?>"><?= $item['otddescricao'] ?></label>
                                    <input type="checkbox" id="pupotdoid_<?= $item['otdoid'] ?>" 
                                        name="pupotdoid[]" value="<?= $item['otdoid'] ?>"
                                        <? $this->checkArray($item['otdoid'], $form['tipoDefeitoOS']) ?> />
                                </li>
                                <? endforeach ?>
	                    </ul>
	                </div>
				</div>
		            <div class="campo medio>
		            	<label for="os_agendada_data_posterior" style="float:left;">O.S. agendada com data posterior a data atual:</label>
						<input type="checkbox" name="os_agendada_data_posterior" value="1" <?=($form['puaagenda_posterior']=='t' ? 'checked="checked"' : "")?> />
		            </div>
		             </div>
            <div class="right">
				<div class="campo medio" >
					<label>Ação:</label>
		            <div class="combo-check">
	                    <ul>
	                    	<?php while($acao = pg_fetch_array($resAcaoOS)) : ?>
	                        <li>
	                            <label for="os_acao_id_<?php echo $acao[0]; ?>"><?php echo $acao[1]; ?></label>
	                            <input type="checkbox" id="os_acao_id_<?php echo $acao[0]; ?>"
	                                name="os_acao_id[]" <?=(in_array($acao[0], $form['acaoOS']) ? 'checked="checked"' : "")?>
	                                value="<?php echo $acao[0]; ?>" />
	                        </li>
	                        <?php endwhile; ?>
	                    </ul>
	                </div>
				</div>
                <div class="campo medio">
	                <label>Status:</label>
	                <div class="combo-check">
	                    <ul>
	                    	<?php while($tipo = pg_fetch_array($resOsStatus)) : ?>
	                        <li>
	                            <label for="os_status_id_<?php echo $tipo[0]; ?>"><?php echo $tipo[1]; ?></label>
	                            <input type="checkbox" 	id="os_status_id_<?php echo $tipo[0]; ?>"
		                        name="os_status_id[]" <?=(in_array($tipo[0], $form['statusOS']) ? 'checked="checked"' : "")?>
		                      value="<?php echo $tipo[0]; ?>" />
	                        </li>
	                        <?php endwhile; ?>
						</ul>
	                </div>
            	</div>
            	</div>

			</div>
			<div class="clear"></div>
			<br />
		
		</div>

	<div class="bloco_titulo block-margin">Desconsiderar - Contratos</div>
    <div class="bloco_conteudo" style="height: 300px;">
        <div class="conteudo">
        	<div class="left">
        		<div class="campo medio">
	                <label>Tipo de Contrato:</label>
	                <div class="combo-check">
	                    <ul>
	                       <?php while($tipo = pg_fetch_array($resTipoContrato)) : ?>
	                        <li>
	                            <label for="tipo_contrato_id_<?php echo $tipo[0]; ?>"><?php echo $tipo[1]; ?></label>
	                            <input type="checkbox" 	id="tipo_contrato_id_<?php echo $tipo[0]; ?>"
		                        name="tipo_contrato_id[]" <?=(in_array($tipo[0], $form['tipoContrato']) ? 'checked="checked"' : "")?>
		                        value="<?php echo $tipo[0]; ?>" />
	                        </li>
	                        <?php endwhile; ?>
	                    </ul>
	                </div>
            	</div>
				<div class="campo medio">
	                <label>Status Contrato:</label>
	                <div class="combo-check">
	                    <ul>
	                       <?php while($status = pg_fetch_array($resStatusContrato)) : ?>
	                        <li>
	                            <label for="status_contrato_id_<?php echo $status[0]; ?>"><?php echo $status[1]; ?></label>
	                            <input type="checkbox" 	id="status_contrato_id_<?php echo $status[0]; ?>"
		                                				name="status_contrato_id[]" <?=(in_array($status[0], $form['statusContrato']) ? 'checked="checked"' : "")?>
		                                				value="<?php echo $status[0]; ?>" />
	                        </li>
	                        <?php endwhile; ?>
	                    </ul>
	                </div>
            	</div>
        	</div>
        </div>
        <div class="clear"></div>
	</div>
	<div class="bloco_acoes">
        <button name="botao_salvar_assistencia"  type="button" id="botao_salvar_assistencia"  value="assistenciaSalvar">Salvar</button>
		<button name="botao_salvar_predefinidos" type="button" id="botao_salvar_predefinidos" value="predefinidosSalvar" > Predefinidos</button>
       
 </div>
</form>

<? require_once '_footer.php' ?>