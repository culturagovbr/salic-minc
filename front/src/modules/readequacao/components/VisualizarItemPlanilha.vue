<template>
    <v-container
        grid-list-md
    >
        <slot name="header"/>
        <v-layout row>
            <v-flex
                xs12
                md2
            >
                <b>Unidade</b>
                <div>{{ item.Unidade }}</div>
            </v-flex>
            <v-flex
                xs12
                md1
            >
                <b>Dias</b>
                <div>{{ item.QtdeDias }}</div>
            </v-flex>
            <v-flex
                xs12
                md1
            >
                <b>Qtd.</b>
                <div>{{ item.Quantidade }}</div>
            </v-flex>
            <v-flex
                xs12
                md2
            >
                <b>Ocorrência</b>
                <div>{{ item.Ocorrencia }}</div>
            </v-flex>
            <v-flex
                xs12
                md3
            >
                <b>Vl. Unitário (R$)</b>
                <div>{{ item.vlUnitario | filtroFormatarParaReal }}</div>
            </v-flex>
            <v-flex
                xs12
                md4
            >
                <b>Vl. Total (R$)</b>
                <div>{{ item.vlAprovado | filtroFormatarParaReal }}</div>
            </v-flex>
            <v-flex
                xs12
                md4
            >
                <b>Vl. Comprovado (R$)</b>
                <div>{{ item.vlComprovado | filtroFormatarParaReal }}</div>
            </v-flex>
            <v-flex
                xs12
                md12
            >
                <b>Justificativa</b>
                <div
                    v-html="item.dsJustificativa"
                />
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'VisualizarItemPlanilha',
    mixins: [
        utils,
    ],
    props: {
        item: {
            type: [Array, Object],
            default: () => {},
        },
    },
};
</script>
