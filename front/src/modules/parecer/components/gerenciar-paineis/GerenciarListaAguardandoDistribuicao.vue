<template>
    <v-data-table
        :headers="headers"
        :items="produtos"
        :rows-per-page-items="[15, 35, 50, {'text': 'Todos', value: -1}]"
        :search="search"
        item-key="idDistribuirParecer"
        class="elevation-1"
        disable-initial-sort
    >
        <template
            slot="items"
            slot-scope="props"
        >
            <tr :class="mxObterClasseItem(props.item)">
                <td-numero-pronac :produto="props.item" />
                <td>{{ props.item.nomeProjeto }}</td>
                <td-nome-produto :produto="props.item" />
                <td-tipo-produto :produto="props.item" />
                <td>{{ props.item.segmento }}</td>
                <td class="text-xs-right">
                    {{ props.item.valor | filtroFormatarParaReal }}
                </td>
                <td class="text-xs-right">
                    {{ props.item.dtEnvioMincVinculada | formatarData }}
                </td>
                <td class="text-xs-center">
                    <v-tooltip
                        bottom
                    >
                        <v-btn
                            slot="activator"
                            color="blue-grey darken-2"
                            flat
                            icon
                            class="mr-2"
                            @click="$emit('distribuir-produto', props.item)"
                        >
                            <v-icon>
                                person
                            </v-icon>
                        </v-btn>
                        <span>Distribuir ou encaminhar produto</span>
                    </v-tooltip>
                </td>
            </tr>
        </template>

        <template slot="no-data">
            <div class="text-xs-center">
                {{ `Sem produtos aguardando análise` }}
            </div>
        </template>
    </v-data-table>
</template>

<script>

import MxUtils from '@/mixins/utils';
import MxUtilsParecer from '@/modules/parecer/mixins/UtilsParecer';
import TdNomeProduto from '@/modules/parecer/components/gerenciar-paineis/TdNomeProduto';
import TdTipoProduto from '@/modules/parecer/components/gerenciar-paineis/TdTipoProduto';
import TdNumeroPronac from '@/modules/parecer/components/gerenciar-paineis/TdNumeroPronac';

export default {
    name: 'GerenciarListaAguardandoDistribuicao',
    components: { TdNumeroPronac, TdTipoProduto, TdNomeProduto },
    mixins: [MxUtils, MxUtilsParecer],

    props: {
        produtos: {
            type: Array,
            default: () => [],
        },
        search: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            headers: [
                {
                    text: 'Pronac',
                    value: 'pronac',
                    width: '1',
                },
                {
                    text: 'Nome do Projeto',
                    align: 'left',
                    value: 'nomeProjeto',
                },
                {
                    text: 'Produto para análise',
                    align: 'left',
                    value: 'nomeProduto',
                },
                {
                    text: 'Tipo',
                    value: 'stPrincipal',
                    width: '2',
                },
                {
                    text: 'Segmento',
                    align: 'left',
                    value: 'segmento',
                },
                { text: 'Valor Produto (R$)', value: 'valor', width: '2' },
                { text: 'Dt. de Recebimento', value: 'dtEnvioMincVinculada', width: '2' },
                {
                    text: 'Ações', align: 'center', value: 'siAnalise',
                },
            ],
        };
    },

};
</script>
