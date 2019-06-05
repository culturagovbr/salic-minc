<template>
    <tr>
        <td>
            <v-tooltip
                bottom
            >
                <a
                    slot="activator"
                    :href="`/projeto/#/${item.idPronac}`"
                    target="_blank"
                    class="mr-2"
                >
                    {{ item.pronac }}
                </a>
                <span>Consultar projeto {{ item.nomeProjeto }}</span>
            </v-tooltip>
        </td>
        <td>{{ item.nomeProjeto }}</td>
        <td>
            <v-tooltip bottom>
                <router-link
                    slot="activator"
                    :to="{
                        name: 'parecer-gerenciar-visualizar-view',
                        params: {
                            id: item.idProduto,
                            idPronac: item.idPronac,
                            produtoPrincipal: item.stPrincipal,
                        }
                    }"
                    class="subheading font-weight-medium"
                    color="primary"
                >
                    {{ item.nomeProduto }}
                </router-link>
                <span>Clique para analisar o produto {{ item.nomeProduto }}</span>
            </v-tooltip>
        </td>
        <td class="text-xs-center">
            <v-tooltip
                v-if="item.stPrincipal === 1"
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
        <td class="text-xs-right">
            {{ item.dtDistribuicao | formatarData }}
        </td>
        <td class="text-xs-center">
            <v-tooltip
                bottom
            >
                <v-btn
                    slot="activator"
                    :color="obterConfigDiligencia(item).cor"
                    target="_blank"
                    icon
                    small
                    @click="visualizarDiligencia(item)"
                >
                    <v-badge
                        :value="item.diasEmDiligencia > 0"
                        color="grey lighten-1"
                        overlap
                        left
                    >
                        <span slot="badge">{{ item.diasEmDiligencia }}</span>
                        <v-icon
                            :color="obterConfigDiligencia(item).corIcone"
                        >
                            notification_important
                        </v-icon>
                    </v-badge>
                </v-btn>
                <span> {{ obterConfigDiligencia(item).texto }} </span>
            </v-tooltip>
        </td>
        <td class="layout px-0">
            <v-tooltip
                bottom
            >
                <v-btn
                    slot="activator"
                    :to="{
                        name: 'analise-conteudo',
                        params: {
                            id: item.idProduto,
                            idPronac: item.idPronac,
                            produtoPrincipal: item.stPrincipal,
                        }
                    }"
                    color="primary"
                    flat
                    icon
                    class="mr-2"
                >
                    <v-icon>
                        {{ obterConfigsBotaoPrincipal(item).icone }}
                    </v-icon>
                </v-btn>
                <span>{{ obterConfigsBotaoPrincipal(item).texto }}</span>
            </v-tooltip>
            <v-tooltip
                bottom
            >
                <v-btn
                    v-if="item.siAnalise === SI_ANALISE_AGUARDANDO_ANALISE
                        || !item.siAnalise"
                    slot="activator"
                    color="blue-grey darken-2"
                    flat
                    icon
                    class="mr-2"
                    @click="declararImpedimento(item)"
                >
                    <v-icon>
                        voice_over_off
                    </v-icon>
                </v-btn>
                <span>Declarar impedimento para análise deste produto</span>
            </v-tooltip>
        </td>
    </tr>
</template>

<script>

import MxUtils from '@/mixins/utils';
import MxDiligencia from '@/modules/diligencia/mixins/diligencia';
import MxConstantes from '@/modules/parecer/mixins/const';

export default {
    name: 'GerenciarListaAguardandoAnalise',
    mixins: [MxUtils, MxDiligencia, MxConstantes],
    props: {
        item: {
            type: Object,
            default: () => {},
        },
    },
    methods: {
        obterConfigsBotaoPrincipal(item) {
            switch (item.siAnalise) {
            case 1:
                return {
                    icone: 'border_color',
                    texto: 'Continuar analisando',
                };
            case 2:
                return {
                    icone: 'assignment',
                    texto: 'Assinar parecer e finalizar',
                };
            default:
                return {
                    icone: 'edit',
                    texto: 'Analisar produto',
                };
            }
        },
    },
};
</script>
