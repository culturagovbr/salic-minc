<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>PLANO DE DIVULGAÇÃO</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.planoDeDivulgacao"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ props.item.Peca }}</td>
                    <td class="text-xs-left">{{ props.item.Veiculo }}</td>
                    <td class="text-xs-left">{{ props.item.siPlanoDeDivulgacao | tipoPlanoDivulgacao }}</td>
                    <td
                        v-if="props.item.nmArquivo"
                        class="text-xs-left">
                        <v-btn
                            :href="`/upload/abrir?id=${props.item.idArquivo}`"
                            target="_blank"
                            style="text-decoration: none"
                            round
                            small
                        >
                            {{ props.item.nmArquivo }}
                        </v-btn>
                    </td>
                    <td v-else> - </td>
                    <td class="text-xs-center pl-5">{{ props.item.dtEnvio | formatarData }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'PlanoDivulgacao',
    filters: {
        tipoPlanoDivulgacao(siPlanoDeDivulgacao) {
            let planoDeDivulgacao = '';
            switch (siPlanoDeDivulgacao) {
            case '0':
                planoDeDivulgacao = 'Sem Informação';
                break;
            case '1':
                planoDeDivulgacao = 'Não Realizado';
                break;
            case '2':
                planoDeDivulgacao = 'Realizado';
                break;
            case '3':
                planoDeDivulgacao = 'Realizado com outras fontes';
                break;
            default:
                planoDeDivulgacao = '';
            }
            return planoDeDivulgacao;
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
                    text: 'Peça',
                    align: 'left',
                    value: 'Peca',
                },
                {
                    text: 'Veículo',
                    align: 'left',
                    value: 'Veiculo',
                },
                {
                    text: 'Realizado',
                    align: 'left',
                    value: 'qtFisicaExecutada',
                },
                {
                    text: 'Arquivo',
                    align: 'left',
                    value: 'nmArquivo',
                },
                {
                    text: 'Data da Anexação',
                    align: 'center',
                    value: 'dtEnvio',
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
