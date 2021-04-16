
</div>
<?php if (isset($this->view->parametros->acao) && isset($this->view->ordemServicos) && in_array($this->view->parametros->acao, array('detalhe', 'notificarOS', 'analiseBackoffice'))): ?>
<div class="bloco_acoes">
    <?php if ($this->view->parametros->etapa != 'agenda'): ?>
    <button type="button" id="bt_voltar" onclick="window.location.href='<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php'">Voltar</button>
    <?php else: ?>
    <button type="button" id="bt_voltar" onclick="window.location.href='<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=detalhe&operacao=<?php echo $this->view->parametros->operacao; ?>&id=<?php echo $this->view->parametros->id; ?>&etapa=info&retirada_reinstalacao=<?php echo $retirada_reinstalacao ;?>'">Voltar</button>
    <?php endif; ?>
</div>
<?php endif; ?>
<div class="separador"></div>

<!-- Rodapé -->
<?php require_once 'lib/rodape.php'; ?>

</body>
</html>