<template>
    <div id="planilha-homologada">
        <Carregando v-if="loading" :text="'Procurando planilha'"></Carregando>
        <Planilha v-if="Object.keys(planilha).length > 0" :arrayPlanilha="planilha">
            <template slot-scope="slotProps">
                <PlanilhaItensHomologados :table="slotProps.itens"></PlanilhaItensHomologados>
            </template>
        </Planilha>
        <div v-if="semResposta" class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
    import Carregando from '@/components/Carregando';
    import Planilha from '@/components/Planilha/Planilha';
    import PlanilhaItensHomologados from '@/components/Planilha/PlanilhaItensHomologados';

    import { mapGetters } from 'vuex';

    export default {
        name: 'PlanilhaPropostaHomologada',
        data() {
            return {
                planilha: [],
                loading: true,
                semResposta: false,
                mensagem: '',
            };
        },
        components: {
            Carregando,
            Planilha,
            PlanilhaItensHomologados,
        },
        mounted() {
            if (typeof this.dadosProjeto !== 'undefined') {
                this.fetch(this.dadosProjeto.idPronac);
            }
        },
        watch: {
            dadosProjeto(value) {
                if (typeof value !== 'undefined') {
                    this.fetch(value.idPronac);
                }
            },
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
            }),
        },
        methods: {
            fetch(id) {
                if (typeof id === 'undefined') {
                    return;
                }

                const self = this;
                // eslint-disable-next-line
                $3
                    .ajax({
                        url: '/projeto/orcamento/obter-planilha-homologada-ajax/',
                        data: {
                            idPronac: id,
                        },
                    })
                    .done((response) => {
                        self.planilha = response.data;
                    })
                    .fail((response) => {
                        self.semResposta = true;
                        self.mensagem = response.responseJSON.msg;
                    })
                    .always(() => {
                        self.loading = false;
                    });
            },
        },
    };
</script>
