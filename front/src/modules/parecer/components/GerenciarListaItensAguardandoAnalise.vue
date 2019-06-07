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
        <td>{{ item.segmento }}</td>
        <td class="text-xs-right">
            {{ item.dtEnvioMincVinculada | formatarData }}
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
                    @click="$emit('distribuir-produto', item)"
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
                    class="mr-2"
                    @click="$emit('distribuirProjeto', item)"
                >
                    <v-icon>
                        person_add
                    </v-icon>
                </v-btn>
                <span>Distribuir projeto</span>
            </v-tooltip>
        </td>
    </tr>
</template>

<script>

import MxUtils from '@/mixins/utils';
import MxDiligencia from '@/modules/diligencia/mixins/diligencia';
import MxConstantes from '@/modules/parecer/mixins/const';

export default {
    name: 'GerenciarListaItensAguardandoAnalise',
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
