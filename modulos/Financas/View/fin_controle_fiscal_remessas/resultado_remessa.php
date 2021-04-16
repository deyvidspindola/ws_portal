<link type="text/css" rel="stylesheet" href="lib/css/style.css"/>
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css"/>
<div class="bloco_titulo">Resultado da Pesquisa por
    <?=$this->view->parametros['tipoRelatorio']=='S'?'Serial': ($this->view->parametros['tipoRelatorio']=='P'?'Produto':'Remessa')?>
</div>
<?

$cols=15;
if($this->view->parametros['tipoRelatorio']=='P') $cols--;
if($this->view->parametros['tipoRelatorio']=='NF') $cols=$cols-4;
?>
<div class="bloco_conteudo">
        <input type="hidden" name="dt_ini" value="<?=$this->view->parametros['dt_ini'];?>">
        <input type="hidden" name="dt_fim" value="<?=$this->view->parametros['dt_fim'];?>">
        <input type="hidden" name="nRemessa" value="<?=$this->view->parametros['nRemessa'];?>">
        <input type="hidden" name="nfRemessa" value="<?=$this->view->parametros['nfRemessa'];?>">
        <input type="hidden" name="tipoRelatorio" value="<?=$this->view->parametros['tipoRelatorio'];?>">
        <input type="hidden" name="tipoMovimentacao" value="<?=$this->view->parametros['tipoMovimentacao'];?>">
        <input type="hidden" name="statusRemessa" value="<?=$this->view->parametros['statusRemessa'];?>">
        <input type="hidden" name="repreRespRem" value="<?=$this->view->parametros['repreRespRem'];?>">
        <input type="hidden" name="repreRespDest" value="<?=$this->view->parametros['repreRespDest'];?>">
        <input type="hidden" name="nSerie" value="<?=$this->view->parametros['nSerie'];?>">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <? if($this->view->parametros['tipoRelatorio']=='S'){?>
                                <th style="centro">S&eacute;rie</th>
                            <?}?>
                            <? if($this->view->parametros['tipoRelatorio']!='NF'){?>
                            <th style="centro">C&oacute;d.</th>
                            <th style="centro">Produto</th>
                            <th style="centro">Qtde</th>
                            <?}?>
                            <th style="centro">Remetende</th>
                            <th style="centro">CNPJ Rem.</th>
                            <th style="centro">UF Rem.</th>
                            <th style="centro">Destinat&aacute;rio</th>
                            <th style="centro">CNPJ Dest.</th>
                            <th style="centro">UF Dest.</th>
                            <th style="centro">N&deg; Rem.</th>
                            <th style="centro">Data Rem.</th>
                            <th style="centro">NF Rem.</th>
                            <th style="centro">N&deg; Pedido</th>
                            <th style="centro">Status</th>
                            
                        </tr>
                    </thead>
                    <tbody>	
                    <?php
                    
                    if($this->view->remessa!==false){                   
                    
                    foreach ( $this->view->remessa as $row){
                    $class = $class == '' ? 'par' : '';
                    ?>						
                        <tr class="<?=$class?>">
                            <? if($this->view->parametros['tipoRelatorio']=='S'){?>
                                <td><?=$row['esrinumero_serie']?></td>
                            <?}?>
                            <? if($this->view->parametros['tipoRelatorio']!='NF'){?>
                                <td><?=$row['prdoid']?></td>
                                <td><?=utf8_encode($row['prdproduto'])?></td>
                                <td><?=$row['quantidade']?></td>
                            <?}?>
                            <td><?=utf8_encode($this->representantes[$row['esrrelroid_emitente']]['nome'])?></td>
                            <td><?=formata_cgc_cpf($this->representantes[$row['esrrelroid_emitente']]['cnpj'])?></td>
                            <td><?=$this->representantes[$row['esrrelroid_emitente']]['uf']?></td>
                            <? if($row['esrforoid']>0){?>
                                <td><?='<strong>Fornecedor: </strong>'.utf8_encode($this->fornecedores[$row['esrforoid']]['nome'])?></td>
                                <td><?=formata_cgc_cpf($this->fornecedores[$row['esrforoid']]['cnpj'])?></td>
                                <td align="center"><?=$this->fornecedores[$row['esrforoid']]['uf']?></td>
                            <?}else{?>
                                <td><?=  utf8_encode($this->representantes[$row['esrrelroid']]['nome'])?></td>
                                <td><?=formata_cgc_cpf($this->representantes[$row['esrrelroid']]['cnpj'])?></td>
                                <td align="center"><?=$this->representantes[$row['esrrelroid']]['uf']?></td>
                            <?}?>
                            <td><?=$row['esroid']?></td>
                            <td><?=$row['data']?></td>
                            <td><?=$row['esrpnfno_numero']?></td>
                            <td><?=$row['esrpnfoid']?></td>
                            <td><?=$row['ersdescricao']?></td>
                            
                            
                        </tr>

                    <?}}?>
                    </tbody>
                    <tfoot>

                        <tr style="center">
                            <td align="center" colspan="<?=$cols?>">
                                     <?=($this->view->remessa!==false) ? sizeof($this->view->remessa) . ' registros encontrados.' : '0 registro encontrado.';?>
                            </td>
                        </tr>

                         <tr style="center">
                            <td align="center" colspan="<?=$cols?>">
                            <button type="button" id="gerarCsv">Gerar CSV</button>
                            </td>
                        </tr>

                    </tfoot>
                </table>
            </div>
</div>
