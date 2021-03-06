<template>
    <v-layout
        row
        wrap
        align-content-start
        style="min-height: calc(100vh - 332px)"
    >
        <v-flex xs4>
            <br>
            <v-switch
                v-if="$route.path == '/painel/aba-em-analise'"
                v-model="filtro"
                input-value="true"
                color="success"
                label="Ocultar diligenciados"
                value="Diligenciado"
            />
        </v-flex>
        <v-flex xs8>
            <v-card-title>
                <v-spacer />
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Pesquisar"
                    single-line
                    hide-details
                />
            </v-card-title>
        </v-flex>

        <v-flex xs12>
            <carregando-vuetify
                v-if="loading"
                text="Carregando ..."
            />
            <v-data-table
                v-else
                :headers="headers()"
                :items="dados.items"
                :search="search"
                :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
            >
                <template
                    v-if="filtragem(statusDiligencia(props.item).desc, filtro)"
                    slot="items"
                    slot-scope="props"
                >
                    <td class="text-xs-right">
                        <v-flex
                            xs12
                            sm4
                        >
                            <div>
                                <v-btn
                                    :href="'/projeto/#/'+ props.item.idPronac"
                                    flat
                                    small
                                    color="primary"
                                    style="text-decoration: underline"
                                >
                                    {{ props.item.PRONAC }}
                                </v-btn>
                            </div>
                        </v-flex>
                    </td>
                    <td class="text-xs-left">
                        {{ props.item.NomeProjeto }}
                    </td>
                    <td class="text-xs-center">
                        {{ props.item.Situacao }}
                    </td>
                    <td class="text-xs-center">
                        {{ props.item.UfProjeto }}
                    </td>
                    <td
                        v-if="mostrarTecnico"
                        class="text-xs-center"
                    >
                        {{ props.item.usu_nome }}
                    </td>
                    <td
                        class="text-xs-center"
                        style="min-width: 360px;"
                    >
                        <template
                            v-for="(componente, index) in componentes.acoes"
                            d-inline-block
                        >
                            <component
                                :is="componente"
                                :key="index"
                                :obj="props.item"
                                :filtros="filtro"
                                :link-direto-assinatura="true"
                                :documento="props.item.idDocumentoAssinatura"
                                :id-pronac="props.item.IdPRONAC.toString()"
                                :pronac="props.item.PRONAC"
                                :nome-projeto="props.item.NomeProjeto"
                                :atual="componentes.atual"
                                :proximo="componentes.proximo"
                                :id-tipo-do-ato-administrativo="componentes.idTipoDoAtoAdministrativo"
                                :usuario="componentes.usuario"
                                :laudo="false"
                                :retorno="retornoUrl"
                                :tecnico="{
                                    idAgente: props.item.idAgente,
                                    nome: props.item.usu_nome
                                }"
                            />
                        </template>
                    </td>
                </template>
                <template slot="no-data">
                    <v-alert
                        :value="true"
                        color="info"
                        icon="info"
                    >
                        Nenhum dado encontrado ¯\_(ツ)_/¯
                    </v-alert>
                </template>
            </v-data-table>
        </v-flex>
    </v-layout>
</template>

<script>
import { mapGetters } from 'vuex';
import statusDiligencia from '../../../mixins/statusDiligencia';
import CarregandoVuetify from '@/components/CarregandoVuetify';

export default {
    name: 'TabelaProjetos',
    components: { CarregandoVuetify },
    mixins: [statusDiligencia],
    props: {
        dados: {
            type: [Object, Array],
            default: () => {},
        },
        componentes: {
            type: Object,
            default: () => {},
        },
        mostrarTecnico: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            pagination: {
                rowsPerPage: 10,
            },
            retornoUrl: `&origin=${encodeURIComponent('avaliacao-resultados/#/painel/assinar')}`,
            selected: [],
            search: '',
            filtro: '',
            diligencias: [
                'Todos projetos',
                'A Diligenciar',
                'Diligenciado',
                'Diligencia respondida',
                'Diligencia não respondida',
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosTabelaTecnico: 'avaliacaoResultados/dadosTabelaTecnico',
            getProjetosFinalizados: 'avaliacaoResultados/getProjetosFinalizados',
        }),
        pages() {
            if (this.pagination.rowsPerPage == null
                || this.pagination.totalItems == null
            ) return 0;
            return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage);
        },
    },
    watch: {
        dadosTabelaTecnico() {
            if (this.dados.items !== undefined) {
                this.pagination.totalItems = this.dados.items.length;
            }
        },
    },
    methods: {
        // ...mapActions({
        //     obterDadosTabelaTecnico: 'avaliacaoResultados/obterDadosTabelaTecnico',
        // }),
        filtragem(projeto, filtro) {
            if (filtro === 'Todos projetos' || this.filtro === '') {
                return true;
            }
            return projeto !== filtro;
        },
        // filtroDiligencia(val) {
        //     this.filtro = val;
        //     this.$emit('filtros', this.filtro);
        // },
        headers() {
            let dados = [];

            dados = [
                {
                    text: 'PRONAC',
                    value: 'PRONAC',
                },
                {
                    text: 'Nome Do Projeto',
                    align: 'left',
                    value: 'NomeProjeto',
                },
                {
                    text: 'Situacao',
                    align: 'center',
                    value: 'Situacao',
                },
                {
                    text: 'Estado',
                    align: 'center',
                    value: 'UfProjeto',
                },
            ];

            if (this.mostrarTecnico) {
                dados[5] = {
                    text: 'Tecnico',
                    align: 'center',
                    value: '',
                };
            }

            dados[6] = {
                text: 'Ações',
                sortable: false,
                align: 'center',
            };

            return dados;
        },
    },
};
</script>
