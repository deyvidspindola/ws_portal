<div class="separador"></div>
<!-- <div class="resultado bloco_titulo">Resultado da Pesquisa</div> -->
<div class="resultado bloco_conteudo">
    <div id="tabela-mtc" class="tabela-equipamentos listagem">
        <table style="font-size: 9px;">
            <?php

                if (count($this->view->dados['COMMAND_MTC'])) : ?>
                    <thead>
                        <tr>
                            <th class="esquerda" colspan="20">
                                Grupo Equipamento MTC
                                <button id="tabela-mtc-column-button" style="float: right;">
                                    Selecione Colunas <img style="width: 12px; margin: 0px;" src="images/detalhes.png"  alt="Detalhes">
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th class="coluna-tabela menor placa">Placa</th>
                            <!--<th class="coluna-tabela medio">Chassi</th>-->
                            <th class="coluna-tabela medio cliente">Cliente</th>

                            <th class="coluna-tabela menor equesn">Eq Esn</th>
                            <th class="coluna-tabela menor eveversao">Eq Versão</th>
                            <th class="coluna-tabela menor eprnome">Eq Projeto</th>
                            <th class="coluna-tabela menor versao_firmware">Firmware V</th>
                            <th class="coluna-tabela menor firmware_date">Firmware Date</th>
                            <th class="coluna-tabela menor lua_versao_script">Lua V</th>
                            <th class="coluna-tabela menor data_script_lua">Lua Date</th>
                            <th class="coluna-tabela menor data_chegada">Data Chegada</th>

                            <th class="acao">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($this->view->dados) > 0):
                            $classeLinha = "par";
                            ?>

                            <?php foreach ($this->view->dados['COMMAND_MTC'] as $resultado) : ?>
                                <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                                    <tr class="<?php echo $classeLinha; ?>" data-equesn="<?php echo $resultado->equesn; ?>" >
                                        
                                        <td class="coluna-tabela centro placa"><?php echo $resultado->veiplaca; ?></td>
                                        <td class="coluna-tabela centro cliente"><?php echo $resultado->clinome; ?></td>
                                        <td class="coluna-tabela centro equesn"><?php echo $resultado->equesn; ?></td>
                                        <td class="coluna-tabela centro eveversao"><?php echo $resultado->eveversao; ?></td>
                                        <td class="coluna-tabela centro eprnome"><?php echo $resultado->eprnome; ?></td>
                                        <td class="coluna-tabela centro dados_equipamento versao_firmware"></td>
                                        <td class="coluna-tabela centro dados_equipamento firmware_date"></td>
                                        <td class="coluna-tabela centro dados_equipamento lua_versao_script"></td>
                                        <td class="coluna-tabela centro dados_equipamento data_script_lua"></td>
                                        <td class="coluna-tabela centro dados_equipamento data_chegada"></td>
                                        
                                        <td class="acao centro">
                                            <img title="" class="icone erro-consulta" src="images/icon_error.png"  alt="Download" style="display: none;">

                                            <a title="Detalhes" class="detalhes" data-equesn="<?php echo $resultado->equesn; ?>" href="?acao=showXml&esn=<?php echo $resultado->equesn; ?>&projeto=<?php echo $resultado->eprnome; ?>" target="_blank" style="display: none;"><img class="icone" src="images/detalhes.png"  alt="Detalhes"></a>

                                            <a title="Download" class="download" data-equesn="<?php echo $resultado->equesn; ?>" href="?acao=download&esn=<?php echo $resultado->equesn; ?>&projeto=<?php echo $resultado->eprnome; ?>" target="_blank" style="display: none;">
                                                <img class="icone" src="images/download.png"  alt="Download">
                                            </a>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                <?php
                endif;
            ?>
        </table>

        <div id="tabela-mtc-dialog" class="dialog window" style="display: none;" title="Seleção colunas tabela Grupo Equipamento MTC">
            <div>
                <label><input type="checkbox" checked="" value="placa" name="tabela-mtc-column">Placa</label>
                <label><input type="checkbox" checked="" value="cliente" name="tabela-mtc-column">Cliente</label>
                <label><input type="checkbox" checked="" value="equesn" name="tabela-mtc-column">Eq Esn</label>
                <label><input type="checkbox" checked="" value="eveversao" name="tabela-mtc-column">Eq Versão</label>
                <label><input type="checkbox" checked="" value="eprnome" name="tabela-mtc-column">Eq Projeto</label>

                <label><input type="checkbox" checked="" value="versao_firmware" name="tabela-mtc-column">Firmware V</label>
                <label><input type="checkbox" checked="" value="firmware_date" name="tabela-mtc-column">Firmware Date</label>
                <label><input type="checkbox" checked="" value="lua_versao_script" name="tabela-mtc-column">Lua V</label>
                <label><input type="checkbox" checked="" value="data_script_lua" name="tabela-mtc-column">Lua Date</label>

                <label><input type="checkbox" checked="" value="data_chegada" name="tabela-mtc-column">Data Chegada</label>
            </div>
        </div>
    </div>
</div>