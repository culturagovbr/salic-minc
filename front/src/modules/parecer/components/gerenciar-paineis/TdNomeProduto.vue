<template>
    <td>
        <v-tooltip bottom>
            <router-link
                slot="activator"
                :to="{
                    name: 'parecer-gerenciar-visualizar-view',
                    params: {
                        id: produto.idProduto,
                        idPronac: produto.idPronac,
                        produtoPrincipal: produto.stPrincipal,
                    }
                }"
                class="subheading font-weight-medium"
                color="primary"
            >
                <v-badge
                    left
                    color="orange darken-4"
                    :value="produto.tipoAnalise === 1"
                >
                    <span slot="badge">
                        <v-icon dark>attach_money</v-icon>
                    </span>
                    {{ produto.nomeProduto }}
                </v-badge>
            </router-link>
            <span>{{ tooltipProduto }}</span>
        </v-tooltip>
        <v-icon
            v-if="produto.tipoAnalise === 1"
            @click="$root.$dialogAjuda(textoAjuda)"
        >
            help
        </v-icon>
    </td>
</template>

<script>

export default {
    name: 'TdNomeProduto',

    props: {
        produto: {
            type: Object,
            default: () => {},
        },
    },

    data: () => ({
        textoAjuda: 'Este produto pertence a outra unidade, foi solicitado análise financeira complementar.',
    }),

    computed: {
        tooltipProduto() {
            if (this.produto.tipoAnalise === 1) {
                return 'Solicitado análise financeira complementar';
            }
            return `Clique para analisar o produto ${this.produto.nomeProduto}`;
        },
    },

};
</script>
