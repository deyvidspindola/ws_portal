
<style>
div.paginacao {
    margin: 20px;
    padding: 0px;
    text-align: center;
}

div.paginacao ul {
    margin: 0px;
    padding: 0px;
    list-style: none;
}

div.paginacao ul li {
    display: inline-block;
    margin: 0px;
    padding: 5px 10px;
    font-size: 12px;
    border: 1px solid #94adc2;
    background: #ffffff;
}

div.paginacao ul li.atual {
    background: #e6eaee;
}

div.paginacao ul li.texto {
    border: 1px solid #ffffff;
}

div.paginacao ul li a:hover {
    text-decoration: none;
}

</style>
	<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="conteudo">
        <div style="width: 570px; float: left;">
       
                       <fieldset>
                <legend>Legenda</legend>
           
               
                    <div style="float: left;">
                      
                            <img src="./images/apr_neutro.gif">
                            <?php echo utf8_encode("Pendente");?>&nbsp;&nbsp; 
                     
                           <img src="./images/apr_ruim.gif">
                            <?php echo "Não aprovado"; ?>&nbsp;&nbsp;
                        
                            <img src="./images/apr_bom.gif">
                          Aprovado&nbsp;&nbsp;
                        
                               <img src="./images/apr_roxo.gif">
                           Em andamento&nbsp;&nbsp;
                            
                            <img src="./images/apr_excluido.gif">
                            Finalizado&nbsp;&nbsp;
            
                            <img src="./images/apr_azul.gif">
                            Concluido&nbsp;&nbsp;
                            
                            <img src="./images/apr_cinza.gif">
                            Cancelado
                            
                       
                    </div>
                 
            
            </fieldset>
        </div>
    </div>
    <div class="clear"></div>
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="mini">Proposta</th>
                    <th class="medio">Cliente</th>
                    <th class="medio">Data de Cadastro</th>
                    <th class="mini">Status Serasa</th>
                     <th class="mini">Status Transferencia Divida</th>
                     <th class="mini">Status Proposta</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $row) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                         <tr class="<?php echo $classeLinha; ?>">
                            <td class="direita"><a href="fin_transferencia_titularidade.php?acao=editar&idProposta=<?php echo $row->ptraoid; ?>"><?php echo $row->ptraoid; ?></a></td>
                            <td class="esquerda"><?php echo $row->clinome; ?></td>
                            <td class="esqueda"><?php echo $row->ptradt_cadastro; ?></td>
                            <td class="centro">
                                <?php
                                switch ($row->ptrasfoid_analise_credito) :
                                case "2":
                                	echo '<img src="./images/apr_bom.gif" title="Aprovado">';
                                break;
                                case "1":
                                	echo '<img src="./images/apr_neutro.gif" title="Pendente">';
                               break;
                                case "3":
                                	echo '<img src="./images/apr_ruim.gif" title="Não Aprovado">';
                                break; 
                                	endswitch;
                            ?>
                            </td>
                            <td class="centro">
                                <?php
                            switch ($row->ptrasfoid_analise_divida) :
                                case "2":
                                	echo '<img src="./images/apr_bom.gif" title="Aprovado">';
                                break;
                                case "1":
                                	echo '<img src="./images/apr_neutro.gif" title="Pendente">';
                               break;
                                case "3":
                                	echo '<img src="./images/apr_ruim.gif" title="Não Aprovado">';
                                break;
                            endswitch;
                            ?></td>
                            <td class="centro">
                            <?php
                          
                           switch (trim($row->ptrastatus_conclusao_proposta)) :
                                case "F":
                                    echo '<img src="./images/apr_excluido.gif" title="Finalizada">';
                                    break;
                                case "A":
                                    echo '<img src="./images/apr_roxo.gif" title="Em andamento">';
                                    break;
                                case "C":
                                    echo '<img src="./images/apr_azul.gif" title="Concluido">';
                                    break;
                                case "CA":
                                    echo '<img src="./images/apr_cinza.gif" title="Cancelado">';
                                    break;
                            endswitch;
              
                            ?>
                            </td>
                        </tr>
                        
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php echo $this->view->paginacao; ?>

	   <table style="width: 100%">
		<tr>
			<td align="center" >
			<button type="button" id="gera_csv">Exportar CSV</button> 
			</td>
		</tr>
			<tr>
			<td align="center" >
			<div id="arquivo_csv"></div>
			<div id="download_csv">
            <a id="download_csv_link" title=Downloads target="_blank" href=""><img src="images/icones/t3/caixa2.jpg"><br>Download do arquivo CSV</a>
			</div>
			</td>
		</tr>
		</tbody><tfoot><tr class='center'><td align='center' colspan='5'></td></tr></tfoot>
		</table>
		
</div>