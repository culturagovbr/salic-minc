<template>
    <v-data-table
        :headers="headers"
        :items="produtos"
        :rows-per-page-items="[15, 35, 50, {'text': 'Todos', value: -1}]"
        :search="search"
        item-key="idDistribuirParecer"
        class="elevation-1 table__expand"
        disable-initial-sort
    >
        <template
            slot="items"
            slot-scope="props"
        >
            <tr :class="mxObterClasseItem(props.item)">
                <td>
                    <v-btn
                        color="blue-grey darken-2"
                        class="ma-0"
                        flat
                        icon
                        @click.prevent="props.expanded = !props.expanded"
                    >
                        <v-icon>
                            {{ props.expanded ? 'expand_less' : 'expand_more' }}
                        </v-icon>
                    </v-btn>
                </td>
                <td-numero-pronac :produto="props.item" />
                <td>{{ props.item.nomeProjeto }}</td>
                <td-nome-produto :produto="props.item" />
                <td-tipo-produto :produto="props.item" />
                <td>{{ props.item.segmento }}</td>
                <td>{{ props.item.nomeParecerista }}</td>
                <td class="text-xs-right">
                    {{ props.item.dtDistribuicao | formatarData }}
                </td>
                <td
                    class="text-xs-center"
                    style="min-width: 212px"
                >
                    <v-tooltip
                        bottom
                    >
                        <v-btn
                            slot="activator"
                            color="blue-grey darken-2"
                            flat
                            icon
                            class="ma-0"
                            @click="$emit('distribuir-produto', props.item)"
                        >
                            <v-icon>
                                person
                            </v-icon>
                        </v-btn>
                        <span>Distribuir produto</span>
                    </v-tooltip>
                    <v-tooltip
                        bottom
                    >
                        <v-btn
                            slot="activator"
                            color="blue-grey darken-2"
                            flat
                            icon
                            class="ma-0"
                            @click="$emit('visualizar-historico', props.item)"
                        >
                            <v-icon>
                                history
                            </v-icon>
                        </v-btn>
                        <span>Visualizar histórico</span>
                    </v-tooltip>
                </td>
            </tr>
        </template>

        <template v-slot:expand="props">
            <gerenciar-lista-expand :produto="props.item" />
        </template>

        <template slot="no-data">
            <div class="text-xs-center">
                Sem produtos em análise
            </div>
        </template>
    </v-data-table>
</template>

<script>

/** mixins */
import MxUtils from '@/mixins/utils';
import MxUtilsParecer from '@/modules/parecer/mixins/UtilsParecer';

import TdNomeProduto from '@/modules/parecer/components/gerenciar-paineis/TdNomeProduto';
import TdTipoProduto from '@/modules/parecer/components/gerenciar-paineis/TdTipoProduto';
import TdNumeroPronac from '@/modules/parecer/components/gerenciar-paineis/TdNumeroPronac';
import GerenciarListaExpand from '@/modules/parecer/components/gerenciar-paineis/GerenciarListaExpand';

export default {
    name: 'GerenciarListaItensAguardandoAnalise',
    components: {
        GerenciarListaExpand, TdNumeroPronac, TdTipoProduto, TdNomeProduto,
    },
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
                    text: '#',
                    sortable: false,
                },
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
                    text: 'Produto',
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
                {
                    text: 'Parecerista',
                    align: 'left',
                    value: 'nomeParecerista',
                },
                {
                    text: 'Dt. de Envio',
                    value: 'dtDistribuicao',
                    width: '2',
                },
                {
                    text: 'Ações',
                    width: '2',
                    value: 'siAnalise',
                },
            ],
        };
    },
};
</script>
