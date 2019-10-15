<template>
    <v-container
        fluid
    >
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
                    v-if="usuarioGetter.grupo_ativo === CONST.PERFIL_COORDENADOR"
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
                        :loading="Object.keys(getProjetosParaDistribuir).length === 0"
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
                                v-if="(usuarioGetter.grupo_ativo === CONST.PERFIL_COORDENADOR
                                    || usuarioGetter.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL)"
                                :analisar="true"
                                :dados="dadosTabelaTecnico"
                                :componentes="listaAcoesCoordenador"
                                :mostrar-tecnico="true"
                                :loading="Object.keys(dadosTabelaTecnico).length === 0"
                            />
                            <TabelaProjetos
                                v-else
                                :analisar="true"
                                :dados="dadosTabelaTecnico"
                                :componentes="listaAcoesTecnico"
                                :loading="Object.keys(dadosTabelaTecnico).length === 0"
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
                                v-if="(usuarioGetter.grupo_ativo === CONST.PERFIL_COORDENADOR)"
                                :dados="getProjetosAssinarCoordenador"
                                :componentes="listaAcoesAssinarCoordenador"
                                :loading="Object.keys(getProjetosAssinarCoordenador).length === 0"
                            />
                            <TabelaProjetos
                                v-else-if="(usuarioGetter.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL)"
                                :dados="getProjetosAssinarCoordenadorGeral"
                                :componentes="listaAcoesAssinarCoordenadorGeral"
                                :loading="Object.keys(getProjetosAssinarCoordenadorGeral).length === 0"
                            />
                            <TabelaProjetos
                                v-else
                                :dados="getProjetosFinalizados"
                                :componentes="listaAcoesAssinar"
                                :loading="Object.keys(getProjetosFinalizados).length === 0"
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
                                :loading="Object.keys(getProjetosHistorico).length === 0"
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

export default {
    name: 'Painel',
    components: {
        TabelaProjetos,
    },

    data() {
        return {
            tabActive: null,
            projetoAnaliseDados: { code: 300, items: [] },
            listaAcoesTecnico: {
                atual: 0,
                proximo: '',
                acoes: [
                    Diligencias,
                    AnaliseButton,
                    Historico,
                    VisualizarParecer,
                    VisualizarPlanilhaButtton,
                ],
            },
            listaAcoesAssinar: {
                usuario: this.usuarioGetter,
                atual: CONST.ESTADO_PARECER_FINALIZADO,
                proximo: CONST.ESTADO_ANALISE_PARECER,
                idTipoDoAtoAdministrativo: CONST.ATO_ADMINISTRATIVO_PARECER_TECNICO,
                acoes: [
                    Diligencias,
                    AssinarButton,
                    Historico,
                    VisualizarPlanilhaButtton,
                    VisualizarParecer,
                ],
            },
            listaAcoesCoordenador: {
                usuario: this.getUsuario,
                atual: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_PARECER,
                proximo: 0,
                acoes: [
                    Diligencias,
                    Encaminhar,
                    Historico,
                    VisualizarPlanilhaButtton,
                    VisualizarParecer,
                ],
            },
            listaAcoesAssinarCoordenador: {
                usuario: this.usuarioGetter,
                atual: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_PARECER,
                proximo: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_PARECER,
                idTipoDoAtoAdministrativo: CONST.ATO_ADMINISTRATIVO_PARECER_TECNICO,
                acoes: [
                    Diligencias,
                    AssinarButton,
                    Devolver,
                    Historico,
                    VisualizarPlanilhaButtton,
                    VisualizarParecer,
                ],
            },
            listaAcoesAssinarCoordenadorGeral: {
                usuario: this.usuarioGetter,
                atual: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_PARECER,
                proximo: CONST.ESTADO_ANALISE_PARECER,
                idTipoDoAtoAdministrativo: CONST.ATO_ADMINISTRATIVO_PARECER_TECNICO,
                acoes: [
                    Diligencias,
                    AssinarButton,
                    Devolver,
                    Historico,
                    VisualizarPlanilhaButtton,
                    VisualizarParecer,
                ],
            },
            distribuirAcoes: { atual: 0, proximo: 0, acoes: [Encaminhar] },
            historicoAcoes: { atual: 0, proximo: 0, acoes: [Historico, VisualizarPlanilhaButtton] },
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
            getProjetosAssinarCoordenador: 'avaliacaoResultados/getProjetosAssinarCoordenador',
            getProjetosAssinarCoordenadorGeral: 'avaliacaoResultados/getProjetosAssinarCoordenadorGeral',
            usuarioGetter: 'autenticacao/getUsuario',
            route: 'route',
        }),
    },

    watch: {
        $route: {
            deep: true,
            handler() {
                this.tabActive = this.$route.meta.tab;
            },
        },
    },

    created() {
        this.CONST = CONST;
        const idSecretaria = this.usuarioGetter.usu_org_max_superior;
        const isTecnico = this.usuarioGetter.grupo_ativo === CONST.PERFIL_TECNICO;
        const isCoordenador = this.usuarioGetter.grupo_ativo === CONST.PERFIL_COORDENADOR;
        const isCoordenadorGeral = this.usuarioGetter.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL;

        let projetosTecnico = {
            estadoid: CONST.ESTADO_ANALISE_PARECER,
            idAgente: this.usuarioGetter.usu_codigo,
            idSecretaria,
        };

        let whereProjetosAssinatura = {
            estadoid: CONST.ESTADO_PARECER_FINALIZADO,
            idAgente: this.usuarioGetter.usu_codigo,
            idSecretaria,
        };

        if (isCoordenador || isCoordenadorGeral) {
            projetosTecnico = {
                estadoid: CONST.ESTADO_ANALISE_PARECER,
                idSecretaria,
            };

            whereProjetosAssinatura = {
                estadoid: CONST.ESTADO_PARECER_FINALIZADO,
                idSecretaria,
            };
        }
        if (isTecnico) {
            Vue.set(this.listaAcoesAssinar, 'usuario', this.usuarioGetter);
        }

        if (isCoordenador) {
            Vue.set(this.listaAcoesCoordenador, 'usuario', this.usuarioGetter);
            Vue.set(this.listaAcoesAssinarCoordenador, 'usuario', this.usuarioGetter);
            this.distribuir();
            this.projetosAssinarCoordenador({
                estadoid: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_PARECER,
                idSecretaria,
            });
        }

        if (isCoordenadorGeral) {
            this.distribuir();
            this.projetosAssinarCoordenadorGeral({
                estadoid: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_PARECER,
                idSecretaria,
            });
            Vue.set(this.listaAcoesAssinarCoordenadorGeral, 'usuario', this.usuarioGetter);
        }

        this.obterDadosTabelaTecnico(projetosTecnico);
        this.buscarProjetosAssinaturaAction(whereProjetosAssinatura);
    },

    methods: {
        ...mapActions({
            obterDadosTabelaTecnico: 'avaliacaoResultados/obterDadosTabelaTecnico',
            buscarProjetosAssinaturaAction: 'avaliacaoResultados/projetosFinalizados',
            projetosAssinaturaAction: 'avaliacaoResultados/projetosAssinatura',
            distribuir: 'avaliacaoResultados/projetosParaDistribuir',
            projetosAssinarCoordenador: 'avaliacaoResultados/projetosAssinarCoordenador',
            projetosAssinarCoordenadorGeral: 'avaliacaoResultados/projetosAssinarCoordenadorGeral',
        }),
        r(val) {
            this.$router.push(val);
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
