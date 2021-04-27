<template>
    <v-container grid-list-xl>
        <v-form ref="form">
            <v-dialog
                v-model="dialog"
                fullscreen
                hide-overlay
                transition="dialog-bottom-transition">
                <v-card>
                    <v-toolbar
                        dark
                        color="primary">
                        <v-btn
                            :to="{ name: 'Laudo' }"
                            icon
                            dark>
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title>Avaliação de Resultados - Visualizar Laudo</v-toolbar-title>
                    </v-toolbar>
                    <v-container grid-list-sm>
                        <v-layout
                            row
                            wrap>
                            <v-flex
                                xs12
                                sm12
                                md12>
                                <p><b>Projeto:</b> {{ projeto.AnoProjeto }}{{ projeto.Sequencial }} - {{ projeto.NomeProjeto }}</p>
                            </v-flex>
                            <v-flex
                                v-if="proponente.CgcCpf || proponente.Nome"
                                xs12
                                sm12
                                md12>
                                <p><b>Proponente:</b> {{ proponente.CgcCpf | cnpjFilter }} - {{ proponente.Nome }}</p>
                            </v-flex>
                        </v-layout>
                        <v-divider/>
                    </v-container>
                    <v-container
                        v-if="parecerLaudoFinal.items"
                        grid-list-sm>
                        <v-layout
                            wrap
                            align-center>
                            <v-flex
                                xs12
                                sm12
                                md12 >
                                <div>
                                    <p v-if="parecerLaudoFinal.items.siManifestacao == 'A'"><b>Manifestação: </b> Aprovado</p>
                                    <p v-else-if="parecerLaudoFinal.items.siManifestacao == 'P'"><b>Manifestação: </b> Aprovado com ressalva</p>
                                    <p v-else-if="parecerLaudoFinal.items.siManifestacao == 'R'"><b>Manifestação: </b> Reprovado</p>
                                </div>
                            </v-flex>
                            <v-flex
                                xs12
                                sm12
                                md12 >
                                <div>
                                    <h4>Parecer: </h4>
                                    <p v-html="parecerLaudoFinal.items.dsLaudoFinal"/>
                                </div>
                            </v-flex>
                        </v-layout>
                        <v-divider/>
                    </v-container>
                </v-card>
            </v-dialog>
        </v-form>
    </v-container>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import cnpjFilter from '@/filters/cnpj';

export default {
    filters: {
        cnpjFilter,
    },
    data() {
        return {
            dialog: true,
        };
    },

    computed: {
        ...mapGetters({
            proponente: 'avaliacaoResultados/proponente',
            projeto: 'avaliacaoResultados/projeto',
            parecerLaudoFinal: 'avaliacaoResultados/getParecerLaudoFinal',
        }),
    },

    mounted() {
        this.requestEmissaoParecer(this.$route.params.id);
        this.getLaudoFinal(this.$route.params.id);
    },

    methods: {
        ...mapActions({
            requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
            getLaudoFinal: 'avaliacaoResultados/getLaudoFinal',
        }),
    },


};
</script>
