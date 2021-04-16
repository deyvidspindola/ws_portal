<div class="separador"></div>
<!-- <div class="resultado bloco_titulo">Resultado da Pesquisa</div> -->
<div class="resultado bloco_conteudo">
    <div id="tabela-lmu" class="tabela-equipamentos listagem">
        <table style="font-size: 9px;">
            <?php

                if (count($this->view->dados['COMMAND_LMU'])) : ?>
                    <thead>
                        <tr>
                            <th class="esquerda" colspan="20">
                                Grupo Equipamento LMU
                                <button id="tabela-lmu-column-button" style="float: right;">
                                    Selecione Colunas <img style="width: 12px; margin: 0px;" src="images/detalhes.png"  alt="Detalhes">
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th class="coluna-tabela menor placa">Placa</th>
                            <!--<th class="medio">Chassi</th>-->
                            <th class="coluna-tabela medio cliente">Cliente</th>

                            <th class="coluna-tabela menor equesn">Eq Esn</th>
                            <th class="coluna-tabela menor eveversao">Eq Versão</th>
                            <th class="coluna-tabela menor eprnome">Eq Projeto</th>

                            <th class="coluna-tabela menor firmware">Firmware</th>	
                            <th class="coluna-tabela menor versao_perfil">Versão Perfil</th>
                            <th class="coluna-tabela menor versao_teclado">Versão Teclado</th>
                            <th class="coluna-tabela menor hosted_app">Hosted App</th>
                            <th class="coluna-tabela menor modelo_isv">Modelo ISV</th>

                            <!-- <th class="coluna-tabela menor telemetria_segundo_a_segundo">Blackbox</th> -->
                            <th class="coluna-tabela menor peg_enables" style="display: none;">Peg Enables</th>
                            <th class="coluna-tabela menor logoff_30seg">Logoff Após 30seg</th>
                            <th class="coluna-tabela menor app_tempo_direcao">App Tempo de Direção</th>
                            <th class="coluna-tabela menor bloq_auto_ignicao">Bloqueio Automático Ignição</th>

                            <th class="coluna-tabela menor inbound_url00">Inbound Url00</th>
                            <th class="coluna-tabela menor inbound_port00">Inbound Port00</th>
                            <th class="coluna-tabela menor inbound_url01">Inbound Url01</th>
                            <th class="coluna-tabela menor inbound_port01">Inbound Port01</th>

                            <th class="coluna-tabela menor data_chegada">Data Chegada</th>

                            <th class="coluna-tabela menor ini_speed_deaccel" style="display: none;">Ini Speed Deaccel</th>
                            <th class="coluna-tabela menor breack_deaccel" style="display: none;">Breack Deaccel</th>

                            <th class="acao">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($this->view->dados) > 0):
                            $classeLinha = "par";
                            ?>

                            <?php foreach ($this->view->dados['COMMAND_LMU'] as $resultado) : ?>
                                <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                                    <tr class="<?php echo $classeLinha; ?>" data-equesn="<?php echo $resultado->equesn; ?>" >
                                        
                                        <td class="coluna-tabela centro placa"><?php echo $resultado->veiplaca; ?></td>
                                        <td class="coluna-tabela centro cliente"><?php echo $resultado->clinome; ?></td>
                                        
                                        <td class="coluna-tabela centro equesn"><?php echo $resultado->equesn; ?></td>
                                        <td class="coluna-tabela centro eveversao"><?php echo $resultado->eveversao; ?></td>
                                        <td class="coluna-tabela centro eprnome"><?php echo $resultado->eprnome; ?></td>
                                        
                                        <td class="coluna-tabela centro dados_equipamento firmware"></td>
                                        <td class="coluna-tabela centro dados_equipamento versao_perfil"></td>
                                        <td class="coluna-tabela centro dados_equipamento versao_teclado"></td>
                                        <td class="coluna-tabela centro dados_equipamento hosted_app"></td>
                                        <td class="coluna-tabela centro dados_equipamento modelo_isv"></td>

                                        <!-- <td class="coluna-tabela centro dados_equipamento telemetria_segundo_a_segundo"></td> -->
                                        <td class="coluna-tabela centro dados_equipamento peg_enables" style="display: none;"></td>
                                        <td class="coluna-tabela centro dados_equipamento logoff_30seg"></td>
                                        <td class="coluna-tabela centro dados_equipamento app_tempo_direcao"></td>
                                        <td class="coluna-tabela centro dados_equipamento bloq_auto_ignicao"></td>

                                        <td class="coluna-tabela centro dados_equipamento inbound_url00"></td>
                                        <td class="coluna-tabela centro dados_equipamento inbound_port00"></td>
                                        <td class="coluna-tabela centro dados_equipamento inbound_url01"></td>
                                        <td class="coluna-tabela centro dados_equipamento inbound_port01"></td>

                                        <td class="coluna-tabela centro dados_equipamento data_chegada"></td>

                                        <td class="coluna-tabela centro dados_equipamento ini_speed_deaccel" style="display: none;"></td>
                                        <td class="coluna-tabela centro dados_equipamento breack_deaccel" style="display: none;"></td>

                                        <td class="acao centro">
                                            <img title="" class="icone erro-consulta" src="images/icon_error.png"  alt="Download" style="display: none;">

                                            <a title="Detalhes" class="detalhes" data-equesn="<?php echo $resultado->equesn; ?>" href="?acao=showXml&esn=<?php echo $resultado->equesn; ?>&projeto=<?php echo $resultado->eprnome; ?>" target="_blank" style="display: none;"><img class="icone" src="images/detalhes.png"  alt="Detalhes"></a>

                                            <a title="Download" class="download" data-equesn="<?php echo $resultado->equesn; ?>" href="?acao=download&esn=<?php echo $resultado->equesn; ?>&projeto=<?php echo $resultado->eprnome; ?>" target="_blank" style="display: none;"><img class="icone" src="images/download.png"  alt="Download"></a>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                <?php
                endif;
            ?>
        </table>

        <div id="tabela-lmu-dialog" class="dialog window" style="display: none;" title="Seleção colunas tabela Grupo Equipamento LMU">
            <div>
                <label><input type="checkbox" checked="" value="placa" name="tabela-lmu-column">Placa</label>
                <label><input type="checkbox" checked="" value="cliente" name="tabela-lmu-column">Cliente</label>
                <label><input type="checkbox" checked="" value="equesn" name="tabela-lmu-column">Eq Esn</label>
                <label><input type="checkbox" checked="" value="eveversao" name="tabela-lmu-column">Eq Versão</label>
                <label><input type="checkbox" checked="" value="eprnome" name="tabela-lmu-column">Eq Projeto</label>
                <label><input type="checkbox" checked="" value="firmware" name="tabela-lmu-column">Firmware</label>
                <label><input type="checkbox" checked="" value="versao_perfil" name="tabela-lmu-column">Versão Perfil</label>
                <label><input type="checkbox" checked="" value="versao_teclado" name="tabela-lmu-column">Versão Teclado</label>
                <label><input type="checkbox" checked="" value="hosted_app" name="tabela-lmu-column">Hosted App</label>
                <label><input type="checkbox" checked="" value="modelo_isv" name="tabela-lmu-column">Modelo ISV</label>
                <!-- <label><input type="checkbox" checked="" value="telemetria_segundo_a_segundo" name="tabela-lmu-column">Blackbox</label> -->
                <label><input type="checkbox" value="peg_enables" name="tabela-lmu-column">Peg Enables</label>
                <label><input type="checkbox" checked="" value="logoff_30seg" name="tabela-lmu-column">Logoff Após 30seg</label>
                <label><input type="checkbox" checked="" value="app_tempo_direcao" name="tabela-lmu-column">App Tempo de Direção</label>
                <label><input type="checkbox" checked="" value="bloq_auto_ignicao" name="tabela-lmu-column">Bloqueio Automático Ignição</label>
                <label><input type="checkbox" checked="" value="inbound_url00" name="tabela-lmu-column">Inbound Url00</label>
                <label><input type="checkbox" checked="" value="inbound_port00" name="tabela-lmu-column">Inbound Port00</label>
                <label><input type="checkbox" checked="" value="inbound_url01" name="tabela-lmu-column">Inbound Url01</label>
                <label><input type="checkbox" checked="" value="inbound_port01" name="tabela-lmu-column">Inbound Port01</label>
                <label><input type="checkbox" checked="" value="data_chegada" name="tabela-lmu-column">Data Chegada</label>
                <label><input type="checkbox" value="ini_speed_deaccel" name="tabela-lmu-column">Ini Speed Deaccel</label>
                <label><input type="checkbox" value="breack_deaccel" name="tabela-lmu-column">Breack Deacce</label>
            </div>
        </div>
    </div>
</div>