<template>
    <v-container
        fluid
        class="pa-0">
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
                sm6
                md6
            >
                <b>Parecerista</b><br>
                <span v-html="produto.nomeParecerista" />
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>

import { utils } from '@/mixins/utils';

export default {
    name: 'DialogHeader',
    mixins: [utils],

    props: {
        produto: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
