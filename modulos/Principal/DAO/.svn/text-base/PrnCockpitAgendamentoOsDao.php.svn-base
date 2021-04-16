<?php
/**
 * Cockpit de Agendamento de OS
 *
 * @package Principal
 * @author  Kleber Goto Kihara <kleber.kihara@meta.com.br>
 */
class PrnCockpitAgendamentoOsDao {

    /**
     * Conexão com o banco de dados.
     *
     * @var Resource
     */
    private $conn;

    /**
     * Mensagem de erro padrão.
     *
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * Método construtor.
     *
     * @param resource $conn conexão
     *
     * @return Void
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Método que traz os dados do contrato.
     *
     * @param stdClass $parametros Filtros
     *
     * @return stdClass
     * @throws ErrorException
     */
    public function buscarContrato(stdClass $parametros) {
        $retorno = new stdClass();

        $sql = "
            SELECT
                ordem_servico.ordconnumero AS numero
            FROM
                ordem_servico
        ";

        if (!empty($parametros->ordoid)) {
            $sql.= "
                WHERE
                    ordem_servico.ordoid = ".$parametros->ordoid."
            ";
        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno = $row;
        }

        return $retorno;
    }

    /**
     * Método que traz os equipamentos disponíveis.
     *
     * @param stdClass $parametros Filtros
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarEquipamentoDisponivel(stdClass $parametros) {
        $retorno = array();

        if (empty($parametros->repoid)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $sql = "
            SELECT
                equipamentos.prdoid AS oid,
                equipamentos.prdproduto AS nome,
                SUM(equipamentos.equqtd_transito) AS transito,
                SUM(equipamentos.equqtd_disponivel) AS disponivel,
                SUM(equipamentos.equqtd_retirada) AS retirada,
                SUM(equipamentos.equqtd_conferencia) AS conferencia
            FROM
                (
                    SELECT
                        produto.prdoid,
                        produto.prdproduto,
                        equipamento.equeqsoid,
                        relacionamento_representante.relrrepoid,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT equipamento.equoid)
                            WHERE
                                equipamento.equeqsoid = 2
                            AND
                                relacionamento_representante.relrrepoid = " . $parametros->repoid . "
                        ), 0) AS equqtd_transito,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT equipamento.equoid)
                            WHERE
                                equipamento.equeqsoid = 3
                            AND
                                relacionamento_representante.relrrepoid = " . $parametros->repoid . "
                        ), 0) AS equqtd_disponivel,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT equipamento.equoid)
                            WHERE
                                (
                                        equipamento.equeqsoid = 10
                                    OR
                                        equipamento.equeqsoid = 60
                                )
                            AND
                                relacionamento_representante.relrrepoid = " . $parametros->repoid . "
                        ), 0) AS equqtd_retirada,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT equipamento.equoid)
                            WHERE
                                equipamento.equeqsoid = 62
                            AND
                                relacionamento_representante.relrrepoid = " . $parametros->repoid . "
                        ), 0) AS equqtd_conferencia
                    FROM
                        contrato
                            INNER JOIN
                                equivalencia_equipamento ON (
                                        contrato.conmodalidade = equivalencia_equipamento.eqqmodalidade
                                    AND
                                        contrato.coneqcoid = equivalencia_equipamento.eeqeqcoid
                                )
                            INNER JOIN
                                equivalencia_equipamento_item ON (
                                        equivalencia_equipamento.eeqoid = equivalencia_equipamento_item.eeieeqoid
                                    AND
                                        equivalencia_equipamento_item.eeidt_exclusao IS NULL
                                )
                            INNER JOIN
                                produto ON equivalencia_equipamento_item.eeiprdoid = produto.prdoid
                            INNER JOIN
                                equipamento ON (
                                        produto.prdoid = equipamento.equprdoid
                                    AND
                                        equipamento.equdt_exclusao IS NULL
                                    AND
                                        (
                                            equipamento.equeqsoid IN (3, 10, 60, 62)
                                            OR  
                                            (
                                                equipamento.equeqsoid = 2
                                                AND equipamento.equoid IN (
                                                    SELECT 
                                                        esrirefoid
                                                    FROM
                                                        estoque_remessa,
                                                        estoque_remessa_item 
                                                    WHERE
                                                        esrdt_exclusao IS NULL  
                                                        AND esrioid = esroid
                                                        AND esriimotoid = 3
                                                        AND esrersoid = '1'  
                                                        AND (esritipo IS NULL OR esritipo = 'E')
                                                        AND esriimotoid IN (3, 4, 5, 6, 7, 8, 10, 11, 13, 14, 15, 16, 18, 19, 20, 21, 22)
                                                        AND esrrelroid IN (SELECT r.relroid FROM relacionamento_representante r WHERE r.relrrepoid = " . $parametros->repoid . ")
                                                )
                                            )
                                        )
                                )
                            INNER JOIN
                                relacionamento_representante ON equipamento.equrelroid = relacionamento_representante.relroid
                    WHERE
                        (
                                equivalencia_equipamento.eeqtpcoid = contrato.conno_tipo
                            OR
                                equivalencia_equipamento.eeqtpcoid IS NULL
                        )
        ";

        if (!empty($parametros->connumero)) {
            $sql.= "
                AND
                    contrato.connumero = ".$parametros->connumero."
            ";
        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }


        $sql.= "
                    GROUP BY
                        produto.prdoid,
                        produto.prdproduto,
                        equipamento.equeqsoid,
                        relacionamento_representante.relrrepoid
                ) AS equipamentos
            GROUP BY
                equipamentos.prdoid,
                equipamentos.prdproduto
            ORDER BY
                nome
        ";

        if (!$rs = pg_query($this->conn, $sql)) { 
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }


    /**
     * Método que traz os equipamentos disponíveis, somente considerando representante, mas sem cadastro de equivalencia.
     *
     * @param stdClass $parametros Filtros
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarEquipamentoDisponivelSemEquivalencia(stdClass $parametros) {
    	$retorno = array();

    	if (empty($parametros->repoid)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	if (!empty($parametros->connumero)) {
    		$sql= "SELECT
    					conmodalidade
    				FROM
    					contrato
    				WHERE
                    	contrato.connumero = ".$parametros->connumero." ";

    		if (!$rs = pg_query($this->conn, $sql)) {
    			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    		}

    		$con = pg_fetch_object($rs);

    		$tipoprodutobusca = '1, 2, 4, 5';
    		if($con->conmodalidade == "L"){
    			$tipoprodutobusca = '1, 2';
    		}elseif($con->conmodalidade == "R"){
    			$tipoprodutobusca = '4, 5';
    		}

    	} else {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	$sql = "SELECT
    				eproid,
    				eqcoid,
				    prdoid AS oid,
				    descricao AS nome,
				    sum(trans) as transito,
				    sum(disp) as disponivel,
				    sum(retir) as retirada,
				    sum(confer) as conferencia
			    FROM (
				    SELECT
    					eproid,
    					eqcoid,
    					prdoid,
				    	CASE
				    		WHEN eveoid in (196,213,218,231,243,250,257,264,272,292) then 'X-1364 MÓVEL'
				    		WHEN eveoid in (232,244,251,258,263,267,273,274,293,294) then 'X-1364 FIXO'
				    		ELSE prdproduto
				    		END as descricao,
	    				(select count(*) as qtde_disponivel where equeqsoid=3 and equeqsoid=eqsoid) as disp,
	    				(select count(*) as qtde_disponivel where equeqsoid=2 and equeqsoid=eqsoid) as trans,
	    				(select count(*) as qtde_disponivel where equeqsoid IN (10,60) and equeqsoid=eqsoid) as retir,
	    				(select count(*) as qtde_disponivel where equeqsoid=62 and equeqsoid=eqsoid) as confer
				    FROM
    					equipamento,
	    				produto,
	    				equipamento_versao,
	    				equipamento_classe,
	    				equipamento_status,
	    				relacionamento_representante,
	    				equipamento_projeto
				    WHERE
    					equrelroid=relroid
				    AND
    					equeveoid=eveoid
				    AND
    					equprdoid = prdoid
				    AND
    					prdptioid in (1, 2)
				    AND
    					equdt_exclusao is null
				    AND
    					eveprojeto=eproid
				    AND
    					eveeqcoid=eqcoid
				    AND
    					equeqsoid=eqsoid
				    AND
    					(
                            equeqsoid in (3, 62, 10,60)
                            OR  
                            (
                                equipamento.equeqsoid = 2
                                AND equipamento.equoid IN (
                                    SELECT 
                                        esrirefoid
                                    FROM
                                        estoque_remessa,
                                        estoque_remessa_item 
                                    WHERE
                                        esrdt_exclusao IS NULL  
                                        AND esrioid = esroid
                                        AND esriimotoid = 3
                                        AND esrersoid = '1'  
                                        AND (esritipo IS NULL OR esritipo = 'E')
                                        AND esrrelroid IN (SELECT r.relroid FROM relacionamento_representante r WHERE r.relrrepoid = " . $parametros->repoid . ")
                                )
                            )
                        )
				    AND
    					relrrep_terceirooid=".intval($parametros->repoid)."
    			  	AND
    					produto.prdptioid in (".$tipoprodutobusca.")
    			) as tabela
				GROUP BY descricao, eproid, eqcoid, prdoid
				ORDER BY descricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método que traz os "outros" acessórios disponíveis.
     *
     * @param stdClass $parametros Filtros
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarOutroDisponivel(stdClass $parametros) {
        $retorno = array();

        if (empty($parametros->repoid)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $sql = "
            SELECT
                outros.prdoid AS oid,
                outros.prdproduto AS nome,
                outros.otrordem AS ordem,
                outros.otrtipo AS tipo,
                COUNT(outros.prdoid) OVER (
                    PARTITION BY outros.otrordem
                ) AS quantidade,
                SUM(outros.otrqtd_transito) AS transito,
                SUM(outros.otrqtd_disponivel) AS disponivel,
                SUM(outros.otrqtd_retirada) AS retirada,
                SUM(outros.otrqtd_conferencia) AS conferencia
            FROM
                (
                    SELECT
                        produto.prdoid,
                        produto.prdproduto,
                        suboutros.otrotsoid,
                        suboutros.otrordem,
                        suboutros.otrtipo,
                        relacionamento_representante.relrrepoid,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT suboutros.otroid)
                            WHERE
                                suboutros.otrotsoid = 2
                            AND
                                relacionamento_representante.relrrepoid = " . $parametros->repoid . "

                        ), 0) AS otrqtd_transito,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT suboutros.otroid)
                            WHERE
                                suboutros.otrotsoid = 3
                            AND
                                relacionamento_representante.relrrepoid = " . $parametros->repoid . "
                        ), 0) AS otrqtd_disponivel,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT suboutros.otroid)
                            WHERE
                                suboutros.otrotsoid = 8
                            AND
                                relacionamento_representante.relrrepoid = " . $parametros->repoid . "
                        ), 0) AS otrqtd_retirada,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT suboutros.otroid)
                            WHERE
                                suboutros.otrotsoid = 62
                            AND
                                relacionamento_representante.relrrepoid = " . $parametros->repoid . "
                        ), 0) AS otrqtd_conferencia
                    FROM
                        contrato
                            INNER JOIN
                                equivalencia_equipamento ON (
                                        contrato.conmodalidade = equivalencia_equipamento.eqqmodalidade
                                    AND
                                        contrato.coneqcoid = equivalencia_equipamento.eeqeqcoid
                                    AND contrato.condt_exclusao IS NULL

                                )
                            INNER JOIN
                                equivalencia_equipamento_item ON (
                                        equivalencia_equipamento.eeqoid = equivalencia_equipamento_item.eeieeqoid
                                    AND
                                        equivalencia_equipamento_item.eeidt_exclusao IS NULL
                                )
                            INNER JOIN
                                produto ON equivalencia_equipamento_item.eeiprdoid = produto.prdoid
                            INNER JOIN
                                (
                                        SELECT
                                            imobilizado.imoboid AS otroid,
                                            imobilizado.imobprdoid AS otrprdoid,
                                            imobilizado.imobrelroid AS otrrelroid,
                                            imobilizado.imobimsoid AS otrotsoid,
                                            imobilizado.imobimotoid AS otrordem,
                                            imobilizado_tipo.imotdescricao AS otrtipo
                                        FROM
                                            imobilizado
                                        JOIN imobilizado_tipo
                                           ON imobilizado.imobimotoid=imobilizado_tipo.imotoid
                                        JOIN relacionamento_representante
                                           ON imobilizado.imobrelroid=relacionamento_representante.relroid
                                        WHERE
                                            imobilizado.imobexclusao IS NULL
                       AND relacionamento_representante.relrrep_terceirooid = " . $parametros->repoid . "
                       AND imobilizado.imobimotoid NOT IN (3,30)
                                        AND
                                            (
                                                imobilizado.imobimsoid IN (3, 8, 62)
                                                OR (
                                                    imobilizado.imobimsoid = 2
                                                    AND imobilizado.imoboid IN (
                                                    SELECT 
                                                        estoque_remessa_item.esrirefoid
                                                    FROM estoque_remessa_item
                                                    JOIN estoque_remessa
                                                      ON esrioid = esroid
                                                    JOIN relacionamento_representante rel_rep
                                                      ON esrrelroid = rel_rep.relroid
                                                        
                                                  WHERE
                                                        esrdt_exclusao IS NULL  
                                                        AND esriimotoid = imobilizado.imobimotoid
                                                        AND esrersoid = '1'  
                                                        AND (esritipo IS NULL OR esritipo = 'E')
                                                        AND rel_rep.relroid = esrrelroid AND relrrepoid = " . $parametros->repoid . "
                                                    )
                                                )
                                            )
                        
                                    
                                ) AS suboutros ON produto.prdoid = suboutros.otrprdoid
                            INNER JOIN
                                relacionamento_representante ON suboutros.otrrelroid = relacionamento_representante.relroid
                    WHERE
                        (
                                equivalencia_equipamento.eeqtpcoid = contrato.conno_tipo
                            OR
                                equivalencia_equipamento.eeqtpcoid IS NULL
                        )
        ";

        if (!empty($parametros->connumero)) {
            $sql.= "
                AND
                    contrato.connumero = ".$parametros->connumero."
            ";
        } else if (!empty($parametros->prdoid)) {

            $sql .= " AND otrprdoid = ".$parametros->prdoid." ";

        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }


        $sql.= "
                    GROUP BY
                        produto.prdoid,
                        produto.prdproduto,
                        suboutros.otrotsoid,
                        suboutros.otrordem,
                        suboutros.otrtipo,
                        relacionamento_representante.relrrepoid
                ) AS outros
            GROUP BY
                outros.prdoid,
                outros.prdproduto,
                outros.otrordem,
                outros.otrtipo
            ORDER BY
                ordem,
                nome
        ";

        //echo "<pre>$sql</pre>";exit;
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }


    /**
     * Método que traz os "outros" acessórios disponíveis caso nao haja equivalencia cadastrada.
     *
     * @param stdClass $parametros Filtros
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarOutroDisponivelSemEquivalencia(stdClass $parametros) {
        $retorno = array();

        if (empty($parametros->repoid)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!empty($parametros->connumero)) {
            $sql= "SELECT
                        conmodalidade
                    FROM
                        contrato
                    WHERE
                        contrato.connumero = ".$parametros->connumero." ";

            if (!$rs = pg_query($this->conn, $sql)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            $con = pg_fetch_object($rs);

            $tipoprodutobusca = '1, 2, 4, 5';
            if($con->conmodalidade == "L"){
                $tipoprodutobusca = '1, 2';
            }elseif($con->conmodalidade == "R"){
                $tipoprodutobusca = '4, 5';
            }

        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $sql = "
            SELECT
                outros.prdoid AS oid,
                outros.prdproduto AS nome,
                outros.otrordem AS ordem,
                outros.otrtipo AS tipo,
                COUNT(outros.prdoid) OVER (
                    PARTITION BY outros.otrordem
                ) AS quantidade,
                SUM(outros.otrqtd_transito) AS transito,
                SUM(outros.otrqtd_disponivel) AS disponivel,
                SUM(outros.otrqtd_retirada) AS retirada,
                SUM(outros.otrqtd_conferencia) AS conferencia
            FROM
                (
                    SELECT
                        produto.prdoid,
                        produto.prdproduto,
                        suboutros.otrotsoid,
                        suboutros.otrordem,
                        suboutros.otrtipo,
                        relacionamento_representante.relrrep_terceirooid,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT suboutros.otroid)
                            WHERE
                                suboutros.otrotsoid = 2
                            AND
                                relacionamento_representante.relrrep_terceirooid = " . $parametros->repoid . "

                        ), 0) AS otrqtd_transito,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT suboutros.otroid)
                            WHERE
                                suboutros.otrotsoid = 3
                            AND
                                relacionamento_representante.relrrep_terceirooid = " . $parametros->repoid . "
                        ), 0) AS otrqtd_disponivel,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT suboutros.otroid)
                            WHERE
                                suboutros.otrotsoid = 8
                            AND
                                relacionamento_representante.relrrep_terceirooid = " . $parametros->repoid . "
                        ), 0) AS otrqtd_retirada,
                        COALESCE((
                            SELECT
                                COUNT(DISTINCT suboutros.otroid)
                            WHERE
                                suboutros.otrotsoid = 62
                            AND
                                relacionamento_representante.relrrep_terceirooid = " . $parametros->repoid . "
                        ), 0) AS otrqtd_conferencia
                    FROM
                        produto
                            INNER JOIN
                                (
                                        SELECT
                                            imobilizado.imoboid AS otroid,
                                            imobilizado.imobprdoid AS otrprdoid,
                                            imobilizado.imobrelroid AS otrrelroid,
                                            imobilizado.imobimsoid AS otrotsoid,
                                            imobilizado.imobimotoid AS otrordem,
                                            imobilizado_tipo.imotdescricao AS otrtipo
                                        FROM
                                            imobilizado,imobilizado_tipo,relacionamento_representante
                                        WHERE
                                            imobilizado.imobexclusao IS NULL
                                            AND imobilizado.imobimotoid=imobilizado_tipo.imotoid
                                            AND imobilizado.imobrelroid=relacionamento_representante.relroid
                                            AND relacionamento_representante.relrrep_terceirooid = " . $parametros->repoid . "
                                            AND imobilizado.imobimotoid NOT IN (3,30)
                                        AND
                                            (
                                                imobilizado.imobimsoid IN (3, 8, 62)
                                                OR (
                                                    imobilizado.imobimsoid = 2
                                                    AND imobilizado.imoboid IN (

                                                    SELECT 
                                                        estoque_remessa_item.esrirefoid
                                                    FROM estoque_remessa_item
                                                    JOIN estoque_remessa
                                                      ON esrioid = esroid
                                                    JOIN relacionamento_representante rel_rep
                                                      ON esrrelroid = rel_rep.relroid
                                                        
                                                  WHERE
                                                        esrdt_exclusao IS NULL  
                                                        AND esriimotoid = imobilizado.imobimotoid
                                                        AND esrersoid = '1'  
                                                        AND (esritipo IS NULL OR esritipo = 'E')
                                                        AND rel_rep.relroid = esrrelroid AND relrrepoid = " . $parametros->repoid . " 
                                                    )
                                                )
                                            )
                                            
                                            UNION 
                                           (
                                            SELECT DISTINCT
                                            imobilizado.imobprdoid AS otroid,
                                            imobilizado.imobprdoid AS otrprdoid,
                                            1 AS otrrelroid,
                                            1 otrotsoid,
                                            imobilizado.imobimotoid AS otrordem,
                                            imobilizado_tipo.imotdescricao AS otrtipo
                                                FROM
                                            imobilizado,imobilizado_tipo  
                                                WHERE
                                            imobilizado.imobexclusao IS NULL
                                            AND imobilizado.imobimotoid=imobilizado_tipo.imotoid
                                            AND imobilizado.imobimotoid NOT IN (3,30)
                                            )
                                    
                                ) AS suboutros ON produto.prdoid = suboutros.otrprdoid
                            INNER JOIN
                                relacionamento_representante ON suboutros.otrrelroid = relacionamento_representante.relroid
                    WHERE
                        produto.prdptioid in (".$tipoprodutobusca.")
                        AND  produto.prdgrmoid=34
                    GROUP BY
                        produto.prdoid,
                        produto.prdproduto,
                        suboutros.otrotsoid,
                        suboutros.otrordem,
                        suboutros.otrtipo,
                        relacionamento_representante.relrrep_terceirooid

                ) AS outros
            GROUP BY
                outros.prdoid,
                outros.prdproduto,
                outros.otrordem,
                outros.otrtipo
            ORDER BY
                ordem,
                nome";
        //echo "<pre>$sql</pre>";exit();
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método que traz os dados do representante.
     *
     * @param stdClass $parametros Filtros
     *
     * @return stdClass
     * @throws ErrorException
     */
    public function buscarRepresentante(stdClass $parametros) {
        $retorno = new stdClass();

        $sql = "
            SELECT
                representante.reprazao AS nome,
                LPAD(representante.repcgc::VARCHAR, 14, '0') AS cnpj,
                endereco_representante.endvrua AS endereco,
                endereco_representante.endvnumero AS enderecoNumero,
                endereco_representante.endvcomplemento AS enderecoComplemento,
                endereco_representante.endvcidade AS cidade,
                endereco_representante.endvuf AS estado,
                endereco_representante.endvddd || endereco_representante.endvfone AS telefone,
                representante.repcontato AS contato,
                representante.repcontato_ddd || representante.repcontato_fone AS contatoTelefone,
                representante.repe_mail AS email,
                CASE
                    WHEN representante.reprevenda THEN
                        'REVENDA'
                    WHEN representante.repinstalacao THEN
                        'INSTALAÇÃO'
                    WHEN representante.repassistencia THEN
                        'ASSISTÊNCIA'
                    WHEN representante.repcentral THEN
                        'CENTRAL'
                    ELSE
                        ''
                END AS funcao
            FROM
                representante
            INNER JOIN
                endereco_representante ON representante.repoid = endereco_representante.endvrepoid
            WHERE
                representante.repexclusao IS NULL
        ";

        if (!empty($parametros->repoid)) {
            $sql.= "
                AND
                    representante.repoid = ".$parametros->repoid."
            ";
        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno = $row;
        }

        return $retorno;
    }
    /*
     * Busca por todos os itens com o ordoid diferente do ordoid informado.
     */

    public function buscarProdutosReservadosRepresentante($repoid, $ordoid){
       
        $produtos = array();
        $sql = "SELECT
                        ragoid              AS id_reserva_agendamento,
                        ragordoid           AS id_ordem_servico,
                        raioid              AS id_reserva_agendamento_item,
                        raiprdoid           AS id_produto,
                        ragrasoid           AS id_reserva_agendamento_status,
                        prdoid              AS id_produto,
                        prdproduto          AS descricao_produto,
                        raiqtde_estoque     AS quantidade_disponivel,
                        raiqtde_transito    AS quantidade_transito
                    FROM
                        reserva_agendamento
                    INNER JOIN
                        reserva_agendamento_item ON ragoid = rairagoid
                    INNER JOIN
                        produto ON prdoid = raiprdoid
                    WHERE 
                        ragrasoid IN (1,3) AND                       
                        ragrepoid = " . $repoid . "
                    AND
                        raidt_exclusao IS NULL
                    AND ragordoid != " . $ordoid; 

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_assoc($rs)) {
            $produtos[] = $row;
        }     
        return $produtos;        
    }

    /**
     * Método para buscar os produtos reservados
     *
     * @return array
     * @param $ordoid
     */
    public function buscarProdutosReservados($ordoid, $repoid){

    	$produtos = array();

    	if(!empty($ordoid)) {

	    	$sql = "SELECT
	    				ragoid 				AS id_reserva_agendamento,
	    				ragordoid 			AS id_ordem_servico,
	    				raioid 				AS id_reserva_agendamento_item,
	    				raiprdoid 			AS id_produto,
	    				ragrasoid 			AS id_reserva_agendamento_status,
	    				prdoid 				AS id_produto,
	    				prdproduto 			AS descricao_produto,
	    				raiqtde_estoque 	AS quantidade_disponivel,
	    				raiqtde_transito 	AS quantidade_transito,
                        raiesroid           AS remessa,
                        to_char(esrdata::date + coalesce(esrtempotransporte,'0')::integer + coalesce(esrtemporetencao,'0') + 1,'dd/mm/yyyy') AS data_chegada
	    			FROM
	    				reserva_agendamento
	    			INNER JOIN
	    				reserva_agendamento_item ON ragoid = rairagoid
                    LEFT JOIN
                        estoque_remessa ON raiesroid = esroid                        
	    			INNER JOIN
						produto ON prdoid = raiprdoid
	    			WHERE
                        ragrasoid IN (1,3) AND
	    				ragordoid = " . $ordoid . " AND
                        ragrepoid = " . $repoid . "
	    			AND
	    				raidt_exclusao IS NULL";           
	    	if (!$rs = pg_query($this->conn, $sql)) {
	    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	    	}

	    	while ($row = pg_fetch_assoc($rs)) {
	    		$produtos[] = $row;
	    	}

    	}

    	return $produtos;

    }

    public function pesquisarAgendamentoUsuario($ordoid, $usuario){
        $sql = "SELECT *
                FROM 
                    reserva_agendamento
                WHERE
                    ragusuoid = $usuario
                AND
                    ragordoid = $ordoid";

        if (!$rs = pg_query($this->conn, $sql)) {
            
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }    
        
        if (pg_num_rows($rs) > 0) {
            $agendamento = pg_fetch_object($rs);
            return $agendamento;
        }

        return null;        
    }

    public function cancelarAgendamento($ragoid, $justificativa){

        $sql = "UPDATE 
                  reserva_agendamento
                SET 
                  ragrasoid = 2,
                  ragjustificativa_cancelamento = '$justificativa',
                  ragdt_cancelamento = now()
                WHERE 
                  ragoid = $ragoid";

        if (!pg_query($this->conn, $sql)) {
            
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $sql = "UPDATE 
                  reserva_agendamento_item
                SET 
                  raijustificativa = '$justificativa',
                  raidt_exclusao   = now()
                WHERE
                  rairagoid = $ragoid";

        if (!pg_query($this->conn, $sql)) {
            
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }   
    }

    /**
     * Metodo para inserir agendamento referente ao ordem de servico
     *
     */
    public function inserirAgendamento($ordoid, $repoid, $status, $usuario){

    	//$this->begin();

    	$sql = "INSERT INTO
    				reserva_agendamento (
    					ragordoid,
                        ragrepoid,
    					ragdt_cadastro,
    					ragrasoid,
                        ragusuoid
    			)
    			VALUES (
    				" . $ordoid  . ",
                    " . $repoid  . ",
    				NOW(),
    				" . $status  . ",
                    " . $usuario ."
    			) RETURNING ragoid;";

    	if (!$rs = pg_query($this->conn, $sql)) {
    		//$this->rollback();
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	$agendamento = pg_fetch_object($rs);

    	if($agendamento->ragoid > 0) {
    		//$this->commit();
    		return $agendamento->ragoid;
    	}

    	//$this->rollback();
    	return false;

    }

    /**
     * Metodo para inserir os itens do agendamento
     */
    public function inserirItemAgendamento($idProduto, $qtdDisponivel, $qtdTransito, $id_agendamento, $remessa = 'NULL'){

    	//$this->begin();
        $remessas = null;

        

    	$sql = "INSERT INTO
    				reserva_agendamento_item (
    					raiprdoid,
    					raidt_cadastro,
    					raiqtde_estoque,
    					raiqtde_transito,
    					rairagoid,
                        raiesroid
    			)
    			VALUES (
    				$idProduto,
    				NOW(),
    				$qtdDisponivel,
    				$qtdTransito,
    				$id_agendamento,
                    $remessa
    			) RETURNING raioid";

    	if (!$rs = pg_query($this->conn, $sql)) {
    		//$this->rollback();
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	$id_item = 0;

    	if (pg_num_rows($rs) > 0) {
    		$id_item = pg_fetch_result($rs, 0, 'raioid');
    	}

    	//$this->commit();
    	return $id_item;
    }

    /**
     * Metodo para inserir os itens do agendamento
     */
    public function atualizarItemAgendamento($idProduto, $qtdDisponivel, $qtdTransito, $id_item_agendamento){

    	//$this->begin();

    	$sql = "UPDATE
    				reserva_agendamento_item
    			SET
    				raiqtde_estoque = $qtdDisponivel,
    				raiqtde_transito = $qtdTransito
    			WHERE
    				raioid = $id_item_agendamento
    			AND
    				raiprdoid = $idProduto";

    	if (!$rs = pg_query($this->conn, $sql)) {
    	//$this->rollback();
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	//$this->commit();
    }


    /**
     * Metodo para exclui item agendamento
     */
    public function excluirItemAgendamento($idProduto, $justificativa, $id_agendamento, $id_item_agendamento) {

    	//$this->begin();

    	$justificativa = pg_escape_string($justificativa);

    	$sql = "UPDATE
    				reserva_agendamento_item
    			SET
    				raidt_exclusao = NOW(),
    				raijustificativa = '$justificativa'
    			WHERE
    				raiprdoid = $idProduto
    			AND
    				rairagoid = $id_agendamento
    			AND
    				raioid = $id_item_agendamento";

    	if (!$rs = pg_query($this->conn, $sql)) {
    		//$this->rollback();
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	//$this->commit();
    }

    /**
     * Metodo para gravacao de log
     *
     */
    public function inserirLog($item, $operacao, $usuario) {

    	//$this->begin();

    	$sql = "INSERT INTO
    				log_reserva_agendamento (lraraioid, lradt_operacao, lraoperacao, lrausuoid)
    			VALUES ($item, NOW(), '$operacao', $usuario) ";

    	if (!$rs = pg_query($this->conn, $sql)) {
    		//$this->rollback();
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	//$this->commit();

    }

    /**
     * Metodo gravarSolicitacaoAgendamento()
     *
     * @param stdClass $parametros
     *
     * @return boolean
     * @return string sagoid
     */
    public function gravarSolicitacaoAgendamento(stdClass $parametros) {

        $sql = "INSERT INTO
                        solicitacao_agendamento
                        (sagusuoid, sagordoid, sagdt_cadastro, sagobservacao, sagsaisoid, sagrepoid)
                VALUES
                        (
                        " . intval($parametros->sagusuoid) . ",
                        " . intval($parametros->sagordoid) . ",
                        'NOW()',
                        '" . pg_escape_string(utf8_decode($parametros->sagobservacao)) . "',
                        1,
                        " . intval($parametros->repoid) . "
                        )
                RETURNING sagoid";
                

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs) > 0) {
            return pg_fetch_result($rs, 0, 'sagoid');
        } else {
            return false;
        }

    }

    /**
     * Método gravarItemSolicitacaoAgendamento()
     * Grava item a item referente a solicitação de andamento
     *
     * @param stdClass $parametros
     *
     * @return boolean
     */
    public function gravarItemSolicitacaoAgendamento(stdClass $parametros) {

        $sql = "INSERT INTO
                        solicitacao_agendamento_item
                        (saisagoid, saiprdoid, saisaisoid, saiqtde_solicitacao, saidt_cadastro)
                VALUES
                        (
                        " . intval($parametros->saisagoid) . ",
                        " . intval($parametros->saiprdoid) . ",
                        " . intval($parametros->saisaisoid) . ",
                        " . intval($parametros->saiqtde_solicitacao) . ",
                        'NOW()'
                        )";


        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Obtem a descricao do produto
     */
    public function obterDescricaoProduto($idProduto) {

    	$sql = "SELECT
    				prdproduto
    			FROM
    				produto
    			WHERE
    				prdoid = $idProduto
    			AND
    				prddt_exclusao IS NULL";
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	if (pg_num_rows($rs) > 0) {
    		$descricao = pg_fetch_result($rs, 0, 'prdproduto');
    		return $descricao;
    	}

    	return null;
    }

    /**
     * Totaliza o estoque reservado de um produto especifico
     * @param int $ragrepoid
     * @param int $raiprdoid
     * @return int
     * @throws ErrorException
     */
    public function sumarizarEstoqueReservado($ragrepoid, $raiprdoid, $ordoid = '') {

        $sql = "
            SELECT
                COALESCE(SUM(raiqtde_estoque),0) AS saldo
            FROM
                reserva_agendamento
            INNER JOIN
                reserva_agendamento_item ON rairagoid = ragoid
            WHERE
                ragrepoid = ".intval($ragrepoid)."
            AND
                raiprdoid = ".intval($raiprdoid)."
            AND
                raidt_exclusao IS NULL
            ";
        if(!empty($ordoid)){
            $sql .= "AND ragordoid != ".intval($ordoid);
        }

        if (!$rs = pg_query($this->conn, $sql)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

        if (pg_num_rows($rs) > 0) {
    		$saldo = pg_fetch_result($rs, 0, 'saldo');
    		return $saldo;
    	}

    	return 0;
    }

    /**
     * Método que abre uma transação.
     *
     * @return void
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Método que finaliza uma transação.
     *
     * @return void
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Método que aborta uma transação.
     *
     * @return void
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

    /**
    * Busca as remessas do representante pelo produto
    * @param int $prdoid ID do produto
    * @param int $repoid ID do representante
    * @param int $quantidade quantidade que será necessário reservar em remessas
    * @return array|boolean
    */
    public function buscaRemessaPorProduto($prdoid, $repoid, $quantidade) {

        if (empty($prdoid) || empty($repoid)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $remessas = array();

        $sql = "SELECT  
                    *
                FROM
                (
                SELECT DISTINCT ON (esrioid)
                    COUNT(produto) OVER (PARTITION BY esrioid) -
                    COALESCE(
                    (SELECT
                        SUM(raiqtde_transito)
                    FROM
                        reserva_agendamento
                        LEFT JOIN reserva_agendamento_item ON ragoid = rairagoid
                    WHERE
                        ragrepoid = $repoid
                        AND raiprdoid = produto
                        AND ragrasoid IN (1,3)
                        AND raidt_exclusao IS NULL
                        AND raiesroid = esroid
                    ),0)  AS quantidade,
                    esrdata + COALESCE(esrtempotransporte + esrtemporetencao + 1, 9) * interval '1 day' AS data_chegada,
                    *
                FROM
                (
                    SELECT  
                        estoque_remessa_item.*,
                        estoque_remessa.*,
                        CASE 
                            WHEN esriimotoid = 3 THEN
                                (SELECT prdoid FROM equipamento, produto WHERE equoid = esrirefoid AND prdoid = equprdoid)
                            WHEN esriimotoid = 4 THEN
                                (SELECT prdoid FROM antena_satelital, produto WHERE asatoid = esrirefoid AND prdoid = asatprdoid)
                            WHEN esriimotoid = 5 THEN
                                (SELECT prdoid FROM carb, produto WHERE carboid = esrirefoid AND prdoid = carbprdoid)
                            WHEN esriimotoid = 6 THEN
                                (SELECT prdoid FROM conversor, produto WHERE convoid = esrirefoid AND prdoid = convprdoid)
                            WHEN esriimotoid = 7 THEN
                                (SELECT prdoid FROM giga_teste, produto WHERE gtesoid = esrirefoid AND prdoid = gtesprdoid)
                            WHEN esriimotoid = 8 THEN
                                (SELECT prdoid FROM sensor_volvo, produto WHERE snvoid = esrirefoid AND prdoid = snvprdoid)
                            WHEN esriimotoid = 10 THEN
                                (SELECT prdoid FROM sleep, produto WHERE slevoid = esrirefoid AND prdoid = slevprdoid)
                            WHEN esriimotoid = 11 THEN
                                (SELECT prdoid FROM trava_bau, produto WHERE trboid = esrirefoid AND prdoid = trbprdoid)
                            WHEN esriimotoid = 13 THEN
                                (SELECT prdoid FROM trava_5roda, produto WHERE trroid = esrirefoid AND prdoid = trrprdoid)
                            WHEN esriimotoid = 14 THEN
                                (SELECT prdoid FROM valvula, produto WHERE valoid = esrirefoid AND prdoid = valprdoid)
                            WHEN esriimotoid = 15 THEN
                                (SELECT prdoid FROM val_blackpower, produto WHERE vbpoid = esrirefoid AND prdoid = vbpprdoid)
                            WHEN esriimotoid = 16 THEN
                                (SELECT prdoid FROM sensor_desengate, produto WHERE sndoid = esrirefoid AND prdoid = sndprdoid)
                            WHEN esriimotoid = 18 THEN
                                (SELECT prdoid FROM jackrabbit, produto WHERE jckoid = esrirefoid AND prdoid = jckprdoid)
                            WHEN esriimotoid = 19 THEN
                                (SELECT prdoid FROM multisensor, produto WHERE mtsoid = esrirefoid AND prdoid = mtsprdoid)
                            WHEN esriimotoid = 20 THEN
                                (SELECT prdoid FROM afere, produto WHERE afroid = esrirefoid AND prdoid = afrprdoid)
                            WHEN esriimotoid = 21 THEN
                                (SELECT prdoid FROM teclado, produto WHERE tecoid = esrirefoid AND prdoid = tecprdoid)
                            WHEN esriimotoid = 22 THEN
                                (SELECT prdoid FROM computador_bordo, produto WHERE cboroid = esrirefoid AND prdoid = cborprdoid)
                        END AS produto
                        
                    FROM 
                        estoque_remessa
                        LEFT JOIN estoque_remessa_item ON esrioid = esroid
                    WHERE 
                        esrdt_exclusao IS NULL  
                        AND esrersoid = '1'  
                        AND (esritipo IS NULL OR esritipo = 'E')
                         AND esriimotoid IN (3, 4, 5, 6, 7, 8, 10, 11, 13, 14, 15, 16, 18, 19, 20, 21, 22)
                        AND esrrelroid IN (SELECT relroid FROM relacionamento_representante WHERE relrrepoid = $repoid)  
                    ORDER BY 
                        esroid
                ) AS sub
                WHERE
                    produto = $prdoid
                ) sub_geral
                WHERE
                    quantidade > 0
                ORDER BY
                    data_chegada ASC
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        } 
        $quantidadeTotal = 0;
        while ($remessa = pg_fetch_object($rs)) {
            $remessas[] = $remessa;
            $quantidadeTotal += $remessa->quantidade;
        }

        // Se não houver quantidade suficiente em transito retorna false
        if ($quantidadeTotal < $quantidade) {
            return false;
        } else {
            return $remessas;
        }
    }

    /**
     * Retorna a quantidade reservada de um produto de um representante.   
     * @param int $repoid
     * @param int $prdoid
     * @return null|Object
     * @throws ErrorException
    */
    public function buscaQuantidadeReservada($repoid, $prdoid) {
        
        $rs = null;
        $reserva = null;

        if (empty($repoid) || empty($prdoid)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $sql = "SELECT
                    COALESCE(SUM(raiqtde_estoque),0) AS disponivel,
                    COALESCE(SUM(raiqtde_transito),0) AS transito
                FROM
                    reserva_agendamento
                    LEFT JOIN reserva_agendamento_item ON ragoid = rairagoid
                WHERE
                    ragrepoid = $repoid
                    AND raiprdoid = $prdoid
                    AND ragrasoid IN (1,3)
                    AND raidt_exclusao IS NULL
                    AND ragdt_cancelamento IS NULL
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        } else {

            if (pg_num_rows($rs) > 0) {
                $reserva = pg_fetch_object($rs);
            }
            
            return $reserva;
        }
    }

    public function registraHistoricoOS($ordoid, $msg){
       $sql = "INSERT INTO ordem_situacao 
                (orsordoid, orsusuoid, orssituacao, orsdt_situacao)
                VALUES 
                ($ordoid, 2750, '$msg', now())";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

}