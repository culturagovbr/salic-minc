<template>
    <v-container fluid>
        <v-card>
            <v-tabs
                centered
                color="green"
                dark
                icons-and-text
            >
                <v-tabs-slider color="deep-orange accent-3"></v-tabs-slider>
                <v-tab href="#tab-0">
                    <template v-if="Object.keys(getProjetosLaudoFinal).length == 0">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            dark
                        ></v-progress-circular>
                    </template>
                    <template v-else>
                        Em Analise
                        <v-icon>how_to_reg</v-icon>
                    </template>
                </v-tab>
                <v-tab href="#tab-1">
                     Assinar
                    <v-icon>done</v-icon>
                </v-tab>
                <v-tab href="#tab-2">
                     Em assinatura
                    <v-icon>done_all</v-icon>
                </v-tab>

                <v-tab-item
                    :id="'tab-0'"
                    :key="0"
                >
                    <v-card flat
                        v-if="getProjetosLaudoFinal"
                    >
                        <Laudo :analisar="true"
                               :dados="getProjetosLaudoFinal">
                        </Laudo>
                    </v-card>
                </v-tab-item>
                <v-tab-item
                    :id="'tab-1'"
                    :key="1"
                >
                    <v-card flat>
                        <v-card-text>
                            <!-- <TabelaProjetos
                                :dados="getProjetosFinalizados"
                                :componentes="listaAcoesAssinar"
                            ></TabelaProjetos> -->
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item
                    :id="'tab-2'"
                    :key="2"
                >
                    <v-card flat>
                        <v-card-text>
                            <!-- <TabelaProjetos
                                :dados="getLaudosEmAssinatura"
                                :componentes="listaAcoesTecnico"
                            ></TabelaProjetos> -->
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-container>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import Laudo from './Laudo';

export default {
    name: 'PainelLaudo',
    created() {
        this.projetosAssinatura({ estado: 'assinar' });
        this.projetosAssinatura({ estado: 'em_assinatura' });
        this.projetosAssinatura({ estado: 'historico' });

    },
    // watch: {
    //     getUsuario(val) {
    //         if (Object.keys(val).length > 0 && val.usu_codigo != 0 ) {

    //             let projetosTecnico = {};
    //             let projetosFinalizados = {};
    //             if (this.getUsuario.grupo_ativo == 125) {
    //                 projetosTecnico = {
    //                     estadoid: 5,
    //                 };

    //                 projetosFinalizados = {
    //                     estadoid: 6,
    //                 };
    //             } else {
    //                 projetosTecnico = {
    //                     estadoid: 5,
    //                     idAgente: this.getUsuario.usu_codigo,
    //                 };

    //                 projetosFinalizados = {
    //                     estadoid: 6,
    //                     idAgente: this.getUsuario.usu_codigo,
    //                 };
    //             }

    //             this.obterDadosTabelaTecnico(projetosTecnico);
    //         }
    //     },
    // },
    data() {
        return { };
    },
    components: {
        Laudo,
    },
    methods: {
        ...mapActions({
            obterProjetosLaudoFinal: 'avaliacaoResultados/obterProjetosLaudoFinal',
            projetosAssinatura: 'avaliacaoResultados/projetosAssinatura',
            usuarioLogado: 'autenticacao/usuarioLogado',
        }),
    },
    computed: {
        ...mapGetters({
            getProjetosLaudoFinal: 'avaliacaoResultados/getProjetosLaudoFinal',
            getLaudosAssinar: 'avaliacaoResultados/getLaudosAssinar',
            getLaudosEmAssinatura: 'avaliacaoResultados/getLaudosEmAssinatura',
            getUsuario: 'autenticacao/getUsuario',
        }),
    },
};
</script>