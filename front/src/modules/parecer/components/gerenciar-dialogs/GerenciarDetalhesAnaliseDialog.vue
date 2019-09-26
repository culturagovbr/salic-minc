<template>
    <v-dialog
        v-model="dialog"
        max-width="700px"
    >
        <v-card>
            <v-card-title>
                <span class="headline">Detalhes da avaliação</span>
            </v-card-title>
            <v-card-text>
                <v-layout
                    row
                    wrap
                    class="mb-3"
                >
                    <v-flex
                        xs12
                        sm2
                        md2
                    >
                        <b>Pronac</b><br>
                        {{ produto.pronac }}
                    </v-flex>
                    <v-flex
                        xs12
                        sm6
                        md6
                    >
                        <b>Nome do Projeto</b><br>
                        <span v-html="produto.nomeProjeto" />
                    </v-flex>
                    <v-flex
                        xs12
                        sm4
                        md4
                    >
                        <b>Produto</b><br>
                        <span v-html="produto.nomeProduto" />
                    </v-flex>
                </v-layout>
                <v-layout
                    row
                    wrap
                    class="mb-3"
                >
                    <v-flex
                        xs12
                        sm2
                        md2
                    >
                        <b>Área</b><br>
                        {{ produto.area }}
                    </v-flex>
                    <v-flex
                        xs12
                        sm6
                        md6
                    >
                        <b>Segmento</b><br>
                        <span v-html="produto.segmento" />
                    </v-flex>
                    <v-flex
                        xs12
                        sm4
                        md4
                    >
                        <b>Valor do Produto</b><br>
                        R$ {{ produto.valor | formatarParaReal }}
                    </v-flex>
                </v-layout>
                <v-layout
                    v-if="produto.parecerista"
                    row
                    wrap
                    class="mb-3"
                >
                    <v-flex
                        xs12
                        sm4
                        md4
                    >
                        <b>Parecerista</b><br>
                        {{ produto.parecerista }}
                    </v-flex>
                    <v-flex
                        v-if="produto.tempoTotalAnalise"
                        xs12
                        sm4
                        md4
                    >
                        <b>Tempo total de análise</b><br>
                        {{ produto.tempoTotalAnalise }}
                    </v-flex>
                    <v-flex
                        v-if="produto.tempoParecerista"
                        xs12
                        sm4
                        md4
                    >
                        <b>Tempo total com parecerista</b><br>
                        {{ produto.tempoParecerista }}
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="blue darken-1"
                    flat
                    @click="dialog = false"
                >
                    Fechar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>

import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'GerenciarDistribuirProdutoDialog',
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
        filtro: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            dialog: false,
        };
    },

    computed: {
        ...mapGetters({
            pareceristas: 'parecer/getPareceristas',
            vinculadas: 'parecer/getVinculadas',
        }),
    },

    watch: {
        $route() {
            this.dialog = false;
        },
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            this.$emit('input', val);
        },
    },

    mounted() {
        this.dialog = this.value;
    },
};
</script>
