<script>
<?php
$solicita_unidades_estoque = "";
if ($_SESSION['funcao']['solicita_unidades_estoque'] != '1') {
	$solicita_unidades_estoque = "desabilitado";
} ?>
</script>
<div class="separador"></div>
<div class="bloco_titulo">Produtos Reservados</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th id="th_produto">Produto</th>
                    <th id="th_estoque" class="menor">Em Estoque</th>
                    <th id="th_transito" class="menor">Em Trânsito</th>
                    <th id="th_remessa" class="menor">Remessa</th>
                    <!--<th id="th_acao" class="mini" style="width: 80px;">Ação</th>-->
                </tr>
            </thead>
            <tbody>
            <?php
          
                $classeLinha = "par";
                if ($this->view->produtos->sessao) : ?>
                	<input type="hidden" name="id_agendamento" value="<?php echo $this->view->id_reserva_agendamento ?>">
	            <?php foreach($this->view->produtos->sessao as $produto):
	                    $classeLinha = ($classeLinha == "") ? "par" : "";
                        $permissaoSolicitarMaisProdutos = isset($_SESSION['funcao']['solicita_unidades_estoque']) ? $_SESSION['funcao']['solicita_unidades_estoque'] : 0;
                        $desabilitadorCampoEstoque = array();
                        $desabilitadorCampoEstoque['classe'] = '';
                        $desabilitadorCampoEstoque['leitura'] = '';
                        $desabilitadorCampoTransito = array();
                        $desabilitadorCampoTransito['classe'] = '';
                        $desabilitadorCampoTransito['leitura'] = '';
                        if ($produto['quantidade_disponivel'] <= 0 || !$permissaoSolicitarMaisProdutos){
                            $desabilitadorCampoEstoque['classe'] = 'desabilitado';
                            $desabilitadorCampoEstoque['leitura'] = 'readonly="readonly"';
                        }
                        if ($produto['quantidade_transito'] <= 0 || !$permissaoSolicitarMaisProdutos){
                            $desabilitadorCampoTransito['classe'] = 'desabilitado';
                            $desabilitadorCampoTransito['leitura'] = 'readonly="readonly"';
                        }
	            ?>
					<tr class="<?php echo $classeLinha ?>" id="<?php echo $produto['id_produto'] ?>" database-id="<?php echo $produto['id_reserva_agendamento_item'] ?>">
						<input type="hidden" name="reserva[<?php echo $produto['id_produto'] ?>][dbitem]" value="<?php echo $produto['id_reserva_agendamento_item'] ?>">
						<td><?php echo $produto['descricao_produto'] ?></td>
						<td class="centro td_reservado"><?php echo $produto['quantidade_disponivel'] ?>
							<!--<input type="text" value="<?php echo $produto['quantidade_disponivel'] ?>"
                                   name="reserva[<?php echo $produto['id_produto'] ?>][disponivel]"
                                   id="reserva_disponivel_<?php echo $produto['id_produto'] ?>"
                                   class="<?php echo $solicita_unidades_estoque .' '. $desabilitadorCampoEstoque['classe'] ?> campo direita mini somenteNumero parametrizao_qtd reservado_estoque_<?php echo $produto['id_produto'] ?>" <?php echo $desabilitadorCampoEstoque['leitura'] ?>
                                   /> -->
							<input type="hidden" value="<?php echo $produto['quantidade_disponivel_original'] ?>" name="reserva[<?php echo $produto['id_produto'] ?>][disponivelOriginal]" />
						</td>
						<td style="width: 120px;" class="centro"><?php echo $produto['quantidade_transito'] ?>
							<!-- <input type="text" 
                                id="reserva_transito_<?php echo $produto['id_produto'] ?>" 
                                value="<?php echo $produto['quantidade_transito'] ?>" 
                                name="reserva[<?php echo $produto['id_produto'] ?>][transito]" 
                                class="<?php echo $solicita_unidades_estoque.' '. $desabilitadorCampoTransito['classe']?> campo direita mini somenteNumero parametrizao_transito_qtd reservado_transito_<?php echo $produto['id_produto'] ?>" <?php echo $desabilitadorCampoTransito['leitura'] ?>
                                /> -->
							<input type="hidden" value="<?php echo $produto['quantidade_transito_original'] ?>" name="reserva[<?php echo $produto['id_produto'] ?>][transitoOriginal]" />
						</td>
	                   <!-- <td style="width: 100px;" class="centro">
	                    	<?php if($produto['quantidade_transito'] > 0): ?><a href="#">Ver remessa</a> <?php endIf;?>
	                    </td> -->
						<td class="centro">
                          <!--<?php if($produto['btn_cancelar']) : ?>
							<a href="#" class="btnExcluirItem" id="<?php echo $produto['id_produto'] ?>">
								<img src="images/icon_error.png" title="Cancelar Reserva" class="icone" />
							</a>
                           <?php else : ?>
                                ---
                           <?php endif; ?> 
                       -->

						</td>
					</tr>

				<?php endForeach;
				endIf; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="bloco_acoes">
    <!-- <button type="button" id="btn_salvar_reservas">Salvar Reservas</button> -->
    <?php if($this->view->mostrar_btn_cancelar) : ?>
       <button type="button" id="btn_cancelar_reservas">Cancelar Reservas</button>
    <?php endif; ?>
</div>

<div class="clear"></div>

<div id="motivo_cancelamento_form" style="display: none" title="Justificativa obrigatória para cancelamento de reservas">
    <form >
        <input type="hidden" name="ordoid" value="<?php echo $this->param->ordoid; ?>" id="solicitar_produtos_ordoid">

        <div id="cancelamento_nome_produto" class="conteudo" style="margin-left: 23px;">
            <p></p>
        </div>
        <div class="campo maior" style="margin-left: 23px;">
            <label id="lbl_justificativa" for="justificativa" style="cursor: default;">Justificativa *</label>
            <textarea style="width: 380px; height: 120px !important" id="justificativa" name="justificativa"></textarea>
        </div>

        <div class="separador"></div>
    </form>
</div>

<div class="clear"></div>