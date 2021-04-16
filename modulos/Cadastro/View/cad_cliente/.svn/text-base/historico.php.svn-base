<div class="bloco_titulo">Históricos Cadastrados</div>
<div class="bloco_conteudo">

 <?php
 	if ($this->clioid != '')
 	{
		$historico = $this->getHistorico($this->clioid);
		$numhistorico = count($historico);
	} else {
		$numhistorico = 0;
	}
	if ($numhistorico > 0) {
    ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Tipo</th>
                    <th>Observação</th>
                    <th>Usuário</th>
                </tr>
            </thead>
            <tbody>
                <?
                	$cor = 'par';
                    foreach ($historico as $his)
                    {
                        $cor = ($cor=="par") ? "" : "par";?>
                                <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
                                    <td><?php echo $his['dt_cadastro']; ?></td>
                                    <td><?php echo $his['tipo']; ?></td>
                                    <td><?php echo $his['clihalteracao']; ?></td>
                                    <td><?php echo $his['login']; ?></td>
                                </tr>
                                <?
                    }
                ?>            								
            </tbody>
        </table>
    </div>
    <?
    }
    ?>
</div>
<?php
if ($numhistorico > 0 ) { ?>
	<div class="bloco_acoes"><p><?php echo $this->getMensagemTotalRegistros(count($historico));?></p></div>
<?php } else { ?>
<div class="bloco_acoes"><p>Nenhum Resultado Encontrado.</p></div>
<?php } ?>
<div class="bloco_acoes">
    <button id="buttonVoltar" onclick="window.location.href='cad_cliente.php'" name="buttonVoltar" value="Voltar" type="button">Voltar</button>
    <? if($this->retCliente!=""){ ?>
    		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
    <? } ?>
</div>