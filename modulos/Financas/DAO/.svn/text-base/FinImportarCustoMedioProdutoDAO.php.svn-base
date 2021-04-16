<?php

class FinImportarCustoMedioProdutoDAO{
	
	private $conn;
	
	
	public function __construct($conn){
		$this->conn = $conn;
	}
	
	/**
	 * Abre a transação
	 */
	public function begin() {
		pg_query ( $this->conn, 'BEGIN' );
	}
	
	
	/**
	 * Finaliza um transação
	 */
	public function commit() {
		pg_query ( $this->conn, 'COMMIT' );
	}
	
	/**
	 * Aborta uma transação
	 */
	public function rollback() {
		pg_query ( $this->conn, 'ROLLBACK' );
	}
	
	//varifica se já existe o codigo do produto cadastrado na tabela
	public function listaProdutoCodigo($codigo){
		
		try{
			
			$sql = "SELECT * FROM produto WHERE prdoid = $codigo";
			
			if (! $result = pg_query ( $this->conn, $sql )) {
				throw new Exception ( "Erro de SQL ao efetuar a consulta numero contratos por id da proposta" );
			}
			
		
			
		} catch ( Exception $e ) {
			throw new Exception ( "Erro ao efetuar a consulta da tabela produtos" );
		}
		
		return pg_num_rows($result);
		
	}
	
	
	//salva os dados do arquivo importado
	public function salvaCustoMedio($data){
		

		try{
			$this->begin ();
			$count = 0;
			foreach ($data as $key) {
				$cliendoid = 0;
				$sql = "INSERT INTO produto_custo_medio(
					 pcmprdoid,
  					 pcmcusto_medio ,
  					 pcmdt_referencia,
  					 pcmdt_cadastro,
  					 pcmusuoid
					)
					VALUES (
						$key[codigoproduto],
						$key[customedio],
						'$key[data]',
						now(),
						$key[usuario]
					) RETURNING pcmoid ";

				
				if (! $result = pg_query ( $this->conn, $sql )) {
					$this->rollback ();
					throw new Exception ( "Erro de SQL ao efetuar o cadastro de produto_custo_medio" );
				}
				
				$arr = pg_fetch_array ( $result, 0 );
				$cliendoid = $arr[pcmoid];
				
				if($cliendoid != 0) {
					$count ++;
				}
			
			}
			
			$this->commit ();
			
		} catch(Exception $e) {
			$this->rollback ();
			throw new Exception ( "Erro ao cadastrar custo médio na tabela produto_custo_medio" );
		}
		
		return $count;
	}
	
	
	//verifica se ja existe arquivo importado na data de referencia
	public function listaCustoMedioData($data){

		try{
				
			$sql = "SELECT * FROM produto_custo_medio WHERE pcmdt_referencia = '$data' AND pcmdt_exclusao IS NULL AND pcmusuoid_exclusao IS NULL";
				
			if (! $result = pg_query ( $this->conn, $sql )) {
				throw new Exception ( "Erro de SQL ao efetuar a consulta por data da tabela produto_custo_medio" );
			}
				
		
				
		} catch ( Exception $e ) {
			throw new Exception ( "Erro ao efetuar a consulta da tabela produto_custo_medio" );
		}
		
		return pg_num_rows($result);
	}
	
	//lista os arquivos importados já com limits para paginacao
	public function listaArquivosImportadosCustoMedio(){
		
		try{
			
	/*	if (isset($paginacao->limite) && isset($paginacao->offset)) {
			$paginas = "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "";
		}*/
			
			$sql = "SELECT 
                                    pcmusuoid,
                                    pcmdt_cadastro as pcmdt_cadastro,
                                    nm_usuario,
                                    TO_CHAR(pcmdt_referencia, 'MM/YYYY') as pcmdt_referencia
                            FROM produto_custo_medio 
                            INNER JOIN usuarios ON pcmusuoid = cd_usuario
                            WHERE
                                pcmdt_exclusao IS NULL
                                AND pcmusuoid_exclusao IS NULL
                            GROUP BY 
                                    pcmusuoid,
                                    pcmdt_cadastro,
                                    nm_usuario,
                                    pcmdt_referencia
                            ORDER BY pcmdt_cadastro desc";
                        
			if(!$result = pg_query($this->conn,$sql)){
				throw new Exception("Erro de SQL ao efetuar consulta de arquivos importados");
			}
			
		}catch(Exception $e){
			throw  new Exception("Erro os efetuar consulta dos arquivos importados");
		}
		
			while ($row = pg_fetch_object($result)) {
			$retorno[] = $row;
		}
		
		return $retorno;
	}
        
        //retornar registros do Arquivo selecionado
        public function getRegistrosArquivosImportados($pcmsuoid, $data)
        {
            try
            {
                $sql = "
                    SELECT
                        pcmprdoid,
                        pcmcusto_medio
                    FROM
                        produto_custo_medio
                    WHERE
                        pcmdt_cadastro = '$data'
                        AND pcmusuoid = '$pcmsuoid'
                    GROUP BY
                        pcmprdoid,
                        pcmcusto_medio
                    ORDER BY
                        pcmprdoid DESC
                ";
                
                if(!$result = pg_query($this->conn,$sql))
                {
                    throw new Exception("Erro de SQL ao efetuar consulta de arquivos no BD");
                }
            }
            catch (Exception $e)
            {
            }
            
            while($row = pg_fetch_object($result))
            {
                $retorno[] = $row;
            }
            
            return $retorno;
        }
        
        //update inserindo a data de exclusão e quem excluiu
        public function excluiArquivosImportadosCustoMedio($pcmusuoid, $data)
        {
            try
            {
                $usuario_atual = $_SESSION['usuario']['oid'];
                $sql = "
                    UPDATE
                        produto_custo_medio
                    SET
                        pcmdt_exclusao = now(),
                        pcmusuoid_exclusao = '$usuario_atual'
                    WHERE
                        pcmdt_cadastro = '$data'
                        AND pcmusuoid = '$pcmusuoid'
                ";
                
                if(!$result = pg_query($this->conn, $sql))
                {
                   throw new Exception("Erro de SQL ao efetuar a exclusão dos arquivos importados"); 
                }
            }
            catch (Exception $e)
            {
                throw new Exception("Erro ao efetuar a exclusão");
            }
        }
}