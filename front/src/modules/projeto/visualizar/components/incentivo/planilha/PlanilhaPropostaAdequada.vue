<template>
    <div id="planilha-proposta-original">
        <Carregando
            v-if="loading"
            :text="'Procurando planilha'"/>

        <div v-if="Object.keys(planilha).length > 0">

            <div class="right-align">
                <router-link
                    :to="{ name: 'planilhaproposta', params: { idPronac: idPronac }}"
                    class="btn btn-primary">
                    <i class="material-icons left">visibility</i>Planilha Original
                </router-link>
            </div>

            <Planilha :array-planilha="planilha"/>
        </div>
        <div
            v-if="semResposta"
            class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/Carregando';
import Planilha from '@/components/Planilha/Planilha';

export default {
    components: {
        Carregando,
        Planilha,
    },
    data() {
        return {
            loading: true,
            semResposta: false,
            mensagem: '',
            idPronac: this.$route.params.idPronac,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            planilha: 'projeto/planilhaAdequada',
        }),
    },
    watch: {
        dadosProjeto(value) {
            if (typeof value !== 'undefined') {
                this.buscaPlanilhaAdequada(value.idPronac);
            }
        },
        planilha() {
            this.loading = false;
        },
    },
    mounted() {
        this.buscaPlanilhaAdequada(this.dadosProjeto.idPronac);
    },
    methods: {
        ...mapActions({
            buscaPlanilhaAdequada: 'projeto/buscaPlanilhaAdequada',
        }),
    },
};
</script>
