<?php

/**
 * Classe de persist�ncia de dados 
 */
require (_MODULEDIR_ . "Relatorio/DAO/RelTempoDeDirecaoDAO.php");

/**
 * RelTempoDeDirecao.php
 * 
 * Classe Action para o Relatorio Tempo de Direcao
 * 
 * @author Denilson Sousa
 * @package Relatorio
 * @since 08/04/2020
 * 
 */
class RelTempoDeDirecao {

    private $dao_gerenciadora;
    private $dao_gerenciadora2;
    private $dao;
    private $max_dias;
    private $limite_resultados;

    /**
     * M�todo principal
     * Chama a view do relat�rio
     * 
     * @author Denilson Sousa
     */
    public function index() {

        /**
         * Cabecalho da pagina (menus)
         */
        cabecalho();

        /*
         * Inclui a view
         */
        include(_MODULEDIR_ . 'Relatorio/View/rel_tempodedirecao/index.php');
    }


     /**
     * M�todo de pesquisa do relat�rio
     * 
     * @author Denilson Sousa
     */
    public function pesquisar() {

        try {
            
            $data_inicial = trim($_POST['data_inicio_pesquisa']);
            $data_final = trim($_POST['data_fim_pesquisa']);
            $clioid = (!empty($_POST['clioid'])) ? trim($_POST['clioid']) : '';
	        $nome_cliente = (!empty($_POST['nome_cliente'])) ? trim($_POST['nome_cliente']) : '';
            $placa = (!empty($_POST['placa'])) ? trim($_POST['placa']) : '';
            $motorista = (!empty($_POST['motorista'])) ? trim($_POST['motorista']) : '';
            $parametros = array("mentdata BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59'");

            $data_inicial = strtotime(str_replace('/','-',$data_inicial));
            $data_final = strtotime(str_replace('/','-',$data_final));
            $total_dias = ($data_final - $data_inicial)/60/60/24;
            if($total_dias < 0 || $total_dias > $this->max_dias){
	    	    throw new Exception("O intervalo da data inicial e final deve ser de no maximo $this->max_dias dias.");
            }

            if(!empty($placa)){
                array_push($parametros, " AND veiplaca ilike '$placa'");
            $clioid_placa = $this->dao->recuperarClioidPorPlaca($placa);
            if(is_null($clioid_placa)){
                throw new Exception("Nenhum contrato encontrado para a placa pesquisada.");
            }else{
                if((!empty($clioid) && !empty($nome_cliente)) && $clioid != $clioid_placa){
                    throw new Exception("A placa pesquisada nao pertence ao cliente informado, tente pesquisar apenas pela placa ou pelo cliente.");
                }else{
                    array_push($parametros, " AND mentclioid = $clioid_placa ");
                    $clioid_ultnum = substr($clioid_placa,-1);
                }
            }
            }elseif(empty($clioid) || empty($nome_cliente)){
                throw new Exception("Obrigatorio preencher a Placa ou o Cliente.");
            }elseif(!empty($clioid) && !empty($nome_cliente)){
                array_push($parametros, " AND mentclioid = $clioid ");
                $clioid_ultnum = substr($clioid,-1);
            }
		
	        if(!empty($motorista)){
                array_push($parametros, " AND motonome ilike '%$motorista%'");
            }

            $campos = (!empty($parametros)) ? implode(' ', $parametros) : '';
            
            $this->dao_gerenciadora->setLimiteResultados($this->limite_resultados);
            $this->dao_gerenciadora2->setLimiteResultados($this->limite_resultados);

            $resultado = $this->dao_gerenciadora->pesquisar($campos,$clioid_ultnum) + $this->dao_gerenciadora2->pesquisar($campos,$clioid_ultnum);

            if (count($resultado) > 0) {
                foreach ($resultado as $historico) {						
                    $arrHistorico['pesquisa'][] = array(
                        'data_envio' => date('d/m/Y H:i:s', strtotime(utf8_encode($historico['data_envio']))),
                        'data_chegada' => date('d/m/Y H:i:s', strtotime(utf8_encode($historico['data_chegada']))),
                        'clinome' => utf8_encode($historico['clinome']),
                        'veiplaca' => utf8_encode($historico['veiplaca']),
                        'motonome' => utf8_encode($historico['motonome']),
                        'mentmotologin' => utf8_encode($historico['mentmotologin']),
                        'mttdnome' => utf8_encode($historico['mttdnome']),
                        'tipo' => utf8_encode($historico['tipo']),
                        'tmttdescricao' => utf8_encode($historico['tmttdescricao']),
                        'mentmensagem' => utf8_encode($historico['mentmensagem'])
                    );
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
     * M�todo que abre a janela para downlod do csv
     * Mudamos o header para fazer download no click do bot�o
     * 
     * @author Denilson Sousa
     */
    public function gerarCsv() {

        $data = date('Y_m_d');
        header("Content-disposition: attachment; filename=tempo_de_direcao_$data.csv");
        header("Content-Type: application/force-download");

        echo $_POST['exportdata'];
        exit;
    }

    /**
     * Buscar dados Cliente - AJAX
     *
     * @return array $retorno
     */
    public function recuperarCliente() {

        $retorno = $this->dao->recuperarCliente($_POST['nome_cliente']);

        echo json_encode($retorno);
        exit;
    }

    /**
    * Tratamento de input de dados, contra injection code
    * @param string $dado
    * @return string
    */
    private function tratarTextoInput($dado, $autocomplete = false){

        //Elimina acentos para pesquisa
        if($autocomplete){
            $dado = utf8_decode($dado);
        }

        $dado  = trim($dado);
        $dado  = str_replace("'", '', $dado);
        $dado  = str_replace('\\', '', $dado);
        $dado  = strip_tags($dado);

        return $dado;
    }

    /**
    * Retorna o limite de resultados do relatorio
    * @return array
    */
    public function getLimiteResultados() {
        return $this->limite_resultados;
    } 

    /**
     * Construtor
     * @param max_dias Maximo de dias do intervalo de datas da pesquisa
     * @param limite_resultados Numero maximo de registros retornados no resultado
     * @author Denilson Sousa
     */
    public function RelTempoDeDirecao($max_dias = 31,$limite_resultados = 1000) {
        global $conn; 
        global $dbstring_gerenciadoras;
        global $dbstring_gerenciadoras2;
        
        $conn_gerenciadoras = pg_connect ($dbstring_gerenciadoras);
        $conn_gerenciadoras2 = pg_connect ($dbstring_gerenciadoras2);

        $this->dao_gerenciadora = new RelTempoDeDirecaoDAO($conn_gerenciadoras);
        $this->dao_gerenciadora2 = new RelTempoDeDirecaoDAO($conn_gerenciadoras2);
        $this->dao = new RelTempoDeDirecaoDAO($conn);

        $this->max_dias = $max_dias;
        $this->limite_resultados = $limite_resultados;
    }

}
