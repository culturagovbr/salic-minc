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
            Ver Receitas e Despesas
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
                <v-toolbar-title>Execução da Receita e Despesa</v-toolbar-title>
            </v-toolbar>

            <v-card-text>
                <execucao-receita-despesa
                    v-if="dialog"
                    :id-pronac="idPronac"
                />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
<script>

import ExecucaoReceitaDespesa from '@/modules/projeto/visualizar/components/prestacaoContas/ExecucaoReceitaDespesa';

export default {
    name: 'ExecucaoReceitaDespesaDialog',
    components: { ExecucaoReceitaDespesa },
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
