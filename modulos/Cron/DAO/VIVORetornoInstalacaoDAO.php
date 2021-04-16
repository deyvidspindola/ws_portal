<?php

/**
 * Camada de regras de persistência de dados.
 * 
 * @package VIVORetornoInstalacaoDAO
 * @author  Angelo Frizzo <angelo.frizzo@meta.com.br>
 * @since   01/10/2013
 * 
 */
class VIVORetornoInstalacaoDAO {

    /**
     * Objeto Parâmetros.
     *
     * @var stdClass
     */
    private $conn;    
    
    /**
     * Metodo Construtor
     *
     * @return $this->conn
     */
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Lista as ordens de serviço sem Retorno de Instalação para a Vivo
     * 
     * @return array 
     */
    public function buscarOrdemServicoSemRetorno() {
        //echo '\n\n';
        //echo "****** BUSCA ORDENS DE SERVIÇOS SEM RETORNO DE INSTALAÇÃO ENVIADO PARA A VIVO ******\n";
        $sql = "
			SELECT DISTINCT
				ordoid,
				vppaoid,
				ordstatus,
				vppapedido AS numeroPedido,
				vppaversao AS versao,
				vppalinha AS numeroLinha,
				(CASE WHEN ordstatus = 9 THEN 'NOK' ELSE 'OK' END) AS status,
				(CASE WHEN ordstatus = 9 THEN aoamdescricao ELSE '' END) AS descricaoStatus
			FROM 
				contrato
			INNER JOIN 
				ordem_servico ON (ordconnumero = connumero AND ordstatus IN (3,9) AND conno_tipo = 844 ) 
			INNER JOIN 
				ordem_servico_item ON (ositordoid = ordoid AND (ositstatus = 'X' OR ositstatus = 'C'))  
			INNER JOIN 
				os_tipo_item ON (otioid = ositotioid AND otitipo = 'E') 
			LEFT JOIN 
				analise_os_acao_motivo ON aoamoid = ordaoamoid
			INNER JOIN 
				veiculo_pedido_parceiro ON vppaconoid = connumero
			WHERE 
				vpparetorno_instalacao is null
				AND ordoid IN ( 
					SELECT
						ordoid
					FROM 
						ordem_servico
					WHERE
						ordconnumero = connumero
					ORDER BY 
						ordoid DESC
					LIMIT 1
				)              
			ORDER BY 
				vppaoid DESC; ";

        $rs = pg_query($this->conn, $sql);

        $buscarOrdemServicoSemRetorno = array();

        for ($i = 0; $i < pg_num_rows($rs); $i++) {
            $buscarOrdemServicoSemRetorno[$i]['idRetorno'] = pg_fetch_result($rs, $i, 'vppaoid');
            $buscarOrdemServicoSemRetorno[$i]['ordStatusRetorno'] = pg_fetch_result($rs, $i, 'ordstatus');
            $buscarOrdemServicoSemRetorno[$i]['numeroPedido'] = pg_fetch_result($rs, $i, 'numeroPedido');
            $buscarOrdemServicoSemRetorno[$i]['versao'] = pg_fetch_result($rs, $i, 'versao');
            $buscarOrdemServicoSemRetorno[$i]['numeroLinha'] = pg_fetch_result($rs, $i, 'numeroLinha');
            $buscarOrdemServicoSemRetorno[$i]['status'] = pg_fetch_result($rs, $i, 'status');
            $buscarOrdemServicoSemRetorno[$i]['descricaoStatus'] = pg_fetch_result($rs, $i, 'descricaoStatus');
        }
        return $buscarOrdemServicoSemRetorno;
    }
    
    /**
     * Método que atualiza na base retorno instalação após chamada do WS VIVO Atualiza Pedido
     * 
     * @param string $id      => Id do registro a ser localizado na tabela veiculo_pedido_parceiro  
     * @param string $retorno => Status Retorno Instalação (Instalado = TRUE ou Cancelado = FALSE)
     * 
     * @return boolean $atualizado => True ou False
     */
	public function atualizaRetornoInstalacao($id, $retorno) {


        //3.2.1.5   O sistema desvincula número do contrato, 
        //do veículo anterior, no caso de troca 
        $sql = "
			UPDATE
				veiculo_pedido_parceiro
			SET
				vppaconoid =  NULL
			WHERE
				vppaoid  IN  (
					SELECT 
						vppaoid
					FROM 
						veiculo_pedido_parceiro
					WHERE
						vppaconoid = (
							SELECT 
								vppaconoid
							FROM 
								veiculo_pedido_parceiro
							WHERE 
								vppaoid = " . $id . "
						) 
						AND vppaoid NOT IN (
								SELECT 
									MAX(vppaoid)
								FROM 
									veiculo_pedido_parceiro
								WHERE
									vppaconoid = (
										SELECT 
											vppaconoid
										FROM 
											veiculo_pedido_parceiro
										WHERE 
											vppaoid = " . $id . "
									)                            
						)                                
				); ";

        pg_query($this->conn, $sql);

        $sql = "
			UPDATE 
				veiculo_pedido_parceiro
			SET 
				vpparetorno_instalacao = '".$retorno."'
			WHERE 
				vppaoid = ". $id ."";
    
        return pg_affected_rows(pg_query($this->conn, $sql));
    }    

}