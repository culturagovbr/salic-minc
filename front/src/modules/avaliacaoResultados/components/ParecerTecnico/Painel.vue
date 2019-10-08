<template>
    <v-container fluid>
        <v-subheader>
            <h2>{{ route.meta.title }}</h2>
        </v-subheader>
        <v-card>
            <v-tabs
                v-model="$route.meta.tab"
                centered
                color="primary"
                dark
                icons-and-text
            >
                <v-tabs-slider color="deep-orange accent-3" />
                <v-tab
                    href="#tab-6"
                    @click="r('/painel/dashboard')"
                >
                    Dashboard
                    <v-icon>assignment_ind</v-icon>
                </v-tab>
                <v-tab-item
                    :key="6"
                    :value="'tab-6'"
                >
                    <v-card
                        flat
                        class="pa-2"
                    >
                        <v-layout
                            align-center
                            justify-space-around
                            row
                            fill-height
                        >
                            <v-card
                                v-for="(dashboad, index) in dashboardItens"
                                :key="index"
                                max-width="400"
                            >
                                <v-card-title>
                                    <span class="title font-weight-light">{{ index }}</span>
                                </v-card-title>

                                <v-card-text class="headline font-weight-bold ">
                                    <p class="display-3 text-xs-center">
                                        <a :href="dashboad.url">{{ dashboad.valor }}</a>
                                    </p>
                                </v-card-text>
                            </v-card>
                        </v-layout>
                    </v-card>
                </v-tab-item>
                <v-tab
                    v-if="getUsuario.grupo_ativo == 125"
                    href="#tab-0"
                    @click="r('/painel/distribuir')"
                >
                    <template v-if="Object.keys(getProjetosParaDistribuir).length == 0">
                        <v-progress-circular
                            indeterminate
                            color="secondary"
                            dark
                        />
                    </template>
                    <template v-else>
                        Distribuir
                        <v-icon>assignment_ind</v-icon>
                    </template>
                </v-tab>
                <v-tab
                    href="#tab-1"
                    @click="r('/painel/aba-em-analise')"
                >
                    <template v-if="Object.keys(dadosTabelaTecnico).length == 0">
                        <v-progress-circular
                            indeterminate
                            color="secondary"
                            dark
                        />
                    </template>
                    <template v-else>
                        Em Analise
                        <v-icon>gavel</v-icon>
                    </template>
                </v-tab>

                <v-tab
                    href="#tab-2"
                    @click="r('/painel/assinar')"
                >
                    <template v-if="Object.keys(getProjetosFinalizados).length == 0">
                        <v-progress-circular
                            indeterminate
                            color="secondary"
                            dark
                        />
                    </template>
                    <template v-else>
                        Assinar
                        <v-icon>edit</v-icon>
                    </template>
                </v-tab>

                <v-tab
                    href="#tab-4"
                    @click="buscarHistorico"
                >
                    <template v-if="loadingHistorico">
                        <v-progress-circular
                            indeterminate
                            color="secondary"
                            dark
                        />
                    </template>
                    <template v-else>
                        Historico
                        <v-icon>history</v-icon>
                    </template>
                </v-tab>

                <v-tab-item
                    :key="0"
                    :value="'tab-0'"
                >
                    <TabelaProjetos
                        v-if="getProjetosParaDistribuir"
                        :dados="getProjetosParaDistribuir"
                        :componentes="distribuirAcoes"
                    />
                </v-tab-item>
                <v-tab-item
                    :key="1"
                    :value="'tab-1'"
                >
                    <v-card
                        v-if="dadosTabelaTecnico"
                        flat
                    >
                        <v-card-text>
                            <TabelaProjetos
                                v-if="(getUsuario.grupo_ativo == 125 || getUsuario.grupo_ativo == 126)"
                                :analisar="true"
                                :dados="dadosTabelaTecnico"
                                :componentes="listaAcoesCoordenador"
                                :mostrar-tecnico="true"
                            />
                            <TabelaProjetos
                                v-else
                                :analisar="true"
                                :dados="dadosTabelaTecnico"
                                :componentes="listaAcoesTecnico"
                            />
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item
                    :key="2"
                    :value="'tab-2'"
                >
                    <v-card flat>
                        <v-card-text>
                            <TabelaProjetos
                                v-if="(getUsuario.grupo_ativo == CONST.PERFIL_COORDENADOR)"
                                :dados="getProjetosAssinarCoordenador"
                                :componentes="listaAcoesAssinar"
                            />
                            <TabelaProjetos
                                v-else-if="(getUsuario.grupo_ativo == CONST.PERFIL_COORDENADOR_GERAL)"
                                :dados="getProjetosAssinarCoordenadorGeral"
                                :componentes="listaAcoesAssinarCoordenadorGeral"
                            />
                            <TabelaProjetos
                                v-else
                                :dados="getProjetosFinalizados"
                                :componentes="listaAcoesAssinar"
                            />
                        </v-card-text>
                    </v-card>
                </v-tab-item>

                <v-tab-item
                    :key="4"
                    :value="'tab-4'"
                >
                    <v-card flat>
                        <v-card-text>
                            <TabelaProjetos
                                :dados="getProjetosHistorico"
                                :componentes="historicoAcoes"
                            />
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-container>
</template>
<script>
import Vue from 'vue';
import { mapActions, mapGetters } from 'vuex';
import CONST from '../../const';

import TabelaProjetos from './components/TabelaProjetos';
import Historico from '../components/Historico';
import Encaminhar from './components/ComponenteEncaminhar';
import AnaliseButton from '../analise/analisarButton';
import AssinarButton from '../analise/AssinarButton';
import Devolver from '../components/Devolver';
import VisualizarPlanilhaButtton from '../analise/VisualizarPlanilhaButtton';
import Diligencias from '../components/HistoricoDiligencias';
import VisualizarParecer from '../components/VisualizarParecer';
import ProjetosSimilaresButtton from '../analise/ProjetosSimilaresButton';

export default {
    name: 'Painel',
    components: {
        TabelaProjetos,
    },

    data() {
        return {
            tabActive: null,
            dashboardItens: {},
            projetoAnaliseDados: { code: 300, items: [] },
            listaAcoesTecnico: {
                atual: '',
                proximo: '',
                acoes: [
                    Diligencias,
                    Historico,
                    AnaliseButton,
                    VisualizarParecer,
                    VisualizarPlanilhaButtton,
                    ProjetosSimilaresButtton,
                ],
            },
            listaAcoesAssinar: {
                usuario: this.getUsuario,
                atual: CONST.ESTADO_PARECER_FINALIZADO,
                proximo: CONST.ESTADO_ANALISE_PARECER,
                idTipoDoAtoAdministrativo: CONST.ATO_ADMINISTRATIVO_PARECER_TECNICO,
                acoes: [Diligencias, Historico, AssinarButton, Devolver, VisualizarPlanilhaButtton, VisualizarParecer],
            },
            listaAcoesCoordenador: {
                usuario: this.getUsuario,
                atual: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_PARECER,
                proximo: '',
                acoes: [
                    Diligencias,
                    Encaminhar,
                    Historico,
                    VisualizarPlanilhaButtton,
                    VisualizarParecer,
                ],
            },
            listaAcoesAssinarCoordenadorGeral: {
                usuario: this.getUsuario,
                atual: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_PARECER,
                proximo: CONST.ESTADO_ANALISE_PARECER,
                idTipoDoAtoAdministrativo: CONST.ATO_ADMINISTRATIVO_PARECER_TECNICO,
                acoes: [Diligencias, Historico, AssinarButton, Devolver, VisualizarPlanilhaButtton, VisualizarParecer],
            },
            distribuirAcoes: { atual: '', proximo: '', acoes: [Encaminhar] },
            historicoAcoes: { atual: '', proximo: '', acoes: [Historico, VisualizarPlanilhaButtton] },
            CONST: '',
            loadingHistorico: false,
        };
    },

    computed: {
        ...mapGetters({
            dadosTabelaTecnico: 'avaliacaoResultados/dadosTabelaTecnico',
            getProjetosFinalizados: 'avaliacaoResultados/getProjetosFinalizados',
            getProjetosAssinar: 'avaliacaoResultados/getProjetosAssinar',
            getProjetosEmAssinatura: 'avaliacaoResultados/getProjetosEmAssinatura',
            getProjetosHistorico: 'avaliacaoResultados/getProjetosHistorico',
            getProjetosParaDistribuir: 'avaliacaoResultados/getProjetosParaDistribuir',
            getUsuario: 'autenticacao/getUsuario',
            getProjetosAssinarCoordenador: 'avaliacaoResultados/getProjetosAssinarCoordenador',
            getProjetosAssinarCoordenadorGeral: 'avaliacaoResultados/getProjetosAssinarCoordenadorGeral',
            route: 'route',
            getDashboard: 'avaliacaoResultados/getDashboardQuantidade',
        }),
    },

    watch: {
        $route: {
            deep: true,
            handler() {
                this.tabActive = this.$route.meta.tab;
            },
        },
        getDashboard: {
            deep: true,
            handler(val) {
                this.dashboardItens = val.items;
            },
        },
    },

    created() {
        this.CONST = CONST;

        let projetosTecnico = {};
        let projetosFinalizados = {};

        if (
            parseInt(this.getUsuario.grupo_ativo, 10) === parseInt(CONST.PERFIL_COORDENADOR, 10)
            || parseInt(this.getUsuario.grupo_ativo, 10) === parseInt(CONST.PERFIL_COORDENADOR_GERAL, 10)
        ) {
            projetosTecnico = {
                estadoid: 5,
            };

            projetosFinalizados = {
                estadoid: 6,
            };
        } else {
            projetosTecnico = {
                estadoid: 5,
                idAgente: this.getUsuario.usu_codigo,
            };

            projetosFinalizados = {
                estadoid: 6,
                idAgente: this.getUsuario.usu_codigo,
            };
        }

        this.setDashboard();
        this.distribuir();
        this.obterDadosTabelaTecnico(projetosTecnico);
        this.projetosFinalizados(projetosFinalizados);
        this.projetosAssinarCoordenador();
        this.projetosAssinarCoordenadorGeral();
        // this.projetosAssinatura({ estado: 'historico' });


        Vue.set(this.listaAcoesAssinar, 'usuario', this.getUsuario);
        Vue.set(this.listaAcoesCoordenador, 'usuario', this.getUsuario);
        Vue.set(this.listaAcoesAssinarCoordenadorGeral, 'usuario', this.getUsuario);
    },

    methods: {
        ...mapActions({
            obterDadosTabelaTecnico: 'avaliacaoResultados/obterDadosTabelaTecnico',
            projetosFinalizados: 'avaliacaoResultados/projetosFinalizados',
            projetosAssinaturaAction: 'avaliacaoResultados/projetosAssinatura',
            distribuir: 'avaliacaoResultados/projetosParaDistribuir',
            projetosAssinarCoordenador: 'avaliacaoResultados/projetosAssinarCoordenador',
            projetosAssinarCoordenadorGeral: 'avaliacaoResultados/projetosAssinarCoordenadorGeral',
            setDashboard: 'avaliacaoResultados/dashboardQuantidades',
        }),
        r(val) {
            this.$router.push(val);
        },
        dashboard() {
            return (typeof this.getDashboard !== 'undefined'
                && Object.keys(this.getDashboard).length > 0)
                ? this.getDashboard.items
                : {};
        },
        buscarHistorico() {
            this.loadingHistorico = true;
            this.projetosAssinaturaAction({ estado: 'historico' })
                .finally(() => {
                    this.r('/painel/historico');
                    this.loadingHistorico = false;
                });
        },
    },
};
</script>
