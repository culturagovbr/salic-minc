<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Liberação'"/>
        </div>
        <div v-else-if="Object.keys(dadosLiberacao).length > 0">
            <v-card>
                <v-card-text>
                    <v-container
                        grid-list-md
                        text-xs-left
                    >
                        <v-layout
                            justify-space-around
                            row
                            wrap>
                            <v-flex
                                lg12
                                dark
                                class="text-xs-left">
                                <b><h4>LIBERAÇÃO</h4></b>
                                <v-divider class="pb-2"/>
                            </v-flex>
                            <v-flex>
                                <p><b>Dt. Liberação</b></p>
                                <p>
                                    {{ dadosLiberacao.DtLiberacao | formatarData }}
                                </p>
                            </v-flex>
                            <v-flex>
                                <p><b>Vl. Liberado</b></p>
                                <p>
                                    {{ dadosLiberacao.vlLiberado | filtroFormatarParaReal }}
                                </p>
                            </v-flex>
                            <v-flex>
                                <p><b>Conta liberada por:</b></p>
                                <p>
                                    {{ dadosLiberacao.usu_Nome }}
                                </p>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
            </v-card>
        </div>
        <v-layout v-else>
            <v-container
                grid-list-md
                text-xs-center>
                <v-layout
                    row
                    wrap>
                    <v-flex>
                        <v-card>
                            <v-card-text class="px-0">Nenhuma Liberação encontrada</v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-layout>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'Liberacao',
    components: {
        Carregando,
    },
    mixins: [utils],
    data() {
        return {
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosLiberacao: 'dadosBancarios/liberacao',
        }),
    },
    watch: {
        dadosLiberacao() {
            this.loading = false;
        },
        dadosProjeto(value) {
            this.loading = true;
            this.buscarLiberacao(value.idPronac);
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarLiberacao(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarLiberacao: 'dadosBancarios/buscarLiberacao',
        }),
    },
};
</script>
