<template>
    <v-card tile>
        <v-toolbar
            card
            dark
            color="primary"
        >
            <v-toolbar-title>
                Alterações
            </v-toolbar-title>
        </v-toolbar>
        xxhkojxojxohjxoehjohjxe
        <v-card-text>
            ses-i0909s09g0s9g0es9gs
            {{ dadosReadequacao }}
            {{ dadosReadequacao.dsSolicitacao }}
        </v-card-text>
    </v-card>
</template>
<script>
import { mapGetters } from 'vuex';

export default {
    name: 'analisar-alteracoes',
    computed: {
        ...mapGetters({
            dadosReadequacao: 'readequacao/getReadequacao',
        }),
    },
};
</script>
