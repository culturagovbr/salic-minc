<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        hide-overlay
        scrollable
    >
        <v-btn
            slot="activator"
        >
            CONSOLIDAÇÃO
        </v-btn>
        <v-card>
            <v-toolbar
                dark
                color="primary"
            >
                <v-btn
                    icon
                    dark
                    @click="dialog = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>Consolidação - {{ nomeProjeto }}</v-toolbar-title>
                <v-spacer />
            </v-toolbar>
            <v-card-text>
                <div
                    v-if="Object.keys(getConsolidacaoAnalise).length > 0"
                    class="mt-3"
                >
                    <v-expansion-panel>
                        <v-expansion-panel-content
                            v-for="(consolidacao, i) in getConsolidacaoAnalise"
                            :key="i"
                        >
                            <v-layout
                                slot="header"
                                class="blue--text"
                            >
                                <v-icon class="mr-3 blue--text">
                                    {{ consolidacao.icon }}
                                </v-icon>
                                <span v-text="consolidacao.title" />
                                <v-spacer />
                            </v-layout>

                            <v-card>
                                <v-card-text>
                                    <v-data-table
                                        :headers="consolidacaoHeaders(consolidacao.cols)"
                                        :items="consolidacao.lines"
                                        hide-actions
                                        no-data-text="Não há dados disponíveis."
                                    >
                                        <template
                                            slot="items"
                                            slot-scope="props"
                                        >
                                            <td
                                                v-for="(celula, i) in props.item"
                                                :key="i"
                                                v-html="celula"
                                            />
                                        </template>
                                    </v-data-table>
                                </v-card-text>
                            </v-card>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </div>
                <Carregando
                    v-else
                    :text="'Carregando dados da consolidação ...'"
                />
            </v-card-text>
            <v-btn
                color="red"
                flat
                @click="dialog = false"
            >
                FECHAR
            </v-btn>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';

export default {
    name: 'ConsolidacaoAnalise',
    components: { Carregando },
    props: {
        idPronac: { type: String, default: '' },
        nomeProjeto: { type: String, default: '' },
    },
    data() {
        return {
            dialog: false,
        };
    },
    computed: {
        ...mapGetters({
            getConsolidacaoAnalise: 'avaliacaoResultados/consolidacaoAnalise',
        }),
    },
    watch: {
        dialog(value) {
            if (value) {
                this.setConsolidacaoAnalise(this.idPronac);
            }
        },
    },
    mounted() {
    },
    methods: {
        ...mapActions({
            setConsolidacaoAnalise: 'avaliacaoResultados/consolidacaoAnalise',
        }),
        consolidacaoHeaders: (cols) => {
            const headerKeys = Object.keys(cols);
            const headers = headerKeys.map(item => (
                {
                    text: cols[item].name,
                    value: item,
                    sortable: false,
                    class: cols[item].class,
                }
            ));
            return headers;
        },
    },
};
</script>
