<template>
    <div v-if="custosVinculados.length > 0">
        <v-data-table
            :pagination.sync="pagination"
            :headers="headers"
            :items="custosVinculados"
            class="elevation-1 container-fluid"
        >
            <template
                slot="items"
                slot-scope="props">
                <td class="text-xs-left">{{ props.item.Descricao }}</td>
                <td class="text-xs-center pl-5">{{ props.item.dtCadastro | formatarData }}</td>
                <td class="text-xs-right">{{ props.item.Percentual }}</td>
            </template>
        </v-data-table>
    </div>
    <div
        v-else
        class="text-xs-left pa-2">
        Custos vinculados não encontrado
    </div>
</template>

<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'CustosVinculados',
    mixins: [utils],
    props: {
        dadoComplementar: {
            type: String,
            default: '',
        },
        dsDadoComplementar: {
            type: String,
            default: '',
        },
        custosVinculados: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            pagination: {
                sortBy: 'dtCadastro',
                descending: true,
            },
            headers: [
                {
                    text: 'ITEM',
                    align: 'left',
                    value: 'Descricao',
                },
                {
                    text: 'DATA',
                    align: 'center',
                    value: 'dtCadastro',
                },
                {
                    text: 'PERCENTUAL (%)',
                    align: 'right',
                    value: 'Percentual',
                },
            ],
        };
    },
};
</script>
