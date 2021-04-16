<?php

/**
 * Classe de persistência de dados
 * 
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * 
 */
class RelDebitoAutomaticoDAO {

    private $conn;

    /**
     * Busca os motivos cadastrados no banco para suspensão/exclusão de débito automático
     * 
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    public function getMotivos() {

        $sql = "SELECT
                    msdaoid as id,
					convert_from(convert_to(msdadescricao,'UTF8'),'UTF8') as descricao
                FROM
                    motivo_susp_debito_automatico
                WHERE
                    msdadt_exclusao IS NULL";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: Falha de conexão ao tentar busca os motivos.');
        }

        return pg_fetch_all($rs);
    }

    /**
     * Efetua a pesquisa do relatório analítico
     * 
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    public function pesquisarAnalitico($pCampos) {

        $sql = "SELECT
                hdadt_cadastro as data_cadastro_historico,
                clinome as nome_cliente,
                clitipo as tipo_cliente,
                CASE WHEN 
                        hdaentrada = 'I'
                     THEN 
                        'Intranet'
                     ELSE 
                        'Portal'
                END as tipo_entrada,
                
                CASE 
                    WHEN clitipo = 'F' THEN clino_cpf
                    WHEN clitipo = 'J' THEN clino_cgc
                END AS documento,

                CASE WHEN
                        hdatipo_operacao = 'I'
                     THEN
                        'Inclusão'
                     WHEN	
                        hdatipo_operacao = 'A'
                     THEN
                        'Alteração'
                     WHEN 
                    hdatipo_operacao = 'E' 
                    THEN
                        'Exclusão'
                END as tipo_operacao,
                hdatipo_operacao as tipo_operacao_contador,
                msdadescricao as descricao,
                hdaprotocolo as protocolo,
                nm_usuario as nome_usuario,
                depdescricao as departamento,
                (SELECT 
                        forcnome 
                 FROM 
                        forma_cobranca 
                 WHERE
                        hdaforcoid_anterior = forcoid) as forma_cobranca_anterior,
                (SELECT 
                        bannome 
                 FROM 
                        banco 
                 WHERE 
                        bancodigo =  hdabanoid_anterior) as banco_anterior,
                hdaagencia_anterior as agencia_anterior,
                hdacc_anterior as conta_corrente_anterior,
                (SELECT 
                        forcnome 
                 FROM 
                        forma_cobranca 
                 WHERE
                        hdaforcoid_posterior = forcoid) as forma_cobranca_posterior,
                (SELECT 
                        bannome 
                 FROM 
                        banco 
                WHERE
                        hdabanoid_posterior = bancodigo) as banco_posterior,
                hdaagencia_posterior as agencia_posterior,
                hdacc_posterior as conta_corrente_posterior
            FROM 
                    historico_debito_automatico
            INNER JOIN
                    clientes ON hdaclioid = clioid
            LEFT JOIN
                    motivo_susp_debito_automatico ON hdamsdaoid = msdaoid
            INNER JOIN
                    usuarios ON hdausuoid_cadastro = cd_usuario
            INNER JOIN
                    departamento ON depoid = usudepoid
            WHERE
                    $pCampos
            ORDER BY
                data_cadastro_historico DESC, nome_cliente
                ";
        //echo $sql;
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: Falha de conexão ao tentar realizar a pesquisa.');
        }

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_all($rs);
        }

        return array();
    }

    
    /**
     * Efetua a pesquisa do relatório sintético
     * 
     * @author Willian Ouchi <willian.ouchi@meta.com.br>
     */
    public function pesquisarSintetico($pCampos){

        $sql = "
            SELECT 
                TO_CHAR(hdadt_cadastro, 'YYYY-MM-DD') as data_cadastro_historico,
                COUNT(hdatipo_operacao) as qtd_operacao,
                hdatipo_operacao
            FROM 
                historico_debito_automatico
                INNER JOIN clientes ON hdaclioid = clioid
                LEFT JOIN motivo_susp_debito_automatico ON hdamsdaoid = msdaoid
                INNER JOIN usuarios ON hdausuoid_cadastro = cd_usuario
                INNER JOIN departamento ON depoid = usudepoid
            WHERE
                    $pCampos
            GROUP BY
                data_cadastro_historico, 
                hdatipo_operacao
            ORDER BY 
                data_cadastro_historico DESC
                ";
        //echo $sql;
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: Falha de conexão ao tentar realizar a pesquisa.');
        }

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_all($rs);
        }

        return array();
    }
    
    
    public function pesquisarDebitoAutomaticoFormaCobranca()
    {
    	$sql = "SELECT forcnome, COUNT(clicoid) AS valor 
    			FROM cliente_cobranca AS cc 
				INNER JOIN clientes AS c ON clicclioid = clioid
				INNER JOIN forma_cobranca AS fc ON cc.clicformacobranca = fc.forcoid
				WHERE c.clidt_exclusao IS NULL AND fc.forcdebito_conta = true
				GROUP BY forcnome";
    	if (!$res = pg_query($this->conn, $sql)) {
    		throw new Exception('ERRO: Falha de conexão ao tentar realizar a pesquisa.');
    	}
    	if (pg_num_rows($res) > 0) {
    		return pg_fetch_all($res);
    	}
    	return array();
    }
    
    /**
     * Construtor
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    public function RelDebitoAutomaticoDAO($conn) {

        $this->conn = $conn;
    }

}