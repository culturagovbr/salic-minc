<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Recursos'"/>
        </div>
        <div v-else-if="Object.keys(fasesRecurso).length > 0">
            <template>
                <div
                    v-for="(recurso, index) in fasesRecurso"
                    :key="index">
                    <v-layout
                        class="ml-4"
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg12
                            dark>
                            <b><h4>FASE {{ index | labelFase }}</h4></b>
                            <v-divider class="pb-2"/>
                        </v-flex>
                    </v-layout>
                    <v-expansion-panel
                        class="mb-4"
                        popout
                        focusable>
                        <v-expansion-panel-content
                            v-for="(item, index) in recurso"
                            :key="index"
                            class="elevation-1">
                            <v-layout
                                slot="header"
                                class="primary--text">
                                <v-icon class="mr-3 primary--text">assignment</v-icon>
                                <span v-html="item.dadosRecurso.tpRecursoDesc"/>
                            </v-layout>
                            <desistencia-recursal
                                v-if="item.desistenciaRecurso === true"
                                :dados-recurso="item.dadosRecurso"/>
                            <component
                                v-else
                                :is="`conteudo-recurso-fase-${item.dadosRecurso.siFaseProjeto}`"
                                :recurso="item"/>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </div>
            </template>
        </div>
        <div v-else>
            <v-container
                grid-list-md
                text-xs-center>
                <v-layout
                    row
                    wrap>
                    <v-flex>
                        <v-card>
                            <v-card-text>Nenhum Recurso encontrado</v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </div>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';
import DesistenciaRecursal from './components/DesistenciaRecursal';
import ConteudoRecursoFase1 from './components/ConteudoRecursoFase1';
import ConteudoRecursoFase2 from './components/ConteudoRecursoFase2';

export default {
    name: 'Recurso',
    components: {
        ConteudoRecursoFase1,
        ConteudoRecursoFase2,
        DesistenciaRecursal,
        Carregando,
    },
    filters: {
        labelFase(fase) {
            let label = '';
            switch (fase) {
            case '1':
                label = '1 - Admissão da Proposta';
                break;
            case '2':
                label = '2 - Aprovação / Homologação da Execução';
                break;
            case '3':
                label = '3 - Avaliação de Resultados';
                break;
            default:
                label = '';
            }
            return label;
        },
    },
    mixins: [utils],
    data() {
        return {
            fasesRecurso: {},
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'analise/recurso',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.buscarRecurso(value.idPronac);
        },
        dados() {
            this.loading = false;
            this.fasesRecurso = this.obterFasesRecursos();
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarRecurso(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarRecurso: 'analise/buscarRecurso',
        }),
        obterFasesRecursos() {
            const Recursos = this.dados;
            const tiposRecurso = {};

            Object.keys(Recursos).forEach((indice) => {
                const recurso = Recursos[indice];
                const { dadosRecurso } = Recursos[indice];
                const { siFaseProjeto } = dadosRecurso;

                if (tiposRecurso[`${siFaseProjeto}`] == null) {
                    tiposRecurso[`${siFaseProjeto}`] = [];
                }
                tiposRecurso[`${siFaseProjeto}`].push(recurso);
            });

            return tiposRecurso;
        },
    },
};
</script>
