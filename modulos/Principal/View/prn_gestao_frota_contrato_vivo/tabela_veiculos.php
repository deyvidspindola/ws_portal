<div class="separador"></div>
        
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th width="250">
                        <a id="exibir_veiculos" class="float-left" href="prn_gestao_frota_contrato_vivo.php?acao=pesquisarVeiculos" target="_blank">
                            Veículos Ativos
                        </a>
                    </th>
                    <th width="70"><span class="float-left">Quant.</span> <span class="float-left" id="qtd_veiculos_ativos"></span></th>                            
                    <th>
                    <table>
                        <tr>
                            <th>
                                <span class="float-left"> Pesquisar Placa</span>
                                <input type="text" name="placa" class="float-left" id="placa" maxlength="10"> 
                                <button id="btn_pesquisar_veiculos_nova_aba" class="float-left" type="button">Pesquisar</button>
                            </th>
                            <th>
                                <span class="float-left"> Pesquisar ID Vivo</span>
                                <input type="text" name="idvivo_veiculo" class="float-left" id="idvivo_veiculo" maxlength="30" size="30"> 
                                <button id="btn_pesquisar_veiculos_idvivo_nova_aba" class="float-left" type="button">Pesquisar</button>
                            </th>
                        </tr>
                    </table>
                    </tr>
            </thead>                    
        </table>                
    </div>
</div>

<div class="separador" id="tabela_veiculos_separador"></div>

<div id="alerta_veiculos" class="mensagem alerta invisivel"></div>