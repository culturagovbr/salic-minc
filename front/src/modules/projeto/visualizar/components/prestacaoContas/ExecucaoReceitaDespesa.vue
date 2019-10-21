<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Execução da receita e despesa'" />
        </div>
        <div v-else>
            <ExecucaoReceita :valor-receita-total="valorReceitaTotal" />
            <ExecucaoDespesa :valor-despesa-total="valorDespesaTotal" />
            <v-expansion-panel
                v-model="panel"
                popout
                focusable
                expand
            >
                <v-expansion-panel-content
                    class="elevation-1"
                >
                    <v-card>
                        <v-container fluid>
                            <v-layout
                                row
                                wrap
                            >
                                <v-flex xs6>
                                    <h3 class="subheading mr-3">
                                        VALOR DO SALDO
                                    </h3>
                                </v-flex>
                                <v-flex
                                    xs5
                                    offset-xs1
                                    class="text-xs-right"
                                >
                                    <h3 class="subheading">
                                        <v-chip
                                            outline
                                            color="black"
                                        >
                                            R$ {{ total | filtroFormatarParaReal }}
                                        </v-chip>
                                    </h3>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </div>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';
import ExecucaoReceita from './components/ExecucaoReceita';
import ExecucaoDespesa from './components/ExecucaoDespesa';

export default {
    name: 'ExecucaoReceitaDespesa',
    components: {
        Carregando,
        ExecucaoReceita,
        ExecucaoDespesa,
    },
    mixins: [utils],

    props: {
        idPronac: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            panel: [true],
            pagination: {
                sortBy: '',
                descending: true,
            },
            loading: true,
        };
    },

    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/execucaoReceitaDespesa',
        }),
        valorReceitaTotal() {
            if (Object.keys(this.dados).length === 0) {
                return 0;
            }
            const table = this.dados.relatorioExecucaoReceita;
            let soma = 0;

            Object.entries(table).forEach(([, row]) => {
                soma += parseFloat(row.vlIncentivado);
            });

            return soma;
        },
        valorDespesaTotal() {
            if (Object.keys(this.dados).length === 0) {
                return 0;
            }
            const table = this.dados.relatorioExecucaoDespesa;
            let soma = 0;

            Object.entries(table).forEach(([, row]) => {
                soma += parseFloat(row.vlPagamento);
            });

            return soma;
        },
        total() {
            return this.valorReceitaTotal - this.valorDespesaTotal;
        },
    },

    watch: {
        idPronac(value) {
            this.buscarExecucaoReceitaDespesa(value);
        },
    },

    mounted() {
        this.buscarExecucaoReceitaDespesa(this.idPronac);
    },

    methods: {
        ...mapActions({
            buscarExecucaoReceitaDespesaAction: 'prestacaoContas/buscarExecucaoReceitaDespesa',
        }),
        buscarExecucaoReceitaDespesa(param) {
            this.loading = true;
            this.buscarExecucaoReceitaDespesaAction(param)
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>
