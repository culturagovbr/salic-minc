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
            Ver extratos
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
                <v-toolbar-title>Extrato bancário</v-toolbar-title>
            </v-toolbar>

            <v-card-text>
                <extratos-bancarios
                    v-if="dialog"
                    :id-pronac="idPronac" />

            </v-card-text>
        </v-card>
    </v-dialog>
</template>
<script>

import ExtratosBancarios from '@/modules/projeto/visualizar/components/dadosBancarios/ExtratosBancarios';

export default {
    name: 'ExtratosBancariosDialog',
    components: { ExtratosBancarios },
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
