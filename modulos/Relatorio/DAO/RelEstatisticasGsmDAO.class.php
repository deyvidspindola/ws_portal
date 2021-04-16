<?php
/**
 * @file RelEstatisticasGsmDAO.class.php
 * @author Paulo Henrique da Silva Junior
 * @version 20/08/2013
 * @since 20/08/2013
 * @package SASCAR RelEstatisticasGsmDAO.class.php
*/
class RelEstatisticasGsmDAO {
    
    private $conn;
    private $ora_user;
    private $ora_senha;
    private $ora_bd;
    private $conn_oracle;
    
    public function __construct() {        
        global $conn;
        global $ora_user;
        global $ora_senha;
        global $ora_bd;
        $this->conn = $conn;
        $this->ora_user = $ora_user;
        $this->ora_senha = $ora_senha;
        $this->ora_bd = $ora_bd;
        $this->cd_usuario = $_SESSION['usuario']['oid'];    


        try {	
		$this->conn_oracle = oci_connect($this->ora_user, $this->ora_senha, $this->ora_bd);

		// Trata a conexão
			if (!$this->conn_oracle) {
				$e = oci_error();
				throw new exception (htmlentities($e['message'], ENT_QUOTES));
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
    }


    public function getDadosVeiculo ($veioid) {

    	try {
		$bufferDados = array();
		$entradas = '';
		$saidas = '';

    	// $sqlBuscaDadosOracle = "SELECT to_char(UPOSDATAHORA - 180/1440, 'DD/MM/YYYY HH24:MI:SS') AS UPOSDATAHORA, to_char(uposdatachegada - 180/1440,'DD/MM/YYYY hh24:mi:ss') AS UPOSDATACHEGADA FROM SASCAR.ULTPOSICAO_VIEW WHERE UPOSVEIOID = 541981";
    	$sqlBuscaDadosOracle = "
			SELECT
			    to_char(CAST(FROM_TZ(CAST(UPOSDATAHORA AS TIMESTAMP),'GMT') AT TIME ZONE SESSIONTIMEZONE as date), 'DD/MM/YYYY HH24:MI:SS') AS UPOSDATAHORA,
	            to_char(CAST(FROM_TZ(CAST(UPOSDATACHEGADA AS TIMESTAMP),'GMT') AT TIME ZONE SESSIONTIMEZONE as date), 'DD/MM/YYYY HH24:MI:SS') AS UPOSDATACHEGADA,
				UPOSGPS_VELOCIDADE,
				UPOSIGNICAO,
				UPOSBLOQUEIO,
				'0' AS MUNICIPIO,
				'0' AS UF,
				'0' AS RUA,
				'0' AS NUMERO,
				(SELECT x FROM TABLE(SDO_UTIL.GETVERTICES(UPOSCOORDENADA_LAT_LONG))) AS LONGITUDE,
				(SELECT y FROM TABLE(SDO_UTIL.GETVERTICES(UPOSCOORDENADA_LAT_LONG))) AS LATITUDE,
        		'0' AS ALERTA_INTERNO,
        		UPOSGPS_ODOMETRO,
				UPOSTEMPERATURA1,
				UPOSTEMPERATURA2,
				UPOSRPM,
				UPOSID_IBUTTON ,
				UPOSSAIDAS,
				UPOSENTRADAS,
				UPOSGPS_VALIDO,
				UPOSSATELITES,
				UPOSPOS_MEMORIA, 
				UPOSVCC_ALIM AS BATERIA,
				(
					CASE WHEN UPOSENTRADA7 > 0 THEN
						1
					ELSE
						0
					END
				) AS entrada_bit1,
				(
					CASE WHEN UPOSENTRADA6 > 0 THEN
						1
					ELSE
						0
					END
				) AS entrada_bit2,
				(
					CASE WHEN UPOSENTRADA5 > 0 THEN
						1
					ELSE
						0
					END
				) AS entrada_bit3,
				(
					CASE WHEN UPOSENTRADA4 > 0 THEN
						1
					ELSE
						0
					END
				) AS entrada_bit4,
				(
					CASE WHEN UPOSENTRADA3 > 0 THEN
						1
					ELSE
						0
					END
				) AS entrada_bit5,
				(
					CASE WHEN UPOSENTRADA2 > 0 THEN
						1
					ELSE
						0
					END
				) AS entrada_bit6,
				(
					CASE WHEN UPOSENTRADA1 > 0 THEN
						1
					ELSE
						0
					END
				) AS entrada_bit7,
				(
					CASE WHEN UPOSENTRADA0 > 0 THEN
						1
					ELSE
						0
					END
				) AS entrada_bit8,
				(
					CASE WHEN UPOSSAIDA7 > 0 THEN
						1
					ELSE
						0
					END
				) AS saida_bit1,
				(
					CASE WHEN UPOSSAIDA6 > 0 THEN
						1
					ELSE
						0
					END
				) AS saida_bit2,
				(
					CASE WHEN UPOSSAIDA5 > 0 THEN
						1
					ELSE
						0
					END
				) AS saida_bit3,
				(
					CASE WHEN UPOSSAIDA4 > 0 THEN
						1
					ELSE
						0
					END
				) AS saida_bit4,
				(
					CASE WHEN UPOSSAIDA3 > 0 THEN
						1
					ELSE
						0
					END
				) AS saida_bit5,
				(
					CASE WHEN UPOSSAIDA2 > 0 THEN
						1
					ELSE
						0
					END
				) AS saida_bit6,
				(
					CASE WHEN UPOSSAIDA1 > 0 THEN
						1
					ELSE
						0
					END
				) AS saida_bit7,
				(
					CASE WHEN UPOSSAIDA0 > 0 THEN
						1
					ELSE
						0
					END
				) AS saida_bit8
			FROM
				SASCAR.ULTPOSICAO_VIEW
			WHERE
				UPOSVEIOID = $veioid";

// var_dump($sqlBuscaDadosOracle);
			// Contagem de linhas
			// $numRowsReturned = oci_num_rows(oci_parse($this->conn_oracle, $sqlBuscaDadosOracle));
			// var_dump($numRowsReturned);

			// Quando houver resultado
				// Query de contagem de linhas
			$sqlContaLinhas = "SELECT COUNT(*) AS NUM_ROWS FROM ($sqlBuscaDadosOracle)";
			// Contagem de linhas
			$stmtContaLinhas = oci_parse($this->conn_oracle, $sqlContaLinhas);
			oci_execute($stmtContaLinhas);
			$numRowsReturned = oci_fetch_assoc($stmtContaLinhas);

			oci_free_statement($stmtContaLinhas);

			// Quando houver resultado
			if ($numRowsReturned['NUM_ROWS'] > 0) {


				/*
				 * Armazene o retorno da busca em memória
				 */
				$stmtBuscaDadosOracle = oci_parse($this->conn_oracle, $sqlBuscaDadosOracle);

				if (!oci_execute($stmtBuscaDadosOracle)) {
					throw new Exception('Erro ao buscar dados.');
				}

				$dados = oci_fetch_assoc($stmtBuscaDadosOracle);

				/*
				 * Monta Byte de entradas
				}
				*/
			    $entradas = sprintf("%08d",decbin($dados['UPOSENTRADAS']));
				
				/*
				 * Monta Byte de saidas
				*/
				
				$saidas = sprintf("%08d",decbin($dados['UPOSSAIDAS']));
				
				/*
				 * Monta estrutura de retorno
				 */
				$bufferDados = array(
					'datahora'			=> $dados['UPOSDATAHORA'],
					'datahorachegada'	=> $dados['UPOSDATACHEGADA'],
					'velocidade'		=> $dados['UPOSGPS_VELOCIDADE'],
					'ignicao'			=> $dados['UPOSIGNICAO'],
					'bloq'				=> $dados['UPOSBLOQUEIO'],
					'bateria'			=> $dados['BATERIA'],
					'municipio'			=> $dados['MUNICIPIO'],
					'uf'				=> $dados['UF'],
					'rua'				=> $dados['RUA'],
					'numero'			=> $dados['NUMERO'],
					'long2'				=> $dados['LONGITUDE'],
					'lat2'				=> $dados['LATITUDE'],
					'alerta_interno'	=> $dados['ALERTA_INTERNO'],
					'odometro'			=> $dados['UPOSGPS_ODOMETRO'],
					'rpm'				=> $dados['UPOSRPM'],
					'velocidade2'		=> '0',
					'temp1'				=> $dados['UPOSTEMPERATURA1'],
					'temp2'				=> $dados['UPOSTEMPERATURA2'],
					'entradas'			=> $entradas,
					'saidas'			=> $saidas,
					'hdop'				=> '0',
					'satelites'			=> $dados['UPOSSATELITES'],
					'ibutton' 			=> $dados['UPOSID_IBUTTON'],
					'gps'				=> $dados['UPOSGPS_VALIDO'],
					'pos_memoria'       => $dados['UPOSPOS_MEMORIA']
				);

				/*
				 * Finaliza statement e conexão do Oracle
				 */
				oci_free_statement($stmtBuscaDadosOracle);
			}		

			return ($bufferDados);

		} catch (Exception $e) {
			return $e->getMessage();
		}
    }


    public function fecharConexao() {
    	oci_close($this->conn_oracle);
    }
}