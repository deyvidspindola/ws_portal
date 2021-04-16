<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	15/02/2013
 */

/**
 * Fornece os dados necessarios para o módulo do módulo cadastro para
 * efetuar ações referentes a manutenção dos testes para equipamentos
 */
class CadRestricoesVinculacaoAcessorioDAO {

    const STATUS_RESERVA = 3;
    const STATUS_PRE_RESERVA = 1;

	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	public $conn;


	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn)
	{
		$this->conn = $conn;
	}

	/**
	 * Responsável por aplicar os filtros da tela de
	 * pesquisa e retornar os dados dos equipamentos
	 */
    public function pesquisar() {
        try {

            $where = "";

            $where .= (isset($_POST['ravtipo_restricao']) && $_POST['ravtipo_restricao'] != "") ? " AND ravtipo_restricao = '".$_POST['ravtipo_restricao']."'" : "";
            $where .= (isset($_POST['eproid']) && $_POST['eproid'] != "") ? " AND eproid = ".$_POST['eproid'] : "";
            $where .= (isset($_POST['eqcoid']) && $_POST['eqcoid'] != "") ? " AND eqcoid = ".$_POST['eqcoid'] : "";
            $where .= (isset($_POST['eveoid']) && $_POST['eveoid'] != "") ? " AND eveoid = ".$_POST['eveoid'] : "";
            $where .= (isset($_POST['prdproduto']) && $_POST['prdproduto'] != "") ? " AND prdproduto ILIKE '%".$_POST['prdproduto']."%'" : "";
            $where .= (isset($_POST['tpcoid']) && $_POST['tpcoid'] != "") ? " AND ravtpcoid = ".$_POST['tpcoid'] : "";

            $sql = "SELECT ravoid,
                           eprnome,
                           eveversao,
                           eqcdescricao,
                           prdproduto,
                           to_char(ravdt_cadastro, 'DD/MM/YYYY') AS ravdt_cadastro ,
                           nm_usuario
                      FROM restricao_acessorio_vinculacao
                 LEFT JOIN equipamento_projeto
                        ON eproid = raveproid
                 LEFT JOIN equipamento_versao
                        ON eveoid = raveveoid
                 LEFT JOIN equipamento_classe
                        ON eqcoid = raveqcoid
                 LEFT JOIN produto
                        ON prdoid = ravprdoid
                 LEFT JOIN usuarios
                        ON ravusuoid_cadastro = cd_usuario
                     WHERE ravdt_exclusao IS NULL
                      $where
                  ORDER BY eprnome,
                           eveversao,
                           eqcdescricao,
                           prdproduto";

            $resultado = array('results');

            $cont = 0;

            $rs = pg_query($this->conn, $sql);

            while ($rResultados = pg_fetch_assoc($rs)) {
                $resultado['results'][$cont]['ravoid']       = $rResultados['ravoid'];
                $resultado['results'][$cont]['eprnome']      = $rResultados['eprnome'];
                $resultado['results'][$cont]['eqcdescricao'] = $rResultados['eqcdescricao'];
                $resultado['results'][$cont]['eveversao']    = $rResultados['eveversao'];
                $resultado['results'][$cont]['prdproduto']   = $rResultados['prdproduto'];
                $resultado['results'][$cont]['dt_cadastro']  = $rResultados['ravdt_cadastro'];
                $resultado['results'][$cont]['nm_usuario']   = $rResultados['nm_usuario'];

                $cont++;
            }

            $resultado['total_registros'] = 'A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).';

            return $resultado;

        }catch(Exception $e ) {
            return false;
        }
    }

    /**
     * Retorna lista de todos os projetos cadastrados
     */
    public function buscaEquipamentosProjeto()
    {
        try {

            $sql = "SELECT eproid,
                           eprnome
                      FROM equipamento_projeto
                  ORDER BY eprnome";

            $rs = pg_query($this->conn, $sql);

            $cont = 0;

            while ($rEquipamentos = pg_fetch_assoc($rs)) {

                $resultado[$cont]['eproid']  = $rEquipamentos['eproid'];
                $resultado[$cont]['eprnome'] = $rEquipamentos['eprnome'];

                $cont++;
            }

            return $resultado;

        } catch(Exception $e) {
            return "erro";
        }
    }

    /**
     * Retorna lista de todas as classes cadastradas
     */
    public function buscaEquipamentosClasse()
    {
        try {
            $sql = "SELECT eqcoid,
                           eqcdescricao
                      FROM equipamento_classe
                  ORDER BY eqcdescricao";

            $rs = pg_query($this->conn, $sql);

            $cont = 0;

            while ($rEquipamentos = pg_fetch_assoc($rs)) {

                $resultado[$cont]['eqcoid']       = $rEquipamentos['eqcoid'];
                $resultado[$cont]['eqcdescricao'] = $rEquipamentos['eqcdescricao'];

                $cont++;
            }

            return $resultado;

        } catch(Exception $e) {
            return "erro";
        }
    }

    /**
     * Retorna a lista de todas as versões cadastradas
     *
     * @param boolean $encode - Caso true, retorna dados em formato JSON, caso false, retorna array
     */
    public function buscaEquipamentosVersao($encode)
    {
        try {

            $where = (isset($_POST['eveprojeto']) && $_POST['eveprojeto'] != "" && $_POST['eveprojeto'] != 0)?" AND eveprojeto = ".$_POST['eveprojeto']:"";

            $sql = "SELECT eveoid,
                           eveversao
                      FROM equipamento_versao
                     WHERE eveversao != ''
                       AND evedt_exclusao IS NULL
                       $where
                  ORDER BY eveversao";

            $rs = pg_query($this->conn, $sql);

            $cont = 0;

            $resultado = array('versoes');

            if(pg_num_rows($rs) > 0) {
                while ($rEquipamentos = pg_fetch_assoc($rs)) {

                    $resultado['versoes'][$cont]['eveoid']    = $rEquipamentos['eveoid'];
                    $resultado['versoes'][$cont]['eveversao'] = $rEquipamentos['eveversao'];

                    $cont++;
                }

                if($encode === true) {
                    return json_encode($resultado);
                } else {
                    return $resultado;
                }
            } else {
                return false;
            }

        } catch(Exception $e) {
            return "erro";
        }
    }

    /**
     * Retorna lista dos tipos de contrato cadastrados
     */
    public function buscaEquipamentosTipoContrato()
    {
        $tipos = array();

        $sql = "SELECT tpcoid,tpcdescricao
                FROM tipo_contrato
                WHERE tpcativo = 't'
                ORDER BY tpcdescricao ";

        $rs = pg_query($this->conn, $sql);

        while ($row = pg_fetch_object($rs)) {
            $tipos[$row->tpcoid] = $row->tpcdescricao;
        }

        return $tipos;
    }

    /**
     * Retorna lista de todos os projetos cadastrados
     */
    public function salvar()
    {
        try {

            $eproid = ($_POST['eproid'] != "")?$_POST['eproid'] : 'null';
            $eqcoid = ($_POST['eqcoid'] != "")?$_POST['eqcoid'] : 'null';
            $eveoid = ($_POST['eveoid'] != "")?$_POST['eveoid'] : 'null';
            $prdoid = ($_POST['prdoid'] != "")?$_POST['prdoid'] : 'null';
            $tpcoid = ($_POST['tpcoid'] != "")?$_POST['tpcoid'] : 'null';
            $tipo_restricao = ($_POST['ravtipo_restricao'] != "")?$_POST['ravtipo_restricao'] : 'null';

            $usuoid = $_SESSION['usuario']['oid'];


            $sql = "INSERT INTO restricao_acessorio_vinculacao (
                                    raveproid,
                                    raveqcoid,
                                    raveveoid,
                                    ravprdoid,
                                    ravtpcoid,
                                    ravtipo_restricao,
                                    ravdt_cadastro,
                                    ravusuoid_cadastro
                                )
                         VALUES (
                                    $eproid,
                                    $eqcoid,
                                    $eveoid,
                                    $prdoid,
                                    $tpcoid,
                                    '$tipo_restricao',
                                    'now()',
                                    $usuoid
                                )";

            $result = pg_query($this->conn, $sql);


            return "ok";

        } catch(Exception $e) {
            return "erro";
        }
    }

    /**
     * Verifica se já existe um teste com a mesma configuração do formulário
     */
    public function verificaIntegridade()
    {
        $eproid         = ($_POST['eproid'] != "")?$_POST['eproid'] : 'null';
        $eqcoid         = ($_POST['eqcoid'] != "")?$_POST['eqcoid'] : 'null';
        $eveoid         = ($_POST['eveoid'] != "")?$_POST['eveoid'] : 'null';
        $prdoid         = ($_POST['prdoid'] != "")?$_POST['prdoid'] : 'null';
        $tpcoid         = ($_POST['tpcoid'] != "")?$_POST['tpcoid'] : 'null';
        $tipo_restricao = ($_POST['tipo_restricao'] != "")?$_POST['tipo_restricao'] : 'null';

        try {
            $sql = "SELECT count(ravoid) as qtd
                      FROM restricao_acessorio_vinculacao
                     WHERE raveproid = $eproid
                       AND raveqcoid = $eqcoid
                       AND raveveoid = $eveoid
                       AND ravprdoid = $prdoid
                       AND ravtpcoid = $tpcoid
                       AND ravtipo_restricao = '$tipo_restricao'
                       AND ravdt_exclusao IS NULL";

            $rs = pg_query($this->conn, $sql);

            while ($arrRs = pg_fetch_array($rs)){
                $qtd = $arrRs['qtd'];
            }

            return $qtd;
        } catch(Exception $e) {
            return "erro";
        }
    }

    /**
     * Exclui comando
     *
     * @param integer $emctoid
     */
    public function excluiRestricao()
    {
        $ravoid = $_POST['ravoid'];
        $usuoid = $_SESSION['usuario']['oid'];

        try {
            $sql = "UPDATE restricao_acessorio_vinculacao
                       SET ravdt_exclusao = 'now()',
                           ravusuoid_exclusao = $usuoid
                     WHERE ravoid = $ravoid";

            $rs = pg_query($this->conn, $sql);

            return "ok";
        } catch(Exception $e) {
            return "erro";
        }
    }

    public function buscaProdutos()
    {
        $div = "";

        $palavra = $_POST['palavra'];

        $palavra = trim($palavra);

        $sql = "SELECT prdoid as codigo,
                       prdproduto as descricao,
                       prdptioid, prdtp_cadastro
                  FROM produto
                 WHERE prddt_exclusao IS NULL
                   AND prdtp_cadastro = 'P'
                   AND prdptioid in (1,4)";

        if(is_numeric($palavra)){
            $sql .= " AND (prdproduto ilike '%$palavra%' OR prdoid = $palavra) ";
        }else{
            $sql .= " AND prdproduto ilike '%$palavra%' ";
        }

        $sql .= " ORDER BY prdproduto";


        $rs = pg_query($this->conn,$sql);

        if($rs){

            $count = pg_num_rows($rs);

            if($count > 0){

                $size = ($count>20)?20:$count;
                $size = ($size==1)?3:$size;
                $div .= '
                <select name="produto_inicio" id="produto_inicio" onchange="selecionar_produto(this.value);" size="'.$size.'" style="width:570px;">';

                for($i=0;$i<$count;$i++){

                    $codigo         = pg_fetch_result($rs,$i,"codigo");
                    $descricao      = pg_fetch_result($rs,$i,"descricao");
                    $prdptioid      = pg_fetch_result($rs,$i,"prdptioid");
                    $prdtp_cadastro = pg_fetch_result($rs,$i,"prdtp_cadastro");

                    $tipo = "";

                    if($prdtp_cadastro == "S"){
                        $tipo = "(S) - ";
                    }else{

                        if($prdptioid == 1){
                            $tipo = "(I) - ";
                        }

                        if($prdptioid == 2){
                            $tipo = "(E) - ";
                        }

                        if($prdptioid == 3){
                            $tipo = "(C) - ";
                        }

                        if($prdptioid == 4){
                            $tipo = "(R) - ";
                        }

                        if($prdptioid == 5){
                            $tipo = "(ER) - ";
                        }

                    }

                    $div .= '<option value="'.$codigo . '|' . $tipo . utf8_encode($descricao ).'">'.$tipo.$codigo . ' - '.utf8_encode($descricao).'</option>';

                }

                $div .= '</select>';

            }

        }

        return $div;
    }

    public function atualizaProduto()
    {
        $produto = $_POST['produto'];

        //Verifica se foi informado um produto
        if($produto){

            //faz a consulta para ver se o produto exite
            $sql = "SELECT prdoid,
                           prdproduto,
                           prdplcoid,
                           prdptioid,
                           prdtp_cadastro
                      FROM produto
                     WHERE prddt_exclusao IS NULL
                       AND prdtp_cadastro = 'P'
                       AND prdptioid IN (1,4)
                       AND prdoid = $produto ";

            $rs = pg_query($this->conn,$sql);

            //Se existir preenche os campos com os dados do produto
            if(pg_num_rows($rs) > 0){

                $row = pg_fetch_array($rs);

                $prdptioid      = $row["prdptioid"];
                $prdtp_cadastro = $row["prdtp_cadastro"];

                if($prdtp_cadastro == "S"){
                    $tipo = "(S) - ";
                }else{

                    if($prdptioid == 1){
                        $tipo = "(I) - ";
                    }

                    if($prdptioid == 2){
                        $tipo = "(E) - ";
                    }

                    if($prdptioid == 3){
                        $tipo = "(C) - ";
                    }

                    if($prdptioid == 4){
                        $tipo = "(R) - ";
                    }

                    if($prdptioid == 5){
                        $tipo = "(ER) - ";
                    }

                }

                $retorno['produto']['id']   = $row['prdoid'];
                $retorno['produto']['nome'] = utf8_encode($tipo.$row['prdproduto']);

                return json_encode($retorno);

            }else{

                return false;

            }

        }

    }

    public function retornaNomeProduto($produto)
    {
        //Verifica se foi informado um produto
        if($produto){

            //faz a consulta para ver se o produto exite
            $sql = "SELECT prdoid,
                           prdproduto,
                           prdplcoid,
                           prdptioid,
                           prdtp_cadastro
                      FROM produto
                     WHERE prddt_exclusao IS NULL
                       AND prdtp_cadastro = 'P'
                       AND prdptioid in (1,4)
                       AND prdoid = $produto ";

            $rs = pg_query($this->conn,$sql);

            //Se existir preenche os campos com os dados do produto
            if(pg_num_rows($rs) > 0){

                $row = pg_fetch_array($rs);

                $prdptioid      = $row["prdptioid"];
                $prdtp_cadastro = $row["prdtp_cadastro"];

                if($prdtp_cadastro == "S"){
                    $tipo = "(S) - ";
                }else{

                    if($prdptioid == 1){
                        $tipo = "(I) - ";
                    }

                    if($prdptioid == 2){
                        $tipo = "(E) - ";
                    }

                    if($prdptioid == 3){
                        $tipo = "(C) - ";
                    }

                    if($prdptioid == 4){
                        $tipo = "(R) - ";
                    }

                    if($prdptioid == 5){
                        $tipo = "(ER) - ";
                    }

                }

                return $tipo.$row['prdproduto'];

            }else{

                return false;

            }

        }

    }

    public function retornaDadosValidacaoRestricoes($consoid)
    {
        if($consoid > 0) {
            try {
                $sql = "SELECT eveoid,
						   eqcoid,
						   eveversao,
						   eproid,
						   eprnome,
						   coneqcoid,
						   eqcdescricao,
               conno_tipo,
               tpcdescricao
					  FROM contrato
					   JOIN contrato_servico ON consconoid=connumero
            LEFT JOIN tipo_contrato
              ON conno_tipo = tpcoid
					  JOIN equipamento
						ON conequoid = equoid
					  JOIN equipamento_versao
						ON equeveoid = eveoid
					  JOIN equipamento_projeto
						ON eveprojeto = eproid
					  JOIN equipamento_classe
						ON coneqcoid = eqcoid
					 WHERE consoid=".$consoid;

                $rs = pg_query($this->conn, $sql);

                while($row = pg_fetch_array($rs)) {

                    $ret['eveoid']       = $row['eveoid'];
                    $ret['eqcoid']       = $row['eqcoid'];
                    $ret['eveversao']    = $row['eveversao'];
                    $ret['eproid']       = $row['eproid'];
                    $ret['eprnome']      = $row['eprnome'];
                    $ret['coneqcoid']    = $row['coneqcoid'];
                    $ret['eqcdescricao'] = $row['eqcdescricao'];
                    $ret['conno_tipo']   = $row['conno_tipo'];
                    $ret['tpcdescricao'] = $row['tpcdescricao'];
                }

                return $ret;

            } catch (Exception $e) {
                return "";
            }
        } else {
            return "";
        }
    }

    /**
     * @param int $eproid id do equipamento_projeto
     * @param int $eveoid id do equipamento_versao
     * @param int $prdoid id do produto
     *
     * @return number
     */
    public function verificaRestricaoProjetoVersao($eproid, $eveoid, $prdoid)
    {
        try{
            $prdoid = (trim($prdoid) != '') ? $prdoid : 0;
            $qtd = 0;

            $sql = "SELECT count(*) AS qtd
                      FROM restricao_acessorio_vinculacao
                     WHERE raveproid = $eproid
                       AND raveveoid = $eveoid
                       AND ravprdoid = $prdoid
                       AND ravtipo_restricao = 'V'
                       AND ravdt_exclusao IS NULL";

            $rs = pg_query($this->conn, $sql);

            while ($arrRs = pg_fetch_array($rs)){
                $qtd = $arrRs['qtd'];
            }

            return $qtd;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     *
     */
    public function verificaRestricaoProjeto($eproid, $prdoid)
    {
        try{
            $prdoid = (trim($prdoid) != '') ? $prdoid : 0;
            $qtd = 0;

            $sql = "SELECT count(*) as qtd
                      FROM restricao_acessorio_vinculacao
                     WHERE raveproid = $eproid
                       AND ravprdoid = $prdoid
                       AND ravtipo_restricao = 'P'
                       AND ravdt_exclusao IS NULL
                       AND raveveoid IS NULL";

            $rs = pg_query($this->conn, $sql);

            while ($arrRs = pg_fetch_array($rs)){
                $qtd = $arrRs['qtd'];
            }

            return $qtd;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     *
     */
    public function verificaRestricaoAcessorioTipoContrato($tpcoid, $prdoid)
    {
        try{
            $prdoid = (trim($prdoid) != '') ? $prdoid : 0;
            $qtd = 0;

              $sql = "SELECT count(*) as qtd
                        FROM restricao_acessorio_vinculacao
                       WHERE ravtpcoid = $tpcoid
                         AND ravprdoid = $prdoid
                         AND ravtipo_restricao = 'T'
                         AND ravdt_exclusao IS NULL
                         AND raveveoid IS NULL
                         AND raveproid IS NULL";

              $rs = pg_query($this->conn, $sql);

              while ($arrRs = pg_fetch_array($rs)){
                  $qtd = $arrRs['qtd'];
              }

              return $qtd;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     *
     */
    public function verificaRestricaoEquipamentoTipoContrato($connumero, $equoid)
    {
        try{
              $sql = "SELECT true
                        FROM tipo_contrato,contrato
                      WHERE conno_tipo=tpcoid
                      AND
                      ( tpcoid NOT IN (SELECT tpcitpcoid FROM tipo_contrato_instalacao WHERE tpcidt_exclusao IS NULL AND tpcitpcoid=tpcoid)
                        OR ((SELECT eveprojeto FROM equipamento,equipamento_versao WHERE equeveoid=eveoid AND equoid=$equoid)
                          IN (SELECT tpcieproid FROM tipo_contrato_instalacao WHERE tpcidt_exclusao IS NULL AND tpcitpcoid=tpcoid)))
                      AND connumero=$connumero";

              $rs = pg_query($this->conn, $sql);

              if (pg_num_rows($rs) > 0) {
                  return true;
              } else {
                  return false;
              }

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *
     */
    public function verificaRestricaoClasse($eqcoid, $prdoid)
    {
        try{
            $prdoid = (trim($prdoid) != '') ? $prdoid : 0;
            $qtd = 0;

            $sql = "SELECT count(*) as qtd
                      FROM restricao_acessorio_vinculacao
                     WHERE ravprdoid = $prdoid
                       AND raveqcoid = $eqcoid
                       AND ravtipo_restricao = 'C'
                       AND ravdt_exclusao IS NULL
                       AND raveveoid IS NULL
                       AND raveproid IS NULL";

            $rs = pg_query($this->conn, $sql);

            while ($arrRs = pg_fetch_array($rs)){
                $qtd = $arrRs['qtd'];
            }

            return $qtd;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     *
     */
    public function retornaDadosValidacaoRestricoesEquipamento($ordconnumero)
    {
        if($ordconnumero > 0) {
            try {
                $sql = "SELECT connumero,
                               consrefioid,
                               consobroid,
                               conno_tipo,
                               tpcdescricao,
                               obrigacao_financeira_tecnica.*,
                               obrprdoid
                          FROM contrato
                          JOIN contrato_servico
                            ON connumero = consconoid
                          LEFT JOIN tipo_contrato
                            ON conno_tipo = tpcoid
                          JOIN obrigacao_financeira_tecnica
                            ON oftcobroid = consobroid
                          JOIN obrigacao_financeira
                            ON obroid = consobroid
                         WHERE consiexclusao IS NULL
                           AND consinstalacao IS NOT NULL
                           --AND consrefioid IS NOT NULL
                           AND oftcexclusao IS NULL
                           AND connumero=$ordconnumero";

                $rs = pg_query($this->conn, $sql);

                $i = 0;

                while($row = pg_fetch_array($rs)) {

                    $ret[$i]['connumero']             = $row['connumero'];
                    $ret[$i]['consrefioid']           = $row['consrefioid'];
                    $ret[$i]['consobroid']            = $row['consobroid'];
                    $ret[$i]['conno_tipo']            = $row['conno_tipo'];
                    $ret[$i]['tpcdescricao']          = $row['tpcdescricao'];
                    $ret[$i]['oftcoid']               = $row['oftcoid'];
                    $ret[$i]['oftcobroid']            = $row['oftcobroid'];
                    $ret[$i]['oftctabela']            = $row['oftctabela'];
                    $ret[$i]['oftcprefixo']           = $row['oftcprefixo'];
                    $ret[$i]['oftcatgoid']            = $row['oftcatgoid'];
                    $ret[$i]['oftcexclusao']          = $row['oftcexclusao'];
                    $ret[$i]['oftcusuoid_exclusao']   = $row['oftcusuoid_exclusao'];
                    $ret[$i]['oftcprefixo_status']    = $row['oftcprefixo_status'];
                    $ret[$i]['oftcprefixo_historico'] = $row['oftcprefixo_historico'];
                    $ret[$i]['oftcimotoid']           = $row['oftcimotoid'];
                    $ret[$i]['oftcattoid']            = $row['oftcattoid'];
                    $ret[$i]['oftcnome']              = $row['oftcnome'];
                    $ret[$i]['obrprdoid']             = $row['obrprdoid'];
                    $i++;
                }

                return $ret;

            } catch (Exception $e) {
                return "";
            }
        } else {
            return "";
        }
    }

    public function retornaDadosContrato($consoid)
    {
        try{
            $sql = "SELECT consobroid,
                           conssituacao,
                           consconoid,
                           consqtde,
                           obrobrigacao AS nome_obrigacao,
                           oftcnome AS nome_descricao
                      FROM obrigacao_financeira
                INNER JOIN contrato_servico
                        ON consobroid = obroid
                INNER JOIN contrato
                        ON consconoid = connumero
                 LEFT JOIN obrigacao_financeira_tecnica
                        ON oftcobroid = obroid
                     WHERE consoid = $consoid";

            $rs = pg_query($this->conn,$sql);

            while($row = pg_fetch_array($rs)) {

                $arrDadosContrato = $row;
            }

            return $arrDadosContrato;

        } catch (Exception $e) {
            return "";
        }
    }

    public function retornaDadosTipoContrato($connumero)
    {
        $arrDadosTipoContrato = array();

        $sql = "SELECT
                  connumero,
                  tipo_contrato.*
                FROM contrato
                  LEFT JOIN tipo_contrato
                  ON conno_tipo = tpcoid
                WHERE connumero=$connumero";

        $rs = pg_query($this->conn,$sql);

        while($row = pg_fetch_array($rs)) {

            $arrDadosTipoContrato = $row;
        }

        return $arrDadosTipoContrato;
    }

    public function retornaDadosObrigacaoFinanceira($consobroid)
    {
        try {
            $sql = "SELECT obroftoid,
                           oftcatgoid,
                           oftctabela,
                           oftcprefixo,
                           oftcimotoid,
                           obrprdoid,
                           oftcprefixo_status,
                           oftcprefixo_historico,
                           oftcnome
                      FROM obrigacao_financeira
                INNER JOIN obrigacao_financeira_tipo
                        ON obroftoid = oftoid
                 LEFT JOIN obrigacao_financeira_tecnica
                        ON oftcobroid = obroid
                     WHERE obroid = $consobroid
                       AND obrdt_exclusao IS NULL
                       AND oftexclusao IS NULL
                       AND oftcexclusao IS NULL
                     LIMIT 1";

            $rs = pg_query($this->conn,$sql);

            while($row = pg_fetch_array($rs)) {

                $arrDadosObrigacaoFinanceira = $row;
            }

            return $arrDadosObrigacaoFinanceira;

        } catch(Exception $e) {
            return "";
        }
    }

    public function retornaProjetoVersao($equoid){
        try{
            $sql = "SELECT eveoid,
                           eveprojeto,
                           eprnome
                      FROM equipamento
                      JOIN equipamento_versao
                        ON equeveoid = eveoid
                      JOIN equipamento_projeto
                        ON eproid = eveprojeto
                     WHERE equoid = $equoid";

            $rs = pg_query($this->conn,$sql);

            if(pg_num_rows($rs)>0){
            	while($row = pg_fetch_array($rs)) {

	                $arrProjetoVersao['eveoid']     = $row['eveoid'];
	                $arrProjetoVersao['eveprojeto'] = $row['eveprojeto'];
	                $arrProjetoVersao['eprnome']    = $row['eprnome'];
				}

                return $arrProjetoVersao;
            } else {
                return "";
            }

          } catch (Exception $e) {

          }

    }

    public function retornaPrdOidNaoImobilizado($oftcprefixo, $oftcprefixo_status, $oftctabela, $cpserial, $consrefioid)
    {
        try{
            $sql = "SELECT ".$oftcprefixo."oid,
                           ".$oftcprefixo.$oftcprefixo_status."oid,
                           ".$oftcprefixo."prdoid AS prdoid
                      FROM $oftctabela
                     WHERE ".$oftcprefixo."$cpserial::text = '$consrefioid'";

            $rs = pg_query($this->conn,$sql);

            if(pg_num_rows($rs)>0){

                $oid_tabela    = pg_fetch_result($rs,0,0);
                $status_tabela = pg_fetch_result($rs,0,1);
                $prdoid = pg_fetch_result($rs, 0, 'prdoid');

                //VERIFICAR A DISPONIBILIDADE DO SERIAL
                /*if($status_tabela!=3 && $status_tabela!=8){
                    return "Erro - O serial informado não está disponível para o representante.";
                }*/

                return $prdoid;

            }else{
                return "Erro - Número de série inválido."; //Serial inválido
            }
        } catch (Exception $e) {
            return "Erro - ";
        }

    }

    public function retornaPrdOidImobilizado($oftcimotoid, $consrefioid, $cpserial)
    {
        try {
            $sql = "SELECT imoboid,
                           imobprdoid AS prdoid
                      FROM imobilizado
                     WHERE imob".$cpserial."::text = '$consrefioid'
                       AND imobimotoid = $oftcimotoid";

            $rs = pg_query($this->conn,$sql);

            if(pg_num_rows($rs)>0){
                $oid_tabela = pg_fetch_result($rs,0,0);
                $prdoid = pg_fetch_result($rs, 0, 'prdoid');
            }else{
                return "Erro - Número de série inválido."; //Serial inválido
            }

            return $prdoid;

        } catch (Exception $e) {
            return "Erro - ";
        }
    }

	public function retornaEquOid($no_serie)
	{
		try{
			$sql ="SELECT equoid
				     FROM equipamento
				    WHERE equno_serie = $no_serie";

            $resultado = pg_query ($this->conn,$sql);

            if(pg_num_rows($resultado) >0){
                $equoid = pg_result($resultado,0,'equoid');

                return $equoid;
            } else {
				return "0";
            }
        } catch(Exception $e) {
        	return "0";
        }
	}

    /**
     * Valida se o equipamento pode ser instalado para um cliente que é premium
     * @STI 86821
     */
    public function validaEquipamentoPremium($eqcserie)
    {
        $sqlVerificaInstalacao = "SELECT EXISTS (
                                    SELECT equoid
                                    FROM equipamento
                                        JOIN produto ON equprdoid = prdoid
                                    WHERE equno_serie = $eqcserie
                                        AND prdpremium IS TRUE
                                ) AS OK";

        $rsInstallOk = pg_query($this->conn, $sqlVerificaInstalacao);
        $equInstall  = pg_fetch_object($rsInstallOk);

        return $equInstall->ok == 't';
    }

    /**
     * Valida se o acessório pode ser instalado para um cliente que é premium
     * @STI 86821
     */
    public function validaAcessorioPremium($prdoid)
    {
        $sqlVerificaInstalacao = "SELECT EXISTS (
                                    SELECT prdoid
                                    FROM produto
                                    WHERE prdoid = $prdoid
                                        AND prdpremium IS TRUE
                                ) AS OK";

        $rsInstallOk = pg_query($this->conn, $sqlVerificaInstalacao);
        $equInstall  = pg_fetch_object($rsInstallOk);

        return $equInstall->ok == 't';
    }

  /**
   * Verificando o último equipamento instalado no contrato
   * Se o $obj->equoid passado for o último equipamento do $obj->connumero, retorna true senão retorna false
   * @param stdClass $obj - $obj->connumero = número do contrato, $obj->equoid = oid do equipamento
   **/
  public function validaUltimoEquipamento($obj)
  {
        /**
        * Validando se o número de série é o antigo equipamento do contrato
        **/
        try{

          $sql_coneq = "SELECT conequoid, conequoid_antigo
                        FROM contrato
                        WHERE connumero = {$obj->connumero}
                        AND conequoid IS NULL
                        AND conequoid_antigo = {$obj->equoid}";
          $res_coneq = pg_query($this->conn, $sql_coneq);
          $linhas_coneq = pg_num_rows($res_coneq);

          if ($linhas_coneq > 0){
            return true;
          }else{
            return false;
          }
        }catch(Exception $ex){
          return $ex->getMessage();
        }
  }

/**
  * Busca as particularidades dos status da linha
  * @author Thomas de Lima <thomas.lima.ext@sascar.com.br>
  * @param
  **/
  public function recuperaParticularidadesStatusLinha()
  {
    $sql = "SELECT cslpoid, cslpdominio, cslpchave, cslpcsloid, cslpbloqueia_acao, cslpmensagem, cslpcallback
        FROM celular_status_linha_particularidade
        WHERE cslpdominio = 'ERP' AND cslpchave = 'CONTRATO_SERVICOS'";
    $rs = pg_query($this->conn, $sql);

    if(pg_num_rows($rs) == 0) {
      throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    }
    $resultados = pg_fetch_all($rs);

    /**
    * Montando o array com as regras
    * @return array $retorno - $retorno[ID_STATUS_LINHA]
    **/
    $retorno = array();
    foreach ($resultados as $r) {

      $statusLinha = $r['cslpcsloid'] == '' ? 0 : $r['cslpcsloid'];

      $retorno[$statusLinha] = array(
        'trava' => ($r['cslpbloqueia_acao'] == 't' ? true : false),
        'alerta' => $r['cslpmensagem'],
        'callback' => $r['cslpcallback']
      );

    }

    return $retorno;

  }

    /**
     * inicia transação com o BD
     */
    public function begin()
    {
        $rs = pg_query($this->conn, "BEGIN;");
    }

    /**
     * confirma alterações no BD
     */
    public function commit()
    {
        $rs = pg_query($this->conn, "COMMIT;");
    }

    /**
     * desfaz alterações no BD
     */
    public function rollback()
    {
        $rs = pg_query($this->conn, "ROLLBACK;");
    }

}