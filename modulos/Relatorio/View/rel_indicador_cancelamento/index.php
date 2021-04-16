

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_indicador_cancelamento/cabecalho.php"; ?>



<!-- Mensagens-->
<div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>
<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemErro; ?>
</div>


<?php if (isset($this->view->dados['dadosContratos']) && count($this->view->dados['dadosContratos']) == 0 && !$this->view->nadaEncontrado) : ?>
    <div id="mensagem_alerta" class="mensagem alerta">
        Cliente não possui contrato.
    </div>
<?php endif; ?>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>


<form id="form"  method="post" action="rel_indicador_cancelamento.php">
    <input type="hidden" id="acao" name="acao" value=""/>   
    <input type="hidden" id="acao" name="sub_acao" value="pesquisar"/>    
    
    <?php require_once _MODULEDIR_ . "Relatorio/View/rel_indicador_cancelamento/formulario_pesquisa.php"; ?>

</div>


<?php if (count($this->view->dados['dadosContratos']) > 0) : ?>

<div id="area_contratos">

    <img id="grafico"  src="<?php echo $this->view->dados['graficoContratos'] ?>">

    <div id="tabela_grafico">        
        <div class="resultado bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th class="menor">Classe do Termo</th>
                            <th style="width: 35px" >Qtde</th>
                            <th style="width: 35px" >%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($this->view->dados['dadosClasse']) > 0):
                            $classeLinha = "par";
                        ?>

                        <?php foreach ($this->view->dados['dadosClasse'] as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td class=""><?php echo $resultado->classe; ?></td>
                            <td class="direita"><?php echo $resultado->qtd; ?></td>
                            <td class="direita"><?php echo $resultado->porcentagem; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<div id="tabela_grafico" style="margin-right: 1.3%">        
    <div class="resultado bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="menor">Status do Termo</th>
                        <th style="width: 35px" >Qtde</th>
                        <th style="width: 35px" >%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($this->view->dados['dadosContratos']) > 0):
                        $classeLinha = "par";

                    $totalTermos = 0;
                    ?>

                    <?php foreach ($this->view->dados['dadosContratos'] as $resultado) : ?>
                    <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                    <?php $totalTermos +=  $resultado->qtd; ?>
                    
                    <tr class="<?php echo $classeLinha; ?> <?php echo $resultado->destacado ? 'red' : '' ?>">
                        <td class=""><?php echo $resultado->status; ?></td>
                        <td class="direita"><?php echo $resultado->qtd; ?></td>
                        <td class="direita"><?php echo $resultado->porcentagem; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="" class="centro">
                    Total de Termos
                </td>

                <td colspan="" class="direita">
                    <?php echo $totalTermos ?>
                </td>

                <td colspan="" class="centro">

                </td>
            </tr>
        </tfoot>
    </table>
</div>
</div>
</div>
<div class="clear"></div>
</div>

<?php endif; ?>


<?php if (count($this->view->dados['dadosSugestoesReclamacoes']['tabela']) > 0) : ?>
<div id="area_contratos">

    <img id="grafico" src="<?php echo $this->view->dados['graficoSugestoesReclamacoes'] ?>">

    
<div id="tabela_grafico_sugestao">
<div class="resultado bloco_titulo">Reclamações/Sugestões</div>        
    <div class="resultado bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th style="width: 290px" >Tipos</th>
                        <th style="width: 35px" >Motivos</th>
                        <th style="width: 35px" >Qtde</th>
                        <th style="width: 150px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($this->view->dados['dadosSugestoesReclamacoes']['tabela']) > 0):
                        $classeLinha = "par";

                    $totalTermos = 0;
                    ?>

                    <?php foreach ($this->view->dados['dadosSugestoesReclamacoes']['tabela'] as $resultado) : ?>
                    <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                    <?php $totalTermos +=  $resultado->qtd; ?>
                    <tr class="<?php echo $classeLinha; ?>">
                        <td class=""><?php echo $resultado->trsdescricao; ?></td>
                        <td class="centro"><a class="listar_motivos" data-status="<?php echo $resultado->csugstatus ?>" data-tipo="<?php echo $resultado->trsoid ?>" href="javascript:void(0)">Motivos</a></td>
                        <td class="direita"><?php echo $resultado->qtd; ?></td>
                        <td class="centro"><?php echo $resultado->status; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>
</div>

<div class="clear"></div>
</div>

<?php endif; ?>


</form>

<div title="Motivos" id="dialog-motivos" class="invisivel">
    <div class="resultado bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th style="width:260px" >Motivos</th>
                        <th style="width: 35px" >Qtde</th>
                    </tr>
                </thead>
                <tbody id="motivo_conteudo">

                </tbody>
    </table>
</div>
</div>
</div>


<?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>
    
<?php endif; ?>

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_indicador_cancelamento/rodape.php"; ?>
