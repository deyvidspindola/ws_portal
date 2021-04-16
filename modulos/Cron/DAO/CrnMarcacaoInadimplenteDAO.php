<?php

/** 
 * @author Paulo Sergio Bernardo Pinto
 */
 
class CrnMarcacaoInadimplenteDAO {
    
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function buscarParametroDiasAtraso(){
        
        $resultado = array();
		
	$sql = " select * from parametros_configuracoes_sistemas_itens
                    where  pcsioid = 'AUTOMACAO_CLIENTE_INADIMPLENTE_DIAS' limit 1";
		
        $rs = pg_query($this->conn, $sql);
        
        if(pg_num_rows($rs) > 0) {
            $parametros = pg_fetch_all($rs);
            foreach ($parametros as $parametro) {
                $resultado = $parametro['pcsidescricao'];
            }
        }
        return $resultado;
    }

    public function buscarClientesInadimplentes($diasAtraso){
        
        $resultado = array();
		
		/**
		 * Método que localiza dados de todos os clientes indimplentes
		 */
	$sql = " SELECT tit.titcobr_terceirizada,clioid, clinome, cliclicloid cd_classe,  Upper(clicldescricao) AS classe, 
                    Count(atrasados.titoid) AS parcelas, current_date - Min(atrasados.titdt_vencimento) AS atraso, 
                    SUM(atrasados.titvl_titulo) AS valor_total, cternome AS empresa_cobranca
                from clientes 
                inner join titulo tit ON titclioid = clioid 
                inner join forma_cobranca ON titformacobranca = forcoid 
                left join cliente_classe ON cliclicloid = clicloid 
                AND clicldt_exclusao IS NULL 
                left join cobranca_terceirizada ON titcteroid = cteroid 
                left join titulo atrasados on tit.titoid = atrasados.titoid 
                      AND atrasados.titnao_cobravel = 'f' 
                      AND (forccobranca IS TRUE OR forcnome = 'Título Avulso' or forcnome = 'Baixa como Perda') 
                      and atrasados.titdt_vencimento < current_date 
                      AND (atrasados.titformacobranca = 51 
                      and atrasados.titdt_credito is not null OR ( atrasados.titdt_credito is null )) 
                where tit.titclioid=clioid 
                --- AND  Trim(clinome) ILIKE '%MOVIDA PARTICIPACOES%' 
                and tit.titdt_cancelamento is null 
                AND tit.titnao_cobravel = 'f' 
              ---  and cliclicloid not in (12,17,38,29)
              ---  and clientes.clioid =  250104
              ---  and (tit.titmotioid != 6 or tit.titmotioid is null)
                and (tit.titmotioid is null)
                and (current_date - atrasados.titdt_vencimento) > ".$diasAtraso." 
                group by tit.titcobr_terceirizada, clioid, clinome, clicldescricao, cternome 
                order by 1"; 
		
        $rs = pg_query($this->conn, $sql);
        
        if(pg_num_rows($rs) > 0) {
            $resultado = pg_fetch_all($rs);
        }
        
        return $resultado;
        
    }
	
    public function buscarTitulosClientesInadimplentes($clioid, $diasAtraso ){
        
        $resultadoTitulo = array();
		
		/**
		 * Método que localiza dados de todos os clientes indimplentes
		 */
	$sql = " select (select cternome from cobranca_terceirizada where titcteroid=cteroid) as empresa_cobranca, 
                    case when titnfloid is not null then (select nflno_numero||' '||nflserie as nota from nota_fiscal where nfloid=titnfloid) else case when titno_cheque is not null then titno_cheque||'/CH' when titno_cartao is not null and titno_cartao<>'' then 'CARTÃO' when titnota_promissoria is not null then titnota_promissoria||'/NP' when titno_avulso IS NOT NULL THEN titno_avulso||'/AV' end end as nota, 
                    to_char(titdt_vencimento,'dd/mm/yyyy') as titdt_vencimento,
                    titvl_titulo,
                    (current_date - titdt_vencimento) as atraso,
                    ( SELECT motidescricao FROM motivo_inadimplente WHERE motioid = titmotioid AND motiexclusao IS NULL ) AS motidescricao, 
                    titnfloid , titoid,
                    clioid,clinome
                from clientes,titulo, forma_cobranca 
                where titclioid= clioid 
                AND titformacobranca = forcoid 
                and titdt_pagamento is null 
                and titdt_cancelamento is null 
                and clioid = ".$clioid." 
                and (current_date - titdt_vencimento) > ".$diasAtraso." 
                AND (titformacobranca = 51 and titdt_credito is not null OR ( titdt_credito is null )) 
                and titnao_cobravel is not true AND (forccobranca IS TRUE OR forcnome = 'Título Avulso' or forcnome = 'Baixa como Perda') 
                and titdt_vencimento::date < current_date 
                --- and (titmotioid != 6 or titmotioid is null)
                and (titmotioid is null)
                order by titdt_vencimento";
        $rs = pg_query($this->conn, $sql);
        
        if(pg_num_rows($rs) > 0) {
            $resultadoTitulo = pg_fetch_all($rs);
        }
        
        return $resultadoTitulo;
        
    }
    
    public function atualizarInadimplentes($titoid, $cd_usuario) {
        
        $sql = " UPDATE titulo SET titusuoid_alteracao=".$cd_usuario.",titmotioid = 6 
                     WHERE titoid in (". $titoid ." )";	
        
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
    public function adicionarHistoricoInadimplentes($titoid, $cd_usuario, $clioid,$diasAtraso) {
	$sql ="insert into titulo_acionamento (tiaacionamento,tiadt_acionamento,tiadt_agenda,tiatitoid,tiamotivo,tiausuoid,tiaclioid) "
            . "values('Marcacao automatica devido ao atraso superior a ".$diasAtraso." dias',current_timestamp(0),NULL,$titoid,'INADIMPLENTE',$cd_usuario,$clioid) ";

        return pg_affected_rows(pg_query($this->conn, $sql));
    }
}

?>


