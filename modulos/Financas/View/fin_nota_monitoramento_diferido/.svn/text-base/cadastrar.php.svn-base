<div class="bloco_titulo">Dados para o Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <table width="100%">
            <tr>
                <td>                    
                    <div class="campo medio">					
                        <label for="nota">Nota:</label>
                        <input type="text" id="nota" name="nota" value="" class="campo" />
                    </div>
                    
                    <div class="campo menor">					
                        <label for="serie">S&eacute;rie:</label>
                        <select name="serie" id="serie">
                            <option value="">Escolha</option>
                            <?php
                                if(!empty($serie)){
                                    foreach($serie as $row){
                                         echo "<option value='".$row['nfsserie']."'>".$row['nfsserie']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
        </table>		
    </div>
</div>		

<div class="bloco_acoes">
    <button type="button" id="confirmar">Confirmar</button>
    <button type="button" id="voltar">Voltar</button>
</div>