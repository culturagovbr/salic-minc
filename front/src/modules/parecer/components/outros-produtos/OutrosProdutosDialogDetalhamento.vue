<template>
    <v-dialog
        v-model="dialogDetalhamento"
        row
        justify-center
        @keydown.esc="dialogDetalhamento = false"
    >
        <v-card
            v-if="dialogDetalhamento"
            tile
        >
            <v-toolbar
                card
                dark
                color="primary lighten-1"
            >
                <v-btn
                    icon
                    dark
                    @click="dialogDetalhamento = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>
                    Visualizar análise do Produto
                    <b>"{{ produto.nomeProduto }}"</b>
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <visualizacao-analise-produto :produto="produto" />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import VisualizacaoAnaliseProduto from '@/modules/parecer/components/VisualizacaoAnaliseProduto';

export default {
    name: 'OutrosProdutosDialog',
    components: {
        VisualizacaoAnaliseProduto,
    },
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
            dialogDetalhamento: false,
            loading: true,
        };
    },
    watch: {
        value(val) {
            this.dialogDetalhamento = val;
        },
        dialogDetalhamento(val) {
            this.$emit('input', val);
        },
    },
};
</script>
