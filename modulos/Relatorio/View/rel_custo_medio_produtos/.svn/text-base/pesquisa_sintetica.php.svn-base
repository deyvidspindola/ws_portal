<div class="resultado">
    
<div class="separador"></div>

<div class="bloco_titulo resultado">Resultados da Pesquisa</div>

<div class="bloco_conteudo">

    <div class="listagem">
        <table>

            <thead>
                <tr>
                    <th>Código <br>Produto</th>
                    <th>Descrição</th>                    
                    <th>Quantidade</th>
                    <th>Custo Médio Unit.</th>
                    <th>Total</th>
                </tr>
            </thead>

            <?php if ( $this->countRelatorio > 0 ): ?>
            <tbody>	
                <?php foreach ( $this->arrayRelatorio as $produto ): ?>

                <?php $class = ( $class == "par" ) ? "" : "par" ?>
                <tr class="<?php echo $class; ?>">
                    <td class="direita"><?php echo $produto['produto_id']; ?></td>
                    <td><?php echo $produto['produto_descricao']; ?></td>
                    <td class="direita"><?php echo $produto['quantidade']; ?></td>
                    <td class="direita"><?php echo number_format( $produto['custo_medio_unitario'], 2, ',', '.' ); ?></td>
                    <td class="direita"><?php echo number_format( $produto['total'], 2, ',', '.' ); ?></td>
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