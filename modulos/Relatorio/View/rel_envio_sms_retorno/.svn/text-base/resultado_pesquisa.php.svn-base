	<div class="separador"></div>
    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
    	<!-- 
		<div class="conteudo">
			<fieldset>
				<legend>Legenda Resultado Envio SMS</legend>
				<ul class="legenda">
					<li><img alt="Item" src="images/apr_bom.gif"> Sucesso</li>
					<li><img alt="Item" src="images/apr_ruim.gif"> Insucesso</li>
				</ul>
			</fieldset>
		</div>
		 -->
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro">Dt Agenda</th>
                    	<th class="centro">Dt Envio SMS</th>
                        <th class="centro">Nº O.S.</th>
						<th class="centro">Tipo da O.S.</th>
                        <th class="medio centro">Cliente</th>
                    	<th class="centro">Placa</th>
                    	<th class="centro">Nº Celular</th>
                        <th class="menor centro">Cód. Cancelamento</th>
                    </tr> 
                </thead>
                <tbody>
                    <?php
                        foreach ($this->view->dados as $dados):
                        $cor = ($cor=="par") ? "impar" : "par";
                    ?>
                        <tr class="<?php echo $cor; ?>">
                            <td class="centro"  ><?php echo $dados->dt_agenda; ?></td>
                            <td class="centro"  ><?php echo $dados->dt_envio; ?></td>
                            <td class="direita" ><?php echo $dados->ordoid; ?></td>
							<td class="direita" ><?php echo $dados->ostdescricao; ?></td>
                            <td class="esquerda"><?php echo $dados->clinome; ?></td>
                            <td class="esquerda"><?php echo $dados->veiplaca; ?></td>
                            <td class="esquerda"><?php echo $dados->hsetelefone; ?></td>
                            <td class="esquerda"><?php echo $dados->hsecodigo_retorno; ?></td>
                        </tr>
                    <?php
                        endForeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8">
                            <?php
                                if($this->view->totalResultados == "1"){
                                    echo "1 registro encontrado.";
                                } else{
                                    echo $this->view->totalResultados . " registros encontrados.";
                                }
                            ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>	