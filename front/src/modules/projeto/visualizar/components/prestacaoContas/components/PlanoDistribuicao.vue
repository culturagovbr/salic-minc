<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>PLANO DE DISTRIBUIÇÃO</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.planoDistribuicao"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ props.item.Produto }}</td>
                    <td class="text-xs-right">{{ props.item.QtdePatrocinador | filtroFormatarQuantidade }}</td>
                    <td class="text-xs-right">{{ props.item.QtdeProponente | filtroFormatarQuantidade }}</td>
                    <td class="text-xs-right">{{ props.item.QtdeOutros| filtroFormatarQuantidade }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'PlanoDistribuicao',
    mixins: [utils],
    data() {
        return {
            pagination: {
                sortBy: '',
                descending: true,
            },
            headers: [
                {
                    text: 'Produto',
                    align: 'left',
                    value: 'Produto',
                },
                {
                    text: 'Patrocionador',
                    align: 'right',
                    value: 'QtdePatrocinador',
                },
                {
                    text: 'Divulgação',
                    align: 'right',
                    value: 'QtdeProponente',
                },
                {
                    text: 'Beneficiários',
                    align: 'right',
                    value: 'QtdeOutros',
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
