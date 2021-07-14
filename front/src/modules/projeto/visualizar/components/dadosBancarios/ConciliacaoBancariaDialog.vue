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
            Ver conciliação
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
                <v-toolbar-title>Conciliação bancária</v-toolbar-title>
            </v-toolbar>

            <v-card-text>
                <conciliacao-bancaria
                    v-if="dialog"
                    :id-pronac="idPronac"
                />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
<script>

import ConciliacaoBancaria from '@/modules/projeto/visualizar/components/dadosBancarios/ConciliacaoBancaria';

export default {
    name: 'ConciliacaoBancariaDialog',
    components: { ConciliacaoBancaria },
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
