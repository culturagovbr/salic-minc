<template>
    <div
        v-if="loading"
        ma-5
    >
        <carregando
            :text="'Carregando lista de readequações...'"
        />
    </div>
    <div
        v-else
    >
        <v-card
            flat
        >
            <v-card-title>
                <v-spacer/>
                <v-spacer/>
                <v-spacer/>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Buscar"
                    single-line
                    hide-details
                />
            </v-card-title>
        </v-card>
        <v-data-table
            :headers="head"
            :items="dadosReadequacao.items"
            :pagination.sync="pagination"
            :search="search"
            item-key="item.index"
            rows-per-page-text="Items por Página"
        >
            <template
                slot="items"
                slot-scope="props">
                <td>{{ props.index+1 }}</td>
                <td class="text-xs-left">{{ props.item.PRONAC }}</td>
                <td class="text-xs-left">{{ props.item.NomeProjeto }}</td>
                <td class="text-xs-left">{{ props.item.dsTipoReadequacao }}</td>
                <template
                    v-if="specificHead[painel] !== 'undefined'"
                >
                    <template
                        v-for="(item, index) in specificHead[painel]"
                    >
                        <td
                            :key="index"
                            class="text-xs-center"
                            v-html="filterField(props.item[item.value])"
                        />
                    </template>
                </template>
                <td
                    v-if="componentes.acoes"
                    class="text-xs-center"
                >
                    <v-layout
                        row
                        justify-center
                        align-end
                    >
                        <v-layout
                            align-center
                            justify-center
                            fill-height
                        >
                            <template
                                v-for="(item, index) in (componentes.acoes)"
                            >
                                <template v-if="perfilAceito(item.permissao) || item.permissao === 'all'">
                                    <component
                                        :key="index"
                                        :obj="props.item"
                                        :is="item.componente"
                                        :dados-readequacao="props.item"
                                        :id-documento-assinatura="props.item.idDocumentoAssinatura"
                                        :id-tipo-do-ato-administrativo="props.item.idTipoDoAtoAdministrativo"
                                        :min-char="minChar"
                                        :modal="true"
                                        :disponivel-assinatura="disponivelAssinatura(props.item.siEncaminhamento, props.item.idDocumentoAssinatura)"
                                        :origin="`%23/readequacao/painel`"
                                        class="pa-0 ma-0 align-center justify-center fill-height"
                                    />
                                </template>
                            </template>
                        </v-layout>
                    </v-layout>
                </td>
            </template>
            <template slot="no-data">
                <v-alert
                    :value="true"
                    color="error"
                    icon="warning">
                    Nenhum dado encontrado ¯\_(ツ)_/¯
                </v-alert>
            </template>
        </v-data-table>
    </div>
</template>

<script>
import { utils } from '@/mixins/utils';
import Carregando from '@/components/CarregandoVuetify';
import MxReadequacao from '../mixins/Readequacao';
import Const from '../const';

export default {
    name: 'TabelaReadequacoesPainel',
    components: {
        Carregando,
    },
    mixins: [
        utils,
        MxReadequacao,
    ],
    props: {
        dadosReadequacao: {
            type: [Array, Object],
            default: () => {},
        },
        painel: {
            type: String,
            default: '',
        },
        componentes: {
            type: Object,
            default: () => {},
        },
        perfisAceitos: {
            type: [Array, Object],
            default: () => [],
        },
        perfil: {
            type: [Number, String],
            default: 0,
        },
        minChar: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            pagination: {
                rowsPerPage: 10,
                descending: true,
                sortBy: 'dtSolicitacao',
            },
            head: [
                {
                    text: '#',
                    align: 'left',
                    sortable: false,
                    value: 'numero',
                },
                {
                    text: 'Pronac',
                    value: 'PRONAC',
                },
                {
                    text: 'Projeto',
                    value: 'NomeProjeto',
                },
                {
                    text: 'Tipo de Readequação',
                    value: 'dsTipoReadequacao',
                },
                {
                    text: 'Ações',
                    align: 'center',
                    value: '',
                },
            ],
            specificHead: {
                aguardando_distribuicao: [
                    {
                        text: 'Data envio',
                        align: 'center',
                        value: 'dtEnvio',
                        position: 4,
                    },
                    {
                        text: 'Dias aguardando distribuição',
                        align: 'center',
                        value: 'qtAguardandoDistribuicao',
                        position: 5,
                    },
                ],
                analisar: [
                    {
                        text: 'Data de recebimento',
                        align: 'center',
                        value: 'dtDistribuicao',
                        position: 4,
                    },
                ],
                em_analise: [
                    {
                        text: 'Data distribuição',
                        align: 'center',
                        value: 'dtEncaminhamento',
                        position: 4,
                    },
                    {
                        text: 'Dias em análise',
                        align: 'center',
                        value: 'qtDiasEncaminhar',
                        position: 5,
                    },
                    {
                        text: 'Vinculada',
                        align: 'center',
                        value: 'sgUnidade',
                        position: 6,
                    },
                    {
                        text: 'Técnico/a',
                        align: 'center',
                        value: 'nmTecnicoParecerista',
                        position: 7,
                    },
                ],
                analisados: [
                    {
                        text: 'Data envio',
                        align: 'center',
                        value: 'dtEnvio',
                        position: 4,
                    },
                    {
                        text: 'Data distribuição',
                        align: 'center',
                        value: 'dtDistribuicao',
                        position: 5,
                    },
                    {
                        text: 'Data devolução',
                        align: 'center',
                        value: 'dtDevolucao',
                        position: 6,
                    },
                    {
                        text: 'Total de dias para avaliar',
                        align: 'center',
                        value: 'qtTotalDiasAvaliar',
                        position: 7,
                    },
                    {
                        text: 'Localização',
                        align: 'center',
                        value: 'nmTecnicoParecerista',
                        position: 8,
                    },
                ],
            },
            search: '',
            bindClick: 0,
            loading: true,
        };
    },
    watch: {
        dadosReadequacao(value) {
            if (this.checkData(value)) {
                this.loading = false;
            }
        },
    },
    mounted() {
        if (this.checkData(this.dadosReadequacao)) {
            this.loading = false;
        }
        if (typeof this.specificHead[this.painel] !== 'undefined') {
            this.specificHead[this.painel].forEach((item) => {
                this.head.splice(item.position, 0, item);
            });
        }
    },
    methods: {
        checkData(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    return true;
                }
            }
            return value;
        },
        perfilAceito(tipoPerfil) {
            const perfisAceitos = this.perfisAceitos[tipoPerfil];
            if (typeof perfisAceitos === 'object') {
                return this.verificarPerfil(this.perfil, perfisAceitos);
            }
            return false;
        },
        filterField(value) {
            if (Date.parse(value)) {
                return this.filterDate(value);
            }
            return value;
        },
        filterDate(value) {
            return this.$options.filters.formatarData(value);
        },
        disponivelAssinatura(siEncaminhamento, idDocumentoAssinatura) {
            const assinaturasPorEstado = {
                94: Const.SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA,
                121: Const.SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA,
                122: Const.SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO,
                123: Const.SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL,
                148: Const.SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_DIRETOR,
                149: Const.SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_SECRETARIO,
            };
            if (typeof siEncaminhamento !== 'undefined' && typeof idDocumentoAssinatura === 'number') {
                return siEncaminhamento === assinaturasPorEstado[this.perfil];
            }
            return false;
        },
    },
};
</script>
<style>
#app .v-menu__content {
    min-width: 90px !important;
}
</style>
