<!-- FOMULARIO PARA CADASTRO DOS CRITERIOS DE PARTICIPACAO -->
<div id="boxCriteriosParticipacao" style="display: none;">
    <form id="formularioCriteriosParticipacao" action="<?php echo $this->url(array('controller' => 'edital', 'action' => 'salva-criterio-participacao')); ?>" method="post">
    <table class="tabela" id="tbCriterioAvaliacao" style="margin-bottom: 0px !important; padding-bottom: 0px !important;">
        <tbody>
            <tr>
                <th colspan="2" style="font-size: 14px">Critério de Participa&ccedil;&atilde;o</th>
            </tr>
            <tr>
                <td class="destacar bold w150 esquerdo">Descrição do crit&eacute;rio <span style="color: red;">*</span></td>
                <td>
                    <input type="text" name="dsCriterioParticipacao" size="80" class="input_simples">
                </td>
            </tr>
            <tr>
                <td class="destacar bold w150 esquerdo">Campo: <span style="color: red;">*</span></td>
                <td>
                    <select class="input_simples" id="selectRegra" name="regraCampo">
                        <option value="0">- Selecione -</option>
                        <option value="nascimento">Data de Nascimento</option>
                        <option value="cidade">Cidade</option>
                        <option value="sexo">Sexo</option>
                    </select>
                </td>
            </tr>
            <tr id="trRespostas" class="sumir">
                <td class="destacar bold w150 esquerdo">Opções de resposta <span style="color: red;">*</span></td>
                <td style="margin: 0px !important; padding: 0px !important;">
                    <input type="hidden" name="numOpcaoResposta" id="numOpcaoResposta" value="0">
                    <table id="tblOpcaoResposta" class="tabela" style="margin: 2px !important; width: 400px;">
                        <tbody><tr>
                                <td colspan="2" class="direita">Adicionar opção de resposta: <input type="button" class="btn_adicionar" onclick="JSAdicionaOpcaoResposta()"></td>
                            </tr>
                            <tr>
                                <td class="destacar bold centro">Texto da resposta</td>
                                <td class="destacar bold centro">Excluir</td>
                            </tr>
                            <tr id="trOpcaoResposta_0">
                                <td><input type="text" class="input_simples" size="45"></td>
                                <td class="centro"><input type="text" class="btn_excluir" onclick="JSRemoverOpcaoResposta(0);"></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
            <tr>
                <td class="destacar bold w150 esquerdo">Resposta Obrigatória <span style="color: red;">*</span></td>
                <td>
                    <input type="checkbox" name="respostaObrigatoria" value="1">
                </td>
            </tr>
        </tbody></table>

    <div style="width: 100%; margin: 0 auto; padding: 8px">
        <div id="divBtnIncluirCriterio" class="centro">
            <input type="button" id="salvarCriteriosParticipacao" class="btn_incluir" onclick="JSAdicionarCriterioAvaliacao()" style="vertical-align: bottom">
        </div>
        <div id="divBtnAlterarCriterio" class="sumir">
            <input type="button" class="btn_alterar" onclick="JSAlteraCriterioAvaliacao()" style="vertical-align: bottom">
            <input type="button" class="btn_cancelar" onclick="JSCancelaAlteracaoCriterioAvaliacao()" style="vertical-align: bottom">
        </div>
    </div>

    <table class="tabela" id="tblCriteriosAdicionada" style="margin-top: 0px !important; padding-top: 0px !important; border-top: 1px #fff solid !important; ">
        <tbody><tr>
                <td colspan="4" class="destacar centro bold">CRITÉRIOS ADICIONADOS</td>
            </tr>
            <tr>
                <td class="destacar bold" style="width: 40%">Descrição do critério</td>
                <td class="destacar bold " style="width: 15%">Regra para o campo</td>
                <td class="destacar bold centro" style="width: 15%">Ações</td>
            </tr>
            <?php if (empty($this->criterioparticipacao)) {
    ?>
            <tr id="trCriterio_zero">
                <td colspan="3" class="centro"><em>Nenhum critério adicionado</em></td>
            </tr>
            <?php
} else {
        ?>
            <?php foreach ($this->criterioParticipacao as $critP): ?>
            <tr id="trCriterio_zero">
                <td class="centro"><?php echo $critP['dsCriterioParticipacao']; ?></td>
                <td class="centro"><?php echo $critP['rgCampo']; ?></td>
                <td class="centro">
                    <input type="button" class="btn_editar"     onclick="editarCriterioParticipacao('<?php echo $critP['idCriterioParticipacao']; ?>');" />
                    <input type="button" class="btn_exclusao"   onclick="excluirCriterioParticipacao('<?php echo $critP['idCriterioParticipacao']; ?>');" />
                </td>
            </tr>
            <?php endforeach; ?>
            <?php
    }?>
        </tbody></table>

    <br clear="all">
    <div style="width: 100%; text-align: center;">
        <input id='' class="btn_salvar" type="button" value="" name="salvar" id="salvar">
        <input class="btn_limpar" type="reset" value="" name="limpar" id="limpar">
    </div>
    </form>
</div>
<!-- FIM FOMULARIO PARA CADASTRO DOS CRITERIOS DE PARTICIPACAO -->




<!-- FOMULARIO PARA CADASTRO DO QUESTIONARIO -->
<div id="boxQuestionario" style="display: none;">
    <table class="tabela">
        <tbody><tr onclick="abrirDinamico('#trCadGuia', $(this))" style="cursor:pointer">
                <th style="font-size: 14px; text-transform: none">
                    <img src="<?php echo $this->baseUrl(); ?>/public/img/navigation-right.png" id="imgDinamica" align="left">
                    CADASTRAR GUIA (Questionário)
                </th>
            </tr>
            <tr id="trCadGuia" class="sumir">
                <td align="center">
                    <div id="resultGuiaAdicionada"></div>
                    <form id="formularioGuia" name="frmCadGuia" action="<?php echo $this->url(array('controller' => 'guia', 'action' => 'cadastrar')); ?>" method="post">
                        <input type="hidden" name="guia" id="guia" />
                        <input type="hidden" name="categoria" id="categoria" />
                        <table class="tabela" id="tbGuia" style="margin-bottom: 0px !important; padding-bottom: 0px !important;">
                            <tbody>
                                <tr>
                                    <td class="destacar bold w150 esquerdo">Nome da Guia <span style="color: red;">*</span></td>
                                    <td>
                                        <input maxlength="200" type="text" name="nomeGuia" id="nomeGuia" class="input_simples w400"><br>
                                        <label id="msgErrorNomeGuia" class="error"></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="destacar bold w150 esquerdo">Texto de ajuda da Guia (opcional)</td>
                                    <td><textarea style="width: 99%; height: 80px" name="txtGuia" id="txtGuia" class="input_simples w400"></textarea></td>
                                </tr>
                            </tbody>
                        </table>

                        <div style="width: 100%; margin: 0 auto; padding: 8px">
                            <div id="divBtnIncluirGuia">
                                <input type="button" class="btn_incluir" onclick="GuiaCadastrar()" style="vertical-align: bottom">
                                <input type="button" class="btn_editar sumir" onclick="GuiaAtualizar()" style="vertical-align: bottom">
                                <input type="reset" value="" class="btn_limpar" onclick="GuiaFormResetar()" style="vertical-align: bottom">
                            </div>
                            <div id="divBtnAlterarGuia" class="sumir">
                                <input id='salvarFormularioGuia' type="button" class="btn_alterar" onclick="JSAlteraGuia()" style="vertical-align: bottom">
                                <input type="button" class="btn_cancelar" onclick="JSCancelaAlteracaoGuia()" style="vertical-align: bottom">
                            </div>
                        </div>
                    </form>

                    <form id="frmListaGuia" name="frmListaGuia" action="&lt;?php echo $this-&gt;url(array('controller' =&gt; 'edital', 'action' =&gt; 'lista-guia')); ?&gt;" method="post">
                        <table class="tabela" id="tblGuiaAdicionada" style="margin-top: 0px !important; padding-top: 0px !important; border-top: 1px #fff solid !important; ">
                            <thead><tr>
                                    <td colspan="3" class="destacar centro bold">GUIAS ADICIONADAS</td>
                                </tr>
                                <tr>
                                    <td class="destacar bold" style="width: 35%">Nome da Guia</td>
                                    <td class="destacar bold " style="width: 55%">Texto de ajuda da Guia</td>
                                    <td class="destacar bold centro" style="width: 10%">Ações</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (empty($this->guias)) :
                                        echo '<tr id="trGuia_zero"><td colspan="3" class="centro"><em>Nenhuma guia adicionada</em></td></tr>';
                                    else :
                                        foreach ($this->guias as $guia) :
                                            echo "<tr id='guia_{$guia['idGuia']}'>";
                                            echo "<td>{$guia['nmGuia']}</td><td>{$guia['txAuxilio']}</td>";
                                            echo '<td nowrap="true">';
                                            echo "<input type='button' class='btn_editar' data-guia='{$guia['idGuia']}' />";
                                            echo "<input type='button' class='btn_exclusao' data-guia='{$guia['idGuia']}' />";
                                            echo '</td>';
                                            echo '</tr>';
                                        endforeach;
                                    endif;
                                ?>
                            </tbody>
                        </table>
                    </form>
                </td>
            </tr>
        </tbody></table>

    <br>

    <table class="tabela">
        <tbody><tr onclick="abrirDinamico('#trCadQuestao', $(this))" style="cursor:pointer">
                <th style="font-size: 14px; text-transform: none">
                    <img src="<?php echo $this->baseUrl(); ?>/public/img/navigation-right.png" id="imgDinamica" align="left">
                    CADASTRAR QUESTÕES (Questionário)
                </th>
            </tr>
            <tr id="trCadQuestao" class="sumir">
                <td align="center">

                    <form id="frmCadQuestao" action="<?php echo $this->url(array('controller' => 'questao', 'action' => 'cadastrar')); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="questao" id="questao" />
                        <table class="tabela" id="tbCriterioAvaliacao" style="margin-bottom: 0px !important; padding-bottom: 0px !important;">
                            <tbody><tr>
                                    <td class="destacar bold w150 esquerdo">Guia <span style="color: red;">*</span></td>
                                    <td>
                                        <select name="guia" id="guiaQuestao" class="input_simples">
                                            <option value="0">- Selecione -</option>
                                            <?php
                                                foreach ($this->guias as $guia) :
                                                    echo "<option value='{$guia['idGuia']}'>{$guia['nmGuia']}</option>";
                                                endforeach;
                                            ?>
                                        </select>
                                        <label id="msgErrorGuiaQuestao" class="error"></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="destacar bold w150 esquerdo">Texto da Questão <span style="color: red;">*</span></td>
                                    <td>
                                        <input maxlength="200" type="text" name="textoQuestao" id="textoQuestao" class="input_simples w400"><br>
                                        <label id="msgErrorTextoQuestao" class="error"></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="destacar bold w150 esquerdo">Texto de ajuda da questão (opcional)</td>
                                    <td>
                                        <textarea style="width: 99%; height: 80px" name="textoAjudaQuestao" id="textoAjudaQuestao" class="input_simples w400"></textarea>
                                        <label id="msgErrorTextoAjudaQuestao" class="error"></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="destacar bold w150 esquerdo">Tipo da resposta <span style="color: red;">*</span></td>
                                    <td>
                                        <select name="tipoResposta" id="slcTipoRespostaQuestionario" class="input_simples" onchange="JSTipoRespostaQuestionario(this.value)">
                                            <option value="0">- Selecione -</option>
                                            <?php
                                                foreach ($this->tiposRespostas as $tipoResposta) :
                                                    echo "<option value='{$tipoResposta['idTpResposta']}'>{$tipoResposta['dsTpResposta']}</option>";
                                                endforeach;
                                            ?>
                                        </select>
                                        <label id="msgErrorNomeCriterio" class="error"></label>
                                    </td>
                                </tr>
                                <tr id="trRespostasQuestionario" style="display: none;">
                                    <td class="destacar bold w150 esquerdo">Opções de resposta <span style="color: red;">*</span></td>
                                    <td style="margin: 0px !important; padding: 0px !important;">
                                        <table id="tblOpcaoRespostaQuestionario" class="tabela" style="margin: 2px !important;">
                                            <thead>
                                                <tr>
                                                    <td colspan="3" class="direita">Adicionar opção de resposta: <input type="button" class="btn_adicionar" onclick="JSAdicionaOpcaoRespostaQuestionario()"></td>
                                                </tr>
                                                <tr>
                                                    <td class="destacar bold centro">&nbsp;</td>
                                                    <td class="destacar bold centro">Texto da resposta</td>
                                                    <td class="destacar bold centro">Excluir</td>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody></table>

                        <div style="width: 100%; margin: 0 auto; padding: 8px">
                            <div id="divBtnIncluirCriterio">
                                <input type="button" class="btn_incluir" onclick="QuestaoCadastrar()" style="vertical-align: bottom">
                                <input type="button" class="btn_editar sumir" onclick="QuestaoAtualizar()" style="vertical-align: bottom">
                                <input type="reset" value="" class="btn_limpar" onclick="QuestaoFormResetar()" style="vertical-align: bottom">
                            </div>
                            <div id="divBtnAlterarCriterio" class="sumir">
                                <input type="button" class="btn_alterar" onclick="JSAlteraCriterioAvaliacao()" style="vertical-align: bottom">
                                <input type="button" class="btn_cancelar" onclick="JSCancelaAlteracaoCriterioAvaliacao()" style="vertical-align: bottom">
                            </div>
                        </div>


                        <div id="tabs" style="background: #FFF; width: 98%; margin-top: 10px; margin-bottom: 10px; padding: 0px" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
                            <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
                                <?php
                                    $selected = 'ui-tabs-selected ui-state-active';
                                    foreach ($this->guias as $guia) :
                                        echo "<li style='width: 15%;' class='ui-state-default ui-corner-top {$selected}'><a href='#Guia_{$guia['idGuia']}'>{$guia['nmGuia']}</a></li>";
                                        $selected = null;
                                    endforeach;
                                ?>
                            </ul>
                            <!-- ANALISE INICIAL -->
                            <?php
                                $hideTab = null;
                                $selected = 'ui-tabs-selected ui-state-active';
                                foreach ($this->guias as $guia) :
                            ?>
                                <div id="Guia_<?php echo $guia['idGuia']; ?>" align="left" class="ui-tabs-panel ui-widget-content ui-corner-bottom <?php echo $hideTab; ?>">
                                <table class="tabela" id="tblQuestaoAdicionada" style="margin-top: 0px !important; padding-top: 0px !important; border-top: 1px #fff solid !important; ">
                                    <thead>
                                        <tr nodrag="true">
                                            <td colspan="4" class="destacar centro bold">QUESTÕS ADICIONADAS - <?php echo $guia['nmGuia']; ?></td>
                                        </tr>
                                        <tr nodrag="true">
                                            <td class="destacar bold" style="width: 35%">Texto da Questão</td>
                                            <td class="destacar bold " style="width: 35%">Texto de ajuda da Questão</td>
                                            <td class="destacar bold " style="width: 15%">Tipo Resposta</td>
                                            <td class="destacar bold centro" style="width: 15%">Ações</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if (empty($this->questoes)) :
                                                echo '<tr id="trQuestao_zero" style="cursor: move;">';
                                                    echo '<td colspan="4" class="centro"><em>Nenhuma questão adicionada</em></td>';
                                                echo '</tr>';
                                            endif;
                                            foreach ($this->questoes as $questao) :
                                                if ($questao['idGuia'] != $guia['idGuia']) :
                                                    continue;
                                                endif;
                                                echo "<tr id='questao_{$questao['idQuestao']}'>";
                                                echo "<td><em>{$questao['dsQuestao']}</td>";
                                                echo "<td>&nbsp;</td><td>{$questao['dsTpResposta']}</td>";
                                                echo '<td nowrap="true">';
                                                echo "<input type='button' class='btn_editar' data-questao='{$questao['idQuestao']}' />";
                                                echo "<input type='button' class='btn_exclusao' data-questao='{$questao['idQuestao']}' />";
                                                echo '</td>';
                                                echo '</tr>';
                                            endforeach;
                                        ?>
                                    </tbody>
                                </table>
                                </div>
                            <?php
                                    $hideTab = 'ui-tabs-hide';
                                endforeach;
                            ?>
                        </div>

                    </form>
                </td>
            </tr>
        </tbody></table>

    <br clear="all">
    <div style="width: 100%; text-align: center;">
        <input class="btn_salvar" type="button" value="" name="salvar" id="salvar">
        <input class="btn_limpar" type="reset" value="" name="limpar" id="limpar">
    </div>
</div>
<!-- FIM FOMULARIO PARA CADASTRO DO QUESTIONARIO -->