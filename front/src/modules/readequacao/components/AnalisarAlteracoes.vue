<template>
    <v-card tile>
        <v-card-text>
            <campo-diff
                :original-text="getDadosCampo.valor"
                :changed-text="dadosReadequacao.dsSolicitacao"
                :method="'diffWordsWithSpace'"
            />
        </v-card-text>
    </v-card>
</template>
<script>
import { mapGetters } from 'vuex';
import CampoDiff from '@/components/CampoDiff';
import MxReadequacao from '../mixins/Readequacao';

export default {
    name: 'AnalisarAlteracoes',
    components: {
        CampoDiff,
    },
    mixins: [
        MxReadequacao,
    ],
    computed: {
        ...mapGetters({
            dadosReadequacao: 'readequacao/getReadequacao',
            campoAtual: 'readequacao/getCampoAtual',
        }),
        getDadosCampo() {
            return this.parseDadosCampo(this.campoAtual);
        },
    },
};
</script>
