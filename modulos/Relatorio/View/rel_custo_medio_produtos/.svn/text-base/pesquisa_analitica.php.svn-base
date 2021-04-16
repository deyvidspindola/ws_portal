<div class="resultado">

<div class="separador"></div>

<div class="bloco_titulo">Resultados da Pesquisa</div>

<div class="bloco_conteudo">

    <div class="listagem">
        <table>

            <thead>
                <tr>
                    <th>Código <br>Produto</th>
                    <th>Descrição</th>
                    <th>Representante</th>
                    <th>Data</th>
                    <th>Tipo de Movimentação</th>
                    <th>E ou S</th>
                    <th>Quantidade</th>
                    <th>Custo Médio Unit.</th>
                    <th>Total</th>
                    <th>NF</th>
                    <th>Fornecedor</th>
                    <th>Cliente</th>
                    <th>Contrato</th>
                    <th>Tipo Contrato</th>
                </tr>
            </thead>

            <?php if ( $this->countRelatorio > 0 ): ?>
            <tbody>	
                <?php foreach ( $this->arrayRelatorio as $relatorio ): ?>

                <?php $class = ( $class == "par" ) ? "" : "par" ?>
                <tr class="<?php echo $class; ?>">
                    <td class="direita"><?php echo $relatorio['produto_id']; ?></td>
                    <td><?php echo $relatorio['produto_descricao']; ?></td>
                    <td><?php echo $relatorio['representante_nome']; ?></td>
                    <td class="centro"><?php echo $relatorio['data']; ?></td>
                    <td><?php echo $relatorio['movimentacao_tipo']; ?></td>
                    <td class="centro"><?php echo $relatorio['emvtipo']; ?></td>
                    <td class="direita"><?php echo $relatorio['quantidade']; ?></td>
                    <td class="direita"><?php echo number_format( $relatorio['custo_medio_unitario'], 2, ',', '.' ); ?></td>
                    <td class="direita"><?php echo number_format( $relatorio['total'], 2, ',', '.' ); ?></td>
                    <td class="direita"><?php echo $relatorio['nota']; if( !empty($relatorio['serie']) ): echo "-".$relatorio['serie']; endif;  ?></td>
                    <td><?php echo $relatorio['fornecedor_nome']; ?></td>
                    <td><?php echo $relatorio['cliente_nome']; ?></td>
                    <td class="direita"><?php echo $relatorio['contrato_numero']; ?></td>
                    <td><?php echo $relatorio['contrato_tipo']; ?></td>                           
                </tr>
                <?php endforeach; ?>
            </tbody>
            <?php endif; ?>

        </table>
    </div>

</div>

<div class="bloco_acoes">
    <?php if ( $this->countRelatorio > 0 ): ?>
        <p class='negrito'>Total de <?php echo $this->countRelatorio; ?> registros</p>
    <?php else: ?>
        <p class='negrito'>Nenhum resultado encontrado.</p>
    <?php endif; ?>
</div>

<div class="bloco_rodape"></div>

</div>





    </form> 
    
    </div>
    
    <div class="modulo_rodape"></div>

</body>
    
</html>