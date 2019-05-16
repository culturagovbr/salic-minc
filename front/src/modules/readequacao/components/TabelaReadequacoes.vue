<template>
    <div
        v-if="loading"
        ma-5
    >
        <carregando :text="'Carregando lista de readequações...'"/>
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
                <td class="text-xs-left">{{ props.item.dsTipoReadequacao }}</td>
                <td class="text-xs-center">{{ props.item.dtSolicitacao | formatarData }}</td>
                <td class="text-xs-center">
                    <v-btn
                        v-if="props.item.idDocumento"
                        flat
                        icon
                        @click="abrirArquivo(props.item.idDocumento)"
                    >
                        <v-icon>insert_drive_file</v-icon>
                    </v-btn>
                </td>
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
                                v-for="(componente, index) in componentes.acoes"
                            >
                                <component
                                    :key="index"
                                    :obj="props.item"
                                    :is="componente"
                                    :dados-readequacao="props.item"
                                    :dados-projeto="dadosProjeto"
                                    :bind-click="bindClick"
                                    :perfis-aceitos="perfisAceitos"
                                    :perfil="perfil"
                                    :min-char="minChar"
                                    class="pa-0 ma-0 align-center justify-center fill-height"
                                    @excluir-readequacao="excluirReadequacao"
                                    @atualizar-readequacao="atualizarReadequacao(props.item.idReadequacao)"
                                />
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
import abrirArquivo from '../mixins/abrirArquivo';

export default {
    name: 'TabelaReadequacoes',
    components: {
        Carregando,
    },
    mixins: [
        utils,
        abrirArquivo,
    ],
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        componentes: {
            type: Object,
            default: () => {},
        },
        dadosProjeto: {
            type: Object,
            default: () => {},
        },
        itemEmEdicao: {
            type: Number,
            default: 0,
        },
        perfisAceitos: {
            type: Array,
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
                    text: 'Tipo de Readequação',
                    value: 'dsTipoReadequacao',
                },
                {
                    text: 'Data da Solicitação',
                    align: 'center',
                    value: 'dtSolicitacao',
                },
                {
                    text: 'Arquivo',
                    align: 'center',
                    value: 'idDocumento',
                },
                {
                    text: 'Ações',
                    align: 'center',
                    value: '',
                },
            ],
            search: '',
            bindClick: 0,
            loading: true,
        };
    },
    computed: {},
    watch: {
        itemEmEdicao() {
            this.bindClick = this.itemEmEdicao;
        },
        dadosReadequacao(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    this.loading = false;
                }
            }
        },
    },
    methods: {
        excluirReadequacao() {
            this.$emit('excluir-readequacao');
        },
        atualizarReadequacao(idReadequacao) {
            this.$emit('atualizar-readequacao', { idReadequacao });
        },
    },
};
</script>
