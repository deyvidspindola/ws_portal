<?php require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/cabecalho.php'; ?>

<?php if (!empty($this->view->mensagem->alerta)) : ?>
    <div class="mensagem alerta"><?php echo $this->view->mensagem->alerta; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagem->erro)) : ?>
    <div class="mensagem erro"><?php echo $this->view->mensagem->erro; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagem->sucesso)) : ?>
    <div class="mensagem sucesso"><?php echo $this->view->mensagem->sucesso; ?></div>
<?php endif; ?>

<?php
require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/conteudo_representante.php';

if($this->view->produtos->sessao): ?>
    <form id="form_reservado"  method="post" action="prn_cockpit_agendamento_os.php?ordoid=<?php echo $this->param->ordoid ?>&repoid=<?php echo $this->param->repoid ?>">
        <input type="hidden" id="acao" name="acao" value=""/>
        <input type="hidden" id="ordoid" name="ordoid" value="<?php echo $this->param->ordoid ?>"/>
        <input type="hidden" id="repoid" name="repoid" value="<?php echo $this->param->repoid ?>"/>
        <input type="hidden" name="idProdutoExcluir" id="idProdutoExcluir" value="" />
        <input type="hidden" name="cancelamentoJustificativa" id="cancelamentoJustificativa" value="" />
        <input type="hidden" name="cancelamentoIdItem" id="cancelamentoIdItem" value="" />
<?php require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/lista_produtos_reservados.php'; ?>
    </form>
<?php endif; ?>


        <input type="hidden" id="repoid" name="repoid" value="<?php echo $this->param->repoid ?>"/>
        <input type="hidden" id="ordoid" name="ordoid" value="<?php echo $this->param->ordoid ?>"/>
        <input type="hidden" id="connumero" name="connumero" value="<?php echo $this->param->connumero ?>"/>
<?php require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/listagem_produto_disponivel.php'; ?>


<?php require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/rodape.php'; ?>
