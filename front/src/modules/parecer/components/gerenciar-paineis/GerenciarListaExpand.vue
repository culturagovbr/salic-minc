<template>
    <v-card flat>
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
                v-if="produto.nomeParecerista"
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
                    {{ produto.nomeParecerista }}
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
    </v-card>
</template>

<script>

import { utils } from '@/mixins/utils';

export default {
    name: 'GerenciarListaExpand',
    mixins: [utils],

    props: {
        produto: {
            type: Object,
            default: () => {},
        },
    },

};
</script>
