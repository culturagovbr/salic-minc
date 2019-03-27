<template>
    <v-dialog
        v-model="statusModal"
        fullscreen
        hide-overlay
        transition="dialog-bottom-transition">
        <v-card v-if="Object.keys(dadosVisualizacao).length > 0">
            <v-toolbar
                dark
                color="primary">
                <v-btn
                    icon
                    dark
                    @click="modalClose()">
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>Dados Fiscalizacao Completos</v-toolbar-title>
                <v-spacer/>
            </v-toolbar>
            <v-container style="max-width: 100%;">
                <v-card>
                    <v-subheader class="primary justify-center">
                        <div>
                            <h3 class="display-1 white--text font-weight-light">Locais</h3>
                        </div>
                    </v-subheader>
                    <v-container>
                        <v-layout>
                            <v-flex
                                xs4
                                offset-xs2>
                                <p><b>REGIAO</b></p>
                            </v-flex>
                            <v-flex
                                xs4
                                offset-xs2
                                class="pl-4">
                                <p><b>UF</b></p>
                            </v-flex>
                            <v-flex
                                xs4
                                offset-xs2>
                                <p><b>CIDADE</b></p>
                            </v-flex>
                        </v-layout>
                        <v-layout
                            v-for="(dado, index) in dadosVisualizacao.locaisFiscalizacao"
                            :key="index">
                            <v-flex
                                xs4
                                offset-xs2>
                                <p>{{ dado.regiao }}</p>
                            </v-flex>
                            <v-flex
                                xs4
                                offset-xs2>
                                <p>{{ dado.uf }}</p>
                            </v-flex>
                            <v-flex
                                xs4
                                offset-xs2>
                                <p>{{ dado.cidade }}</p>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card>
                <v-card>
                    <v-subheader class="primary justify-center">
                        <div>
                            <h3
                                class="display-1 white--text font-weight-light"
                            >
                                Oficializar Fiscalização
                            </h3>
                        </div>
                    </v-subheader>
                </v-card>
                <v-card>
                    <v-subheader class="justify-center">
                        <div>
                            <h4
                                class="display-1 grey--text text--darken-4 font-weight-light"
                            >
                                Datas / Demandante
                            </h4>
                        </div>
                    </v-subheader>
                    <v-container
                        v-for="(dado, index) in dadosVisualizacao.oficializarFiscalizacao"
                        :key="index">
                        <v-layout>
                            <v-flex
                                xs6
                                offset-xs2>
                                <p><b>Dt. Inicio</b></p>
                                {{ dado.dtInicio | formatarData }} <br>
                            </v-flex>
                            <v-flex
                                xs6
                                offset-xs2>
                                <p><b>Dt. Fim</b></p>
                                {{ dado.dtFim | formatarData }} <br>
                            </v-flex>
                        </v-layout>
                        <v-layout>
                            <v-flex
                                xs6
                                offset-xs2>
                                <br>
                                <p><b>Demandante da Fiscalização</b></p>
                                <p
                                    v-if="dado.tpDemandante == 0"
                                    class="justify-center">SEFIC</p>
                                <p v-else-if="dado.tpDemandante == 1">SAV</p>
                            </v-flex>
                            <v-flex
                                xs6
                                offset-xs2>
                                <br>
                                <p><b>Data de Resposta</b></p>
                                <p> {{ dado.dtResposta | formatarData }} </p>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card>
                <v-card>
                    <v-subheader class="justify-center">
                        <div>
                            <h4
                                class="display-1 grey--text text--darken-4 font-weight-light"
                            >
                                Identificação do Técnico
                            </h4>
                        </div>
                    </v-subheader>
                    <v-container
                        v-for="(dado, index) in dadosVisualizacao.oficializarFiscalizacao"
                        :key="index">
                        <v-layout>
                            <v-flex
                                xs6
                                offset-xs2>
                                <p><b>CPF</b></p>
                                {{ dado.cpfTecnico }} <br>
                            </v-flex>
                            <v-flex
                                xs6
                                offset-xs2>
                                <p><b>Técnico</b></p>
                                {{ dado.nmTecnico }} <br>
                            </v-flex>
                        </v-layout>
                        <v-layout>
                            <v-flex
                                xs10
                                offset-xs2>
                                <br>
                                <p><b>Dados para Fiscalização</b></p>
                                {{ dado.dsFiscalizacaoProjeto }}
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card>
                <v-card>
                    <v-subheader class="primary justify-center">
                        <div>
                            <h3
                                class="display-1 white--text font-weight-light"
                            >
                                Fiscalização Concluída para Parecer
                            </h3>
                        </div>
                    </v-subheader>
                </v-card>
                <div v-if="parecer">
                    <v-card>
                        <v-card-title class="justify-center">
                            <h2 class="display-1 font-weight-light">Resumo da Execução</h2>
                        </v-card-title>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Ações Programadas</b></p>
                                    <div v-html="parecer.resumoExecucao.dsAcoesProgramadas"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Ações Executadas</b></p>
                                    <div v-html="parecer.resumoExecucao.dsAcoesExecutadas"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Benefícios Alcançados</b></p>
                                    <div v-html="parecer.resumoExecucao.dsBeneficioAlcancado"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Dificuldades Encontradas</b></p>
                                    <div v-html="parecer.resumoExecucao.dsDificuldadeEncontrada"/>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card v-if="parecer.stDtDeCorte == 1">
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Situação do Convênio na Realização da Fiscalização
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Situação SIAFI</b></p>
                                    {{ parecer.stConvenioFiscalizacao.stSiafi }}
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Prestação de Contas Apresentada?</b></p>
                                    <div
                                        v-html="parecer.stConvenioFiscalizacao.stPrestacaoContas"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Foram cumpridas as normas estabelecidas?</b></p>
                                    <div v-html="parecer.stConvenioFiscalizacao.stCumpridasNormas"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Foi cumprido o prazo para
                                            entrega da prestação de contas?
                                        </b>
                                    </p>
                                    <div v-html="parecer.stConvenioFiscalizacao.stCumpridoPrazo"/>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Utilização de Recursos
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Foi apurado por unidade fiscalizadora ou
                                            auditora a aplicação irregular de
                                            recursos?
                                        </b>
                                    </p>
                                    <div
                                        v-html="parecer.utilizacaoRecursos.stApuracaoUFiscalizacao"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Comprovou a correta utilização
                                            dos recursos da contrapartida?
                                        </b>
                                    </p>
                                    <div
                                        v-html="
                                            parecer.utilizacaoRecursos.
                                                stComprovacaoUtilizacaoRecurso
                                        "
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div v-if="parecer.stDtDeCorte == 0"><br>
                                        <p>
                                            <b>
                                                Há compatibilidade entre os recursos
                                                transferidos e a evolução do
                                                projeto?
                                            </b>
                                        </p>
                                    </div>
                                    <div v-else-if="parecer.stDtDeCorte == 1"><br>
                                        <p>
                                            <b>
                                                Há compatibilidade entre o desembolso e a evolução?
                                            </b>
                                        </p>
                                    </div>
                                    <div
                                        v-html="
                                            parecer.utilizacaoRecursos.
                                                stCompatibilidadeDesembolsoEvo
                                        "
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Ocorreu despesas com multas, juros,
                                            taxas bancárias ou correção
                                            monetária?
                                        </b>
                                    </p>
                                    <div v-html="parecer.utilizacaoRecursos.stOcorreuDespesas"/>
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Ocorreu pagamento de servidor público?</b></p>
                                    <div
                                        v-html="
                                            parecer.utilizacaoRecursos.
                                                stPagamentoServidorPublico
                                        "
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Ocorreu despesa com taxa de administração?</b></p>
                                    <div
                                        v-html="
                                            parecer.utilizacaoRecursos.
                                                stDespesaAdministracao
                                        "
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Há transferência de recurso para
                                            clubes/associações ou outras entidades
                                            congêneres?
                                        </b>
                                    </p>
                                    <div
                                        v-html="parecer.utilizacaoRecursos.stTransferenciaRecurso"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Há despesas com publicidade,
                                            salvo as de caráter educativo,
                                            informativo ou de orientação social?
                                        </b>
                                    </p>
                                    <div
                                        v-html="parecer.utilizacaoRecursos.stDespesasPublicidade"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Ocorreu aditamento prevendo alteração de objeto?</b></p>
                                    <div v-html="parecer.utilizacaoRecursos.stOcorreuAditamento"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div v-if="parecer.stDtDeCorte == 0"><br>
                                        <p>
                                            <b>
                                                Os recursos de contrapartida
                                                foram depositados na conta do projeto?
                                            </b>
                                        </p>
                                    </div>
                                    <div v-else-if="parecer.stDtDeCorte == 1"><br>
                                        <p>
                                            <b>
                                                Não foram aplicados os recursos de contrapartida?
                                            </b>
                                        </p>
                                    </div>
                                    <div v-html="parecer.utilizacaoRecursos.stAplicadosRecursos"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Ocorreu aplicação de recursos
                                            em outra finalidade que não a do objeto pactuado?
                                        </b>
                                    </p>
                                    <div
                                        v-html="
                                            parecer.utilizacaoRecursos.
                                                stAplicacaoRecursosFinalidade
                                        "
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 0">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Os recursos captados estão
                                            sendo aplicados em conformidade com a
                                            legislação vigente?
                                        </b>
                                    </p>
                                    <div v-html="parecer.utilizacaoRecursos.stRecursosCaptados"/>
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Ocorreu saldo após o encerramento do projeto?</b></p>
                                    <div
                                        v-html="parecer.utilizacaoRecursos.stSaldoAposEncerramento"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>O saldo verificado foi recolhido ao FNC?</b></p>
                                    <div
                                        v-html="parecer.utilizacaoRecursos.stSaldoVerificacaoFNC"
                                    />
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Comprovantes
                                    Fiscais de Despesa
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div v-if="parecer.stDtDeCorte == 0"><br>
                                        <p>
                                            <b>
                                                O proponente/convenente tem mantido
                                                a documentação relativa ao projeto
                                                em arquivo próprio?
                                            </b>
                                        </p>
                                    </div>
                                    <div v-else-if="parecer.stDtDeCorte == 1"><br>
                                    <p><b>O processo está bem documentado?</b></p></div>
                                    <div
                                        v-html="parecer.comprovantesDespesa.stProcessoDocumentado"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>A documentação está completa e arquivada?</b></p>
                                    <div
                                        v-html="parecer.comprovantesDespesa.stDocumentacaoCompleta"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Guardam conformidade entre o executado e o aprovado?
                                        </b>
                                    </p>
                                    <div
                                        v-html="parecer.comprovantesDespesa.stConformidadeExecucao"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div v-if="parecer.stDtDeCorte == 0"><br>
                                        <p>
                                            <b>
                                                Identificam o projeto com o
                                                número do Pronac/Convênio?
                                            </b>
                                        </p>
                                    </div>
                                    <div v-if="parecer.stDtDeCorte == 1"><br>
                                        <p>
                                            <b>
                                                Identificam o nome do projeto
                                                e o número do convênio?
                                            </b>
                                        </p>
                                    </div>
                                    <div v-html="parecer.comprovantesDespesa.stIdentificaProjeto"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Existem despesas anteriores ao prazo de vigência?</b></p>
                                    <div v-html="parecer.comprovantesDespesa.stDespesaAnterior"/>
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Existem despesas posteriores ao prazo de vigência?
                                        </b>
                                    </p>
                                    <div v-html="parecer.comprovantesDespesa.stDespesaPosterior"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            As despesas coincidem com as
                                            informadas na relação de pagamentos?
                                        </b>
                                    </p>
                                    <div v-html="parecer.comprovantesDespesa.stDespesaCoincidem"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            As despesas estão devidamente
                                            relacionadas no extrato bancário?
                                        </b>
                                    </p>
                                    <div
                                        v-html="parecer.comprovantesDespesa.stDespesaRelacionada"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            Os comprovantes fiscais estão
                                            com o atesto do recebimento?
                                        </b>
                                    </p>
                                    <div v-html="parecer.comprovantesDespesa.stComprovanteFiscal"/>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Divulgação
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Ciência ao Poder legislativo?</b></p>
                                    <div v-html="parecer.divulgacao.stCienciaLegislativo"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Contemplou as exigências legais?</b></p>
                                    <div v-html="parecer.divulgacao.stExigenciaLegal"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Há material informativo do Projeto?</b></p>
                                    <div v-html="parecer.divulgacao.stMaterialInformativo"/>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Execução
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Alcançou a finalidade esperada?</b></p>
                                    <div v-html="parecer.execucao.stFinalidadeEsperada"/>
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 1">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>
                                            As metas/etapas do Plano de Trabalho
                                            foram executadas integralmente?
                                        </b>
                                    </p>
                                    <div v-html="parecer.execucao.stPlanoTrabalho"/>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div v-if="parecer.stDtDeCorte == 0"><br>
                                        <p>
                                            <b>
                                                O projeto está sendo executado
                                                de acordo com o aprovado?
                                            </b>
                                        </p>
                                    </div>
                                    <div v-if="parecer.stDtDeCorte == 1"><br>
                                    <p><b>A execução respeitou o aprovado?</b></p></div>
                                    <div v-html="parecer.execucao.stExecucaoAprovado"/>
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="parecer.stDtDeCorte == 0">
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <br>
                                    <p><b>Observações</b></p>
                                    <div v-html="parecer.execucao.dsObservacao"/>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Empregos gerados
                                    em decorrência do projeto
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs4
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>Diretos: </b>
                                        {{ parecer.empregosGeradosProjeto.qtEmpregoDireto }}
                                    </p>
                                </v-flex>
                                <v-flex
                                    xs4
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>Indiretos: </b>
                                        {{ parecer.empregosGeradosProjeto.qtEmpregoIndireto }}
                                    </p>
                                </v-flex>
                                <v-flex
                                    xs4
                                    offset-xs1>
                                    <br>
                                    <p>
                                        <b>Total: </b>
                                        {{ parecer.empregosGeradosProjeto.qtEmpregoTotal }}
                                    </p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Evidências
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div v-html="parecer.empregosGeradosProjeto.dsEvidencia"/>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Recomendações da
                                    Equipe
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div
                                        v-html="
                                            parecer.empregosGeradosProjeto.
                                                dsRecomendacaoEquipe
                                        "
                                    />
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Conclusão da
                                    Equipe
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div
                                        v-if="
                                            parecer.empregosGeradosProjeto.
                                                dsConclusaoEquipe.length > 1
                                        "
                                        v-html="parecer.empregosGeradosProjeto.dsConclusaoEquipe"/>
                                    <div v-else>Não se Aplica.</div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Parecer da
                                    Fiscalização
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div v-html="parecer.empregosGeradosProjeto.dsParecerTecnico"/>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-subheader class="justify-center">
                            <div>
                                <h4
                                    class="display-1 grey--text text--darken-4 font-weight-light"
                                >
                                    Parecer do
                                    Coordenador
                                </h4>
                            </div>
                        </v-subheader>
                        <v-container>
                            <v-layout>
                                <v-flex
                                    xs10
                                    offset-xs1>
                                    <div v-html="parecer.empregosGeradosProjeto.dsParecer"/>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                </div>
                <v-card>
                    <v-subheader class="justify-center">
                        <div>
                            <h4
                                class="display-1 grey--text text--darken-4 font-weight-light"
                            >
                                Anexos
                            </h4>
                        </div>
                    </v-subheader>
                    <v-container
                        v-for="(dado, index) in dadosVisualizacao.arquivosFiscalizacao"
                        :key="index">
                        <v-layout>
                            <v-flex
                                xs10
                                offset-xs1>
                                <a :href="`/upload/abrir?id=${dado.idArquivo}`">
                                    <v-icon color="black"> file_copy</v-icon>
                                    {{ dado.nmArquivo }}</a>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card>
                <v-footer
                    height="auto"
                >
                    <v-card
                        class="flex"
                        flat
                        tile
                    >
                        <v-card-actions class="justify-center">
                            <v-btn
                                class="primary"
                                @click="modalClose()">
                                FECHAR
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-footer>
            </v-container>
        </v-card>
        <v-card v-else>
            <v-card-text>
                <Carregando :text="'Carregando ...'"/>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'VisualizarFiscalizacao',
    components: {
        Carregando,
    },
    mixins: [utils],
    props: {
        dadosVisualizacao: {
            type: Object,
            default: () => {},
        },
        dialog: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            statusModal: false,
            loading: true,
        };
    },
    computed: {
        parecer() {
            return this.dadosVisualizacao.fiscalizacaoConcluidaParecer;
        },
        ...mapGetters({
            status: 'modal/default',
        }),
    },
    watch: {
        status(value) {
            this.statusModal = value !== '';
        },
    },
    methods: {
        ...mapActions({
            modalClose: 'modal/modalClose',
        }),
        confereDadosVisualizacao() {
            if (typeof this.dadosVisualizacao !== 'undefined') {
                this.loading = false;
            }
        },
    },
};
</script>
