    <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_detalhes_modificacao/sub_abas.php"; ?>
    <div class="bloco_titulo">Detalhes da Modificação</div>
    <div class="bloco_conteudo">
        <div class="separador"></div>

        <div class="bloco_titulo">Histórico</div>
            <div class="bloco_conteudo">
                <div class="separador"></div>
                    <div class="bloco_conteudo">
                        <div class="listagem">
                            <form id="form_desfazer" method="post" action="">
                                <input type="hidden" id="mdfstatus" name="mdfstatus" value="<?php echo $this->view->parametros->mdfstatus;?>">
                                <input type="hidden" id="acao" name="acao" value="">
                                <input type="hidden" id="mdfoid" name="mdfoid" value="<?php echo $this->view->parametros->mdfoid;?>">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="medio centro">Data</th>
                                            <th class="maior centro">Observação</th>
                                            <th class="medio centro">Usuário</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $linhas = 0;
                                            foreach ($this->view->parametros->historico as $dados):
                                                $cor = ($cor=="par") ? "impar" : "par";
                                        ?>
                                            <tr class="<?php echo $cor; ?>">
                                                <td class="centro">
                                                    <?php echo $dados->hmdt_cadastro; ?>
                                                </td>
                                                 <td class="esquerda">
                                                    <?php echo wordwrap($dados->hmobs, 150, "<br />", true); ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->usuario; ?>
                                                </td>
                                            </tr>
                                        <?php
                                            $linhas++;
                                            endForeach;
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">
                                                 <?php
                                                    if($linhas == 1){
                                                        echo "1 registro encontrado.";
                                                    } else{
                                                        echo $linhas . " registros encontrados.";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div>
                    </div>
                <div class="separador"></div>
            </div>
            <div class="separador"></div>
        </div>
    </div>

    <div class="bloco_acoes">
            <button type="button" id="btn_voltar">Voltar</button>
    </div>
