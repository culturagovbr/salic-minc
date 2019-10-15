<template>
    <div>
        <v-card-title>
            <v-spacer />
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Pesquisar"
                single-line
                hide-details
                height="35px"
            />
        </v-card-title>
        <v-data-table
            :headers="cabecalho"
            :items="dados.items"
            :search="search"
            item-key="item.index"
            rows-per-page-text="Items por Página"
            no-data-text="Nenhum dado encontrado"
        >
            <template
                slot="items"
                slot-scope="props"
            >
                <td class="text-xs-center">
                    {{ props.index+1 }}
                </td>
                <td class="text-xs-center">
                    <v-flex>
                        <div>
                            <v-btn :href="'/projeto/#/'+ props.item.IdPronac">
                                {{ props.item.PRONAC }}
                            </v-btn>
                        </div>
                    </v-flex>
                </td>
                <td class="text-xs-center">
                    {{ props.item.NomeProjeto }}
                </td>
                <td class="text-xs-center">
                    <VisualizarParecer
                        :obj="props.item"
                        :id-pronac="props.item.IdPronac"
                        :laudo="true"
                    />
                </td>
                <td class="text-xs-center">
                    <Devolver
                        v-if="usuario"
                        :id-pronac="String(props.item.IdPronac)"
                        :atual="estado"
                        :proximo="proximoEstado()"
                        :nome-projeto="props.item.NomeProjeto"
                        :pronac="props.item.PRONAC"
                        :id-tipo-do-ato-administrativo="atoAdministrativo(estado)"
                        :usuario="getUsuario"
                        :tecnico="{
                            idAgente: props.item.usu_codigo,
                            nome: props.item.usu_nome,
                        }"
                    />
                </td>
                <td
                    v-if="estado == Const.ESTADO_ANALISE_LAUDO"
                    class="text-xs-center"
                >
                    <v-btn
                        id="emitirLaudo"
                        :to="{ name: 'EmitirLaudoFinal', params:{ id:props.item.IdPronac }}"
                        flat
                        icon
                        color="teal darken-1"
                        @click.native="sincState(props.item.IdPronac)"
                    >
                        <v-tooltip bottom>
                            <v-icon
                                slot="activator"
                                class="material-icons"
                            >
                                gavel
                            </v-icon>
                            <span>Emitir Laudo</span>
                        </v-tooltip>
                    </v-btn>
                </td>
                <td
                    v-if="liberarAssinatura(estado)"
                    class="text-xs-center"
                >
                    <v-btn
                        id="assinarLaudo"
                        :href="'/assinatura/index/assinar-projeto?IdPRONAC='
                            +props.item.IdPronac+'&idTipoDoAtoAdministrativo=623'+retornoUrl.toString()"
                        flat
                        icon
                        color="teal darken-1"
                    >
                        <v-tooltip bottom>
                            <v-icon
                                slot="activator"
                                class="material-icons"
                            >
                                edit
                            </v-icon>
                            <span>Assinar Laudo</span>
                        </v-tooltip>
                    </v-btn>
                </td>
                <td
                    v-if="estado == Const.ESTADO_AVALIACAO_RESULTADOS_FINALIZADA"
                    class="text-xs-center"
                >
                    <v-btn
                        id="visualizarLaudo"
                        :to="{ name: 'VisualizarLaudo', params:{ id:props.item.IdPronac }}"
                        flat
                        icon
                        color="teal darken-1"
                        @click.native="sincState(props.item.IdPronac)"
                    >
                        <v-tooltip bottom>
                            <v-icon
                                slot="activator"
                                class="material-icons"
                            >
                                visibility
                            </v-icon>
                            <span>Visualizar Laudo</span>
                        </v-tooltip>
                    </v-btn>
                </td>
            </template>
            <template slot="no-data">
                <v-alert
                    :value="true"
                    color="error"
                    icon="warning"
                >
                    Nenhum dado encontrado ¯\_(ツ)_/¯
                </v-alert>
            </template>
            <v-alert
                slot="no-results"
                :value="true"
                color="error"
                icon="warning"
            >
                Não foi possível encontrar um projeto com a palavra chave '{{ search }}'.
            </v-alert>
        </v-data-table>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import ModalTemplate from '@/components/modal';
import Const from '../../const';
import Devolver from '../components/Devolver';
import VisualizarParecer from '../components/VisualizarParecer';

export default {
    name: 'Painel',
    components: {
        ModalTemplate,
        Devolver,
        VisualizarParecer,
    },
    props: {
        dados: {
            type: Object,
            default: () => {},
        },
        estado: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            pagination: {
                rowsPerPage: 10,
            },
            retornoUrl: `&origin=${encodeURIComponent('avaliacao-resultados/#/laudo')}`,
            searchLength: 0,
            search: '',
            dialog: false,
            cabecalho: [
                {
                    align: 'center',
                    text: '#',
                    sortable: false,
                },
                {
                    align: 'center',
                    text: 'PRONAC',
                    value: 'PRONAC',
                },
                {
                    align: 'center',
                    text: 'Nome Do Projeto',
                    value: 'NomeProjeto',
                },
                {
                    align: 'center',
                    text: 'Manifestação',
                    value: 'dsResutaldoAvaliacaoObjeto',
                },
                {
                    align: 'center',
                    text: 'Devolver',
                    sortable: false,

                },
                {
                    align: 'center',
                    text: 'Ação',
                    sortable: false,
                },
            ],
            Const,
        };
    },
    computed: {
        ...mapGetters({
            getUsuario: 'autenticacao/getUsuario',
        }),
        usuario() {
            return (this.getUsuario !== undefined && Object.keys(this.getUsuario).length > 0);
        },
    },
    methods: {
        ...mapActions({
            requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
            getLaudoFinal: 'avaliacaoResultados/getLaudoFinal',
        }),
        atoAdministrativo(estado) {
            let ato = this.Const.ATO_ADMINISTRATIVO_PARECER_TECNICO;

            if (estado === this.Const.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_LAUDO
            || estado === this.Const.ESTADO_AGUARDANDO_ASSINATURA_DIRETOR_LAUDO
            || estado === this.Const.ESTADO_AGUARDANDO_ASSINATURA_SECRETARIO_LAUDO
            ) {
                ato = this.Const.ATO_ADMINISTRATIVO_LAUDO_FINAL;
            }

            return ato;
        },
        sincState(id) {
            this.requestEmissaoParecer(id);
            this.getLaudoFinal(id);
        },
        proximoEstado() {
            let proximo;
            if (this.estado === this.Const.ESTADO_ANALISE_LAUDO) {
                proximo = this.Const.ESTADO_ANALISE_PARECER;
            } else if (this.estado === this.Const.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_LAUDO) {
                proximo = this.Const.ESTADO_ANALISE_LAUDO;
            } else if (this.estado === this.Const.ESTADO_AGUARDANDO_ASSINATURA_DIRETOR_LAUDO) {
                proximo = this.Const.ESTADO_ANALISE_LAUDO;
            } else if (this.estado === this.Const.ESTADO_AGUARDANDO_ASSINATURA_SECRETARIO_LAUDO) {
                proximo = this.Const.ESTADO_ANALISE_LAUDO;
            } else if (this.estado === this.Const.ESTADO_AVALIACAO_RESULTADOS_FINALIZADA) {
                proximo = this.Const.ESTADO_ANALISE_LAUDO;
            } else {
                proximo = 0;
            }
            return proximo;
        },
        liberarAssinatura(estado) {
            switch (estado) {
            case this.Const.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_LAUDO:
                return true;
            case this.Const.ESTADO_AGUARDANDO_ASSINATURA_DIRETOR_LAUDO:
                return true;
            case this.Const.ESTADO_AGUARDANDO_ASSINATURA_SECRETARIO_LAUDO:
                return true;
            default:
                return false;
            }
        },
    },
};
</script>
