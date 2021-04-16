<?php

/**
 * 
 * @author Robson Silva
 * @since 20/05/2013
 * @package modulos/Relatorio/DAO
 */
class RelAprovacaoCreditoDAO {
	 
    /**
     * Link de conexão com o banco
     * @var resource
     */
    private $conn;

    /**
     * Construtor
     * @param resource $conexao
     */
    public function __construct($conexao) {
        $this->conn  = $conexao;
    }
    
	/**
     * Busca os tipos de contratos a serem utilizados no combo
     * @return object
     * @throws Exception
     */
	public function buscarTiposContratos() {
		//Tipos de contratos
        $tipos = array();
        //Define o retorno
        $retorno = new stdClass();
        try{
            $sql = "SELECT tpcoid, tpcdescricao FROM tipo_contrato ORDER BY tpcdescricao";
            
            $query = pg_query($sql);
            if (!is_resource($query)){
                throw new Exception("Erro ao realizar a pesquisa.");
            }
            if (pg_num_rows($query) > 0) {
				while ($row = pg_fetch_object($query)) {
					array_push($tipos, $row);
				}
			}
            $retorno->error = false;
            $retorno->dados = $tipos;
            return $retorno;
        } catch (Exception $e){
            $retorno->error = true;
            $retorno->message = $e->getMessage();
            return $retorno;
        }		
	}
    
    /**
     * Converte um data no formato DD/MM/YYYY para YYYY-MM-DD
     * @param string $date
     * @return string
     */
	public function dateToDb($date) {
		if (empty($date)) {
			return '';
		}
		
		$data_array = explode('/', $date);
		$date  = $data_array[2].'-'.$data_array[1].'-'.$data_array[0];
		return $date;
	}
    
    /**
     * Prepara filtros para pesquisa do relatório e CSV
     * @param stdClass $filtros
     * @return string 
     */
    public function filtrosPesquisa(stdClass $filtros) {
    	
    	$filtro = "";
        
        //Data inicia e final
        $dt_ini = '';
        $dt_fim = '';
        
        //Faz a conversão das datas
        if ( isset( $filtros->dt_ini ) && !empty( $filtros->dt_ini ) ) {
            $dt_ini = $this->dateToDb( $filtros->dt_ini) . ' 00:00:00';
        }
        
        if ( isset( $filtros->dt_fim ) && !empty( $filtros->dt_fim ) ) {
            $dt_fim = $this->dateToDb( $filtros->dt_fim ) . ' 23:59:59';
        }
        
        //Filtro por período
        if ( !empty($dt_ini) && !empty($dt_fim) ) {
            $filtro .= "AND (proposta.prpdt_cadastro BETWEEN '".$dt_ini."' AND '".$dt_fim."')";
        }
        
        //Filtra pelo status da aprovação do gestor de crédito.
        if ( isset($filtros->cb_gestor) && !empty($filtros->cb_gestor) ){
            
            if ($filtros->cb_gestor == "aguardando"){
                $filtro .= "AND (proposta.prppsfoidgestor = 1)";
            }
            else if ($filtros->cb_gestor == "aprovado"){
               $filtro .= "AND (proposta.prppsfoidgestor = 2)";
            } else {
               $filtro .= "AND (proposta.prppsfoidgestor = 3)";
            }
             
        }
        
        //Filtra pelo status da aprovação financeiro.
        if ( isset($filtros->cb_financeiro) && !empty($filtros->cb_financeiro) ){
            
            if ($filtros->cb_financeiro == "aguardando"){
                $filtro .= "AND (proposta.prppsfoid = 1)";
            } else if ($filtros->cb_financeiro == "aprovado"){
                $filtro .= "AND (proposta.prppsfoid = 2)";
            } else {
                $filtro .= "AND (proposta.prppsfoid = 3)";
            }
        }
        
        //Filtra pelo tipo de proposta
        if ( isset($filtros->cb_tipo_proposta) && !empty($filtros->cb_tipo_proposta) ){
            $filtro .= "AND (proposta.prptipo_proposta = '".$filtros->cb_tipo_proposta."')";
        }
        //Filtra pelo tipo de contrato
        if ( isset($filtros->cb_tipo_contrato) && is_int($filtros->cb_tipo_contrato) ) {
            $filtro .= "AND (proposta.prptpcoid = '".$filtros->cb_tipo_contrato."')";
        }
        
    	return $filtro;
    }
    
    /**
     * Retorna a consulta do relatório
     * @param stdClass $filtros Filtros do relatório
     * @return array
     * @throws Exception
     */
    public function pesquisa(stdClass $filtros ){
        
    	$filtro = $this->filtrosPesquisa($filtros);
        //Define o retorno
        $retorno = new stdClass();
    	try{
            
    		$sql = "
                SELECT proposta.prptermo as contrato,
                       CASE 
                            WHEN
                                clientes.clinome IS NOT NULL THEN
                                    clientes.clinome
                            ELSE
                                proposta.prplocatario
                       END AS cliente,                       
                       proposta.prptipo_proposta as tipo_proposta,
                       tipo_contrato.tpcdescricao as tipo_contrato,
                       to_char(proposta.prpdt_aprovacao_fin, 'DD/MM/YYYY') as data_aprovacao,
                       proposta_status_financeiro.psfdescricao as status_proposta,
                       usuarios.nm_usuario as usuario
                  FROM proposta
                 INNER JOIN contrato
                    ON (contrato.connumero = proposta.prptermo)
                 INNER JOIN tipo_contrato
                    ON (tipo_contrato.tpcoid = proposta.prptpcoid)
                 LEFT JOIN clientes
                    ON (clientes.clioid = contrato.conclioid)
                 INNER JOIN proposta_status_financeiro
                    ON (proposta_status_financeiro.psfoid = proposta.prppsfoid)
                  LEFT JOIN proposta_status_financeiro as gestor
                    ON (gestor.psfoid = proposta.prppsfoidgestor)
                 LEFT JOIN usuarios 
                    ON (usuarios.cd_usuario = proposta.prpusuoid_aprovacao_fin)
                 WHERE TRUE
                       $filtro                           
                 ORDER BY proposta.prpdt_cadastro";    		
            
    		ob_start();
            $qrRelatorio = pg_query($this->conn, $sql);
            ob_end_clean();
            
            if (!is_resource($qrRelatorio)){
                throw new Exception("Houve um erro ao realizar a pesquisa.");
            }
            
            //Retorno
            $retorno->error = false;
            $retorno->resource = $qrRelatorio;
            return $retorno;
        
        } catch(Exception $e) {
            $retorno->error = true;
            $retorno->message = $e->getMessage();
            return $retorno;
        }
    }
    
    /**
     * 
     * @param stdClass $filtros
     * @return object
     * @throws Exception
     */
    public function pesquisaCsv(stdClass $filtros ){
    	
		$filtro = $this->filtrosPesquisa($filtros);    	
        //Define o retorno
        $retorno = new stdClass();
		try{
    	
    		$sql = "
                SELECT proposta.prptermo as contrato,
                       CASE 
                            WHEN
                                clientes.clinome IS NOT NULL THEN
                                    clientes.clinome
                            ELSE
                                proposta.prplocatario
                       END AS cliente,    
                       tipo_contrato.tpcdescricao as tipo_contrato,
                       CASE 
                        WHEN clientes.clitipo = 'F' THEN clientes.clino_cpf
                        WHEN clientes.clitipo = 'J' THEN clientes.clino_cgc
                        ELSE NULL
                       END as cnpf,
                       to_char(proposta.prpdt_cadastro, 'DD/MM/YYYY') as data_cadastro,
                       proposta.prpstatus as status_proposta,
                       CASE 
                        WHEN clientes.clitipo = 'J' THEN 'Jurídica'
                        WHEN clientes.clitipo = 'F' THEN 'Física'
                        ELSE NULL
                       END as tipo_pessoa,
                       proposta.prptipo_proposta as tipo_proposta,
                       regexp_replace(proposta.prpobservacao_financeiro, E'[\\n\\r]+', ' ', 'g' ) as observacao_financeiro,
                       regexp_replace(proposta.prpresultado_aciap, E'[\\n\\r]+', ' ', 'g' ) as aciap,
                       to_char(proposta.prpdt_aprovacao_fin, 'DD/MM/YYYY') as data_aprovacao,
                       proposta_status_financeiro.psfdescricao as status_financeiro,
                       usuarios.nm_usuario as usuario,
                       usuarios. ds_login as login_usuario
                FROM proposta
                INNER JOIN contrato
                    ON (contrato.connumero = proposta.prptermo)
                LEFT JOIN clientes
                    ON (clientes.clioid = contrato.conclioid)
                INNER JOIN tipo_contrato
                    ON (tipo_contrato.tpcoid = proposta.prptpcoid)
                INNER JOIN proposta_status_financeiro
                    ON (proposta_status_financeiro.psfoid = proposta.prppsfoid)
                LEFT JOIN proposta_status_financeiro as gestor
                    ON (gestor.psfoid = proposta.prppsfoidgestor)
                LEFT JOIN usuarios 
                    ON (usuarios.cd_usuario = proposta.prpusuoid_aprovacao_fin)
                WHERE TRUE
                       $filtro
                ORDER BY proposta.prpdt_cadastro";

    		ob_start();
    		$qrRelatorio = pg_query($this->conn, $sql);
    		ob_end_clean();
    	
    		if (!is_resource($qrRelatorio)){
	    		throw new Exception("Houve um erro ao realizar a pesquisa.");
	    	}
            //Retorno
            $retorno->error = false;
            $retorno->resource = $qrRelatorio;
            return $retorno;
        
        } catch(Exception $e) {
            $retorno->error = true;
            $retorno->message = $e->getMessage();
            return $retorno;
        }
    }

}