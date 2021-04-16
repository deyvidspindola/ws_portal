<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
 * @version 12/09/2016
 * @since 12/09/2016
 * @package Core
 * @subpackage Classe DAO do Boleto
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Boleto;

use infra\ComumDAO;

class BoletoDAO extends ComumDAO{
    
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Retorna os dados bancarios.
     * Caso for informado forcoid - busca na tabela forma_cobranca;
     * Caso for informado cfbbanco - busca na tabela config_banco;
     *
     * @param int $forcoid ID da forma de cobranÃ§a
     * @param int $cfbbanco ID do Banco
     * @return array
     */
    public function getDadosBancarios($forcoid=0, $cfbbanco=0){
        if($forcoid > 0){
            $sqlString = "SELECT
                            CASE WHEN forccfbbanco = '341' THEN cfbagencia_convenio ELSE cfbagencia END AS cfbagencia,
                            CASE WHEN forccfbbanco = '341' THEN cfbconta_corrente_convenio ELSE cfbconta_corrente END AS cfbconta_corrente,
                            cfbcodigo_cedente
                        FROM 
                            forma_cobranca
                        INNER JOIN
                            config_banco ON cfbbanco = forccfbbanco
                        WHERE
                            forcoid = $forcoid;";            
        } elseif($cfbbanco > 0){
            $sqlString = "SELECT
                            cfbagencia,
                            cfbconta_corrente,
                            cfbcodigo_cedente
                        FROM 
                            config_banco
                        WHERE
                            cfbbanco = $cfbbanco;";  
        } else{
            return array();
        }
        
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return array();
        }
    }
    
    /**
     * @param int $titulo ID do tÃ­tulo
     * @param string $tipo Define em qual tabela realiza a consulta - Tipo do titulo: titulo; consolidado; retencao;
     * @throw
     * @return mixed
     */
    public function consultarRegistroBoleto($titulo, $tipo){
        //  1, -- Boleto
        // 63, -- Titulo Avulso                      
        // 73, -- Cobranca Registrada Itau
        // 74, -- Cobranca Registrada HSBC
        // 84  -- Cobranca Registrada Santander        
        $sqlString  = '';
        $tpetoid    = '';
        $tpetcodigo = '';
        $tipo       = trim(strtolower($tipo));
        $return     = '';
        
        //Recupera os codigos que sao relacionado com o registro do titulo, ou seja, codigos que o sistema
        //determinda quando um titulo esta registrado.
        $rsConfig = $this->getConfiguracoesSistemas();
        
        if(is_array($rsConfig) && !empty($rsConfig)){
            $tpetcodigo = $rsConfig['pcsidescricao'];
            
            //Recuperar os ids do metodo getParametro('COD_MOVIMENTO_PERMITE_ATERACAO')
            $rsEvento = $this->getTipoEventoTitulo($tpetcodigo);
            
            if(is_array($rsEvento) && !empty($rsEvento)){
                foreach($rsEvento as $row){
                    if($tpetoid != ''){
                        $tpetoid .= ','.$row['tpetoid'];
                    } else{
                        $tpetoid = $row['tpetoid'];
                    }
                }
                
                //Recuperar os ids da forma de cobranÃ§a
                $foma_cobranca = $this->getFormasCobrancaRegistro();
                
                if(is_array($foma_cobranca) && !empty($foma_cobranca)){
                	$forcoid = $foma_cobranca['pcsidescricao'];
                }else{
                	throw new Exception('INF009');
                }
               
                if($tipo == 'titulo' || $tipo == 'retencao'){
                    $table = ($tipo == 'titulo') ? 'titulo' : 'titulo_retencao';
                    
                    $sqlString = "SELECT
                                    titoid, titdt_cancelamento, titdt_vencimento, titrtcroid, titnumero_registro_banco
                                FROM
                                    $table
                                WHERE
                                     titnumero_registro_banco IS NOT NULL
                                AND
                                    titdt_pagamento IS NULL
								--AND titformacobranca IN ($forcoid) 
                                AND
                                    tittpetoid IN ($tpetoid)
                                AND
                                    titoid = $titulo;";
                                    
                } elseif($tipo == 'consolidado'){
                   
                	$sqlString = "SELECT
                                    titcoid, titcdt_cancelamento, titcdt_vencimento, titcrtcroid, titcnumero_registro_banco
                                FROM
                                    titulo_consolidado
                                WHERE
                                    titcnumero_registro_banco IS NOT NULL
                                AND
                                    titcdt_pagamento IS NULL
                                --AND titcformacobranca IN ($forcoid) 
                                AND
                                    titctpetoid IN ($tpetoid)
                                AND
                                    titcoid = $titulo;";
                }
                
                
            } else{
                throw new Exception('INF008');
            }
        } else{
            throw new Exception('INF007');
        }

        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            $return = true;
        } else{
            $return = false;
        }
        
        return $return;
    }
    
    /**
     * Retorna as instruções para apresentar no boleto.
     * @return array
     */
    public function getInstrucoes($tipoBoleto = null){
        if (empty($tipoBoleto)) {
            $tipoBoleto = 'INSTRUCOES_BOLETO';
        }

        $sqlString = "SELECT
                        pcsidescricao
                    FROM
                        parametros_configuracoes_sistemas
                    INNER JOIN
                        parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
                    WHERE
                        pcsipcsoid = 'COBRANCA_REGISTRADA'
                    AND
                        pcsioid = '$tipoBoleto';";
        
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return array();
        }
    }
    
    
    /**
     * Recupera a formas de cobranÃ§a do tÃ­tulo que sÃ£o permitidas para registro
     * 
     * @return \infra\Array
     */
    private function getFormasCobrancaRegistro(){
    	
    	$sqlString = " SELECT
	                        pcsidescricao
	                    FROM
	                        parametros_configuracoes_sistemas
	                    INNER JOIN
	                        parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
	                    WHERE
	                        pcsipcsoid = 'COBRANCA_REGISTRADA'
	                    AND
	                        pcsioid = 'FORMAS_COBRANCA_PARA_REGISTRO'; ";
    	
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Recupera os cÃ³digos que sÃ£o relacionado com o registro do tÃ­tulo, ou seja, cÃ³digos que o sistema
     * determinda quando um tÃ­tulo estÃ¡ registrado.
     *
     * @return array
     */
    private function getConfiguracoesSistemas(){
        $sqlString = "SELECT
                        pcsidescricao
                    FROM
                        parametros_configuracoes_sistemas
                    INNER JOIN
                        parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
                    WHERE
                        pcsipcsoid = 'COBRANCA_REGISTRADA'
                    AND
                        pcsioid = 'COD_MOVIMENTO_REGISTRADO';";
        
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return array();
        }
    }
    
    /**
     * Recuperar os ids do metodo getParametro('COD_MOVIMENTO_PERMITE_ATERACAO')
     *
     * @param String $tpetcodigo = '2,14,27'
     * @return array
     */
    private function getTipoEventoTitulo($tpetcodigo){
        $sqlString = "SELECT
                        tpetoid
                    FROM
                        tipo_evento_titulo
                    WHERE
                        tpetcfbbanco = 33 
                    AND
                        tpetcob_registrada IS TRUE
                    AND
                        tpettipo_evento = 'Retorno'
                    AND
                        tpetcodigo IN ($tpetcodigo);";
                        
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAll();
        } else{
            return array();
        }
    }
    
    
    /**
     * Retorna o nome da tabela em que o título se enconra
     * @return string
     */
    public function getTabelaTitulo($titoid){
    	
    	$sql = "SELECT 'titulo' AS tabela
		    	FROM titulo
		    	WHERE titoid = $titoid
		    	
		    	UNION ALL
		    	
		    	SELECT 'retencao' AS tabela
		    	FROM titulo_retencao
		    	WHERE titoid = $titoid
		    	
		    	UNION ALL
		    	
		    	SELECT 'consolidado' AS tabela
		    	FROM titulo_consolidado
		    	WHERE titcoid = $titoid ";
    	
    	$this->queryExec($sql);
    	
    	if($this->getNumRows() > 0)
    		return $this->getAssoc();
    		
    		return false;
    		
    }    
    
    
    
    /**
     * Retorna a data de vencimento de um boleto
     * @return string
     */
    public function getDataVencimento($titoid){
        
        $sql = "SELECT titdt_vencimento 
				FROM titulo
				WHERE titoid = $titoid 
				
				UNION ALL
				
				SELECT titdt_vencimento 
				FROM titulo_retencao
				WHERE titoid = $titoid 
				
				UNION ALL
				
				SELECT titcdt_vencimento 
				FROM titulo_consolidado
				WHERE titcoid = $titoid 
				
				LIMIT 1;";
        
        $this->queryExec($sql);

        if($this->getNumRows() > 0)
            return $this->getAssoc();

        return false;

    }    
    
    /**
     * Retorna os prazso estipulados pela Febraban com o limite de valor e datas para registro de boletos
     * @return array|\infra\Array
     */
    public function getVerificaPrazosFebraban(){
    	  	     	
      // Verifica os prazos
    	
      $sql_prazos = " SELECT pcsidescricao
                      FROM parametros_configuracoes_sistemas
                      INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
                      WHERE pcsipcsoid = 'COBRANCA_REGISTRADA'
                      AND pcsioid = 'PRAZOS_FEBRABAN'; ";
    	     	
      $this->queryExec($sql_prazos);
    	     	
      if($this->getNumRows() > 0){
        return $this->getAssoc();
      }
    	     	
      return array();

    }
    
    /**
     * Verifica se o id de título fornecido é de retenção (boleto seco).
     * @return bool
     */
     public function isTituloRetencao($titoId)
     {
         if (!is_numeric($titoId)) {
             return false;
         }

         $sql = "SELECT TITOID FROM TITULO_RETENCAO WHERE TITOID = $titoId";
         $this->queryExec($sql);
         return $this->getNumRows() > 0;
     }
     
    /**
     * Retorna o parâmetro de sistema DIAS_BAIXA_DEVOLUCAO_BOLETO_SECO.
     * @return int
     */
    public function getDiasBaixaDevolucaoBoletoSeco() {
        $sql = "SELECT pcsidescricao
                  FROM parametros_configuracoes_sistemas_itens 
                 WHERE pcsioid = 'DIAS_BAIXA_DEVOLUCAO_BOLETO_SECO'";
        $this->queryExec($sql);
        $result = $this->getAssoc();
        return $result['pcsidescricao'];
    }
    
    /**
     * Retorna a forma de registro do Boleto no banco (XML ou CNAB)
     * @return string
     */
    public function getformaRegistro($titoid){ 
		$sql = "
			SELECT 
				COUNT(1) AS qtde
			FROM 
				titulo_historico_online
			WHERE
				thotitoid = $titoid;
			";
        
        $this->queryExec($sql);
        $result = $this->getAssoc();
		$retorno = ($result['qtde'] > 0) ? 'XML' : 'CNAB';
			
		return $retorno;
    }
}