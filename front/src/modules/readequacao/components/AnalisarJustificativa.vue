<template>
    <v-card tile>
        <v-card-text
            v-html="dadosReadequacao.dsJustificativa"
        >
        </v-card-text>
    </v-card>
</template>
<script>
import { mapGetters } from 'vuex';

export default {
    name: 'AnalisarJustificativa',
    computed: {
        ...mapGetters({
            dadosReadequacao: 'readequacao/getReadequacao',
        }),
    },
};
</script>
