
<?php cabecalho(); ?>

    <?php require _MODULEDIR_ . 'Financas/View/fin_credito_futuro_parametrizacao/head.php'; ?>

        <div class="modulo_titulo">Crédito Futuro - Parametrização</div>
        
        <div class="modulo_conteudo">
            
            <form  id="form" name="form" method="post">
                <input type="hidden" name="acao" id="acao" value="index" />
				<ul class="bloco_opcoes">
					<li class="<?php echo (strpos( $this->view, 'motivo_credito') > -1)  ? "ativo" : "";  ?>">
						<a href="?acao=pesquisarMotivoCredito" title="Motivo do Crédito">Motivo do Crédito</a>
					</li>
                    <?php if($_SESSION['funcao']['autoriza_credito_futuro_email_aprovacao']): ?>
					<li class="<?php echo (strpos( $this->view, 'email_aprovacao/email_aprovacao') > -1)  ? "ativo" : "";  ?>">
						<a href="?acao=emailAprovacao" title="E-mail p/ Aprovação">E-mail p/ Aprovação</a></li>
                    <?php else: ?>
                    <li>
                        <a href="javascript:void(0);" title="E-mail p/ Aprovação" style="color: #333333; text-decoration: none;">E-mail p/ Aprovação</a>						
                    </li>
                    <?php endif; ?>
					<li class="<?php echo (strpos( $this->view, 'tipo_campanha_promocional') > -1)  ? "ativo" : ""; ?>">
                        <a href="?acao=pesquisarTipoCampanhaPromocional" title="Tipo de Campanha Promocional">Tipo de Campanha Promocional</a>
                    </li>
					<li><a href="fin_credito_futuro_parametrizacao_campanha.php?acao=index" title="Campanha Promocional">Campanha Promocional</a></li>
				</ul>
                
                
                <?php
                    if (isset($this->view) && !empty($this->view)) {

                        require _MODULEDIR_ . 'Financas/View/fin_credito_futuro_parametrizacao/' . $this->view  ;
                    } 
                ?>
                
            </form>
        </div>
        
        <div class="separador"></div>

        <?php include _SITEDIR_ . "lib/rodape.php" ?>