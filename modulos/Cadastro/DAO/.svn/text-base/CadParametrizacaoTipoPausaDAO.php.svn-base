<?php

/**
 * Classe padrão para DAO
 *
 * @since   version 
 * @category Action
 * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
 * @package Intranet
 */ 
class CadParametrizacaoTipoPausaDAO {
    
    /**
     * Conexão com o banco de dados
     * @var resource
     */
    private $conn;
    
    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
    
    
    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }
    
    /**
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisar(stdClass $parametros = null){
        
        $retorno = array();
        
        $sql = "SELECT 
					hrpoid, 
					hrptempo, 
					hrpmotaoid, 
                    hrpexibe_alerta,
					CASE 
                        WHEN hrpexibe_alerta IS TRUE THEN
                            'Sim'
                        ELSE
                            'Não'
                    END as exibe_alerta, 
					hrpcadastro_obrigatorio, 
                    CASE 
                        WHEN hrpcadastro_obrigatorio IS TRUE THEN
                            'Sim'
                        ELSE
                            'Não'
                    END as cadastro_obrigatorio, 
					hrptolerancia, 
					hrpgtroid,
                    motamotivo,
                    gtrnome
				FROM 
					horario_pausa
                INNER JOIN
                    motivo_pausa ON hrpmotaoid = motaoid
                INNER JOIN
                    grupo_trabalho ON hrpgtroid = gtroid
                WHERE
					hrpdt_exclusao IS NULL
                AND
                    motacentral IS TRUE
                AND
                    gtrdt_exclusao IS NULL";
        
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}
        
        return $retorno;
    }
    
    /**
     * Método para realizar a pesquisa de apenas um registro.
     * 
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function pesquisarPorID(int $id){
        
        $retorno = new stdClass();
        
        $sql = "SELECT 
					hrpoid,
					hrpdt_cadastro, 
					hrpusuoid,
					hrpdt_exclusao, 
					hrptempo, 
					hrpmotaoid, 
					hrpexibe_alerta, 
					hrpcadastro_obrigatorio, 
					hrptolerancia, 
					hrpgtroid
				FROM 
					horario_pausa
				WHERE 
					hrpoid =" . intval( $id ) . "";
        
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }
        
        return $retorno;
    }
    
    /**
     * Verifica se já há horário cadastrado para aquele grupo e tipo de pausa
     * @param stdClass $dados Dados a serem pesquisados
     * @return boolean
     * @throws ErrorException
     */
    public function pesquisarPausaCadastrada(stdClass $dados) {
        
        $sql = "SELECT 
                    1					
				FROM 
					horario_pausa
				WHERE 
					hrpgtroid =" . intval( $dados->gtroid ) . "
                AND
                    hrpmotaoid =" . intval( $dados->motaoid ) . "
                AND
                    hrpdt_exclusao IS NULL";
        
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        if (pg_num_rows($rs) > 0){
            return true;
        }
        
        return false;
    }
    
    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserir(stdClass $dados){
                
        $sql = "INSERT INTO
					horario_pausa
					(					
					hrpdt_cadastro,
					hrpusuoid,					
					hrptempo,
					hrpmotaoid,
					hrpexibe_alerta,
					hrpcadastro_obrigatorio,
					hrptolerancia,
					hrpgtroid
					)
				VALUES
					(					
                    NOW(),
					" . intval( $dados->hrpusuoid ) . ",
					" . intval( $dados->hrptempo ) . ",
					" . intval( $dados->motaoid ) . ",
					" . $dados->hrpexibe_alerta . ",
					" . $dados->hrpcadastro_obrigatorio . ",
					" . intval( $dados->hrptolerancia ) . ",
					" . intval( $dados->gtroid ) . "
				)";
        
        if (!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        return true;
    }
    
    /**
     * Responsável por atualizar os registros 
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function atualizar(stdClass $dados){
        
        $sql = "UPDATE
					horario_pausa
				SET
					hrpatendente = " . intval( $dados->hrpatendente ) . ",
					hrpusuoid = " . intval( $dados->hrpusuoid ) . ",
					hrptempo = " . intval( $dados->hrptempo ) . ",
					hrpmotaoid = " . intval( $dados->hrpmotaoid ) . ",
					hrpexibe_alerta = " . intval( $dados->hrpexibe_alerta ) . ",
					hrpcadastro_obrigatorio = " . intval( $dados->hrpcadastro_obrigatorio ) . ",
					hrptolerancia = " . intval( $dados->hrptolerancia ) . ",
					hrpgtroid = " . intval( $dados->hrpgtroid ) . "
				WHERE 
					hrpoid = " . $dados->hrpoid . "";
        
        if (!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        return true;
    }
    
    /**
     * Exclui (UPDATE) um registro da base de dados.
     * @param int $id Identificador do registro
     * @return boolean
     * @throws ErrorException
     */
    public function excluir($id){
        
        $sql = "UPDATE
					horario_pausa
				SET
					hrpdt_exclusao = NOW() 
				WHERE
					hrpoid = " . intval( $id ) . "";
        
        if (!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        return true;
    }
    
    /**
     * Carrega combo "Grupo de Trabalho"
     * @return boolean
     * @throws ErrorException
     */    
    public function carregarComboGrupoTrabalho() {
       
        $retorno = array();
        
        $sql = "
            SELECT
                gtroid,
                gtrnome
            FROM
                grupo_trabalho
            WHERE
                gtrdt_exclusao IS NULL
            ORDER BY
                gtrnome ASC
            ";
        
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}
        
        return $retorno;
        
    }
    
    /**
     * Carrega combo "Tipo Pausa"
     * @return boolean
     * @throws ErrorException
     */    
    public function carregarComboTipoPausa() {
       
        $retorno = array();
        
        $sql = "
            SELECT
                motaoid,
                motamotivo
            FROM
                motivo_pausa
            WHERE
                motacentral IS TRUE
            ORDER BY
                motamotivo ASC
            ";
        
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}
        
        return $retorno;
        
    }
    
    /**
     * Abre a transação
     */
    public function begin(){
        pg_query($this->conn, 'BEGIN');
    }
    
    /**
     * Finaliza um transação
     */
    public function commit(){
        pg_query($this->conn, 'COMMIT');
    }
    
    /**
     * Aborta uma transação
     */
    public function rollback(){
        pg_query($this->conn, 'ROLLBACK');
    }
    
    
}

?>
