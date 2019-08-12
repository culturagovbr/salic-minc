<template>
    <v-layout>
        <template
            v-if="loading"
        >
            <carregando
                :text="'Montando comparação'"
                class="mt-5 pb-4"
            />
        </template>
        <template v-else>
            <campo-diff
                :original-text="original.valor"
                :changed-text="changed.dsSolicitacao"
                :method="'diffWordsWithSpace'"
            />
        </template>
    </v-layout>
</template>
<script>
import Carregando from '@/components/CarregandoVuetify';
import CampoDiff from '@/components/CampoDiff';

export default {
    name: 'ComparacaoTextual',
    components: {
        Carregando,
        CampoDiff,
    },
    props: {
        original: {
            type: Object,
            default: () => {},
        },
        changed: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            loading: true,
        };
    },
    mounted() {
        this.loading = false;
    },
};
</script>
