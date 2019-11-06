<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        hide-overlay
        transition="dialog-bottom-transition"
        scrollable
    >
        <v-card
            tile
        >
            <v-toolbar
                card
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
                <v-toolbar-title>
                    Histórico: {{ produto.nomeProduto }} - ({{ produto.pronac }}) {{ produto.nomeProjeto }}
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <s-carregando
                    v-if="loading"
                    text="Carregando histórico do produto"
                />
                <div v-else>
                    <v-data-table
                        :headers="headers"
                        :items="historico"
                        :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                        disable-initial-sort
                        class="elevation-0"
                        item-key="idDistribuirParecer"
                    >
                        <template
                            slot="items"
                            slot-scope="props"
                        >
                            <td v-html="props.item.dsProduto" />
                            <td v-html="props.item.Unidade" />
                            <td>{{ props.item.DtDistribuicao | formatarData }}</td>
                            <td v-html="props.item.Observacao" />
                            <td v-html="props.item.nmUsuario" />
                            <td v-html="props.item.nmParecerista" />
                        </template>
                    </v-data-table>
                </div>
            </v-card-text>
            <v-card-actions class="justify-center">
                <v-btn
                    @click="dialog = false"
                >
                    <v-icon left>
                        clear
                    </v-icon>
                    Fechar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import SCarregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'HistoricoProdutoDialog',
    components: {
        SCarregando,
    },
    mixins: [utils],

    props: {
        value: {
            type: Boolean,
            default: false,
        },
        produto: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            dialog: false,
            loading: true,
            headers: [
                { text: 'Nome do Produto', value: 'dsProduto' },
                { text: 'Unidade Responsável', value: 'Unidade' },
                { text: 'Data', value: 'DtDistribuicao' },
                { text: 'Observações', value: 'Observacao' },
                { text: 'Nome do Remetente', value: 'nmUsuario' },
                { text: 'Nome do Parecerista', value: 'nmParecerista' },
            ],
        };
    },

    computed: {
        ...mapGetters({
            historico: 'parecer/getHistoricoProduto',
        }),
    },

    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            if (val) {
                this.obterHistoricoProduto({
                    idProduto: this.produto.idProduto,
                    idPronac: this.produto.idPronac,
                    stPrincipal: this.produto.stPrincipal,
                });
            }
            this.$emit('input', val);
        },
        historico(val) {
            this.loading = val.length === 0;
        },
    },

    mounted() {
        this.dialog = this.value;
    },

    methods: {
        ...mapActions({
            obterHistoricoProduto: 'parecer/obterHistoricoProduto',
        }),
    },
};
</script>
