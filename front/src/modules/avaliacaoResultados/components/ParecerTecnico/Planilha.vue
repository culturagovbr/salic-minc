<template>
    <div>
        <planilha-avaliacao-financeira>
            <template slot-scope="slotProps">
                <v-data-table
                    :headers="headers"
                    :items="Object.values(slotProps.itens)"
                    hide-actions
                    disable-initial-sort
                >
                    <template
                        slot="items"
                        slot-scope="props"
                    >
                        <td>
                            <v-tooltip
                                bottom
                            >
                                <v-progress-circular
                                    v-if="props.item.qtComprovado > 0"
                                    slot="activator"
                                    :value="obterPercentualProgresso(props.item)"
                                    :color="obterCorProgresso(props.item)"
                                    style="margin: 1px 0px"
                                    height="5"
                                >
                                    {{ props.item.qtComprovado }}
                                </v-progress-circular>
                                <span> {{ obterTextoProgresso(props.item) }} </span>
                            </v-tooltip>
                        </td>
                        <td>{{ props.item.Seq }}</td>
                        <td>
                            {{ props.item.item }}
                        </td>
                        <td class="text-xs-right">
                            {{ props.item.varlorAprovado | moedaMasc }}
                        </td>
                        <td class="text-xs-right">
                            {{ props.item.varlorComprovado | moedaMasc }}
                        </td>
                        <td class="text-xs-right">
                            {{ (props.item.varlorAprovado - props.item.varlorComprovado) | moedaMasc }}
                        </td>
                        <td>
                            <v-btn
                                v-if="podeEditar(props.item.varlorComprovado)"
                                color="primary"
                                dark
                                icon
                                title="Avaliar comprovantes do item"
                                @click="avaliarItem(props.item)"
                            >
                                <v-icon>
                                    gavel
                                </v-icon>
                            </v-btn>
                        </td>
                    </template>
                </v-data-table>
            </template>
        </planilha-avaliacao-financeira>
        <analisar-item
            v-if="isModalVisible === 'avaliacao-item'"
            :id-pronac="idPronac"
            :item="itemEmAvaliacao.item"
            :descricao-produto="itemEmAvaliacao.produto"
            :descricao-etapa="itemEmAvaliacao.etapa"
            :uf="itemEmAvaliacao.Uf"
            :produto="itemEmAvaliacao.cdProduto"
            :idmunicipio="itemEmAvaliacao.cdCidade"
            :etapa="itemEmAvaliacao.cdEtapa"
            :cd-produto="itemEmAvaliacao.cdProduto"
            :cd-uf="itemEmAvaliacao.cdUF"
            :dt-inicio-execucao="dadosProjeto.items.dtInicioExecucao"
            :dt-fim-execucao="dadosProjeto.items.dtFimExecucao"
        />
        <v-speed-dial
            v-if="(!isProjetoDiligenciado)"
            v-model="fab"
            bottom
            right
            direction="top"
            open-on-hover
            transition="slide-y-reverse-transition"
            fixed
        >
            <v-btn
                slot="activator"
                v-model="fab"
                color="red darken-2"
                dark
                fab
            >
                <v-icon>menu</v-icon>
                <v-icon>close</v-icon>
            </v-btn>
            <v-tooltip
                v-if="(documento != 0)"
                left
            >
                <v-btn
                    slot="activator"
                    :href="'/assinatura/index/visualizar-projeto?idDocumentoAssinatura=' + documento.idDocumentoAssinatura"
                    fab
                    dark
                    small
                    color="green"
                >
                    <v-icon>edit</v-icon>
                </v-btn>
                <span>Assinar</span>
            </v-tooltip>
            <v-tooltip
                v-if="(documento == 0 && !isProjetoDiligenciado)"
                left
            >
                <v-btn
                    slot="activator"
                    :to="'/emitir-parecer/' + idPronac"
                    fab
                    dark
                    small
                    color="teal"
                    @click.native="getConsolidacao(idPronac)"
                >
                    <v-icon>gavel</v-icon>
                </v-btn>
                <span>Emitir Parecer</span>
            </v-tooltip>
            <v-tooltip
                left
            >
                <v-btn
                    slot="activator"
                    fab
                    dark
                    small
                    color="green"
                    @click.native="confirmacaoValidarTodos = true"
                >
                    <v-icon>thumb_up</v-icon>
                </v-btn>
                <span>Validar todos</span>
            </v-tooltip>
            <v-tooltip
                v-if="(documento == 0) && !isProjetoDiligenciado"
                left
            >
                <v-btn
                    slot="activator"
                    :to="'/diligenciar/' + idPronac"
                    fab
                    dark
                    small
                    color="red ligthen-4"
                >
                    <v-icon>warning</v-icon>
                </v-btn>
                <span>Diligenciar</span>
            </v-tooltip>
        </v-speed-dial>
        <v-dialog
            v-model="confirmacaoValidarTodos"
            width="500"
        >
            <v-card>
                <v-card-title
                    class="headline primary white--text"
                    primary-title
                >
                    Validar todos
                </v-card-title>

                <v-card-text >
                    Deseja realmente aprovar todos os comprovantes?
                </v-card-text>

                <v-divider/>

                <v-card-actions>
                    <v-spacer/>
                    <v-btn
                        color="red"
                        flat
                        @click="confirmacaoValidarTodos = false"
                    >
                        Não
                    </v-btn>
                    <v-btn
                        color="primary"
                        flat
                        @click="aprovarTodos()"
                    >
                        Sim
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import Vue from 'vue';
import { mapActions, mapGetters } from 'vuex';
import AnalisarItem from './AnalisarItem';
import Moeda from '../../../../filters/money';
import CONST from '../../const';
import PlanilhaAvaliacaoFinanceira from '@/modules/avaliacaoResultados/components/planilha/PlanilhaAvaliacaoFinanceira';

Vue.filter('moedaMasc', Moeda);

export default {
    name: 'Planilha',
    components: {
        PlanilhaAvaliacaoFinanceira,
        AnalisarItem,
    },
    data() {
        return {
            CONST,
            headers: [
                {
                    text: 'Análise', value: 'qtComprovado', sortable: true, align: 'left', width: 0,
                },
                {
                    text: '#', value: 'seq', sortable: true, width: 1,
                },
                { text: 'Item', value: 'item', sortable: true },
                {
                    text: 'Vl. Aprovado (R$)', value: 'varlorAprovado', sortable: true, align: 'right',
                },
                {
                    text: 'Vl. Comprovado (R$)', value: 'varlorComprovado', sortable: true, align: 'right',
                },
                {
                    text: 'Vl. a Comprovar (R$)', value: 'vlAComprovar', sortable: true, align: 'right',
                },
                { text: '', value: 'varlorComprovado', sortable: false },
            ],
            confirmacaoValidarTodos: false,
            fab: false,
            idPronac: this.$route.params.id,
            itemEmAvaliacao: {},
            agrupamentos: [
                'Produto',
                'descEtapa',
                'uf',
                'cidade',
            ],
            totaisPlanilha: [],
            mostrarListagem: false,
            ocultarItensNaoComprovados: false,
        };
    },
    computed: {
        ...mapGetters({
            getProjetoAnalise: 'avaliacaoResultados/projetoAnalise',
            isModalVisible: 'modal/default',
        }),
        dadosProjeto() {
            if (Object.keys(this.getProjetoAnalise).length > 0) {
                return this.getProjetoAnalise.data;
            }
            return {};
        },
        documento() {
            if (!this.dadosProjeto.items) {
                return 0;
            }

            let { documento } = this.getProjetoAnalise.data.items;
            documento = documento !== null ? this.getProjetoAnalise.data.items.documento : 0;
            return documento;
        },
        estado() {
            if (!this.dadosProjeto.items) {
                return null;
            }

            let { estado } = this.getProjetoAnalise.data.items;
            estado = (estado !== null) ? this.getProjetoAnalise.data.items.estado : 0;
            return estado;
        },
        isProjetoDiligenciado() {
            if (!this.dadosProjeto.items) {
                return false;
            }

            const { diligencia } = this.dadosProjeto.items;
            return diligencia && diligencia.DtSolicitacao && !diligencia.DtResposta;
        },
    },

    methods: {
        ...mapActions({
            modalOpen: 'modal/modalOpen',
            requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
            validarTodosComprovantes: 'avaliacaoResultados/validarTodosComprovantes',
            mensagemSucesso: 'noticias/mensagemSucesso',
            mensagemErro: 'noticias/mensagemErro',
        }),
        aprovarTodos() {
            this.validarTodosComprovantes(this.idPronac).then(() => {
                this.confirmacaoValidarTodos = false;
                this.mensagemSucesso('Comprovantes validados com sucesso!');
                window.location.reload();
            }).error((e) => {
                this.mensagemErro(e.response.data.error.message);
            });
        },
        getConsolidacao(id) {
            this.requestEmissaoParecer(id);
        },
        moeda: (moedaString) => {
            const moeda = Number(moedaString);
            return moeda.toLocaleString('pt-br', { currency: 'BRL' });
        },
        podeEditar(varlorComprovado) {
            if (varlorComprovado !== 0
                && !this.dadosProjeto.items.diligencia
                && this.documento.length === 0
            ) {
                return true;
            }

            return false;
        },
        avaliarItem(item) {
            this.itemEmAvaliacao = {
                item,
                produto: item.Produto,
                etapa: item.descEtapa,
                Uf: item.uf,
                cdProduto: item.cdProduto,
                cdCidade: item.cdCidade,
                cdEtapa: item.cdEtapa,
                cdUF: item.cdUF,
            };
            this.modalOpen('avaliacao-item');
        },
        goBack() {
            if (window.history.length > 1) {
                this.$router.go(-1);
            } else {
                this.$router.push('/');
            }
        },
        obterPercentualProgresso(item) {
            return ((item.qtComprovadoValidado + item.qtComprovadoRecusada) / item.qtComprovado) * 100;
        },
        obterCorProgresso(item) {
            let cor = 'green';
            if (item.qtComprovadoRecusada > 0) {
                cor = 'orange';
            }

            if (item.qtComprovadoRecusada > item.qtComprovadoValidado) {
                cor = 'red';
            }
            return cor;
        },
        obterTextoProgresso(item) {
            return `${item.qtComprovadoValidado} validado(s) e ${item.qtComprovadoRecusada} recusado(s) `
                + `de ${item.qtComprovado} comprovado(s)`;
        },
    },
};
</script>
