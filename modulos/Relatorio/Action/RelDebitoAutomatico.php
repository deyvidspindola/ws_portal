<?php

/**
 * Classe de persistência de dados 
 */
require (_MODULEDIR_ . "Relatorio/DAO/RelDebitoAutomaticoDAO.php");

/**
 * RelDebitoAutomatico.php
 * 
 * Classe Action para o Relatório de Débito AUtomático
 * 
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * @package Relatório
 * @since 28/09/2012
 * 
 */
class RelDebitoAutomatico {

    private $dao;

    /**
     * Método principal
     * Chama a view do relatório
     * 
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    public function index() {

        /**
         * Cabecalho da pagina (menus)
         */
        cabecalho();

        /*
         * Inclui a view
         */
        include(_MODULEDIR_ . 'Relatorio/View/rel_debito_automatico/index.php');
    }

    /**
     * Método que busca os motivos de suspensão/exclusão cadastrados para débito automático
     * 
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    public function getMotivos() {	
		$motivos = $this->dao->getMotivos();
		
		return $motivos;
    }
    
     /**
     * Método de pesquisa do relatório
     * 
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    public function pesquisar() {

        try {
            
            $tipo_relatorio = $_POST['tipo_relatorio'];
            $data_inicial = trim($_POST['data_inicio_pesquisa']);
            $data_final = trim($_POST['data_fim_pesquisa']);
            $tipo_operacao = (!empty($_POST['tipo_operacao'])) ? trim($_POST['tipo_operacao']) : '';
            $nome_cliente = (!empty($_POST['nome_cliente'])) ? trim($_POST['nome_cliente']) : '';
            $canal_entrada = (!empty($_POST['canal_entrada'])) ? trim($_POST['canal_entrada']) : '';
            $motivo = (!empty($_POST['motivo'])) ? trim($_POST['motivo']) : '';
            
            $parametros = array("hdadt_cadastro BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59'");

            if (!empty($tipo_operacao)) {
                array_push($parametros, " AND hdatipo_operacao = '$tipo_operacao'");
            }

            if (!empty($nome_cliente)) {
                array_push($parametros, " AND clinome ILIKE '%$nome_cliente%'");
            }

            if (!empty($canal_entrada)) {
                array_push($parametros, " AND hdaentrada = '$canal_entrada'");
            }

            if (!empty($motivo)) {
                array_push($parametros, " AND msdaoid = $motivo");
            }

            $campos = (!empty($parametros)) ? implode(' ', $parametros) : '';

            if ($tipo_relatorio == "analitico") {

                $resultado = $this->dao->pesquisarAnalitico($campos);

                if (count($resultado) > 0) {

                    foreach ($resultado as $historico) {

                        $arrHistorico['pesquisa'][] = array(
                            'data_cadastro' => date('d/m/Y H:i:s', strtotime(utf8_encode($historico['data_cadastro_historico']))),
                            'canal_entrada' => utf8_encode($historico['tipo_entrada']),
                            'nome_cliente' => utf8_encode($historico['nome_cliente']),
                            'documento' => $historico['documento'],
                            'tipo_cliente' => $historico['tipo_cliente'],
                            'tipo_operacao' => utf8_encode($historico['tipo_operacao']),
                            'tipo_operacao_contador' => utf8_encode($historico['tipo_operacao_contador']),
                            'motivo' => utf8_encode($historico['descricao']),
                            'protocolo' => utf8_encode($historico['protocolo']),
                            'nome_usuario' => utf8_encode($historico['nome_usuario']),
                            'departamento' => utf8_encode($historico['departamento']),
                            'forma_cobranca_anterior' => utf8_encode($historico['forma_cobranca_anterior']),
                            'banco_anterior' => utf8_encode($historico['banco_anterior']),
                            'agencia_anterior' => utf8_encode($historico['agencia_anterior']),
                            'conta_corrente_anterior' => utf8_encode($historico['conta_corrente_anterior']),
                            'forma_cobranca_posterior' => utf8_encode($historico['forma_cobranca_posterior']),
                            'banco_posterior' => utf8_encode($historico['banco_posterior']),
                            'agencia_posterior' => utf8_encode($historico['agencia_posterior']),
                            'conta_corrente_posterior' => utf8_encode($historico['conta_corrente_posterior'])
                        );
                    }
                }
            }
            elseif($tipo_relatorio == "sintetico"){

            	$tipoResultado = $_POST['resultado'];
                $resultado = $this->dao->pesquisarSintetico($campos);
	            if (count($resultado) > 0) {
	            	if ($tipoResultado == 'D') {
	                    foreach ($resultado as $historico){
	                        $data = date('d/m/Y', strtotime($historico['data_cadastro_historico']));
	                        $arrHistorico['pesquisa'][$data][$historico['hdatipo_operacao']] = $historico['qtd_operacao'];
	                    }
	            	} else {
	            		foreach ($resultado as $historico){
	            			$data = date('m/Y', strtotime($historico['data_cadastro_historico']));
	            			$arrHistorico['pesquisa'][$data][$historico['hdatipo_operacao']] =
	            			$arrHistorico['pesquisa'][$data][$historico['hdatipo_operacao']] + $historico['qtd_operacao'];
	            		}
	            	}
                }
                $res2 = $this->dao->pesquisarDebitoAutomaticoFormaCobranca();
                if (count($res2) > 0) {
                	$cont = 0;
                	foreach($res2 as $linha) {
                		$cont = $cont + $linha['valor'];
                		$forma = utf8_encode($linha["forcnome"]);
                		$arrHistorico['debitos'][$forma] = $linha['valor'];
                	}
                }
                                
            }

            echo json_encode($arrHistorico);
            exit;
            
        } catch (Exception $e) {

            echo json_encode(array('error' => true, 'message' => utf8_encode($e->getMessage())));
            exit;
        }
    }    
       
    
    /**
     * Método que abre a janela para downlod do csv
     * Mudamos o header para fazer download no click do botão
     * 
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    public function gerarCsv() {

        $data = date('Y_m_d');
        header("Content-disposition: attachment; filename=debito_automatico_sintetico_$data.csv");
        header("Content-Type: application/force-download");

        echo $_POST['exportdata'];
        exit;
    }

    /**
     * Construtor
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    public function RelDebitoAutomatico() {

        global $conn;

        $this->dao = new RelDebitoAutomaticoDAO($conn);
    }

}