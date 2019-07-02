<template>
    <v-card tile>
        <v-toolbar
            card
            dark
            color="primary"
        >
            <v-toolbar-title>
                Justificativa da readequação
            </v-toolbar-title>
        </v-toolbar>
        atageag
        <v-card-text>a
            {{ dadosReadequacao }}
            {{ dadosReadequacao.dsJustificativa }}
        </v-card-text>
    </v-card>
</template>
<script>
import { mapGetters } from 'vuex';

export default {
    name: 'analisar-justificativa',
    computed: {
        ...mapGetters({
            dadosReadequacao: 'readequacao/getReadequacao',
        }),
    },
};
</script>
