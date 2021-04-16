<div class="separador"></div>
        
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th width="250">
                        Histórico O.S <br />
                        <a id="exibir_grid_os" href="prn_gestao_frota_contrato_vivo.php?acao=pesquisarOrdemServico" target="_blank">O.S's Pendentes</a>
                    </th>
                    
                    <th width="70">Quant. <span id="qtd_os"></span></th>                            
                    <th>
                        <table>
                            <tr>
                                <th>
                                    <span class="float-left"> Pesquisar O.S.</span>
                                    <input type="text" id="numero_os" name="numero_os"> 
                                    <button id="btn_pesquisar_os_nova_aba" type="button">Pesquisar</button>
                                </th>
                                <th>
                                    <span class="float-left"> Pesquisar ID Vivo</span>
                                    <input type="text" name="idvivo_os" class="float-left" id="idvivo_os" maxlength="30" size="30"> 
                                    <button id="btn_pesquisar_os_idvivo_nova_aba" class="float-left" type="button">Pesquisar</button>
                                </th>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>                    
        </table>                
    </div>
</div>

<div class="separador" id="tabela_os_separador"></div>

<div id="alerta_grid_os" class="mensagem alerta invisivel"></div>

<div class="separador"></div>

<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>    
                        <?php
                        if ($_SESSION['usuario']['vivo']['token']) { ?>
                            <a href="prn_solicitacao_vivo.php?token=<?php echo $_SESSION['usuario']['vivo']['token'] ?>" target="_blank">Cadastro Solicitações Off Line</a>
                        <?php 
                        } else { ?>
                            <a href="prn_solicitacao_vivo.php" target="_blank">Cadastro Solicitações Off Line</a>
                        <?php
                        } ?>
                    </th>                            
                </tr>
            </thead>                    
        </table>                
    </div>
</div>

<div class="separador" id="tabela_os_separador"></div>

<div class="separador"></div>

<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>    
                        <a id="exibir_analise_contas" href="fin_analise_contas_vivo.php" target="_blank">Análise de Contas do Cliente</a>
                    </th>                            
                </tr>
            </thead>                    
        </table>                
    </div>
</div>

<div class="separador" id="tabela_analise_contas_separador"></div>

<div id="alerta_analise_contas" class="mensagem alerta invisivel"></div>
