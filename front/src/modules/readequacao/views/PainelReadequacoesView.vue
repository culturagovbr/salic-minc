<template>
    <v-container fluid>
        <v-layout
            v-if="!permissao"
            column
        >
            <v-flex
                offset-xs1
            >
                <v-btn
                    class="green--text text--darken-4"
                    flat
                    @click="voltar()"
                >
                    <v-icon class="mr-2">keyboard_backspace</v-icon>
                </v-btn>
                <v-card>
                    <salic-mensagem-erro :texto="'Sem permiss&atilde;o de acesso para este projeto'"/>
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
                <carregando :text="'Carregando painel de readequações...'"/>
            </v-flex>
            <v-flex v-else>
                <v-subheader>
                    <v-btn
                        class="green--text text--darken-4"
                        flat
                        @click="voltar()"
                    >
                        <v-icon class="mr-2">keyboard_backspace</v-icon>
                    </v-btn>
                    <h2 class="grey--text text--darken-4">Painel de Readequações</h2>
                    <v-spacer/>
                    <h3
                        class="grey--text text--darken-4"
                    >{{ dadosProjeto.Pronac }} - {{ dadosProjeto.NomeProjeto }}</h3>
                </v-subheader>
                <v-tabs
                    color="#0a420e"
                    centered
                    dark
                    icons-and-text
                    model="abaInicial"
                    @change="trocaAba($event)"
                >
                    <v-tabs-slider color="yellow"/>
                    <v-tab
                        href="#edicao"
                    >Edição
                        <v-icon>edit</v-icon>
                    </v-tab>
                    <v-tab
                        href="#analise"
                    >Em Análise
                        <v-icon>gavel</v-icon>
                    </v-tab>
                    <v-tab
                        href="#finalizadas"
                    >Finalizadas
                        <v-icon>check</v-icon>
                    </v-tab>
                    <v-tab-item :value="'edicao'">
                        <v-card>
                            <tabela-readequacoes
                                :dados-readequacao="getReadequacoesProponente"
                                :componentes="acoesProponente"
                                :dados-projeto="dadosProjeto"
                                :item-em-edicao="itemEmEdicao"
                                :min-char="minChar"
                                :perfis-aceitos="getPerfis('proponente')"
                                :perfil="perfil"
                                @excluir-readequacao="excluirReadequacao"
                                @atualizar-readequacao="atualizarReadequacao"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item :value="'analise'">
                        <v-card>
                            <tabela-readequacoes
                                :dados-readequacao="getReadequacoesAnalise"
                                :componentes="acoesAnalise"
                                :dados-projeto="dadosProjeto"
                                :perfis-aceitos="getPerfis('analise')"
                                :perfil="perfil"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item :value="'finalizadas'">
                        <v-card>
                            <tabela-readequacoes
                                :dados-readequacao="getReadequacoesFinalizadas"
                                :componentes="acoesFinalizadas"
                                :dados-projeto="dadosProjeto"
                            />
                        </v-card>
                    </v-tab-item>
                </v-tabs>
                <criar-readequacao
                    :id-pronac="dadosProjeto.idPronac"
                    @criar-readequacao="criarReadequacao($event)"
                />
            </v-flex>
        </v-layout>
        <v-snackbar
            :color="mensagem.cor"
            :timeout="mensagem.timeout"
            v-model="mensagem.ativa"
            bottom
        ><span>{{ mensagem.conteudo }}</span>
            <v-btn
                dark
                flat
                @click="mensagem.ativa = false"
            >
                Fechar
            </v-btn>
        </v-snackbar>
    </v-container>
</template>
<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import Const from '../const';
import SalicMensagemErro from '@/components/SalicMensagemErro';
import TabelaReadequacoes from '../components/TabelaReadequacoes';
import ExcluirButton from '../components/ExcluirButton';
import FinalizarButton from '../components/FinalizarButton';
import EditarReadequacaoButton from '../components/EditarReadequacaoButton';
import VisualizarReadequacaoButton from '../components/VisualizarReadequacaoButton';
import Carregando from '@/components/CarregandoVuetify';
import CriarReadequacao from '../components/CriarReadequacao';
import verificarPerfil from '../mixins/verificarPerfil';

export default {
    name: 'PainelReadequacoesView',
    components: {
        Carregando,
        TabelaReadequacoes,
        ExcluirButton,
        EditarReadequacaoButton,
        VisualizarReadequacaoButton,
        FinalizarButton,
        CriarReadequacao,
        SalicMensagemErro,
    },
    mixins: [
        verificarPerfil,
    ],
    data() {
        return {
            listaStatus: [
                'proponente',
                'analise',
                'finalizadas',
            ],
            acoesProponente: {
                usuario: '',
                acoes: [
                    ExcluirButton,
                    EditarReadequacaoButton,
                    VisualizarReadequacaoButton,
                    FinalizarButton,
                ],
            },
            acoesAnalise: {
                acoes: [
                    VisualizarReadequacaoButton,
                ],
            },
            acoesFinalizadas: {
                acoes: [
                    VisualizarReadequacaoButton,
                ],
            },
            itemEmEdicao: 0,
            loading: true,
            mensagem: {
                ativa: false,
                timeout: 2300,
                conteudo: '',
                cor: '',
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
            minChar: {
                solicitacao: 3,
                justificativa: 10,
            },
            abaInicial: '#edicao',
            loaded: {
                projeto: false,
                readequacoes: false,
                usuario: false,
            },
            permissao: true,
        };
    },
    computed: {
        ...mapGetters({
            getUsuario: 'autenticacao/getUsuario',
            getReadequacoesProponente: 'readequacao/getReadequacoesProponente',
            getReadequacoesAnalise: 'readequacao/getReadequacoesAnalise',
            getReadequacoesFinalizadas: 'readequacao/getReadequacoesFinalizadas',
            dadosProjeto: 'projeto/projeto',
        }),
        perfil() {
            return this.getUsuario.grupo_ativo;
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
        getReadequacoesProponente(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    this.loaded.readequacoes = true;
                }
            }
        },
        dadosProjeto(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    if (value.permissao === false) {
                        this.permissao = false;
                        return;
                    }
                    this.obterReadequacoesPorStatus('proponente');
                    this.loaded.projeto = true;
                }
            }
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
    },
    created() {
        if (typeof this.$route.params.idPronac !== 'undefined') {
            this.idPronac = this.$route.params.idPronac;
            if (Object.keys(this.dadosProjeto).length === 0) {
                this.buscarProjetoCompleto(this.idPronac);
            }
        }
    },
    methods: {
        ...mapActions({
            obterListaDeReadequacoes: 'readequacao/obterListaDeReadequacoes',
            buscarProjetoCompleto: 'projeto/buscarProjetoCompleto',
        }),
        obterReadequacoesPorStatus(stStatusAtual) {
            if (this.listaStatus.includes(stStatusAtual)) {
                this.obterListaDeReadequacoes({
                    idPronac: this.$route.params.idPronac,
                    stStatusAtual,
                });
            }
        },
        criarReadequacao(idReadequacao) {
            this.itemEmEdicao = idReadequacao;
        },
        excluirReadequacao() {
            this.mensagem.conteudo = 'Readequação excluída!';
            this.mensagem.ativa = true;
            this.mensagem.cor = 'green lighteen-1';
            this.timeout = 1300;
        },
        atualizarReadequacao() {
            this.itemEmEdicao = 0;
            this.obterListaDeReadequacoes({
                idPronac: this.$route.params.idPronac,
                stStatusAtual: 'proponente',
            });
        },
        getPerfis(tipo) {
            return this.perfisAceitos[tipo];
        },
        perfilAceito(tipoPerfil) {
            /* função ainda não utilizada - será usada na visão do painel pelo técnico */
            if (Object.prototype.hasOwnProperty.call(this.perfisAceitos, tipoPerfil)) {
                return this.verificarPerfil(this.perfil, this.perfisAceitos[tipoPerfil]);
            }
            return false;
        },
        voltar() {
            this.$router.back();
        },
        trocaAba(aba) {
            let status = '';
            if (aba === 'edicao') {
                status = 'proponente';
            } else {
                status = aba;
            }
            this.obterListaDeReadequacoes({
                idPronac: this.$route.params.idPronac,
                stStatusAtual: status,
            });
        },
    },
};
</script>
