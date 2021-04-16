<?php
class PrnPropostaSeguradoraView{

    function __construct(){
    
    }
    
    /**
     * Renderiza a tela de pesquisa.
     * @param array $tipoContrato
     */
    public function getTelaPesquisarArquivo($tipoContrato){  
        $prpsdt_ultima_acao_inicio_busca = date("d/m/Y");
        $prpsdt_ultima_acao_final_busca  = $prpsdt_ultima_acao_inicio_busca;
        require _SITEDIR_.'modulos/Principal/View/prn_proposta_seguradora/telaPesquisarArquivo.view.php';       
    }
    
    /**
     * Renderiza a tela de com o resultado da pesquisa.
     * @param array $dados
     */
    public function getTelaResultadoPesquisarArquivo($dados){
        require _SITEDIR_.'modulos/Principal/View/prn_proposta_seguradora/telaResultadoPesquisarArquivo.view.php';
    }
    
    /**
     * Renderiza as abas
     */
    public function getAbas(){
        echo '<tr>
                <td align="center">
                    <table width="98%">
                        <tr>
                            <td align="left" id="navPrincipal">
                                <table>
                                    <tr>
                                        <td align="center" id="tabnav">
                                            <a href="javascript:void(0);" onclick="javascript:abre_abas(\'prn_proposta_seguradora.php\');">Principal</a>
                                        </td>                                                                         
                                        <td align="center" id="tabnav">
                                            <a href="javascript:void(0);" onclick="javascript:abre_abas(\'prn_proposta_seguradora_agendamento.php\');">Agendamento</a>
                                        </td>                                  
                                        <td align="center" id="tabnav">
                                            <a href="javascript:void(0);" onclick="javascript:abre_abas(\'prn_proposta_seguradora_parametrizacao.php\');">Parametrização</a>
                                        </td>  
                                        <td align="center" id="tabnav">
                                            <a href="javascript:void(0);" onclick="javascript:abre_abas(\'prn_proposta_seguradora_processamento_manual.php\');">Processamento Manual</a>
                                        </td>
                                        <td align="center" id="tabnav">
                                            <a href="javascript:void(0);" onclick="javascript:abre_abas(\'prn_proposta_seguradora_arquivos.php\');" class="active">Arquivos</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';
    }
}
?>