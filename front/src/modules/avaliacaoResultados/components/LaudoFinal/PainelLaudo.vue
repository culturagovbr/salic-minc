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
                    v-if="getUsuario.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL"
                    href="#tab-0"
                    @click="r('/laudo/aba-em-analise')"
                >
                    <template v-if="Object.keys(getProjetosLaudoFinal).length === 0">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            dark
                        />
                    </template>
                    <template v-else>
                        Em Analise
                        <v-icon>gavel</v-icon>
                    </template>
                </v-tab>
                <v-tab
                    v-if="getUsuario.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL ||
                        getUsuario.grupo_ativo === CONST.PERFIL_DIRETOR ||
                        getUsuario.grupo_ativo === CONST.PERFIL_SECRETARIO"
                    href="#tab-1"
                    @click="r('/laudo/assinar')"
                >
                    Assinar
                    <v-icon>done</v-icon>
                </v-tab>
                <v-tab
                    v-if="getUsuario.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL"
                    href="#tab-3"
                    @click="r('/laudo/finalizados')"
                >
                    Finalizados
                    <v-icon>collections_bookmark</v-icon>
                </v-tab>

                <v-tab-item
                    v-if="getUsuario.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL"
                    :key="0"
                    :value="'tab-0'"
                >
                    <Laudo
                        :dados="getProjetosLaudoFinal"
                        :estado="CONST.ESTADO_ANALISE_LAUDO"
                    />
                </v-tab-item>
                <v-tab-item
                    v-if="getUsuario.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL ||
                        getUsuario.grupo_ativo === CONST.PERFIL_DIRETOR ||
                        getUsuario.grupo_ativo === CONST.PERFIL_SECRETARIO"
                    :key="1"
                    :value="'tab-1'"
                >
                    <Laudo
                        :dados="getProjetosLaudoAssinar"
                        :estado="assinarPerfil().toString()"
                    />
                </v-tab-item>
                <v-tab-item
                    v-if="getUsuario.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL"
                    :key="3"
                    :value="'tab-3'"
                >
                    <Laudo
                        :dados="getProjetosLaudoFinalizados"
                        :estado="CONST.ESTADO_AVALIACAO_RESULTADOS_FINALIZADA"
                    />
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-container>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import CONST from '../../const';
import Laudo from './Laudo';

export default {
    name: 'PainelLaudo',
    components: {
        Laudo,
    },
    data() {
        return {
            tabActive: null,
            CONST,
        };
    },
    computed: {
        ...mapGetters({
            getProjetosLaudoFinal: 'avaliacaoResultados/getProjetosLaudoFinal',
            getProjetosLaudoAssinar: 'avaliacaoResultados/getProjetosLaudoAssinar',
            getProjetosLaudoEmAssinatura: 'avaliacaoResultados/getProjetosLaudoEmAssinatura',
            getProjetosLaudoFinalizados: 'avaliacaoResultados/getProjetosLaudoFinalizados',
            getUsuario: 'autenticacao/getUsuario',
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
        this.obterProjetosLaudoFinal({ estadoId: CONST.ESTADO_ANALISE_LAUDO });
        this.obterProjetosLaudoAssinar({ estadoId: this.assinarPerfil() });
        this.obterProjetosLaudoFinalizados({ estadoId: CONST.ESTADO_AVALIACAO_RESULTADOS_FINALIZADA });
    },
    methods: {
        ...mapActions({
            obterProjetosLaudoFinal: 'avaliacaoResultados/obterProjetosLaudoFinal',
            obterProjetosLaudoAssinar: 'avaliacaoResultados/obterProjetosLaudoAssinar',
            obterProjetosLaudoFinalizados: 'avaliacaoResultados/obterProjetosLaudoFinalizados',
        }),
        assinarPerfil() {
            if (this.getUsuario.grupo_ativo === this.CONST.PERFIL_COORDENADOR_GERAL) {
                return this.CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_LAUDO;
            }
            if (this.getUsuario.grupo_ativo === this.CONST.PERFIL_DIRETOR) {
                return this.CONST.ESTADO_AGUARDANDO_ASSINATURA_DIRETOR_LAUDO;
            }
            if (this.getUsuario.grupo_ativo === this.CONST.PERFIL_SECRETARIO) {
                return this.CONST.ESTADO_AGUARDANDO_ASSINATURA_SECRETARIO_LAUDO;
            }
            return null;
        },
        r(val) {
            this.$router.push(val);
        },
    },
};
</script>
