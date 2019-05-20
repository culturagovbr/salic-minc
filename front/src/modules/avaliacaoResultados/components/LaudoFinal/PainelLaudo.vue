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
                <v-tabs-slider color="deep-orange accent-3"/>
                <v-tab
                    v-if="getUsuario.grupo_ativo == Const.PERFIL_COORDENADOR_GERAL"
                    href="#tab-0"
                    @click="r('/laudo/aba-em-analise')"
                >
                    <template v-if="Object.keys(getProjetosLaudoFinal).length == 0">
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
                    v-if="getUsuario.grupo_ativo == Const.PERFIL_COORDENADOR_GERAL ||
                        getUsuario.grupo_ativo == Const.PERFIL_DIRETOR ||
                    getUsuario.grupo_ativo == Const.PERFIL_SECRETARIO"
                    href="#tab-1"
                    @click="r('/laudo/assinar')"
                >
                    Assinar
                    <v-icon>done</v-icon>
                </v-tab>
                <v-tab
                    v-if="getUsuario.grupo_ativo == Const.PERFIL_COORDENADOR_GERAL"
                    href="#tab-3"
                    @click="r('/laudo/finalizados')"
                >
                    Finalizados
                    <v-icon>collections_bookmark</v-icon>
                </v-tab>

                <v-tab-item
                    v-if="getUsuario.grupo_ativo == Const.PERFIL_COORDENADOR_GERAL"
                    :value="'tab-0'"
                    :key="0"
                >
                    <Laudo
                        :dados="getProjetosLaudoFinal"
                        :estado="Const.ESTADO_ANALISE_LAUDO"
                    />
                </v-tab-item>
                <v-tab-item
                    v-if="getUsuario.grupo_ativo == Const.PERFIL_COORDENADOR_GERAL ||
                        getUsuario.grupo_ativo == Const.PERFIL_DIRETOR ||
                    getUsuario.grupo_ativo == Const.PERFIL_SECRETARIO"
                    :value="'tab-1'"
                    :key="1"
                >
                    <Laudo
                        :dados="getProjetosLaudoAssinar"
                        :estado="assinarPerfil().toString()"
                    />
                </v-tab-item>
                <v-tab-item
                    v-if="getUsuario.grupo_ativo == Const.PERFIL_COORDENADOR_GERAL"
                    :value="'tab-3'"
                    :key="3"
                >
                    <Laudo
                        :dados="getProjetosLaudoFinalizados"
                        :estado="Const.ESTADO_AVALIACAO_RESULTADOS_FINALIZADA"
                    />
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-container>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import Const from '../../const';
import Laudo from './Laudo';

export default {
    name: 'PainelLaudo',
    components: {
        Laudo,
    },
    data() {
        return {
            tabActive: null,
            Const,
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
        this.obterProjetosLaudoFinal({ estadoId: 10 });
        this.obterProjetosLaudoAssinar({ estadoId: this.assinarPerfil() });
        this.obterProjetosLaudoFinalizados({ estadoId: 12 });
    },
    methods: {
        ...mapActions({
            obterProjetosLaudoFinal: 'avaliacaoResultados/obterProjetosLaudoFinal',
            obterProjetosLaudoAssinar: 'avaliacaoResultados/obterProjetosLaudoAssinar',
            obterProjetosLaudoFinalizados: 'avaliacaoResultados/obterProjetosLaudoFinalizados',
        }),
        assinarPerfil() {
            if (this.getUsuario.grupo_ativo === this.Const.PERFIL_COORDENADOR_GERAL) {
                return this.Const.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_LAUDO;
            }
            if (this.getUsuario.grupo_ativo === this.Const.PERFIL_DIRETOR) {
                return this.Const.ESTADO_AGUARDANDO_ASSINATURA_DIRETOR_LAUDO;
            }
            if (this.getUsuario.grupo_ativo === this.Const.PERFIL_SECRETARIO) {
                return this.Const.ESTADO_AGUARDANDO_ASSINATURA_SECRETARIO_LAUDO;
            }
            return null;
        },
        r(val) {
            this.$router.push(val);
        },
    },
};
</script>
