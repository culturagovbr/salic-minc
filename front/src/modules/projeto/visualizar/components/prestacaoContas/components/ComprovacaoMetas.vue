<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>COMPROVAÇÃO DE METAS</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.dadosCompMetas"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ props.item.Etapa }}</td>
                    <td class="text-xs-right">
                        {{ props.item.qtFisicaAprovada | tipoExecucaoRound | filtroFormatarQuantidade }}
                    </td>
                    <td class="text-xs-right">
                        {{ props.item.qtFisicaExecutada | tipoExecucaoRound | filtroFormatarQuantidade }}
                    </td>
                    <td class="text-xs-right">{{ props.item.PerFisica | tipoExecucao }} %</td>
                    <td class="text-xs-right">R$ {{ props.item.vlAprovado | filtroFormatarParaReal }}</td>
                    <td class="text-xs-right">R$ {{ props.item.vlExecutado | filtroFormatarParaReal }}</td>
                    <td class="text-xs-right">{{ props.item.PercFinanceiro | tipoExecucao }} %</td>
                    <td class="text-xs-right">R$ {{ props.item.SaldoAExecutar | filtroFormatarParaReal }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'ComprovacaoMetas',
    filters: {
        tipoExecucao(qtFisicaExecutada) {
            return (qtFisicaExecutada).toFixed(2);
        },
        tipoExecucaoRound(qtFisicaExecutada) {
            return Math.round(qtFisicaExecutada);
        },
    },
    mixins: [utils],
    data() {
        return {
            pagination: {
                sortBy: '',
                descending: true,
            },
            headers: [
                {
                    text: 'Etapas',
                    align: 'left',
                    value: 'Etapa',
                },
                {
                    text: 'Aprovada',
                    align: 'right',
                    value: 'qtFisicaAprovada',
                },
                {
                    text: 'Executada',
                    align: 'right',
                    value: 'qtFisicaExecutada',
                },
                {
                    text: '% Executado',
                    align: 'right',
                    value: 'PercFinanceiro',
                },
                {
                    text: 'Aprovada',
                    align: 'right',
                    value: 'vlAprovado',
                },

                {
                    text: 'Executada',
                    align: 'right',
                    value: 'vlExecutado',
                },

                {
                    text: '% Executado',
                    align: 'right',
                    value: 'PerFisica',
                },
                {
                    text: 'Saldo à Executar',
                    align: 'right',
                    value: 'SaldoAExecutar',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/relatorioCumprimentoObjeto',
        }),
    },
};
</script>
