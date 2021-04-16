<?php
@$contratos_nao_encontrados = substr($resultado,0,9);

if ($contratos_nao_encontrados == "Contratos" ) { ?>
	<table class="tableMoldura">
		<tr class="tableRodapeModelo1">
			<td colspan="4" align="center">
				<h2><?=$resultado?></h2>
            </td>
        </tr>
    </table>
<?php    
}

if(@pg_num_rows($resultado) > 0){ ?>
	<div id="" class="" >
        <div align="center">
            <form name="mitc_form" id="mitc_form" class="form" method="post" action="man_insere_texto_contrato.php" enctype="multipart/form-data">
                <input type="hidden" name="contratos" id="contratos" value="<?php echo($_POST['contratos']); ?>">
	        <ul class="ul_containner">
    		    <li class="ul_containner_titulo"><h1>Insere Texto no histórico dos contratos</h1></li>
                    <ul class="ul_content">
			    <li class="li_content_titulo"><h2>Inserir</h2></li>
	                    <li style="width: 50%;padding: 7px;"><p>Informe o texto que será inserido nos contratos.</p></li>
        		    <li style="width: 100%;">
		            <textarea name="texto_historico" id="texto_historico" rows="5" style="width: 95%;margin: 5px;float: center;"></textarea>
            		    <li class="li_content_rodape">
	                        <input type="submit" id="pesquisar" class="botao" value="Inserir"  style="width:90px; display: block;">
	                        <input type="hidden" name="acao" value="I">
        		    </li>
                    </ul>
                </ul>
            </form>
        </div>
<?php }else{ ?>
        <form name="form_retorna" id="form_retorna" class="form" method="post" action="man_insere_texto_contrato.php" enctype="multipart/form-data">
            <input type="hidden" name="contratos" id="contratos" value="<?php echo($_POST['contratos']); ?>">
		<table class="tableMoldura">
    	            <li class="li_content_rodape">
        	        <input type="submit" id="retornar" class="botao" value="Retornar"  style="width:90px; display: block;">
            	        <input type="hidden" name="acao" value="">
                    </li>
		</table>
	</form>
<?php } ?>
