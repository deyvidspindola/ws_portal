<?php
/**
* Importação de itens do documento via arquivo CVS DAO
*
* @author Rafel Aguiar <rafael.aguiar@gateware.com.br>
* @package Finanças
* @since 10/04/2016
*/
class FinImportarItensNFDAO
{
  var $conn;

  /**
   * Construtor
   * @param resource $conn	Link de conexão com o banco
   */
  function __construct($conn)
  {
    $this->conn = $conn;
  }

  /**
  * Tipo de documento* :valida se existe o tipo de documento
  *
  * @param int $documentoGrupo codigo do tipo de documento
  * @return boolean
  */
  public function validarTipoDocumento($documentoGrupo)
  {
    # 1 - NF Estaduais ; 2 - NF Municipais ; 3 - Sem NF
    if (!empty($documentoGrupo)){
      if (in_array($documentoGrupo, array(1,2,3))){
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }

  /**
  * Código Produto *: validar se o código do produto existe na base e é válido;
  *
  * @param int $codigoProduto codigo do produto
  * @return false, array
  */
  public function validarCodigoProduto($codigoProduto)
  {
    if (isset($codigoProduto)){
      # montar sql de consulta sql
      $sql = "SELECT prdoid, prdproduto, prdplcoid, prdptioid, prdtp_cadastro
              FROM produto
              WHERE prddt_exclusao IS NULL
              AND (   (   (prdtp_cadastro = 'P')
                          AND prdptioid is not null
                          AND (   (prdptioid = 1 AND prdimotoid is not null) OR (prdptioid != 1)  )   )
                      OR (prdtp_cadastro = 'S') )
              AND prdoid = $codigoProduto ";

      # executa query
      $rs = pg_query($this->conn, $sql);

      # verifica se retornou algum valor
      if(pg_num_rows($rs) > 0){
        return pg_fetch_assoc($rs);
      }else{
        return false;
      }
    }else{
      return false;
    }
  }

  /**
  * NCM: Se informado, validar se existe;
  *
  * @param int $codigoMCN codigo do MSN
  * @return false, array
  */
  public function validarNCM($codigoMCN)
  {
    # montar sql de consulta sql
    if(!empty($codigoMCN)){
    	 $sql = "SELECT ncms_codigo AS codigo, ncmsdescricao AS descricao
				FROM ncms_classificacao_fiscal
				WHERE ncms_codigo = '".$codigoMCN."'";
      # executa query
      $rs = pg_query($this->conn,$sql);
			# verifica se retornou algum valor
      if(pg_num_rows($rs)>0){
        return pg_fetch_assoc($rs);
      }else{
        return false;
      }
    }else{
      return true;
    }
  }


  /**
  * Conta contábil *: validar se existe;
  *
  * @param int $contaContabil numero da conta contabil
  * @return false, array
  */
  public function validarContaContabil($contaContabil, $codEmpresa='')
  {
    if($contaContabil){

      $codEmpresa = $codEmpresa > 0 ? " AND plctecoid = ".intval($codEmpresa) : '';

      # montar sql de consulta sql
      $sql = "SELECT plcoid, plcdescricao, plcconta, plcmovimentacao
                FROM plano_contabil
                WHERE plcoid = $contaContabil
                and plcexclusao IS NULL
                and (plcmovimentacao != 'L')"
                .$codEmpresa;

      # executa query
      $rs = pg_query($this->conn, $sql);

      # verifica se retornou algum valor
      if(pg_num_rows($rs) > 0){
        return pg_fetch_assoc($rs);
      }else{
        return false;
      }
    }else{
      return false;
    }
  }

  /**
  * Centro de Custo *: validar se existe;
  *
  * @param int $centroCusto numero do centro de custo
  * @return boolean
  */
  public function validarCentroCusto($centroCusto, $codEmpresa='')
  {
    if($centroCusto){

      $codEmpresa = $codEmpresa > 0 ? " AND cnttecoid = ".intval($codEmpresa) : '';

      # montar sql de consulta sql
      $sql = "SELECT cntoid, cntconta, cntno_centro
              FROM centro_custo
              WHERE cntdt_exclusao IS NULL
              and cntconta not ilike '%INATIVO%'
              AND cntoid = $centroCusto".
              $codEmpresa;
      # executa query
      $rs = pg_query($this->conn, $sql);

      # verifica se retornou algum valor
      if(pg_num_rows($rs) > 0){
        return pg_fetch_assoc($rs);
      }else{
        return false;
      }
    }
  }


  function validarSTICMS($stIcms)
  {
      if (in_array($stIcms, array(0,10,20,30,40,41,50,51,60,70,90,100,110,120,130,140,141,150,151,160,170,190,200,210,220,230,240,241,250,251,260,270,290) ) )
      {
          return true;
      }else{
          return false;
      }
  }

  function validarCSTIPI($cstIPI)
  {
      if (in_array($cstIPI, array(0,1,2,3,4,5,49,50,51,52,53,54,55,99) ) )
      {
          return true;
      }else{
          return false;
      }
  }

  function validarCFOP($cfop,$estabelecimento, $fornecedoroid)
  {

      // Buscar o endereco vinculado na tabela estabelecimento.
      if ($estabelecimento>0) {
          $sql_end =" SELECT enduf
                      FROM endereco
                      WHERE endoid = (SELECT etbendoid FROM estabelecimento WHERE etboid = $estabelecimento)";
          $res_end = pg_query($this->conn,$sql_end);
          $mEtb=pg_fetch_array($res_end);
      } else {
          //msgerro
          return "Selecione o estabelecimento";
      }


      if (empty($mEtb['enduf'])) {
          //msgerro
          return  "Não foi preenchido o Estado no cadastro do Estabelecimento.";
      }

      if ($fornecedoroid>0) {

          $cSql = "select enduf
                     from fornecedores
                      left join endereco on forendoid = endoid
                    where foroid=$fornecedoroid";
          $cSql_Forn = pg_query($this->conn, $cSql);
          $mForn=pg_fetch_array($cSql_Forn);
      } else {
          //msgerro
          return "Selecione o Fornecedor";
      }

      if (empty($mForn[enduf])) {
          //msgerro
          return "Não foi preenchido o Estado no cadastro do Fornecedor";
      }


      if (!empty($mEtb['enduf']) and !empty($mForn[enduf])) {
          if (substr($cfop,0,1)==1 and $mEtb['enduf']!=$mForn[enduf])
          {
              //msgerro
              return "CFOP que foi selecionada é para operações estaduais, ou seja, dentro do próprio estado e o fornecedor não é do mesmo estado que a empresa selecionada";
          }

          if (substr($cfop,0,1)==2 and $mEtb['enduf']==$mForn[enduf])
          {
              //msgerro
              return "CFOP que foi selecionada é para operações interestaduais, ou seja, fora do estado e o fornecedor é do mesmo estado que a empresa selecionada";
          }

      }

      $cfop=str_replace(array("."," "),"",$cfop);

      if ((strrchr($cfop, "0") == 0) && (strrchr($cfop, "0") !== false)) {
          $cfop = substr($cfop, 0, -1);
          $cfop_cortado = 1;
      } else {
          $cfop_cortado = 0;
      }

      $sql = " select nopcodigo,
                      nopdescricao
                 from natureza_operacao
                where nopexclusao is null
                  and replace(nopcodigo::text,'.','') = '$cfop' ";
      $rs = pg_query($this->conn, $sql);

      if ($cfop_cortado == 1) {
          $cfop = $cfop."0";
      }

      // Se a CFOP nao existir, mostro a mensagem de erro
      if(pg_num_rows($rs) == 0){
          //msgerro
           return "CFOP não cadastrada. Verifique!";
      }
      return false;
  }
}
?>
