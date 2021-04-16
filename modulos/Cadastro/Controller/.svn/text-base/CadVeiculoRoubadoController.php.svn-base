<?php
namespace modulos\Cadastro\Controller;

use Exception;
use Logger;
use modulos\Cadastro\DAO\CadVeiculoRoubadoDAO;
use modulos\Commun\Controller\AbstractController;

require_once _MODULEDIR_ . 'Commun/Controller/AbstractController.php';
require_once _MODULEDIR_ . 'Cadastro/DAO/CadVeiculoRoubadoDAO.php';

/**
 *
 * Classe padrão para Action
 *
 * @package modulos/Cadastro/Action/CadVeiculoRoubadoAction
 * @since   version
 * @category Action
 */
class CadVeiculoRoubadoController extends AbstractController {
    
    
    private $cadVeiculoRoubadoDAO;
    
    public $resultadoPesquisa = array();
    
    private static $__INDEX__ = "Cadastro/View/cad_veiculo_roubado/index";
    
    private static $__MSG_SUCESSO_1 = "Veículo sinalizado como roubado com sucesso, em breve será removido do SASWEB /AVL /CARGO TRACCK!";
    //$msg = "Ocorreu um erro ao sinalizar o veÃ­culo como roubado.";
    private static $__MSG_SUCESSO_2 = "Veículo sinalizado como roubado com sucesso, em breve serÃ¡ removido do SASWEB/AVL/SASGC!";
    
    private static $__MSG_ERRO_1 = "Ocorreu um erro ao sinalizar o veículo como roubado!";
    private static $__MSG_ERRO_2 = "Ocorreu um erro ao retirar veiculo da Grid do SASWEB!";
    private static $__MSG_ERRO_3 = "Ocorreu um erro ao retirar veiculo da Grid da Cargo Tracck!";
    private static $__MSG_ERRO_4 = "Ocorreu um erro ao retirar veiculo da Grid do SASGC!";
    private static $__MSG_ERRO_5 = "PLACA NÃO ENCONTRADA !";
    
    private static $__MSG_ERRO_VAL_CAMPO_1 = "É necessário informar uma placa valida!";
    
    public function __construct() {
        parent::__construct();
        $this->cadVeiculoRoubadoDAO = new CadVeiculoRoubadoDAO();
        $this->arrayLog = array('prefixoNomeArq' => "sinalizar_veiculo_roubado");
    }
    
    /**
     *
     * @param string $acao
     * @param array $resultadoPesquisa
     *
     * @return \modulos\Cadastro\Action\$__INDEX__
     */
    public function index($acao = 'index',$resultadoPesquisa = array()) {
        return $this->view($acao);
    }
    
    /**
     *
     * @return \modulos\Cadastro\Action\$__INDEX__
     */
    public function principal(){
        return $this->index(self::$__INDEX__);
    }
    
    /**
     *
     * @throws Exception
     * @return \modulos\Cadastro\Action\$__INDEX__
     */
    public function pesquisar() {
        try {
            $cadVeiculoRoubadoDAO = new CadVeiculoRoubadoDAO();
            if(!$this->resultadoPesquisa = $cadVeiculoRoubadoDAO->buscarVeiculoPorPlaca($this->params['veiplaca'])){
                $this->mensagemErro = self::$__MSG_ERRO_5;
            }
            return $this->index(self::$__INDEX__);
        } catch (Exception $e) {
            $this->mensagemErro = $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     *
     * @throws Exception
     * @return \modulos\Cadastro\Action\$__INDEX__
     */
    public function sinalizarVeiculoRoubado(){
        try {
            /** Inicio da transação */
            $this->cadVeiculoRoubadoDAO->begin(NULL);
            
            $veioid = $this->params['veioid'];
            Logger::logInfo("\n[INICIO] Sinalização Roubo Veiculo: veioid: {$this->params['veioid']}  | placa: {$this->params['veiplaca']}\n", __FILE__, __LINE__, $this->arrayLog);
            
            if($veioid == false || $veioid == '' || $veioid == NULL){
                throw new Exception(self::$__MSG_ERRO_VAL_CAMPO_1);
            }
            
            $this->inserirVeiculoRoubado($veioid);
            $this->removerVeiculoRoubadoGridSasweb($veioid);
            $this->removerVeiculoRoubadoGridSasgc($veioid);
            $this->retirarVeiculoRoubadoGridCargotracck($veioid);
            
            $this->cadVeiculoRoubadoDAO->commit(NULL);
            $this->mensagemSucesso = self::$__MSG_SUCESSO_1;
            
            Logger::logInfo("\n[FIM] Sinalização Roubo Veiculo: veioid: {$this->params['veioid']}  | placa: {$this->params['veiplaca']} \n", __FILE__, __LINE__, $this->arrayLog);
            
            return $this->view(self::$__INDEX__);
            
        } catch (Exception $e) {
            $this->cadVeiculoRoubadoDAO->rollback(NULL);
            Logger::logError("\nERRO AO REMOVER VEICULO GRID CARGOTRACCK | Veiculo: veioid: {$this->params['veioid']}  | placa: {$this->params['veiplaca']} MENSAGEM DE ERRO: {$e->getMessage()} \n", __FILE__, __LINE__, $this->arrayLog);
            
            $this->mensagemErro = $e->getMessage();
            return $this->view(self::$__INDEX__);
        }
    }
    
    /**
     *
     * @param int $veioid
     * @throws Exception
     * @return boolean
     */
    private function inserirVeiculoRoubado($veioid){
        $observacao = "Veiculo inserido pelo funcionario {$_SESSION['usuario']['nome']} [id:{$_SESSION['usuario']['oid']}, login:{$_SESSION['usuario']['login']}]";
        
        Logger::logInfo("\n[INICIO] INSERIR VEICULO ROUBADO VEICULO_ROUBADO: veioid:
                        {$this->params['veioid']}  | placa: {$this->params['veiplaca']}
                        MENSAGEM: {$observacao} \n",
                        __FILE__, __LINE__, $this->arrayLog);
        
        if(! $this->cadVeiculoRoubadoDAO->inserirVeiculoRoubado($veioid, $observacao)){
            throw new Exception(self::$__MSG_ERRO_1);
        }
        return true;
    }
    
    /**
     *
     * @param int $veioid
     * @throws Exception
     * @return boolean
     */
    private function removerVeiculoRoubadoGridSasweb($veioid){
        
        Logger::logInfo("\n[INICIO] REMOVER VEICULO GRID SASWEB | Veiculo: veioid: {$this->params['veioid']}  | placa: {$this->params['veiplaca']} \n", __FILE__, __LINE__, $this->arrayLog);
        
        if(! $this->cadVeiculoRoubadoDAO->removerVeiculoRoubadoGridSasweb($veioid)){
            throw new Exception(self::$__MSG_ERRO_2);
        }
        return true;
    }
    
    /**
     *
     * @param int $veioid
     * @throws Exception
     * @return boolean
     */
    private function removerVeiculoRoubadoGridSasgc($veioid){
        if(! $this->cadVeiculoRoubadoDAO->validarExclusaoSASGC($veioid)){
            return false;
        }
        $observacao = "Veiculo inserido pelo funcionario {$_SESSION['usuario']['nome']}
                        [id:{$_SESSION['usuario']['oid']}, login:{$_SESSION['usuario']['login']}]";
        if(! $this->cadVeiculoRoubadoDAO->removerVeiculoRoubadoGridSasgc($veioid, $observacao)){
            throw new Exception(self::$__MSG_ERRO_4);
        }
        
        Logger::logInfo("\n[INICIO] REMOVER VEICULO GRID SASGC | Veiculo: veioid:{$this->params['veioid']}  | placa: {$this->params['veiplaca']} \n", __FILE__, __LINE__, $this->arrayLog);
        
        return true;
    }
    
    /**
     *
     * @param int $veioid
     * @throws \Exception
     * @return boolean
     */
    private function retirarVeiculoRoubadoGridCargotracck($veioid){
        /** @todo VALIDAR SE VEICULO POSSUI ISCA CT */
        if(! $ctVO = $this->cadVeiculoRoubadoDAO->validarPlacaPossuiEquipamentoCargoTracck($veioid)){
            return false;
        }
        
        if(! $moduloVO = $this->cadVeiculoRoubadoDAO->buscarModuloCargoTracck($ctVO->ccid)){
            return false;
        }
        if(! $this->cadVeiculoRoubadoDAO->removerVeiculoGridCargoTracck($moduloVO->id)){
            throw new \Exception(self::$__MSG_ERRO_3);
        }
        Logger::logInfo("\n[INICIO] REMOVER VEICULO GRID CARGOTRACCK | Veiculo: veioid: {$this->params['veioid']}  | placa: {$this->params['veiplaca']} \n", __FILE__, __LINE__, $this->arrayLog);
        return true;
    }
    
    protected function verificarPermissaoDeAcesso(){
        $this->verificarAcessoPagina($this->autorAcao->departamento);
        $this->verificarAcessoLog($this->autorAcao->departamento);
        $this->verificarAcessoLinkNovaGerenciadora($this->autorAcao->departamento);
    }
    
}