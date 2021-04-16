<?php include_once '_header.php'; ?>
<script type="text/javascript" src="modulos/web/js/fin_importar_custo_medio_produto.js" charset="utf-8"></script>

<script>
    function confirmaExclusao(usu,ref,dt)
    {
       var confirma = confirm('Deseja realmente excluir este arquivo?');
       
       if(confirma === true)
       {
           window.location.href = "importar_custo_medio_produto.php?acao=excluiRegistro&usu="+ usu +"&ref="+ ref +"&dt="+ dt +"&val=true";
       } else {
          window.location.href = "importar_custo_medio_produto.php?"; 
       }
    }
</script>
<head>
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
</head>

<form name="frm_importar" id="frm_importar" method="POST" action="importar_custo_medio_produto.php?acao=telaImportacao" enctype="multipart/form-data">
    <div class="modulo_titulo">Importação Custo Médio Materiais</div>
    <div class="modulo_conteudo">

        <?php include_once '_msgPrincipal.php'; ?>
        
<div id="mensagem_info" class="mensagem info">Campos com * são obrigatórios.</div>

<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemErro; ?>
</div>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>

        <div class="bloco_titulo">Dados para Importação</div>
        <div class="bloco_conteudo">
            <div class="formulario"> 
			 <div class="campo mes_ano">
				<label for="mes_ano">Mês/ Ano *</label>
				<input id="mes_ano" name="mes_ano" maxlength="10" value="" class="campo" type="text">

			</div>
                <div class="campo maior">					
                    <label for="arquivo_csv">Arquivo *</label>
                    <input type="file" id="arquivo_csv" name="arquivo_csv" />
                </div>					
                <div class="clear"></div>				

            </div>				
        </div>

        <div class="bloco_acoes">
            <button type="submit" id="importar">Importar</button>
        </div>           
<?php 
if($this->view->download != null) {?>
		<div class="separador"></div>
        <div class="bloco_titulo">Download</div>
    <div class="bloco_conteudo">
        <div class="listagem">
         <table style="width: 100%">
			<tr>
			<td align="center" >
			<div id="arquivo_csv"></div>
			<div id="download_csv">
            <a id="download_csv_link" title=Downloads target="_blank" href="download.php?arquivo=<?php echo $this->view->download?>"><img src="images/icones/t3/caixa2.jpg"><br><?php echo "erro_importacao_custo_medio.csv";?></a>
			</div>
			</td>
		</tr>
		</tbody><tfoot><tr class='center'><td align='center' colspan='5'></td></tr></tfoot>
		</table>
        </div>
    </div>
 <?php }?> 
 
        <div class="separador"></div>
    <div class="bloco_titulo">Arquivos Importados</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro">Data Importação</th>
                        <th class="maior centro">Usuário</th>
                        <th class="centro">Mês/ Ano Referência</th>
                        <th class="centro">Ação</th>
                    </tr>
                </thead>
                <tbody>
          <?php  
                if(count($this->view->arquivos) > 0){
               	 $classeLinha = "par";
                
                foreach ($this->view->arquivos as $key) :
               			$classeLinha = ($classeLinha == "") ? "par" : "";
                		$dataRef = date("d/m/Y ", strtotime($key->pcmdt_cadastro));
             
               		?>
                    <tr class="<?php echo $classeLinha; ?>">
                        <td class="centro"><?=$dataRef?></td>
                    	<td class="esquerda"><?=$key->nm_usuario?></td>
                    	<td class="centro"><?=$key->pcmdt_referencia?></td>
                        <td class="centro">
                            <a id="download_csv_registros" title=Download target="_blank" href="importar_custo_medio_produto.php?acao=gerarCSVdoRegistro&usu=<?php echo $key->pcmusuoid ?>&dt=<?php echo $key->pcmdt_cadastro; ?>"><?php if($classeLinha == "") echo "<img src='images/icones/t2/caixa2.jpg'>"; else echo "<img src='images/icones/tf2/caixa2.jpg'>"; ?></a>
                            <a id="excluir_registro" onclick="confirmaExclusao('<?php echo $key->pcmusuoid; ?>','<?php echo $key->pcmdt_referencia;?>','<?php echo $key->pcmdt_cadastro; ?>')" title=Excluir target="_self"><?php if($classeLinha == "") echo "<img src='images/icones/t2/error.jpg'>"; else echo "<img src='images/icones/tf2/error.jpg'>"; ?></a>
                        </td>
                    </tr>
                    
                <?php 
               			endforeach;
               		} ?>
                </tbody>
					       <tfoot>
                         <tr>
                    <td colspan="6" class="centro">
                        <?php
                        $totalRegistros = count($this->view->arquivos);
			
						if($totalRegistros <= 0){
							echo "Nenhum registro encontrado.";
						}else if($totalRegistros == 1){
							echo  $totalRegistros . " registro encontrado.";
						}else{
							echo  $totalRegistros . " registros encontrados.";
						}
                        
                        ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
 <!--   <?php //echo $this->view->paginacao; ?>-->  
    
</div>

    </div>

</form>

