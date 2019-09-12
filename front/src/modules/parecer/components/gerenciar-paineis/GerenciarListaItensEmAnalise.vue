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
            <tr>
                <td>
                    <v-tooltip
                        bottom
                    >
                        <a
                            slot="activator"
                            :href="`/projeto/#/${props.item.idPronac}`"
                            target="_blank"
                            class="mr-2"
                        >
                            {{ props.item.pronac }}
                        </a>
                        <span>Consultar projeto {{ props.item.nomeProjeto }}</span>
                    </v-tooltip>
                </td>
                <td>{{ props.item.nomeProjeto }}</td>
                <td>
                    <v-tooltip bottom>
                        <router-link
                            slot="activator"
                            :to="{
                                name: 'parecer-gerenciar-visualizar-view',
                                params: {
                                    id: props.item.idProduto,
                                    idPronac: props.item.idPronac,
                                    produtoPrincipal: props.item.stPrincipal,
                                }
                            }"
                            class="subheading font-weight-medium"
                            color="primary"
                        >
                            {{ props.item.nomeProduto }}
                        </router-link>
                        <span>Clique para analisar o produto {{ props.item.nomeProduto }}</span>
                    </v-tooltip>
                </td>
                <td class="text-xs-center">
                    <v-tooltip
                        v-if="props.item.stPrincipal === 1"
                        bottom
                    >
                        <v-icon
                            slot="activator"
                            round
                        >
                            looks_one
                        </v-icon>
                        <span>Produto principal</span>
                    </v-tooltip>
                    <v-tooltip
                        v-else
                        bottom
                    >
                        <v-icon
                            slot="activator"
                            color="grey"
                        >
                            looks_two
                        </v-icon>
                        <span>Produto secundário</span>
                    </v-tooltip>
                </td>
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
                            @click="$emit('visualizar-detalhes', props.item)"
                        >
                            <v-icon>
                                visibility
                            </v-icon>
                        </v-btn>
                        <span>Visualizar detalhes</span>
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

        <template slot="no-data">
            <div class="text-xs-center">
                {{ `Sem produtos em análise` }}
            </div>
        </template>
    </v-data-table>
</template>

<script>

import MxUtils from '@/mixins/utils';
import MxConstantes from '@/modules/parecer/mixins/const';

export default {
    name: 'GerenciarListaItensAguardandoAnalise',
    mixins: [MxUtils, MxConstantes],

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
