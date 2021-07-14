<template>
    <v-dialog
        v-model="dialog"
        transition="dialog-bottom-transition"
        scrollable
        fullscreen
    >
        <v-btn
            slot="activator"
        >
            Ver pagamentos
        </v-btn>

        <v-card>
            <v-toolbar
                dark
                color="#0a420e !important"
            >
                <v-btn
                    icon
                    dark
                    @click="dialog = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>Relação de pagamento</v-toolbar-title>
            </v-toolbar>

            <v-card-text>
                <relacao-pagamento
                    v-if="dialog"
                    :id-pronac="idPronac" />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
<script>

import RelacaoPagamento from '@/modules/projeto/visualizar/components/prestacaoContas/RelacaoPagamento';

export default {
    name: 'RelacaoPagamentoDialog',
    components: { RelacaoPagamento },
    props: {
        idPronac: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            dialog: false,
        };
    },
};
</script>
