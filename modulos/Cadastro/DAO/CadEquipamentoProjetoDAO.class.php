<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	11/01/2013 
 */

/**
 * Fornece os dados necessarios para o módulo do módulo cadastro para 
 * efetuar ações referentes a manutenção dos testes para equipamentos 
 */
class CadEquipamentoProjetoDAO {
	
	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	public $conn;
	
  public $usuario;
	
	
	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn)
	{
		$this->conn = $conn;

     $this->usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '' ;
	}
    
	/**
	 * Responsável por aplicar os filtros da tela de 
	 * pesquisa e retornar os dados dos equipamentos 
	 */
    public function pesquisar() {
        try {
            
            $where = "";
            
            $where .= (isset($_POST['eprtipo']) && $_POST['eprtipo'] != "") ? " AND eprtipo = '".$_POST['eprtipo']."'" : "";
            $where .= (isset($_POST['eprnome']) && $_POST['eprnome'] != "") ? " AND eprnome ILIKE '%".$_POST['eprnome']."%'" : "";
    
            $sql = "SELECT eproid, 
                           eprnome, 
                           eprtipo, 
                           eprteste_portal,
                           eprquadriband,
                           eprcadastro,
                           cadastro.nm_usuario
                      FROM equipamento_projeto
                 LEFT JOIN usuarios as cadastro 
                        ON eprusuoid = cd_usuario
                     WHERE eprnome IS NOT NULL 
                       $where 
                  ORDER BY eprnome";

            $resultado = array('equipamentos');
            
            $cont = 0;
            
            $rs = pg_query($this->conn, $sql);
            
            while ($rEquipamentos = pg_fetch_assoc($rs)) {
                $resultado['equipamentos'][$cont]['eproid']          = $rEquipamentos['eproid']; 
                $resultado['equipamentos'][$cont]['eprnome']         = $rEquipamentos['eprnome']; 
                $resultado['equipamentos'][$cont]['eprtipo']         = $rEquipamentos['eprtipo']; 
                $resultado['equipamentos'][$cont]['eprteste_portal'] = $rEquipamentos['eprteste_portal'];
                $resultado['equipamentos'][$cont]['eprquadriband']   = $rEquipamentos['eprquadriband'];
                $resultado['equipamentos'][$cont]['eprcadastro']     = $rEquipamentos['eprcadastro'];
                $resultado['equipamentos'][$cont]['nm_usuario']      = $rEquipamentos['nm_usuario'];
                
                $cont++;
            }
    
            $resultado['total_registros'] = 'A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).';
            
            return $resultado;
            
        }catch(Exception $e ) {
            return false;
        }
    }
    
    /**
     * Responsável por retornar dados do 
     * equipamento na tela de edição
     */
    public function editar()
    {
        try{
            $eproid = $_POST['eproid'];
            
            $sql = "SELECT ep.*, nm_usuario
                      FROM equipamento_projeto as ep
                 LEFT JOIN usuarios
                        ON eprusuoid_alteracao = cd_usuario
                     WHERE eproid = $eproid";
                     
            $rs = pg_query($this->conn, $sql);
    
            $arrEpro = array();
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){
                    $arrEpro = $arrRs;
                }
            }
            $arrEpro['nomeProduto'] = $this->retornaNomeProduto($arrEpro['eprprdoid']);
            
            return $arrEpro;
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
                   AND prdptioid in (1,2)
                   AND prdptioid IS NOT NULL";
        
        if(is_numeric($palavra)){
            $sql .= " AND (prdproduto ilike '%$palavra%' OR prdoid = $palavra) ";
        }else{      
            $sql .= " AND prdproduto ilike '%$palavra%' ";
        }
        
        $sql .= " ORDER BY prdtp_cadastro, prdptioid, prdproduto";
        
        
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
                       AND prdptioid in (1,2)
                       AND prdptioid is not null
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
    
    private function retornaNomeProduto($produto)
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
                       AND prdptioid in (1,2)
                       AND prdptioid is not null
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
    

    /**
     * Retorna lista de todos os projetos cadastrados
     */
    public function salvar()
    {
        try {

            $eproid                            = $_POST['eproid'];
            $eprnome                           = ($_POST['eprnome'])?utf8_decode($_POST['eprnome']):'';
            $eprmotivo                         = ($_POST['eprmotivo'])?utf8_decode($_POST['eprmotivo']):'';
            $eprdescricao_tecnica              = ($_POST['eprdescricao_tecnica'])?utf8_decode($_POST['eprdescricao_tecnica']):'';
            $eprprdoid                         = ($_POST['eprprdoid'])?$_POST['eprprdoid']:'null';
            $eprquadriband                     = ($_POST['eprquadriband'])?$_POST['eprquadriband']:'';
            $eprcompativel_jamming             = ($_POST['eprcompativel_jamming'])?$_POST['eprcompativel_jamming']:'';
            $eprteste_portal                   = ($_POST['eprteste_portal'])?$_POST['eprteste_portal']:'';
            $eprtipo                           = ($_POST['eprtipo'])?$_POST['eprtipo']:'';
            $eprprecisao_odometro_portal       = ($_POST['eprprecisao_odometro_portal'])?$_POST['eprprecisao_odometro_portal']:'null';
            $eprmultiplicador_odometro_posicao = ($_POST['eprmultiplicador_odometro_posicao'])?$_POST['eprmultiplicador_odometro_posicao']:'null';
            $eprtolerancia_odometro            = ($_POST['eprtolerancia_odometro'])?$_POST['eprtolerancia_odometro']:'null';
            $eprqtd_testes_posicao             = ($_POST['eprqtd_testes_posicao'])?$_POST['eprqtd_testes_posicao']:'null';
            $eprintervalo_testes_posicao       = ($_POST['eprintervalo_testes_posicao'])?$_POST['eprintervalo_testes_posicao']:'null';
            $eprresumo_configuracoes           = ($_POST['eprresumo_configuracoes'])?$_POST['eprresumo_configuracoes']:'';
            $eprorigem_ultima_posicao          = isset($_POST['eprorigem_ultima_posicao']) ? $_POST['eprorigem_ultima_posicao'] : 'null';
            $eprtempo_posicao_teste            = ($_POST['eprtempo_posicao_teste'])?$_POST['eprtempo_posicao_teste']:'null';
            $eprtempo_posicao_final            = ($_POST['eprtempo_posicao_final'])?$_POST['eprtempo_posicao_final']:'null';
            $eprtempo_expiracao_bloqueio       = ($_POST['eprtempo_expiracao_bloqueio'])?$_POST['eprtempo_expiracao_bloqueio']:'null';
            $epregtoid                         = isset($_POST['egtgrupo']) ? intval($_POST['egtgrupo']) : 'null';
            $eprvalor_ajuste_rpm               = intval($_POST['eprvalor_ajuste_rpm']) >= 50 ? intval($_POST['eprvalor_ajuste_rpm']) : 'null';
            
            $usuoid = $_SESSION['usuario']['oid'];
            
            if($eproid != "" && $eproid > 0) {
                $sql = "UPDATE equipamento_projeto
                           SET eprnome                           = '$eprnome',
                               eprmotivo                         = '$eprmotivo',
                               eprdescricao_tecnica              = '$eprdescricao_tecnica',
                               eprprdoid                         = ".$eprprdoid.",
                               eprquadriband                     = '$eprquadriband',
                               eprcompativel_jamming             = '$eprcompativel_jamming',
                               eprteste_portal                   = '$eprteste_portal',
                               eprtipo                           = '$eprtipo',
                               eprprecisao_odometro_portal       = ".$eprprecisao_odometro_portal.",
                               eprmultiplicador_odometro_posicao = ".$eprmultiplicador_odometro_posicao.",
                               eprtolerancia_odometro            = ".$eprtolerancia_odometro.",
                               eprqtd_testes_posicao             = ".$eprqtd_testes_posicao.",
                               eprintervalo_testes_posicao       = ".$eprintervalo_testes_posicao.",
                               eprresumo_configuracoes           = '$eprresumo_configuracoes',
                               eprorigem_ultima_posicao          = '".$eprorigem_ultima_posicao."',
                               eprtempo_posicao_teste            = ".$eprtempo_posicao_teste.",
                               eprtempo_posicao_final            = ".$eprtempo_posicao_final.",
                               eprtempo_expiracao_bloqueio       = ".$eprtempo_expiracao_bloqueio.",
                               eprdt_alteracao                   = 'now()',
                               eprusuoid_alteracao               = $usuoid,
                               epregtoid                         = ".$epregtoid.",
                               eprvalor_ajuste_rpm               = ".$eprvalor_ajuste_rpm."
                         WHERE eproid = ".$eproid;
            } else {
                $sql = "INSERT INTO equipamento_projeto (
                                        eprnome,
                                        eprcadastro,
                                        eprusuoid,
                                        eprmotivo,
                                        eprdescricao_tecnica,
                                        eprprdoid, 
                                        eprquadriband, 
                                        eprcompativel_jamming,
                                        eprteste_portal, 
                                        eprtipo, 
                                        eprprecisao_odometro_portal, 
                                        eprmultiplicador_odometro_posicao, 
                                        eprtolerancia_odometro, 
                                        eprqtd_testes_posicao, 
                                        eprintervalo_testes_posicao, 
                                        eprresumo_configuracoes, 
                                        eprorigem_ultima_posicao,
                                        eprtempo_posicao_teste, 
                                        eprtempo_posicao_final,
                                        eprtempo_expiracao_bloqueio,
                                        epregtoid,
                                        eprvalor_ajuste_rpm
                                    )
                             VALUES (
                                        '$eprnome',
                                        'now()',
                                        $usuoid,
                                        '$eprmotivo',
                                        '$eprdescricao_tecnica',
                                        $eprprdoid, 
                                        '$eprquadriband', 
                                        '$eprcompativel_jamming',
                                        '$eprteste_portal', 
                                        '$eprtipo', 
                                        $eprprecisao_odometro_portal, 
                                        $eprmultiplicador_odometro_posicao, 
                                        $eprtolerancia_odometro, 
                                        $eprqtd_testes_posicao, 
                                        $eprintervalo_testes_posicao, 
                                        '$eprresumo_configuracoes',
                                        '$eprorigem_ultima_posicao',
                                        $eprtempo_posicao_teste, 
                                        $eprtempo_posicao_final,
                                        $eprtempo_expiracao_bloqueio,
                                        ".$epregtoid.",
                                        ".$eprvalor_ajuste_rpm."
                                    )";  
                         
            }

            $rs = pg_query($this->conn, $sql);
            
            return "ok";
            
        } catch(Exception $e) {
            return "erro";
        }
    }


    /**
    * Busca por todos grupos de tecnologia ativos
    * 
    * @return array
    *    
    */
    public function listarGrupoTecnologia() {

      $retorno = array();

      $sql = "
        SELECT 
          egtoid,
          egtgrupo
        FROM
          equipamento_grupo_tecnologia
        WHERE
          egtdt_exclusao IS NULL
        ORDER BY
          egtgrupo
        ";

        $rs = pg_query($this->conn,$sql);

        while($registro = pg_fetch_object($rs)) {
          $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
    * Cadastra um grupo de tecnologia
    * 
    * @param string $egtgrupo
    * @return boolean
    *    
    */
    public function incluirGrupoTecnologia($egtgrupo) {

      $retorno = false;

      $sql = "
           INSERT INTO
              equipamento_grupo_tecnologia
              (
                egtgrupo,
                egtusuoid_cadastro
              )
            VALUES 
              (
                '". utf8_decode($egtgrupo)  ."',
                ". intval($this->usuario)."
              )
            ";

        $rs = pg_query($this->conn,$sql);

        if(pg_affected_rows($rs) > 0) {
             $retorno = true;
        }

        return $retorno;
    }

    /**
    * Inativa um grupo de tecnologia
    * 
    * @param int $egtoid
    * @return boolean
    *    
    */
    public function excluirGrupoTecnologia($egtoid) {

      $retorno = false;

      $sql = "
           UPDATE
              equipamento_grupo_tecnologia
            SET
                egtdt_exclusao = NOW(),
                egtusuoid_exclusao =  ". intval($this->usuario)."
          WHERE
            egtoid = ".intval($egtoid)."
            ";

        $rs = pg_query($this->conn,$sql);

        if(pg_affected_rows($rs) > 0) {
             $retorno = true;
        }

        return $retorno;
    }

    /**
    * Altera os dados de um grupo de tecnologia
    * 
    * @param int $egtoid
    * @param string $egtgrupo
    * @return boolean
    *    
    */
    public function alterarGrupoTecnologia($egtoid,$egtgrupo) {

      $retorno = false;

      $sql = "
           UPDATE
              equipamento_grupo_tecnologia
            SET
                egtdt_alteracao = NOW(),
                egtusuoid_alteracao =  ". intval($this->usuario).",
                egtgrupo = '".$egtgrupo."'
          WHERE
            egtoid = ".intval($egtoid)."
            ";

        $rs = pg_query($this->conn,$sql);

        if(pg_affected_rows($rs) > 0) {
             $retorno = true;
        }

        return $retorno;
    }

    /**
    * Verifica a existência de um cadastro de grupo tecnologia
    * 
    * @param string $egtgrupo
    * @return boolean
    *
    */
    public function verificarGrupoTecnologia($egtgrupo) {
      
        $sql = "
          SELECT EXISTS(
              SELECT 1
              FROM
                equipamento_grupo_tecnologia
              WHERE
                egtgrupo = '".$egtgrupo."'
            ) AS existe
          ";

          $rs = pg_query($this->conn, $sql);
          $registro = pg_fetch_object($rs);

          $retorno = ($registro->existe == 't')  ? true : false;

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