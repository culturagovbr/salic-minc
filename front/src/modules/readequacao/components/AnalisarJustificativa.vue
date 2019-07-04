<template>
    <v-card tile>
        <h3>Justificativa da solicitação</h3>
        <v-card-text
            v-html="dadosReadequacao.dsJustificativa"
        />
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
