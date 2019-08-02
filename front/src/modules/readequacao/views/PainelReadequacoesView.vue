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
                    <v-icon class="mr-2">
                        keyboard_backspace
                    </v-icon>
                </v-btn>
                <v-card>
                    <salic-mensagem-erro
                        :texto="'Sem permiss&atilde;o de acesso'"
                    />
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
                <carregando
                    :text="'Carregando painel de readequações...'"
                />
            </v-flex>
            <v-flex v-else-if="acessoNegado">
                <v-btn
                    class="green--text text--darken-4"
                    flat
                    @click="voltar()"
                >
                    <v-icon class="mr-2">keyboard_backspace</v-icon>
                </v-btn>
                <v-card>
                    <salic-mensagem-erro texto="Sem permiss&atilde;o de acesso a esse painel"/>
                </v-card>
            </v-flex>
            <v-flex v-else>
                <v-subheader>
                    <v-btn
                        class="green--text text--darken-4"
                        flat
                        @click="voltar()"
                    >
                        <v-icon class="mr-2">
                            keyboard_backspace
                        </v-icon>
                    </v-btn>
                    <h2 class="grey--text text--darken-4">Painel de Readequações</h2>
                    <v-spacer/>
                    <h3
                        v-if="dadosProjeto.length > 0"
                        class="grey--text text--darken-4"
                    >
                        {{ dadosProjeto.Pronac }} - {{ dadosProjeto.NomeProjeto }}
                    </h3>
                </v-subheader>
                <v-tabs
                    color="#0a420e"
                    centered
                    dark
                    icons-and-text
                    model="abaInicial"
                    @change="trocaAba($event)"
                >
                    <v-tabs-slider
                        color="yellow"
                    />
                    <v-tab
                        v-if="perfilAceito(['proponente'])"
                        href="#edicao"
                    >Edição
                        <v-icon>
                            edit
                        </v-icon>
                    </v-tab>
                    <v-tab
                        v-if="perfilAceito(['coordenador'])"
                        href="#aguardando_distribuicao"
                    >Aguardando distribuição
                        <v-icon>
                            forward
                        </v-icon>
                    </v-tab>
                    <v-tab
                        v-if="perfilAceito(['proponente', 'coordenador'])"
                        href="#em_analise"
                    >Em Análise
                        <v-icon>
                            gavel
                        </v-icon>
                    </v-tab>
                    <v-tab
                        v-if="perfilAceito(['analisar'])"
                        href="#analisar"
                    >Em Análise
                        <v-icon>
                            gavel
                        </v-icon>
                    </v-tab>
                    <v-tab
                        v-if="perfilAceito(['coordenador', 'coordenador_geral'])"
                        href="#analisados"
                    >Analisados
                        <v-icon>
                            done
                        </v-icon>
                    </v-tab>
                    <v-tab
                        v-if="perfilAceito(['coordenador_acompanhamento'])"
                        href="#aguardando_publicacao"
                    >Aguardando publicação
                        <v-icon>
                            schedule
                        </v-icon>
                    </v-tab>
                    <v-tab
                        v-if="perfilAceito(['proponente'])"
                        href="#finalizadas"
                    >Finalizadas
                        <v-icon>
                            check
                        </v-icon>
                    </v-tab>
                    <v-tab-item
                        v-if="perfilAceito(['proponente'])"
                        :value="'edicao'">
                        <v-card>
                            <tabela-readequacoes
                                :dados-readequacao="getReadequacoesProponente"
                                :componentes="acoesProponenteEmEdicao"
                                :dados-projeto="dadosProjeto"
                                :item-em-edicao="itemEmEdicao"
                                :min-char="minChar"
                                :perfis-aceitos="perfisAceitos"
                                :perfil="perfil"
                                @excluir-readequacao="excluirReadequacao"
                                @atualizar-readequacao="atualizarReadequacao"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item
                        v-if="perfilAceito(['coordenador'])"
                        :value="'aguardando_distribuicao'">
                        <v-card>
                            <tabela-readequacoes-coordenador
                                :dados-readequacao="getReadequacoesPainelAguardandoDistribuicao"
                                :componentes="acoesCoordenadorAguardandoDistribuicao"
                                :painel="`aguardando_distribuicao`"
                                :perfis-aceitos="perfisAceitos"
                                :perfil="perfil"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item
                        v-if="perfilAceito(['proponente'])"
                        :value="'em_analise'">
                        <v-card>
                            <tabela-readequacoes
                                :dados-readequacao="getReadequacoesAnalise"
                                :componentes="acoesProponenteEmAnalise"
                                :perfis-aceitos="perfisAceitos"
                                :perfil="perfil"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item
                        v-if="perfilAceito(['coordenador'])"
                        :value="'em_analise'">
                        <v-card>
                            <tabela-readequacoes-coordenador
                                :dados-readequacao="getReadequacoesPainelEmAnalise"
                                :componentes="acoesCoordenadorEmAnalise"
                                :painel="`em_analise`"
                                :perfis-aceitos="perfisAceitos"
                                :perfil="perfil"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item
                        v-if="perfilAceito(['analisar'])"
                        :value="'analisar'">
                        <v-card>
                            <tabela-readequacoes-coordenador
                                :dados-readequacao="getReadequacoesPainelTecnico"
                                :componentes="acoesAnalise"
                                :painel="`analisar`"
                                :perfis-aceitos="perfisAceitos"
                                :perfil="perfil"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item
                        v-if="perfilAceito(['coordenador', 'coordenador_geral'])"
                        :value="'analisados'">
                        <v-card>
                            <tabela-readequacoes-coordenador
                                :dados-readequacao="getReadequacoesPainelAnalisados"
                                :componentes="acoesCoordenadorAnalisados"
                                :painel="`analisados`"
                                :perfis-aceitos="perfisAceitos"
                                :perfil="perfil"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item
                        v-if="perfilAceito(['coordenador_acompanhamento'])"
                        :value="'aguardando_publicacao'">
                        <v-card>
                            <tabela-readequacoes-coordenador
                                :dados-readequacao="getReadequacoesPainelAguardandoPublicacao"
                                :componentes="acoesCoordenadorAguardandoPublicacao"
                                :painel="`aguardando_publicacao`"
                                :perfis-aceitos="perfisAceitos"
                                :perfil="perfil"
                            />
                        </v-card>
                    </v-tab-item>
                    <v-tab-item
                        v-if="perfilAceito(['proponente'])"
                        :value="'finalizadas'">
                        <v-card>
                            <tabela-readequacoes
                                :dados-readequacao="getReadequacoesFinalizadas"
                                :componentes="acoesFinalizadas"
                                :dados-projeto="dadosProjeto"
                                :perfis-aceitos="perfisAceitos"
                                :perfil="perfil"
                            />
                        </v-card>
                    </v-tab-item>
                </v-tabs>
                <criar-readequacao
                    v-if="perfilAceito(['proponente'])"
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
import VisualizarAssinaturaButton from '@/modules/assinatura/components/VisualizarAssinaturaButton';
import AssinarDocumentoButton from '@/modules/assinatura/components/AssinarDocumentoButton';
import TabelaReadequacoes from '../components/TabelaReadequacoes';
import TabelaReadequacoesCoordenador from '../components/TabelaReadequacoesCoordenador';
import ExcluirButton from '../components/ExcluirButton';
import FinalizarButton from '../components/FinalizarButton';
import EditarReadequacaoButton from '../components/EditarReadequacaoButton';
import VisualizarReadequacaoButton from '../components/VisualizarReadequacaoButton';
import AnalisarReadequacaoButton from '../components/AnalisarReadequacaoButton';
import DistribuirReadequacaoButton from '../components/DistribuirReadequacaoButton';
import RedistribuirReadequacaoButton from '../components/RedistribuirReadequacaoButton';
import DevolverReadequacaoButton from '../components/DevolverReadequacaoButton';
import Carregando from '@/components/CarregandoVuetify';
import CriarReadequacao from '../components/CriarReadequacao';
import MxReadequacao from '../mixins/Readequacao';

export default {
    name: 'PainelReadequacoesView',
    components: {
        Carregando,
        VisualizarAssinaturaButton,
        TabelaReadequacoes,
        TabelaReadequacoesCoordenador,
        ExcluirButton,
        EditarReadequacaoButton,
        VisualizarReadequacaoButton,
        AnalisarReadequacaoButton,
        DistribuirReadequacaoButton,
        RedistribuirReadequacaoButton,
        FinalizarButton,
        CriarReadequacao,
        SalicMensagemErro,
    },
    mixins: [
        MxReadequacao,
    ],
    data() {
        return {
            listaStatus: [
                'proponente',
                'analise',
                'finalizadas',
            ],
            acoesProponenteEmEdicao: {
                usuario: '',
                acoes: [
                    {
                        componente: ExcluirButton,
                        permissao: 'proponente',
                    },
                    {
                        componente: EditarReadequacaoButton,
                        permissao: 'proponente',
                    },
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'proponente',
                    },
                    {
                        componente: FinalizarButton,
                        permissao: 'proponente',
                    },
                ],
            },
            acoesProponenteEmAnalise: {
                usuario: '',
                acoes: [
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'proponente',
                    },
                ],
            },
            acoesAnalise: {
                acoes: [
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'analisar',
                    },
                    {
                        componente: AnalisarReadequacaoButton,
                        permissao: 'analisar',
                    },
                    {
                        componente: AssinarDocumentoButton,
                        permissao: 'assinatura',
                    },
                    {
                        componente: VisualizarAssinaturaButton,
                        permissao: 'assinatura',
                    },
                ],
            },
            acoesCoordenadorAguardandoDistribuicao: {
                acoes: [
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'analise',
                    },
                    {
                        componente: DistribuirReadequacaoButton,
                        permissao: 'coordenador',
                    },
                    {
                        componente: AssinarDocumentoButton,
                        permissao: 'assinatura',
                    },
                    {
                        componente: VisualizarAssinaturaButton,
                        permissao: 'assinatura',
                    },
                ],
            },
            acoesCoordenadorEmAnalise: {
                acoes: [
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'analise',
                    },
                    {
                        componente: RedistribuirReadequacaoButton,
                        permissao: 'coordenador',
                    },
                ],
            },
            acoesCoordenadorAnalisados: {
                acoes: [
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'analise',
                    },
                    {
                        componente: DevolverReadequacaoButton,
                        permissao: 'coordenador',
                    },
                    {
                        componente: AssinarDocumentoButton,
                        permissao: 'assinatura',
                    },
                    {
                        componente: VisualizarAssinaturaButton,
                        permissao: 'assinatura',
                    },
                ],
            },
            acoesCoordenadorAguardandoPublicacao: {
                acoes: [
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'analise',
                    },
                ],
            },
            acoesCoordenadorGeralAnalisados: {
                acoes: [
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'coordenador',
                    },
                    {
                        componente: AssinarDocumentoButton,
                        permissao: 'assinatura',
                    },
                    {
                        componente: VisualizarAssinaturaButton,
                        permissao: 'assinatura',
                    },
                ],
            },
            acoesFinalizadas: {
                acoes: [
                    {
                        componente: VisualizarReadequacaoButton,
                        permissao: 'all',
                    },
                ],
            },
            itemEmEdicao: 0,
            loading: true,
            acessoNegado: false,
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
                    Const.PERFIL_PROPONENTE,
                    Const.PERFIL_COORDENADOR_ACOMPANHAMENTO,
                    Const.PERFIL_COORDENADOR_DE_PARECER,
                    Const.PERFIL_COORDENADOR_GERAL_ACOMPANHAMENTO,
                    Const.PERFIL_DIRETOR,
                    Const.PERFIL_SECRETARIO,
                ],
                analisar: [
                    Const.PERFIL_TECNICO_ACOMPANHAMENTO,
                    Const.PERFIL_PARECERISTA,
                ],
                assinatura: [
                    Const.PERFIL_PARECERISTA,
                    Const.PERFIL_COORDENADOR_DE_PARECER,
                    Const.PERFIL_TECNICO_ACOMPANHAMENTO,
                    Const.PERFIL_COORDENADOR_ACOMPANHAMENTO,
                    Const.PERFIL_COORDENADOR_GERAL_ACOMPANHAMENTO,
                    Const.PERFIL_DIRETOR,
                    Const.PERFIL_SECRETARIO,
                ],
                coordenador: [
                    Const.PERFIL_COORDENADOR_DE_PARECER,
                    Const.PERFIL_COORDENADOR_ACOMPANHAMENTO,
                ],
                coordenador_acompanhamento: [
                    Const.PERFIL_COORDENADOR_ACOMPANHAMENTO,
                ],
                coordenador_geral: [
                    Const.PERFIL_COORDENADOR_GERAL_ACOMPANHAMENTO,
                    Const.PERFIL_DIRETOR,
                    Const.PERFIL_SECRETARIO,
                ],
            },
            minChar: {
                solicitacao: 3,
                justificativa: 10,
                dataExecucao: 10,
            },
            abaInicial: '#edicao',
            loaded: {
                readequacao: false,
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
            getReadequacoesPainelTecnico: 'readequacao/getReadequacoesPainelTecnico',
            getReadequacoesPainelAguardandoDistribuicao: 'readequacao/getReadequacoesPainelAguardandoDistribuicao',
            getReadequacoesPainelEmAnalise: 'readequacao/getReadequacoesPainelEmAnalise',
            getReadequacoesPainelAnalisados: 'readequacao/getReadequacoesPainelAnalisados',
            getReadequacoesPainelAguardandoPublicacao: 'readequacao/getReadequacoesPainelAguardandoPublicacao',
            dadosProjeto: 'projeto/projeto',
        }),
        perfil() {
            return this.getUsuario.grupo_ativo;
        },
    },
    watch: {
        getUsuario(value) {
            if (typeof value === 'object') {
                this.loaded.usuario = true;
            }
        },
        getReadequacoesProponente(value) {
            if (typeof value === 'object') {
                this.loaded.readequacao = true;
            }
        },
        getReadequacoesPainelTecnico(value) {
            if (typeof value === 'object') {
                this.loaded.readequacao = true;
            }
        },
        getReadequacoesPainelAguardandoDistribuicao(value) {
            if (typeof value === 'object') {
                this.loaded.readequacao = true;
            }
        },
        getReadequacoesPainelAnalisados(value) {
            if (this.perfisAceitos.coordenador_geral.includes(parseInt(this.perfil, 10))) {
                if (typeof value === 'object') {
                    this.loaded.readequacao = true;
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
        perfil() {
            if (parseInt(this.perfil, 10) === Const.PERFIL_PROPONENTE) {
                if (typeof this.$route.params.idPronac !== 'undefined') {
                    this.idPronac = this.$route.params.idPronac;
                    this.buscarProjetoCompleto(this.idPronac);
                } else {
                    this.loading = false;
                    this.acessoNegado = true;
                }
            } else if (parseInt(this.perfil, 10) === Const.PERFIL_TECNICO_ACOMPANHAMENTO
                       || parseInt(this.perfil, 10) === Const.PERFIL_PARECERISTA) {
                this.buscarReadequacoesPainelTecnico({});
                // TODO: puxar lista para obter lista de projetos finalizáveis
                // .then((esponse) => {
                /* const listaIdReadequacao = [];
                        let { result: items } = response.data.data;
                        result.forEach(() => {
                            // extrair todos os idReadequacao e mandar para o
                        });
                        this.obterDocumentoAssinaturaReadequacao({ listaIdReadequacao });
                        */
                // });
            } else if (parseInt(this.perfil, 10) === Const.PERFIL_COORDENADOR_ACOMPANHAMENTO
                       || parseInt(this.perfil, 10) === Const.PERFIL_COORDENADOR_DE_PARECER) {
                this.buscarReadequacoesPainelAguardandoDistribuicao({ filtro: 'painel_aguardando_distribuicao' });
                this.buscarReadequacoesPainelEmAnalise({ filtro: 'em_analise' });
                this.buscarReadequacoesPainelAnalisados({ filtro: 'analisados' });
                if (parseInt(this.perfil, 10) === Const.PERFIL_COORDENADOR_ACOMPANHAMENTO) {
                    this.buscarReadequacoesPainelAguardandoPublicacao({ filtro: 'aguardando_publicacao' });
                }
            } else if (this.perfisAceitos.coordenador_geral.includes(parseInt(this.perfil, 10))) {
                this.buscarReadequacoesPainelAnalisados({ filtro: 'analisados' });
            } else {
                this.loading = false;
                this.acessoNegado = true;
            }
        },
    },
    mounted() {
        if (this.perfilAceito(['proponente'])) {
            Object.assign(
                this.loaded,
                {
                    projeto: false,
                    usuario: false,
                },
            );
        }
        if (parseInt(this.perfil, 10) === Const.PERFIL_TECNICO_ACOMPANHAMENTO
            || parseInt(this.perfil, 10) === Const.PERFIL_PARECERISTA) {
            if (typeof this.getReadequacoesPainelAnalisados.items !== 'undefined') {
                if (this.getReadequacoesPainelTecnico.items.length > 0) {
                    this.loaded.readequacao = true;
                }
            } else {
                this.buscarReadequacoesPainelTecnico({});
            }
        }
    },
    created() {
        this.loaded = this.checkAlreadyLoadedData(
            this.loaded,
            this.getUsuario,
            this.dadosProjeto,
            this.dadosReadequacao,
        );
    },
    methods: {
        ...mapActions({
            obterListaDeReadequacoes: 'readequacao/obterListaDeReadequacoes',
            buscarProjetoCompleto: 'projeto/buscarProjetoCompleto',
            buscarReadequacoesPainelTecnico: 'readequacao/buscarReadequacoesPainelTecnico',
            buscarReadequacoesPainelAguardandoDistribuicao: 'readequacao/buscarReadequacoesPainelAguardandoDistribuicao',
            buscarReadequacoesPainelEmAnalise: 'readequacao/buscarReadequacoesPainelEmAnalise',
            buscarReadequacoesPainelAnalisados: 'readequacao/buscarReadequacoesPainelAnalisados',
            buscarReadequacoesPainelAguardandoPublicacao: 'readequacao/buscarReadequacoesPainelAguardandoPublicacao',
            obterDocumentoAssinaturaReadequacao: 'readequacao/obterDocumentoAssinaturaReadequacao',
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
        perfilAceito(tiposPerfil) {
            return tiposPerfil.some((perfil) => {
                if (Object.prototype.hasOwnProperty.call(this.perfisAceitos, perfil)) {
                    return this.verificarPerfil(this.perfil, this.perfisAceitos[perfil]);
                }
                return false;
            });
        },
        trocaAba(aba) {
            let status = '';
            if (aba === 'edicao') {
                status = 'proponente';
            } else {
                status = aba;
            }
            if (this.perfilAceito(['proponente'])) {
                this.obterListaDeReadequacoes({
                    idPronac: this.$route.params.idPronac,
                    stStatusAtual: status,
                });
            }
        },
    },
};
</script>
