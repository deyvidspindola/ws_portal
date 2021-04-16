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
                    <th id="th_data_chegada" class="menor">Data Chegada</th>
                    <!--<th id="th_acao" class="mini" style="width: 80px;">Ação</th>-->
                </tr>
            </thead>
            <tbody>
            <?php
                $classeLinha = "par";
                if (count($this->view->produtos->reservados) > 0) : ?>
                	<input type="hidden" name="id_agendamento" value="<?php echo $this->view->id_reserva_agendamento ?>">
    	            <?php foreach($this->view->produtos->reservados as $produto):
    	                    $classeLinha = ($classeLinha == "") ? "par" : "";
    	            ?>
                    <tr class="<?php echo $classeLinha; ?>">
                        <td><?php echo $produto['descricao_produto']; ?></td>
                        <td class="centro"><?php echo $produto['quantidade_disponivel']; ?></td>
                        <td class="centro"><?php echo $produto['quantidade_transito']; ?></td>
                        <td class="centro"><?php echo $produto['remessa']; ?></td>
                        <td class="centro"><?php echo $produto['data_chegada']; ?></td>
                    </tr>

				<?php endForeach; ?>
				<?php endIf; ?>
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