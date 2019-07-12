<template>
    <v-container fluid>
        <v-layout
            v-if="!permissao"
            column
        >
            <v-flex
            >
                <v-btn
                    class="green--text text--darken-4"
                    flat
                    @click="voltar()"
                >
                    <v-icon class="mr-2">keyboard_backspace</v-icon>
                </v-btn>
                <v-card>
                    <salic-mensagem-erro texto="Sem permiss&atilde;o de acesso para este projeto"/>
                </v-card>
            </v-flex>
        </v-layout>
        <v-layout
            v-else
        >
            <v-flex
                v-if="loading"
                xs9
                offset-xs1
            >
                <carregando :text="'Montando readequação de saldo de aplicação...'"/>
            </v-flex>
            <v-flex
                v-else
            >
                <v-toolbar
                    height="90"
                    class="blue-grey darken-2"
                    dark
                >
                    <v-btn
                        icon
                        class="hidden-xs-only"
                        @click="voltar()"
                    >
                        <v-icon color="white">arrow_back</v-icon>
                    </v-btn>
                    <v-toolbar-title class="ml-2">
                        <h5 class="headline font-weight-regular">Readequação: Saldo de Aplicação</h5>
                        <v-divider/>
                        <div class="subheading mt-1">
                            Projeto: {{ dadosProjeto.Pronac }} - {{ dadosProjeto.NomeProjeto }}
                        </div>
                    </v-toolbar-title>
                </v-toolbar>
                <v-layout
                    row
                    pb-2
                    align-center
                >
                    <v-flex
                        v-if="novaReadequacao"
                        xs12
                        class="mt-4 text-xs-center"
                    >
                        <div
                            v-if="!carregandoIniciarSolicitacao">
                            <v-btn
                                dark
                                class="blue"
                                @click="acionarSolicitarUsoSaldo()"
                            >
                                <v-icon>border_color</v-icon>
                                Solicitar uso do saldo de aplicação
                            </v-btn>
                        </div>
                        <div
                            v-else
                        >
                            <carregando :text="'Montando readequação de saldo de aplicação...'"/>
                        </div>
                    </v-flex>
                    <v-flex
                        v-else
                        xs12
                        class="text-xs-center"
                    >
                        <v-stepper
                            v-model="currentStep"
                            non-linear
                        >
                            <v-stepper-header>
                                <v-stepper-step
                                    editable
                                    step="1"
                                >
                                    Dados da readequação
                                </v-stepper-step>
                                <v-stepper-step
                                    editable
                                    step="2"
                                >
                                    Planilha orçamentária
                                </v-stepper-step>
                                <v-stepper-step
                                    editable
                                    step="3"
                                >
                                    Finalizar
                                </v-stepper-step>
                            </v-stepper-header>
                            <v-stepper-items>
                                <v-stepper-content
                                    step="1"
                                >
                                    <v-layout>
                                        <v-flex>
                                            <v-card
                                                class="mb-5"
                                                flat
                                            >
                                                <v-card-title
                                                    class="grey lighten-4 title"
                                                >
                                                    Justificativa da readequação
                                                </v-card-title>
                                                <form-justificativa
                                                    :dados-readequacao="dadosReadequacao"
                                                    :min-char="minChar.justificativa"
                                                    @dados-update="atualizarCampo($event, 'dsJustificativa')"
                                                    @editor-texto-counter="atualizarContador($event, 'justificativa')"
                                                />
                                            </v-card>
                                            <v-card
                                                class="mb-5"
                                                flat
                                            >
                                                <v-card-title
                                                    class="grey lighten-4 title"
                                                >
                                                    Arquivo anexo
                                                </v-card-title>
                                                <v-card-actions>
                                                    <upload-file
                                                        :formatos-aceitos="formatosAceitos"
                                                        :id-documento="dadosReadequacao.idDocumento"
                                                        class="mt-1"
                                                        @arquivo-anexado="atualizarArquivo($event)"
                                                        @arquivo-removido="removerArquivo()"
                                                    />
                                                </v-card-actions>
                                            </v-card>
                                            <v-card
                                                class="mb-5"
                                            >
                                                <v-card-title
                                                    class="grey lighten-4 title"
                                                >
                                                    <h5>
                                                        Valor disponível
                                                    </h5>
                                                </v-card-title>
                                                <valor-disponivel
                                                    :valor="dadosReadequacao.dsSolicitacao"
                                                    @dados-update="atualizarCampo($event, 'dsSolicitacao')"
                                                    @editor-texto-counter="atualizarContador($event, 'solicitacao')"
                                                />
                                            </v-card>
                                        </v-flex>
                                    </v-layout>
                                    <v-layout
                                        justify-end
                                        text-xs-right
                                    >
                                        <v-flex
                                            xs2
                                        >
                                            <v-btn
                                                color="green darken-1"
                                                dark
                                                @click="salvarReadequacao()"
                                            >Salvar
                                                <v-icon
                                                    right
                                                    dark
                                                >done</v-icon>
                                            </v-btn>
                                            <excluir-button
                                                :dados-readequacao="dadosReadequacao"
                                                :dados-projeto="dadosProjeto"
                                                :origem="'saldo'"
                                                :perfis-aceitos="getPerfis('proponente')"
                                                :perfil="perfil"
                                                :tela-edicao="true"
                                                @excluir-readequacao="excluirReadequacao"
                                            />
                                        </v-flex>
                                    </v-layout>
                                </v-stepper-content>
                                <v-stepper-content
                                    step="2"
                                >
                                    <v-card
                                        v-if="Object.keys(getPlanilha).length > 0"
                                        flat
                                    >
                                        <saldo-aplicacao-resumo
                                            v-if="exibirResumo"
                                            :saldo-declarado="getResumoPlanilha.saldoDeclarado"
                                            :saldo-disponivel="getResumoPlanilha.valorTotalDisponivelParaUso"
                                            :saldo-utilizado="getResumoPlanilha.saldoValorUtilizado"
                                        />
                                        <s-planilha-tipos-visualizacao-buttons v-model="opcoesDeVisualizacao" />
                                        <resize-panel
                                            v-if="Object.keys(getPlanilha).length > 0"
                                            :allow-resize="true"
                                            :size="sizePanel"
                                            units="percents"
                                            split-to="columns"
                                        >
                                            <div
                                                v-if="compararPlanilha === true"
                                                slot="firstPane"
                                            >
                                                <v-chip
                                                    color="blue lighten-4"
                                                >
                                                    <v-icon>assignment</v-icon>
                                                    Planilha ativa
                                                </v-chip>
                                                <s-planilha
                                                    :array-planilha="getPlanilhaAtiva"
                                                    :expand-all="expandirTudo"
                                                    :list-items="mostrarListagem"
                                                    :agrupamentos="agrupamentos"
                                                    :totais="totaisPlanilha"
                                                >
                                                    <template
                                                        slot="badge"
                                                        slot-scope="slotProps"
                                                    >
                                                        <v-chip
                                                            v-if="slotProps.planilha.vlAprovado"
                                                            outline="outline"
                                                            label="label"
                                                            color="#565555"
                                                        >
                                                            R$ {{ formatarParaReal(slotProps.planilha.vlAprovado) }}
                                                        </v-chip>
                                                    </template>
                                                    <template slot-scope="slotProps">
                                                        <s-planilha-itens-saldo
                                                            :table="slotProps.itens"
                                                            :readonly="true"
                                                        />
                                                    </template>
                                                </s-planilha>
                                            </div>
                                            <div
                                                slot="secondPane"
                                            >
                                                <v-chip
                                                    color="orange accent-1"
                                                >
                                                    <v-icon>edit</v-icon>
                                                    Planilha em edição
                                                </v-chip>
                                                <s-planilha
                                                    :array-planilha="getPlanilha"
                                                    :expand-all="expandirTudo"
                                                    :list-items="mostrarListagem"
                                                    :agrupamentos="agrupamentos"
                                                    :totais="totaisPlanilha"
                                                >
                                                    <template
                                                        slot="badge"
                                                        slot-scope="slotProps"
                                                    >
                                                        <v-chip
                                                            v-if="slotProps.planilha.vlAprovado"
                                                            outline="outline"
                                                            label="label"
                                                            color="#565555"
                                                        >
                                                            R$ {{ formatarParaReal(slotProps.planilha.vlAprovado) }}
                                                        </v-chip>
                                                    </template>
                                                    <template slot-scope="slotProps">
                                                        <s-planilha-itens-saldo
                                                            :table="slotProps.itens"
                                                        />
                                                    </template>
                                                </s-planilha>
                                            </div>
                                        </resize-panel>
                                    </v-card>
                                    <carregando
                                        v-else
                                        text="Carregando Planilha"
                                    />
                                </v-stepper-content>
                                <v-stepper-content
                                    step="3">
                                    <v-flex
                                        class="text-xs-center"
                                        xs-12
                                    >
                                        <saldo-aplicacao-resumo
                                            v-if="exibirResumo"
                                            :saldo-declarado="getResumoPlanilha.saldoDeclarado"
                                            :saldo-disponivel="getResumoPlanilha.valorTotalDisponivelParaUso"
                                            :saldo-utilizado="getResumoPlanilha.saldoValorUtilizado"
                                        />
                                        <div class="text-xs-right">
                                            <finalizar-button
                                                :readequacao-editada="readequacaoEditada"
                                                :dados-projeto="dadosProjeto"
                                                :tela-edicao="true"
                                                :perfis-aceitos="getPerfis('proponente')"
                                                :perfil="perfil"
                                                :min-char="minChar"
                                                :disabled="!finalizarDisponivel"
                                                :tela="'planilha'"
                                                class="text-xs-center"
                                                dark
                                                @readequacao-finalizada="readequacaoFinalizada()"
                                            />
                                        </div>
                                    </v-flex>
                                </v-stepper-content>
                            </v-stepper-items>
                        </v-stepper>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import Const from '../const';
import SalicMensagemErro from '@/components/SalicMensagemErro';
import FinalizarButton from '../components/FinalizarButton';
import MxReadequacao from '../mixins/Readequacao';
import Carregando from '@/components/CarregandoVuetify';
import FormJustificativa from '../components/FormJustificativa';
import UploadFile from '../components/UploadFile';
import ValorDisponivel from '../components/ValorDisponivel';
import SaldoAplicacaoResumo from '../components/SaldoAplicacaoResumo';
import ExcluirButton from '../components/ExcluirButton';
import SPlanilha from '@/components/Planilha/PlanilhaV2';
import ResizePanel from '@/components/resize-panel/ResizeSplitPane';
import SPlanilhaTiposVisualizacaoButtons from '@/components/Planilha/PlanilhaTiposVisualizacaoButtons';
import SPlanilhaItensSaldo from '../components/PlanilhaItensSaldo';
import MxPlanilha from '@/mixins/planilhas';

export default {
    name: 'SaldoAplicacaoView',
    components: {
        SalicMensagemErro,
        FinalizarButton,
        Carregando,
        UploadFile,
        ValorDisponivel,
        FormJustificativa,
        SaldoAplicacaoResumo,
        ExcluirButton,
        ResizePanel,
        SPlanilha,
        SPlanilhaTiposVisualizacaoButtons,
        SPlanilhaItensSaldo,
    },
    mixins: [
        utils,
        MxReadequacao,
        MxPlanilha,
    ],
    data() {
        return {
            readequacaoEditada: {
                idReadequacao: 0,
                dsSolicitacao: '',
                dsJustificativa: '',
                dtSolicitacao: '',
                documento: {},
                idDocumento: '',
                dsAvaliacao: '',
            },
            minChar: {
                solicitacao: 3,
                justificativa: 10,
            },
            contador: {
                solicitacao: 0,
                justificativa: 0,
            },
            perfisAceitos: {
                proponente: [
                    Const.PERFIL_PROPONENTE,
                ],
                analise: [
                    Const.PERFIL_TECNICO_ACOMPANHAMENTO,
                    Const.PERFIL_COORDENADOR_ACOMPANHAMENTO,
                    Const.PERFIL_COORDENADOR_GERAL_ACOMPANHAMENTO,
                    Const.PERFIL_DIRETOR,
                    Const.PERFIL_SECRETARIO,
                ],
            },
            loaded: {
                projeto: false,
                readequacao: false,
                usuario: false,
            },
            carregandoIniciarSolicitacao: false,
            loading: true,
            permissao: true,
            formatosAceitos: ['application/pdf'],
            novaReadequacao: true,
            totalItensSelecionados: 0,
            totaisPlanilha: [
                {
                    label: 'Valor Aprovado',
                    column: 'vlAprovado',
                },
            ],
            sizePanel: 49.8,
            opcoesDeVisualizacao: [0],
            planilhaSaldo: [],
            agrupamentos: [
                'FonteRecurso',
                'Produto',
                'Etapa',
                'UF',
                'Municipio',
            ],
            currentStep: 1,
            exibirResumo: false,
            finalizarDisponivel: false,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosReadequacao: 'readequacao/getReadequacao',
            getUsuario: 'autenticacao/getUsuario',
            getPlanilha: 'readequacao/getPlanilha',
            getPlanilhaAtiva: 'readequacao/getPlanilhaAtiva',
            getResumoPlanilha: 'readequacao/getResumoPlanilha',
        }),
        perfilAceito() {
            return this.verificarPerfil(this.perfil, this.perfisAceitos);
        },
        perfil() {
            return this.getUsuario.grupo_ativo;
        },
        saldoDisponivel() {
            return this.dadosReadequacao.dsSolicitacao;
        },
        saldoUtilizado() {
            return this.dadosReadequacao.dsSolicitacao;
        },
        expandirTudo() {
            return this.isOptionActive(0);
        },
        compararPlanilha() {
            return this.isOptionActive(1);
        },
        mostrarListagem() {
            return this.isOptionActive(2);
        },
    },
    watch: {
        getUsuario(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    this.loaded.usuario = true;
                }
            }
        },
        dadosProjeto(projeto) {
            if (typeof projeto === 'object') {
                if (Object.keys(projeto).length > 0) {
                    if (projeto.permissao === false) {
                        this.permissao = false;
                        return;
                    }
                    this.loaded.projeto = true;
                }
            }
        },
        dadosReadequacao: {
            handler(value) {
                this.loaded.readequacao = true;
                if (typeof value !== 'undefined') {
                    this.carregandoIniciarSolicitacao = false;
                    this.novaReadequacao = false;
                    if (value.idPronac && value.idTipoReadequacao) {
                        this.inicializarReadequacaoEditada();
                    } else {
                        this.novaReadequacao = true;
                    }
                }
            },
        },
        loaded: {
            handler(value) {
                const fullyLoaded = _.keys(value).every(i => value[i]);
                if (fullyLoaded) {
                    this.loading = false;
                }
            },
            deep: true,
        },
        getResumoPlanilha() {
            this.checkFinalizar();
        },
        readequacaoEditada() {
            this.checkFinalizar();
        },
    },
    created() {
        this.loaded = this.checkAlreadyLoadedData(
            this.loaded,
            this.getUsuario,
            this.dadosProjeto,
            this.dadosReadequacao,
        );
        if (typeof this.$route.params.idPronac !== 'undefined') {
            this.idPronac = this.$route.params.idPronac;
            this.obterDadosIniciais();
        }
    },
    methods: {
        ...mapActions({
            buscarProjetoCompleto: 'projeto/buscarProjetoCompleto',
            buscaReadequacaoPronacTipo: 'readequacao/buscaReadequacaoPronacTipo',
            obterDisponivelEdicaoReadequacaoPlanilha: 'readequacao/obterDisponivelEdicaoItemSaldoAplicacao',
            obterReadequacao: 'readequacao/obterReadequacao',
            updateReadequacao: 'readequacao/updateReadequacao',
            solicitarUsoSaldo: 'readequacao/solicitarUsoSaldo',
            obterPlanilha: 'readequacao/obterPlanilha',
            obterPlanilhaAtiva: 'readequacao/obterPlanilhaAtiva',
            obterUnidadesPlanilha: 'readequacao/obterUnidadesPlanilha',
            calcularResumoPlanilha: 'readequacao/calcularResumoPlanilha',
            mensagemSucesso: 'noticias/mensagemSucesso',
            mensagemErro: 'noticias/mensagemErro',
        }),
        obterDadosIniciais() {
            this.buscarProjetoCompleto(this.idPronac);
            this.buscaReadequacaoPronacTipo({
                idPronac: this.idPronac,
                idTipoReadequacao: 22,
                stStatusAtual: 'proponente',
            });
        },
        inicializarReadequacaoEditada() {
            this.readequacaoEditada = {
                idPronac: this.dadosReadequacao.idPronac,
                idReadequacao: this.dadosReadequacao.idReadequacao,
                idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
                dsAvaliacao: this.dadosReadequacao.dsAvaliacao,
                idDocumento: this.dadosReadequacao.idDocumento || '',
                dsSolicitacao: this.dadosReadequacao.dsSolicitacao,
                dsJustificativa: this.dadosReadequacao.dsJustificativa,
            };
            this.obterPlanilha({
                idPronac: this.dadosReadequacao.idPronac,
                idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
            });
            this.obterPlanilhaAtiva({
                idPronac: this.dadosReadequacao.idPronac,
            });
            this.calcularResumoPlanilha({
                idPronac: this.dadosReadequacao.idPronac,
                idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
            }).then(() => {
                this.exibirResumo = true;
            });
            this.obterUnidadesPlanilha({
                idPronac: this.dadosReadequacao.idPronac,
            });
        },
        salvarReadequacao() {
            this.updateReadequacao(this.readequacaoEditada).then(() => {
                this.mensagemSucesso('Readequação atualizada');
            });
        },
        atualizarArquivo(arquivo) {
            this.readequacaoEditada.documento = arquivo;
            this.updateReadequacao(this.readequacaoEditada).then(() => {
                this.mensagemSucesso('Arquivo adicionado');
            });
        },
        removerArquivo() {
            this.readequacaoEditada.documento = '';
            this.readequacaoEditada.idDocumento = '';
            this.updateReadequacao(this.readequacaoEditada).then(() => {
                this.mensagemSucesso('Arquivo removido!');
            });
        },
        arquivoTipoInvalido(payload) {
            const tiposValidos = payload.formatosAceitos.join(', ');
            this.mensagemErro(`Tipo fornecido (${payload.formatoEnviado}) não é aceito. Tipos aceitos: ${tiposValidos}`);
        },
        atualizarCampo(valor, campo) {
            this.readequacaoEditada[campo] = valor;
        },
        atualizarContador(valor, campo) {
            this.contador[campo] = valor;
            this.validar();
        },
        validar() {
            this.validacao = this.validarFormulario(
                this.readequacaoEditada,
                this.contador,
                this.minChar,
            );
        },
        getPerfis(tipo) {
            return this.perfisAceitos[tipo];
        },
        voltar() {
            this.$router.back();
        },
        excluirReadequacao() {
            this.loading = true;
            this.novaReadequacao = true;
            this.carregandoIniciarSolicitacao = false;
        },
        acionarSolicitarUsoSaldo() {
            this.carregandoIniciarSolicitacao = true;
            this.solicitarUsoSaldo({
                idPronac: this.idPronac,
            });
        },
        isOptionActive(index) {
            return this.opcoesDeVisualizacao.includes(index);
        },
        checkFinalizar() {
            if (this.getResumoPlanilha.saldoValorUtilizado === this.getResumoPlanilha.saldoDeclarado
                && this.readequacaoEditada.dsJustificativa.length >= this.minChar.justificativa) {
                this.finalizarDisponivel = true;
            } else {
                this.finalizarDisponivel = false;
            }
        },
        readequacaoFinalizada() {
            this.voltar();
        },
    },
};
</script>
