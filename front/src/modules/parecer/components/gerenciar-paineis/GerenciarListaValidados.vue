<template>
    <div>
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
                    <td>{{ props.item.nomeParecerista }}</td>
                    <td class="text-xs-right">
                        {{ props.item.dtDistribuicao | formatarData }}
                    </td>
                    <td
                        class="text-xs-center"
                        style="min-width: 230px"
                    >
                        <v-tooltip
                            v-if="props.item.stPrincipal === 1"
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                :href="`${urlAssinatura}?idDocumentoAssinatura=${props.item.idDocumentoAssinatura}&${retornoAssinatura.toString()}`"
                                color="green darken-2"
                                flat
                                icon
                                class="ma-0"
                            >
                                <v-icon>
                                    assignment_turned_in
                                </v-icon>
                            </v-btn>
                            <span>Assinar documento</span>
                        </v-tooltip>
                        <v-tooltip
                            v-else
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="blue-grey darken-2"
                                class="ma-0"
                                flat
                                icon
                                disabled
                            >
                                <v-icon>
                                    watch_later
                                </v-icon>
                            </v-btn>
                            <span>Este produto é secundário, quando o produto principal for assinado ele será finalizado</span>
                        </v-tooltip>
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="blue-grey darken-2"
                                class="ma-0"
                                flat
                                icon
                                @click="$emit('distribuir-produto', props.item)"
                            >
                                <v-icon>
                                    person
                                </v-icon>
                            </v-btn>
                            <span>Redistribuir produto</span>
                        </v-tooltip>
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="blue-grey darken-2"
                                class="ma-0"
                                flat
                                icon
                                @click="$emit('reanalisar-produto', props.item)"
                            >
                                <v-icon>
                                    report_off
                                </v-icon>
                            </v-btn>
                            <span>Devolver para reanálise do parecerista</span>
                        </v-tooltip>
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="blue-grey darken-2"
                                flat
                                icon
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
                    {{ `Sem produtos validados` }}
                </div>
            </template>
        </v-data-table>
    </div>
</template>

<script>

import MxUtils from '@/mixins/utils';
import MxUtilsParecer from '@/modules/parecer/mixins/UtilsParecer';

import TdNomeProduto from '@/modules/parecer/components/gerenciar-paineis/TdNomeProduto';
import TdTipoProduto from '@/modules/parecer/components/gerenciar-paineis/TdTipoProduto';
import TdNumeroPronac from '@/modules/parecer/components/gerenciar-paineis/TdNumeroPronac';

export default {
    name: 'GerenciarListaValidados',
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
            dialogConfirmarEnvio: false,
            loading: false,
            produto: {},
            urlAssinatura: '/assinatura/index/visualizar-projeto',
            prevRoute: null,
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
                { text: 'Dt. de Envio', value: 'dtDistribuicao', width: '2' },
                { text: 'Ações', width: '4', value: 'siAnalise' },
            ],
        };
    },

    computed: {
        retornoAssinatura() {
            const path = `#${this.$route.path}`;
            return `origin=${encodeURIComponent(path)}`;
        },
    },

    methods: {
        isDisponivelParaAssinatura(produto) {
            return produto
                && produto.siEncaminhamento === 5
                && produto.siAnalise === 5
                && produto.idDocumentoAssinatura;
        },
    },
};
</script>
