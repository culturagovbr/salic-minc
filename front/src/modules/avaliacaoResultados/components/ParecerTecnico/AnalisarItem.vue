<template>
    <v-layout
        row
        justify-center
    >
        <v-dialog
            :value="isModalVisible === 'avaliacao-item'"
            scrollable
            fullscreen
            transition="dialog-bottom-transition"
            hide-overlay
            @keydown.esc="fecharModal"
        >
            <v-card>
                <v-toolbar
                    dark
                    color="primary"
                >
                    <v-btn
                        icon
                        dark
                        @click.native="fecharModal"
                    >
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>
                        Avaliar comprovantes
                        <span v-if="item"> do item <b>"{{ item.item }}"</b> - {{ item.cidade }}/{{ item.uf }}</span>
                    </v-toolbar-title>
                </v-toolbar>

                <v-card-text>
                    <v-subheader>Dados da Comprovação</v-subheader>
                    <v-container
                        fluid
                        grid-list-md
                        class="pa-10 elevation-0"
                    >
                        <v-layout wrap>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Produto:</b> {{ item.Produto }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Etapa:</b> {{ item.descEtapa }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Item de Custo:</b> {{ item.item }}
                            </v-flex>
                        </v-layout>
                        <v-divider class="my-2" />
                        <v-layout wrap>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Quantidade:</b> {{ item.quantidade }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Ocorrências:</b> {{ item.numeroOcorrencias }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Valor unitário:</b> {{ item.valor | moeda }}
                            </v-flex>
                        </v-layout>
                        <v-divider class="my-2" />
                        <v-layout wrap>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Valor Aprovado:</b> {{ item.vlAprovado | moeda }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Valor Comprovado:</b> {{ item.vlComprovado | moeda }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Comprovação Validada:</b> {{ valorComprovacaoValidada | moeda }}
                            </v-flex>
                        </v-layout>

                        <v-divider class="my-2" />

                        <v-layout wrap>
                            <v-flex
                                xs12
                                sm6
                                md4
                            >
                                <b>Período de execução:</b> {{ dtInicioExecucao | formatarData }} a {{ dtFimExecucao | formatarData }}
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <lista-de-comprovantes :comprovantes="comprovantes">
                        <template
                            slot="slot-comprovantes"
                            slot-scope="{ props }"
                        >
                            <analisar-item-formulario :comprovante="props" />
                        </template>
                    </lista-de-comprovantes>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ListaDeComprovantes from '@/modules/avaliacaoResultados/components/components/ListaDeComprovantes';
import MxPlanilha from '@/mixins/planilhas';
import AnalisarItemFormulario from '@/modules/avaliacaoResultados/components/ParecerTecnico/AnalisarItemFormulario';

export default {
    name: 'AnalisarItem',
    components: { AnalisarItemFormulario, ListaDeComprovantes },
    filters: {
        moeda: (moedaString) => {
            const moeda = Number(moedaString);
            return moeda.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
        },
    },
    mixins: [MxPlanilha],
    props: {
        item: { type: Object, default: () => {} },
        dtInicioExecucao: { type: String, default: '' },
        dtFimExecucao: { type: String, default: '' },
    },
    data() {
        return {
            comprovantesIsLoading: false,
        };
    },
    computed: {
        ...mapGetters({
            comprovantes: 'avaliacaoResultados/comprovantes',
            isModalVisible: 'modal/default',
        }),
        valorComprovacaoValidada() {
            if (Object.keys(this.comprovantes).length === 0) {
                return 0;
            }
            return this.comprovantes
                .map((item) => {
                    if (item.stItemAvaliado === '1') {
                        return item.valor;
                    }
                    return 0;
                }).reduce((total, valor) => total + valor);
        },
    },
    mounted() {
        if (this.isModalVisible === 'avaliacao-item') {
            this.atualizarComprovantes(true);
        }
    },
    methods: {
        ...mapActions({
            alterarPlanilha: 'avaliacaoResultados/alterarPlanilha',
            obterDadosItemComprovacao: 'avaliacaoResultados/obterDadosItemComprovacao',
            atualizarEstatisticasAvaliacaoAction: 'avaliacaoResultados/buscarEstatisticasAvaliacao',
            modalClose: 'modal/modalClose',
        }),
        getUrlParams() {
            return this.$route.params[0];
        },
        atualizarComprovantes(loading) {
            let params = '';
            if (typeof this.getUrlParams() !== 'undefined') {
                params = this.getUrlParams();
            } else {
                const idPronac = `idPronac/${this.item.IdPRONAC}`;
                const uf = `uf/${this.item.uf}`;
                const produto = `produto/${this.item.cdProduto}`;
                const idMunicipio = `idmunicipio/${this.item.cdCidade}`;
                const idPlanilhaItem = `idPlanilhaItem/${this.item.idPlanilhaItens}`;
                const etapa = `etapa/${this.item.cdEtapa}`;
                const idUf = `idUf/${this.item.cdUF}`;
                params = `${idPronac}/${idPlanilhaItem}/${produto}/${uf}/${idMunicipio}/${etapa}/${idUf}`;
            }

            if (loading) {
                this.comprovantesIsLoading = true;
                this.obterDadosItemComprovacao(params).catch().then(() => {
                    this.comprovantesIsLoading = false;
                });
            }
        },
        fecharModal() {
            this.modalClose();
            this.alterarPlanilha({
                cdProduto: this.item.cdProduto,
                cdEtapa: this.item.cdEtapa,
                cdUF: this.item.cdUF,
                cdCidade: this.item.cdCidade,
                idPlanilhaItens: this.item.idPlanilhaItens,
            });
            this.atualizarEstatisticasAvaliacaoAction(this.item.IdPRONAC);
        },
    },
};
</script>
