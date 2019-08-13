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
                <div>{{ localItem.Unidade }}</div>
            </v-flex>
            <v-flex
                xs12
                md1
            >
                <b>Dias</b>
                <div>{{ localItem.QtdeDias }}</div>
            </v-flex>
            <v-flex
                xs12
                md1
            >
                <b>Qtd.</b>
                <div>{{ localItem.Quantidade }}</div>
            </v-flex>
            <v-flex
                xs12
                md2
            >
                <b>Ocorrência</b>
                <div>{{ localItem.Ocorrencia }}</div>
            </v-flex>
            <v-flex
                xs12
                md3
            >
                <b>Vl. Unitário (R$)</b>
                <div>{{ localItem.vlUnitario | filtroFormatarParaReal }}</div>
            </v-flex>
            <v-flex
                xs12
                md4
            >
                <b>Vl. Total (R$)</b>
                <div>{{ localItem.vlAprovado | filtroFormatarParaReal }}</div>
            </v-flex>
            <v-flex
                xs12
                md4
            >
                <b>Vl. Comprovado (R$)</b>
                <div>{{ localItem.vlComprovado | filtroFormatarParaReal }}</div>
            </v-flex>
            <v-flex
                v-if="localItem.dsJustificativa !== ''"
                xs12
                md12
            >
                <b>Justificativa</b>
                <div
                    v-html="localItem.dsJustificativa"
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
        original: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            localItem: {
                Unidade: '',
                idUnidade: '',
                Ocorrencia: '',
                Quantidade: '',
                QtdeDias: '',
                vlUnitario: '',
                vlAprovado: '',
                vlComprovado: '',
                dsJustificativa: '',
            },
        };
    },
    mounted() {
        this.localItem = JSON.parse(JSON.stringify(this.item));
        if (this.original === true) {
            this.localItem.idUnidade = this.item.idUnidadeAtivo;
            this.localItem.Ocorrencia = this.item.OcorrenciaAtivo;
            this.localItem.Quantidade = this.item.QuantidadeAtivo;
            this.localItem.QtdeDias = this.item.QtdeDiasAtivo;
            this.localItem.vlUnitario = this.item.vlUnitarioAtivo;
            this.localItem.vlAprovado = this.localItem.Ocorrencia * this.localItem.Quantidade * this.localItem.vlUnitario;
            this.localItem.dsJustificativa = '';
        }
    },
};
</script>
