<?php if ( $this->countRelatorio > 0 ): ?>
<div class="resultado">

    <div class="separador"></div>

    <div class="bloco_titulo">Resultado da Pesquisa</div>

    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>Cliente</th>
                        <th>Tipo Contrato</th>
                        <th>Tipo Proposta</th>
                        <th>Aprovação</th>
                        <th>Descrição</th>
                        <th>Usuário</th>                                    
                    </tr>
                </thead>
                <tbody>	
                    <?php foreach ( $this->arrayRelatorio as $relatorio ): ?>
                    <?php $class = ( $class == "par" ) ? "" : "par" ?>
                    <tr class="<?php echo($class);?>">
                        <td class="direita"><?php echo $relatorio['contrato']; ?></td>
                        <td><?php echo $relatorio['cliente']; ?></td>
                        <td><?php echo $relatorio['tipo_contrato']; ?></td>
                        <td><?php echo $this->tipos_proposta[$relatorio['tipo_proposta']]; ?></td>
                        <td class="centro"><?php echo $relatorio['data_aprovacao']; ?></td>
                        <td><?php echo $relatorio['status_proposta']; ?></td>                       
                        <td><?php echo $relatorio['usuario']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bloco_acoes">
        <p class='negrito'>
            Total de <?php echo ($this->countRelatorio);?>  registros
        </p>
    </div>
</div>
<?php endif; ?>