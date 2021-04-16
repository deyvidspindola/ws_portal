<?php
/**
* @author   Leandro Alves Ivanaga
* @email    leandroivanaga@brq.com
* @since    26/08/2013
* */

require_once _MODULEDIR_.'Principal/DAO/PrnParametrosSiggoDAO.class.php';

/**
 * Trata requisições de ações relacionadas a tabela parametros_gerais
 */
class PrnParametrosSiggo {
    
    /**
     * Fornece acesso aos dados necessarios para o módulo
     * @property prnParametrosSiggoDao
     */
    private $prnParametrosSiggoDao;
    
    /**
     * Construtor, configura acesso a dados e parâmetros iniciais do módulo
     */
    public function __construct() 
    {
        global $conn;
        
        $this->prnParametrosSiggoDao = new PrnParametrosSiggoDAO($conn);
    }
    
    // Função para buscar o valor de acordo com os parametros passados
    public function getValorParametros($params = array()) {
    	
    	try {
    		/** Verificar se foi passado os valores obrigatórios na pesquisa **/
    		if (!is_array($params) || empty($params)) {
    			throw new Exception ("Para realizar a busca é necessário informar pelo menos o Tipo de proposta e Nome do Parâmetro.");	
    		}
    			 
			if ($this->informado($params['id_tipo_proposta']) == false || $this->informado($params['nome_parametro']) == false) {
				throw new Exception ("Para realizar a busca é necessário informar pelo menos o Tipo de proposta e Nome do Parâmetro.");
			}
    		
			$params = $this->retiraParametrosVazios($params);
			
			/** A busca do valor deve seguir a seguinte ordem, se não encontrano nivel acima tenta a busca pelo nivel abaixo
			 		1 - tipo de proposta > subtipo de proposta > tipo contrato > tipo classe > parâmetro;
					2 - tipo de proposta > subtipo de proposta > tipo contrato > parâmetro;
					3 - tipo de proposta > subtipo de proposta > tipo classe > parâmetro;
					4 - tipo de proposta > subtipo de proposta > parâmetro;
					5 - tipo de proposta > tipo contrato > tipo classe > parâmetro;
					6 - tipo de proposta > tipo contrato > parâmetro;
					7 - tipo de proposta > tipo classe > parâmetro;
					8 - tipo de proposta > parâmetro;
					9 - parâmetro;
			 */
			
			// Busca 1 - Se todos os parametros forem passados
			if ($this->informado($params['id_subtipo_proposta']) && $this->informado($params['id_tipo_contrato']) && $this->informado($params['id_equipamento_classe']) ) {
				$paramsBusca = $params;
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Busca 2 - Não houve resultado na busca acima
			if (empty($resultado) && $this->informado($params['id_subtipo_proposta']) && $this->informado($params['id_tipo_contrato'])) {
				$paramsBusca = $params;
				unset ($paramsBusca['id_equipamento_classe']); 
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Busca 3 - Não houve resultado na busca acima
			if (empty($resultado) && $this->informado($params['id_subtipo_proposta']) && $this->informado($params['id_equipamento_classe'])) {
				$paramsBusca = $params;
				unset ($paramsBusca['id_tipo_contrato']);
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Busca 4 - Não houve resultado na busca acima
			if (empty($resultado) && $this->informado($params['id_subtipo_proposta'])) {
				$paramsBusca = $params;
				unset ($paramsBusca['id_tipo_contrato']);
				unset ($paramsBusca['id_equipamento_classe']);
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Busca 5 - Não houve resultado na busca acima
			if (empty($resultado) && $this->informado($params['id_tipo_contrato']) && $this->informado($params['id_equipamento_classe'])) {
				$paramsBusca = $params;
				unset ($paramsBusca['id_subtipo_proposta']);
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Busca 6 - Não houve resultado na busca acima
			if (empty($resultado) && $this->informado($params['id_tipo_contrato'])) {
				$paramsBusca = $params;
				unset ($paramsBusca['id_subtipo_proposta']);
				unset ($paramsBusca['id_equipamento_classe']);
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Busca 7 - Não houve resultado na busca acima
			if (empty($resultado) && $this->informado($params['id_equipamento_classe'])) {
				$paramsBusca = $params;
				unset ($paramsBusca['id_subtipo_proposta']);
				unset ($paramsBusca['id_tipo_contrato']);
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Busca 8 - Não houve resultado na busca acima
			if (empty($resultado)) {
				$paramsBusca = $params;
				unset ($paramsBusca['id_subtipo_proposta']);
				unset ($paramsBusca['id_tipo_contrato']);
				unset ($paramsBusca['id_equipamento_classe']);
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Busca 9 - Não houve resultado na busca acima (Busca apenas pelo parametro)
			if (empty($resultado)) {
				$paramsBusca = $params;
				unset ($paramsBusca['id_tipo_proposta']);
				unset ($paramsBusca['id_subtipo_proposta']);
				unset ($paramsBusca['id_tipo_contrato']);
				unset ($paramsBusca['id_equipamento_classe']);
				$resultado = $this->prnParametrosSiggoDao->getValorParametros($paramsBusca);
			}
			
			// Se não tiver encontrado nenhum valor, retorna msg de erro
			if (empty($resultado)) {
					
				throw new Exception ("Não foi possível encontrar nenhum valor com os paramêtros informados.");
			}
		

			$retorno = array(
						'erro'					=>	0,
						$resultado->parsnome	=>	$resultado->parsvalor,
						'valor'					=>	$resultado->parsvalor		
					);
			
			return $retorno;
				
    	}catch (Exception $e) {
	    	$retorno = array(
		    			'erro'		=> 1,
		    			'msg'		=> $e->getMessage()
	    			);
	    	return $retorno;
	    }    	
    }
    
    private function informado($parametro) {
    	
    	// Retorna se o parametro passado contem ou não valor
    	// True -> contem valor
    	// False -> sem valor
    	if (!empty($parametro) || strlen($parametro) > 0) {
    		return true;
    	}
    	
    	return false;
    }
    
    private function retiraParametrosVazios($params = array()) {
    	foreach ($params AS $key => $val) {
    		
    		// Se o parametro passado estiver vazio retira do array de parametros
    		if ($this->informado($val) == false){
    			unset ($params[$key]);
    		}    		
    	}
    	
    	return $params;
    }
    
}