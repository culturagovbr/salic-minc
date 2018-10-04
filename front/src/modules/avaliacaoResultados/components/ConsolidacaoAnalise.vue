<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        hide-overlay
        scrollable
    >
        <v-btn slot="activator" color="success">CONSOLIDAÇÃO</v-btn>
        <v-card>
            <v-card-text>
                <h2>{{ nomeProjeto }}</h2>
                <div class="mt-3">
                    <v-expansion-panel>
                        <v-expansion-panel-content
                            v-for="(consolidacao, i) in getConsolidacaoAnalise"
                            :key="i"
                        >
                            <div slot="header" v-text="consolidacao.title"></div>
                            <v-card>
                                <v-card-text>
                                    <table>
                                        <tr>
                                            <th>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td>
                                            </td>
                                        </tr>

                                    </table>
                                </v-card-text>
                            </v-card>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </div>
            </v-card-text>
            <v-btn color="red" @click="dialog = false" flat>FECHAR</v-btn>
        </v-card>
    </v-dialog>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';

    export default {
        name: 'ConsolidacaoAnalise',
        data() {
            return {
                dialog: false,
            };
        },
        props: {
            idPronac: String,
            nomeProjeto: String,
        },
        computed: {
            ...mapGetters({
                getConsolidacaoAnalise: 'avaliacaoResultados/consolidacaoAnalise',
            }),
        },
        mounted() {
            this.setConsolidacaoAnalise(this.idPronac);
        },
        methods: {
            ...mapActions({
                setConsolidacaoAnalise: 'avaliacaoResultados/consolidacaoAnalise',
            }),
        },
    };
</script>
