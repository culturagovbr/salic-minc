<template>
    <div id="planilha-proposta-original">
        <Carregando v-if="loading" :text="'Procurando planilha'"></Carregando>

        <div v-if="Object.keys(planilha).length > 0">

            <div class="right-align">
                <router-link :to="{ name: 'planilhaproposta', params: { idPronac: idPronac }}"
                             class="btn btn-primary">
                    <i class="material-icons left">visibility</i>Planilha Original
                </router-link>
            </div>

            <Planilha :arrayPlanilha="planilha"></Planilha>
        </div>
        <div v-if="semResposta" class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
    import Carregando from '@/components/Carregando';
    import Planilha from '@/components/Planilha/Planilha';
    import { mapActions, mapGetters } from 'vuex';

    export default {
        data() {
            return {
                loading: true,
                semResposta: false,
                mensagem: '',
                idPronac: this.$route.params.idPronac,
            };
        },
        components: {
            Carregando,
            Planilha,
        },
        mounted() {
            this.buscaPlanilhaAdequada(this.dadosProjeto.idPreProjeto);
        },
        watch: {
            dadosProjeto(value) {
                if (typeof value !== 'undefined') {
                    this.buscaPlanilhaAdequada(value.idPreProjeto);
                }
            },
            planilha() {
                this.loading = false;
            },
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
                planilha: 'projeto/planilhaAdequada',
            }),
        },
        methods: {
            ...mapActions({
                buscaPlanilhaAdequada: 'projeto/buscaPlanilhaAdequada',
            }),
        },
    };
</script>
