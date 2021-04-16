<?php
/**
 * Produtos Reservados.
 *
 * @package Relatório
 * @author  Kleber Goto Kihara <kleber.kihara@meta.com.br>
 */
class RelProdutosReservadosDao {

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
     * Método que busca as Cidades.
     *
     * @param stdClass $param Paramêtros.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarCidade(stdClass $param) {
        $retorno = array();
        $sql     = "
            SELECT
                cidade.cidoid AS oid,
                cidade.ciddescricao AS descricao
            FROM
                cidade
            WHERE
                cidade.cidexclusao IS NULL
        ";

        if (isset($param->ufuf)) {
            $sql.= "
                AND
                    cidade.ciduf = '".$param->ufuf."'
            ";
        }

        $sql.= "
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca as Classes de Equipamento.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarEquipamentoClasse() {
        $retorno = array();
        $sql     = "
            SELECT
                equipamento_classe.eqcoid AS oid,
                equipamento_classe.eqcdescricao AS descricao
            FROM
                equipamento_classe
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca os Estados.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarEstado() {
        $retorno = array();
        $sql     = "
            SELECT
                uf.ufoid AS oid,
                uf.ufuf AS descricao
            FROM
                uf
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca os Tipos de OS.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarOsTipo() {
        $retorno = array();
        $sql     = "
            SELECT
                os_tipo.ostoid AS oid,
                os_tipo.ostdescricao AS descricao
            FROM
                os_tipo
            WHERE
                os_tipo.ostdt_exclusao IS NULL
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca os Produtos Reservados.
     *
     * @param stdClass $param Paramêtros.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarProdutoReservado(stdClass $param) {
        $retorno = array();
        $sql     = "
           SELECT
                reserva_agendamento.ragordoid AS ordem_servico,
                (SELECT ostdescricao FROM  os_tipo WHERE ostoid = ordostoid) AS tipo_os,
                ordem_servico_agenda.osadata AS dt_agenda,
                endereco_representante.endvuf AS uf,
                endereco_representante.endvcidade AS cidade,
                representante.reprazao AS representante,
                instalador.itlnome AS instalador,
                reserva_agendamento.ragdt_cadastro AS dt_reserva,
                reserva_agendamento_status.rasdescricao AS status,
                produto.prdproduto AS produto,
                reserva_agendamento_item.raiqtde_estoque as qtde_estoque,
                equipamento_classe.eqcdescricao AS classe,
                CASE
                    WHEN reserva_agendamento_item.raiqtde_transito > 0 THEN
                        'Sim'::VARCHAR
                    ELSE
                        'Não'::VARCHAR
                END AS transito,
                raiesroid AS remessa,
                (SELECT to_char(esrdata,'dd/mm/yyyy') FROM estoque_remessa WHERE raiesroid=esroid) AS dt_remessa,
                usuarios.nm_usuario AS usuario,
                raiqtde_estoque
            FROM
                reserva_agendamento
                    INNER JOIN
                        ordem_servico_agenda ON (reserva_agendamento.ragordoid = ordem_servico_agenda.osaordoid
                                                AND ordem_servico_agenda.osaoid = reserva_agendamento.ragosaoid)
                    LEFT JOIN
                        instalador ON ordem_servico_agenda.osaitloid = instalador.itloid
                    INNER JOIN
                        ordem_servico ON reserva_agendamento.ragordoid = ordem_servico.ordoid
                    INNER JOIN
                        contrato ON (connumero = ordconnumero)
                    LEFT JOIN
                        endereco_representante ON reserva_agendamento.ragrepoid = endereco_representante.endvrepoid
                    LEFT JOIN
                        representante ON reserva_agendamento.ragrepoid = representante.repoid
                    INNER JOIN
                        reserva_agendamento_status ON reserva_agendamento.ragrasoid = reserva_agendamento_status.rasoid
                    INNER JOIN
                        reserva_agendamento_item ON reserva_agendamento.ragoid = reserva_agendamento_item.rairagoid
                    INNER JOIN
                        produto ON reserva_agendamento_item.raiprdoid = produto.prdoid
                    INNER JOIN
                        equipamento_classe ON contrato.coneqcoid = equipamento_classe.eqcoid
                    INNER JOIN
                        usuarios ON reserva_agendamento.ragusuoid = usuarios.cd_usuario
                    WHERE
                        TRUE
                    ";

        if (!empty($param->reldt_tipo) && !empty($param->reldt_inicial) && !empty($param->reldt_final)) {
            switch ($param->reldt_tipo) {
                case 'agenda' :
                    $sql.= "
                        AND
                            ordem_servico_agenda.osadata BETWEEN '".$param->reldt_inicial." 00:00:00' AND '".$param->reldt_final." 23:59:59'
                    ";
                    break;
                case 'reserva' :
                    $sql.= "
                        AND
                            reserva_agendamento.ragdt_cadastro  BETWEEN '".$param->reldt_inicial." 00:00:00' AND '".$param->reldt_final." 23:59:59'
                    ";
                    break;
            }
        }

        if (!empty($param->ordoid)) {
            $sql.= "
                AND
                    ordoid = ".intval($param->ordoid)."
            ";
        }

        if (!empty($param->repoid)) {
            $sql.= "
                AND
                    representante.repoid = ".intval($param->repoid)."
            ";
        }

        if (!empty($param->itloid)) {
            $sql.= "
                AND
                    instalador.itloid = ".intval($param->itloid)."
            ";
        }

        if (!empty($param->ufuf)) {
            $sql.= "
                AND
                    endereco_representante.endvuf = '".$param->ufuf."'
            ";
        }

        if (!empty($param->ciddescricao)) {
            $sql.= "
                AND
                    endereco_representante.endvcidade = '".$param->ciddescricao."'
            ";
        }

        if (!empty($param->eqcoid)) {
            $sql.= "
                AND
                    equipamento_classe.eqcoid = ".intval($param->eqcoid)."
            ";
        }

        if (!empty($param->ostoid)) {
            $sql.= "
                AND
                    ordem_servico.ordostoid = ".intval($param->ostoid)."
            ";
        }

        if (!empty($param->reserva_remessa)) {
            $sql.= "
                AND
                    raiqtde_transito > 0";
        }

        if (!empty($param->rasoid)) {
            $sql.= "
                AND
                    reserva_agendamento_status.rasoid = ".intval($param->rasoid)."
            ";
        }


        if (!empty($param->prdoid)) {
            $sql.= "
                AND
                    produto.prdoid = ".intval($param->prdoid)."
            ";
        }

        $sql.= "
            ORDER BY
                ordem_servico,
                dt_agenda,
                representante,
                produto
        ";

        //echo"<pre>";var_dump($sql);echo"</pre>";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca os Representantes.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarRepresentante() {
        $retorno = array();
        $sql     = "
                 SELECT
                    repoid AS oid,
                    reprazao AS descricao
                FROM
                    representante
                INNER JOIN
                    relacionamento_representante ON (relrrep_terceirooid = repoid)
                WHERE
                    repexclusao IS NULL
                AND NOT EXISTS
                    (SELECT 1 FROM relacionamento_representante WHERE relrrepoid != relrrep_terceirooid AND repoid = relrrep_terceirooid)
                 AND
                    (reprevenda IS TRUE OR repinstalacao IS TRUE OR repassistencia IS TRUE)
                ORDER BY
                    descricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca os Produtos.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarProdutos() {
    	$retorno = array();
    	$sql     = "SELECT
					    prdoid,
					    prdproduto
				    FROM
				    	produto
				    ORDER BY
				    	prdproduto";

    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	while ($registro = pg_fetch_object($rs)) {
    		$retorno[] = $registro;
    	}

    	return $retorno;
    }

    /**
     * Método que busca os Status de Reserva de Agendamento.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarReservaAgendamentoStatus() {
        $retorno = array();
        $sql     = "
            SELECT
                reserva_agendamento_status.rasoid AS oid,
                reserva_agendamento_status.rasdescricao AS descricao
            FROM
                reserva_agendamento_status
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
    * Recupera os dados de instaladores
    * Request: AJAX
    * @param int $depoid | id do departamento
    * @return array/stdClass
    */
    public function recuperarDadosInstalador($repoid) {

        $retorno = array();
         $i = 0;

        $sql = "
            SELECT
                itloid,
                itlnome
            FROM
                instalador
            WHERE
                itlrepoid = ".intval($repoid)."
            AND
                itldt_exclusao IS NULL
            ORDER BY
                itlnome";

        if(!$rs = pg_query($this->conn,$sql)){
            return $retorno;
        }

        while($registro = pg_fetch_object($rs)) {
            $retorno[$i]['id'] = $registro->itloid;
            $retorno[$i]['descricao'] = utf8_encode($registro->itlnome);
            $i++;
        }

        return $retorno;
    }

    /**
     * Verifica se o depto de um usuario pertence ao depto tecnico
     * @param  [integer] $usuoid [ID do usuario logado]
     * @return [boolean]
     */
    public function verificarDepartamentoTecnico($usuoid) {

        $retorno = false;

        $sql = '
                SELECT EXISTS (
                                SELECT 1
                                from usuarios
                                where usudepoid = 9
                                AND cd_usuario = '.intval($usuoid).') AS existe';

        if(!$rs = pg_query($this->conn, $sql)){
             throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $resultado = pg_fetch_object($rs);

        if( isset($resultado->existe) ){
            $retorno = ($resultado->existe == 't') ? true : false;
        }

        return $retorno;
    }

    /**
     * recupera o ID do prestador de servico relacionado ao usuario
     * @param  [integer] $usuoid [ID do usuario logado]
     * @return [integer]
     */
    public function recuperarCodigoPrestadorServico($usuoid) {

        $sql = '
                SELECT
                    usurefoid
                FROM
                    usuarios
                WHERE
                    cd_usuario = '.intval($usuoid).'';

        if(!$rs = pg_query($this->conn, $sql)){
             throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $resultado = pg_fetch_object($rs);

        $retorno = (isset($resultado->usurefoid)) ? $resultado->usurefoid : 0;

        return $retorno;
    }

}