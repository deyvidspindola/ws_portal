<?php

ini_set('max_execution_time', 0);

/**
 * Inclui funcoes para utilizar comandoSocketTesteEquipamento($comando);
 */
// require_once(_SITEDIR_.'lib/funcoes.php');

/**
 * Classe ManDesembarqueConfiguracoes.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   CÁSSIO VINÍCIUS LEGUIZAMON BUENO <cassio.bueno.ext@sascar.com.br>
 *
 */
class ManDesembarqueConfiguracoes  {

    /** Objeto DAO da classe */
    private $dao;

    /** propriedade para dados a serem utilizados na View. */
    private $view;

    /** Usuario logado */
    private $usuarioLogado;

    /** criado para setar metodos magicos */
    private $dados;

    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {                        

        $this->dao = ( (is_object($dao) ) ? $this->dao = $dao : NULL);

    }
    
    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *
     * @return stdClass Parametros tradados
     * @return stdClass
     */
    private function tratarParametros() {

       $retorno = new stdClass();

       if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo ja existe e nao sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
                }
            }
        }

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {

                if(is_array($value)) {

                    //Tratamento de POST com Arrays
                    foreach ($value as $chave => $valor) {
                        $value[$chave] = trim($valor);
                    }
                    $retorno->$key = isset($_POST[$key]) ? $_POST[$key] : array();

                } else {
                    $retorno->$key = isset($_POST[$key]) ? trim($value) : '';
                }

            }
        }

        if (count($_FILES) > 0) {
           foreach ($_FILES as $key => $value) {

               //Verifica se atributo já existe e não sobrescreve.
               if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_FILES[$key]) ? $value : '';
               }
           }
        }

        return $retorno;
    }
    
    // Enviar comando desembarque
    // Retirada de Equipamento $idEquipamento e $idGerenciadora, onde gerenciadora é a antiga, que deve ser removido o sinal
    public function enviarComandoDesembarque( $idEquipamento, $idGerenciadora = null){

        $arrComandos = array();
        $logComandos = array();

        if(!empty($idEquipamento)){

            $idGerenciadora                 = ((isset($idGerenciadora)) ? $idGerenciadora : NULL); // caso não seja informado a gerenciadora inicio a variavel como nula
            $idVeiculo                      = $this->getVeiculoId( $idEquipamento );
            $classeEquipamento              = $this->getClasseEquipamento( $idEquipamento ); // retorna a classe do equipamento
            $numeroSerieEquipamento         = $this->getNumeroSerieEquipamento( $idEquipamento ); // provavelmente utilizado para o envio de comando
            $projetoEquipamento             = $this->getProjetoEquipamento($idEquipamento, $classeEquipamento);
            $aceitaEmbarqueConfiguracoes    = $this->getEquipamentoProjetoValido($projetoEquipamento);
            $modeloEquipamento = $aceitaEmbarqueConfiguracoes;

            $client = new SoapClient(_WS_PORTAL_.'WS226_enviarComandoDesembarque.php?wsdl');
            $result = $client->enviarComandoDesembarque($idGerenciadora, $idVeiculo, $classeEquipamento, $numeroSerieEquipamento, $projetoEquipamento, $modeloEquipamento);
            $xml = simplexml_load_string($result->body);
            
            if(!empty($xml->log)){
                foreach($xml->log as $registro){

                    $logComandos[] = array(
                        'id' => (int) $registro->id,
                        'dataValidade' => (string) $registro->dataValidade,
                        'layout' => (string) $registro->layout
                    );

                }
            }

        }

        return !empty($logComandos) ? $logComandos : array();

    }

    /**
     * Retorna uma descrição para o layout do equipamento embarcado
     * @return string
     */ 
    public function getDescricaoComando($modeloEquipamento, $comando){


        if (strpos($modeloEquipamento, 'MTC') !== false) {

            if(strpos($comando, 'LIMPA_ACAO') !== false)
                return 'Ação Embarcada';

            if(strpos($comando, 'UPLOADLAYOUT') !== false)
                return 'Macros';

            if(strpos($comando, 'RESET_ALARM') !== false)
                return 'Reset de Alarme'; 


        }elseif (strpos($modeloEquipamento, 'LMU') !== false) {

            if(strpos($comando, 'EMBARCAR_LAYOUT') !== false)
                return 'Macros';

            if(strpos($comando, 'RESET_ALARM') !== false)
                return 'Reset de Alarme'; 

            if(strpos($comando, 'ROTOGRAMA') !== false)
                return 'Rotograma Falado';

        }

        return $comando;

    }

    /**
     * Retorna um array de comandos para a ação desejada
     * @return array
     */    
    public function getComandoProjeto($idProjeto, $acao){
        try {
            $retorno = $this->dao->getComandoProjetoDAO($idProjeto, $acao);
            return $retorno;

        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    /**
     * Formata o comando para envio
     * @return string
     */    
    public function formatarComando($comando, $numeroSerieEquipamento){

        $procurar = array(
            '(enter)',
            '(eqn_equipamento)'
        );

        $substituir = array(
            "\r\n",
            $numeroSerieEquipamento
        );

        return str_replace($procurar, $substituir, $comando);

    }

    /**
     * Retorna a classe do Equipamento.
     * @return void
     */    
    public function getLogComandosEquipamento($serialEquipamento, $limit = 1){
        try {
            $retorno = $this->dao->getLogComandosEquipamentoDAO($serialEquipamento, $limit);
            return $retorno;

        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }        
    }

    /**
     * Retorna a classe do Equipamento.
     * @return void
     */    
    public function getClasseEquipamento($idEquipamento){
        try {
            $retorno = $this->dao->getClasseEquipamentoDAO($idEquipamento);
            return $retorno;

        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }        
    }

    /**
     * Retorna um array com o id dos projetos validos
     * @return array
     */ 
    public function getProjetosValidos(){
        try {
            $retorno = $this->dao->getProjetosValidosDAO();
            $arrProjetosValidos = array();

            foreach($retorno as $row){
                $arrProjetosValidos = array_merge($arrProjetosValidos, explode(';', $row->valvalor));
            }
            return array_unique($arrProjetosValidos);

        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }        
    }
            
    /**
     * Retorna Tipo de Equipamento 
     * LMU4230
     */
    public function getTipoEquipamento( $classeEquipamento, $idEquipamento ){        
        try {            
            $retorno = $this->dao->getTipoEquipamentoDAO($classeEquipamento, $idEquipamento);
            return $retorno;

        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    /**
     * Retorna Id Veículo
     */
    public function getVeiculoId( $idEquipamento ){        
        try {            
            $retorno = $this->dao->getVeiculoIdDAO( $idEquipamento );
            return $retorno;

        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    /**
     * Enviar comandos de Desembarque RotogramaFalado
     */
    public function enviarComandoDesembarqueRotogramaFalado(){

        $retorno = true;        
        return $retorno;
    }

    /**
     * Retornar o Serial do Equipamento
     */
    public function getNumeroSerieEquipamento($idEquipamento){
        try{
            $retorno = $this->dao->getNumeroSerieEquipamentoDAO( $idEquipamento );
            return $retorno;
            
        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    public function getProjetoEquipamento($idEquipamento, $classeEquipamento){

        try {            
            $retorno = $this->dao->getProjetoEquipamentoDAO( $idEquipamento, $classeEquipamento );
            return $retorno;
        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }

    }

    /**
     * Verifica se o equipamento pertecente ao projeto e retorna o nome do projeto
     */
    public function getEquipamentoProjetoValido($projetoEquipamento) {

        $projetosValidos = array(
            46 => 'MTC550',
            20 => 'MTC600',
            63 => 'MTC700',
            66 => 'LMU4220',
            79 => 'LMU4230'
        );

        return isset($projetosValidos[$projetoEquipamento]) ? $projetosValidos[$projetoEquipamento] : false;

    }

    /**
     * Retorna os layouts embarcados de uma gerenciadora específica
     */
    public function getLayoutsEmbarcadosDaGerenciadora($idVeiculo, $idGerenciadora = null){

        $layouts = array();

        $layoutSequenciamento = $this->getProprietarioLayoutSequenciamento($idVeiculo, $idGerenciadora);

        if(!empty($layoutSequenciamento)){

            $layouts['SEQUENCIAMENTO'] = $layoutSequenciamento;

        }else{

            $layoutAcao = $this->getProprietarioLayoutAcao($idVeiculo, $idGerenciadora);
            $layoutMacro = $this->getProprietarioLayoutMacro($idVeiculo, $idGerenciadora);

            if(!empty($layoutAcao))
                $layouts['ACAO'] = $layoutAcao;

            if(!empty($layoutMacro))
                $layouts['MACRO'] = $layoutMacro;

        }

        $layoutRotograma = $this->getProprietarioLayoutRotograma($idVeiculo, $idGerenciadora);

        if(!empty($layoutRotograma))
            $layouts['ROTOGRAMA'] = $layoutRotograma;

        return $layouts;

    }


    /**
     * Responsável por verificar se a class do equipamento pesquisado esta contido na lista de classes do SASGC
     * @return void
     */
    public function getClasseSASGC($classeEquipamento) {
        
        try {
            // lista de ids que estao contidos no banco de dados SASGC
            $stringClassesSASGC = "4,11,12,19,20,21,22,24,26,27,28,29,30,34,36,37,41,47,49,55,56,57,69,70,71,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,106,107,108,113,116,117, 130,140,161,162,172,173";
            
            // Cria um array com todos os Ids;
            $arrClasseSASGC = explode (",", $stringClassesSASGC); 

            //verifica se o IDs do equipamento esta no array e retorna a chave, caso contrario retorna false.
            $key = array_search($classeEquipamento, $arrClasseSASGC);
            
            // retorna a chave se existir ou false caso não exista 
            return $key;
        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }
    } 
    
    /**
     * Retorna o proprietário do rotograma embarcado
     */
    public function getProprietarioLayoutRotograma($idVeiculo, $idGerenciadora = null){
        try{
            $retorno = $this->dao->getProprietarioLayoutRotogramaDAO($idVeiculo, $idGerenciadora);
            return $retorno;
        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    /**
     * Retorna o proprietário do layout sequenciamento de macro embarcado
     */
    public function getProprietarioLayoutSequenciamento($idVeiculo, $idGerenciadora = null){
        try{
            $retorno = $this->dao->getProprietarioLayoutSequenciamentoDAO($idVeiculo, $idGerenciadora);
            return $retorno;
        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    /**
     * Retorna o proprietário do layout ação embarcado
     */
    public function getProprietarioLayoutAcao($idVeiculo, $idGerenciadora = null){
        try{
            $retorno = $this->dao->getProprietarioLayoutAcaoDAO($idVeiculo, $idGerenciadora);
            return $retorno;
            
        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    /**
     * Retorna o proprietário do layout macro embarcado
     */
    public function getProprietarioLayoutMacro($idVeiculo, $idGerenciadora = null){
        try{
            $retorno = $this->dao->getProprietarioLayoutMacroDAO($idVeiculo, $idGerenciadora);
            return $retorno;
            
        }catch (Exception $e){
            $this->view->mensagemErro = $e->getMessage();
        }
    }

} 