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
                        style="min-width: 250px"
                    >
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="green darken-2"
                                flat
                                icon
                                class="ma-0"
                                @click="validarParecer(props.item)"
                            >
                                <v-icon>
                                    done_all
                                </v-icon>
                            </v-btn>
                            <span>Validar análise</span>
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
                                class="ma-0"
                                flat
                                icon
                                @click="$emit('solicitar-analise-complementar', props.item)"
                            >
                                <v-icon>
                                    group_add
                                </v-icon>
                            </v-btn>
                            <span>Solicitar análise complementar</span>
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
                    {{ `Sem produtos em validação` }}
                </div>
            </template>
        </v-data-table>
        <s-progresso-dialog
            v-model="loading"
            label="Aguarde, salvando validação"
        />
    </div>
</template>

<script>

import { mapActions } from 'vuex';

import MxUtils from '@/mixins/utils';
import MxUtilsParecer from '@/modules/parecer/mixins/UtilsParecer';

import * as TbDistribuirParecer from '@/modules/parecer/constantes/TbDistribuirParecer';
import * as TbTipoEncaminhamento from '@/modules/shared/constantes/TbTipoEncaminhamento';

import SProgressoDialog from '@/components/SalicProgressoDialog';
import TdNomeProduto from '@/modules/parecer/components/gerenciar-paineis/TdNomeProduto';
import TdTipoProduto from '@/modules/parecer/components/gerenciar-paineis/TdTipoProduto';
import TdNumeroPronac from '@/modules/parecer/components/gerenciar-paineis/TdNumeroPronac';

export default {
    name: 'GerenciarListaEmValidacao',
    components: {
        TdNumeroPronac, TdTipoProduto, TdNomeProduto, SProgressoDialog,
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
            loading: false,
            produto: {},
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
                { text: 'Ações', width: '2', value: 'stPrincipal' },
            ],
        };
    },

    methods: {
        ...mapActions({
            salvarValidacaoParecer: 'parecer/salvarValidacaoParecer',
        }),
        confirmarValidacao(produto) {
            this.produto = produto;
            this.dialogConfirmarEnvio = true;
        },
        async validarParecer(produto) {
            const mensagemStatus = 'Confirma a validação da análise do produto?';
            if (await this.$root.$confirm(mensagemStatus) === false) {
                return false;
            }
            const distribuicao = Object.assign({}, produto,
                {
                    siAnalise: TbDistribuirParecer.SI_ANALISE_VALIDADO,
                    siEncaminhamento: TbTipoEncaminhamento.SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA,
                });

            this.loading = true;
            return this.salvarValidacaoParecer(distribuicao)
                .then(() => {
                    this.dialog = false;
                }).finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>
