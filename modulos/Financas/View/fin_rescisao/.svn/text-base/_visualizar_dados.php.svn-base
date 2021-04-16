<div class="bloco_titulo">Dados da Rescisão</div>
<div class="bloco_conteudo">   
    <div class="left">
        <dl class="dados-rescisao">
            <dt>Cliente:</dt>
            <dd><?= $dadosRescisao['clinome'] ?></dd>
            
            <? if ($dadosRescisao['resmvalidade']): ?>
                <dt>Data de validade:</dt>
                <dd>
                    <?= date('d/m/Y', strtotime($dadosRescisao['resmvalidade'])) ?>
                </dd>
            <? endif ?>
            
            <dt>Usuário:</dt>
            <dd><?= $dadosRescisao['nm_usuario'] ?></dd>
            
            <? if ($dadosRescisao['resmatt']): ?>
                <dt>Carta aos cuidados de:</dt>
                <dd><?= $dadosRescisao['resmatt'] ?></dd>
            <? endif ?>
            
            <dt>Data da carta:</dt>
            <dd><?= date('d/m/Y', strtotime($dadosRescisao['resmcarta'])) ?></dd>
            
            <dt>Multa:</dt>
            <dd>R$ <?= toMoney($dadosRescisao['resmvl_multa']) ?></dd>
        </dl>
    </div>

    <div class="right">
        <dl class="dados-rescisao">
            <dt>Data rescisão:</dt>
            <dd><?= date('d/m/Y', strtotime($dadosRescisao['resmcadastro'])) ?></dd>
            
            <dt>Status:</dt>
            <dd><?= $dadosRescisao['resmstatus'] ?></dd>
            
            <dt>Motivo:</dt>
            <dd><?= $dadosRescisao['mrescdescricao'] ?></dd>
            
            <dt>Data da solicitação:</dt>
            <dd><?= date('d/m/Y', strtotime($dadosRescisao['resmfax'])) ?></dd>
            
            <dt>Taxa retirada:</dt>
            <dd>R$ <?= toMoney($dadosRescisao['resmvl_remocao']) ?></dd>
            
            <dt>Total rescisão:</dt>
            <dd>R$ <?= toMoney($dadosRescisao['resmvl_total']) ?></dd>
        </dl>
    </div>
    
    <div class="clear"></div>
</div>
<div class="bloco_acoes">
    <a class="botao rescisao-excluir" data-url="fin_rescisao.php?acao=excluir&resmoid=<?= $resmoid ?>">Excluir</a>
    <a class="botao" href="fin_rescisao.php">Retornar</a>
</div>

<div class="separador"></div>