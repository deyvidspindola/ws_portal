<div class="bloco_titulo">Benefícios</div>
<div class="bloco_conteudo">
<form action="" method="post" id="form_beneficios">
    <input type="hidden" name="acao" value="excluirBeneficio">
    <input type="hidden" name="clboid" id="clboid">
 <?php
 	if ($this->clioid != '')
 	{
		$beneficio = $this->getBeneficio($this->clioid);
		$numbeneficio = count($beneficio);
	} else {
		$numbeneficio = 0;
	}
	
    ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Benefícios</th>
                    <th>Apólice</th>
                    <th>Item</th>
                    <th>Cartão</th>
                    <th>Validade</th>
                    <th>Excluir</th>
                </tr>
            </thead>
            <tbody>
         <?php  if ($numbeneficio > 0) :
                
                	$cor = 'par';
                    foreach ($beneficio as $ben):
                        $cor = ($cor=="par") ? "" : "par";?>
                            <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
                               <td><?php echo $ben['empresa']; ?></td>
                               <td><?php echo $ben['beneficio']; ?></td>
                               <td><?php echo $ben['apolice']; ?></td>
                               <td><?php echo $ben['item']; ?></td>
                               <td><?php echo $ben['cartao']; ?></td>
                               <td><?php echo $ben['validade']; ?></td>
                               <td class="centro td_acao_excluir">
                                   <a href="javascript:return false;" class="excluirbeneficio"  clboid="<?php echo $ben['clboid']?>" type="button"><img src="images/icon_error.png" /></a>
                               </td>
                            </tr>
               <?php endforeach; ?>              
				
			<?php else : ?>
                	<tr>
			           <td colspan="7">Nenhum Resultado Encontrado.</td>
			        </tr>
            <?php endif; ?>
            
            </tbody>
        </table>
    </div>
</div>
</form>

<div class="bloco_acoes">
  <p><?php echo $this->getMensagemTotalRegistros(count($beneficio));?></p>
</div> 
  
<div class="bloco_acoes">
    <button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Voltar</button>
    <? if($this->retCliente!=""){ ?>
    		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
    <? } ?>
</div>