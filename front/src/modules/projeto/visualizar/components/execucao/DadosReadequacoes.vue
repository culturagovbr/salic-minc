<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Dados das Readequações'"/>
        </div>
        <v-flex v-else-if="Object.keys(gruposReadequacao).length > 0">
            <v-expansion-panel
                popout
                focusable>
                <v-expansion-panel-content
                    v-for="(dadoAgrupado, titulo) in gruposReadequacao"
                    :key="dadoAgrupado[0].idReadequacao"
                    class="elevation-1"
                >
                    <v-layout
                        slot="header"
                        class="primary--text">
                        <v-icon class="mr-3 primary--text">
                            {{ dadoAgrupado[0].idTipoReadequacao | filtrarIcone }}
                        </v-icon>
                        <span>{{ titulo }} ({{ dadoAgrupado.length }})</span>
                    </v-layout>
                    <v-data-table
                        :headers="headers"
                        :items="dadoAgrupado"
                        class="elevation-1"
                    >
                        <template
                            slot="items"
                            slot-scope="props">
                            <td class="text-xs-left">
                                {{ (props.item.stAtendimento === 'I') ? 'Rejeitado' : 'Recebido' }}
                            </td>
                            <td class="text-xs-center pl-5">
                                {{ props.item.dtSolicitacao | formatarData }}
                            </td>
                            <td
                                class="text-xs-left"
                                v-html="props.item.dsEncaminhamento"/>
                            <td class="text-xs-center pl-5">
                                {{ props.item.dtAvaliador | formatarData }}
                            </td>
                            <td class="text-xs-center">
                                <v-tooltip bottom>
                                    <v-btn
                                        slot="activator"
                                        flat
                                        icon
                                        @click="showItem(props.item)"
                                    >
                                        <v-icon>visibility</v-icon>
                                    </v-btn>
                                    <span>Visualizar Projeto</span>
                                </v-tooltip>
                            </td>
                        </template>
                        <template
                            slot="pageText"
                            slot-scope="props">
                            Items {{ props.pageStart }} -
                            {{ props.pageStop }} de
                            {{ props.itemsLength }}
                        </template>
                    </v-data-table>
                </v-expansion-panel-content>
            </v-expansion-panel>
            <v-dialog v-model="dialog">
                <v-card>
                    <v-card-text v-if="readequacao">
                        <v-container
                            grid-list-md
                            text-xs-left>
                            <div>
                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg12
                                        dark
                                        class="text-xs-left">
                                        <b><h4>SOLICITAÇÃO DO PROPONENTE</h4></b>
                                        <v-divider class="pb-2"/>
                                    </v-flex>
                                    <v-flex>
                                        <b>Arquivo</b><br>
                                        <a
                                            v-if="readequacao.idArquivo"
                                            :href="`/upload/abrir?id=${readequacao.idArquivo}`"
                                        >
                                            <span v-html="readequacao.nmArquivo"/>
                                        </a>
                                        <span v-else>
                                            -
                                        </span>
                                    </v-flex>
                                    <v-flex>
                                        <b>Data envio</b>
                                        <p v-if="readequacao.dtEnvio">
                                            {{ readequacao.dtEnvio | formatarData }}
                                        </p>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                    <v-flex>
                                        <b>Data solicitação</b>
                                        <p>{{ readequacao.dtSolicitacao | formatarData }} </p>
                                    </v-flex>
                                    <v-flex class="text-xs-center">
                                        <b>Data solicitação</b>
                                        <p class="text-xs-center">
                                            {{ readequacao.dtSolicitacao | formatarData }}
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    row
                                    justify-space-between>
                                    <v-flex>
                                        <b>Dados da solicitação</b>
                                        <p
                                            v-if="readequacao.dsSolicitacao"
                                            v-html="readequacao.dsSolicitacao"/>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    row
                                    justify-space-between>
                                    <v-flex>
                                        <b>Justificativa da solicitação</b>
                                        <p v-html="readequacao.dsJustificativa"/>
                                    </v-flex>
                                </v-layout>
                            </div>
                            <br>
                            <div v-if="validarAcessoSituacao(readequacao.siEncaminhamento)">
                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg12
                                        dark
                                        class="text-xs-left">
                                        <b><h4>AVALIAÇÃO</h4></b>
                                        <v-divider class="pb-2"/>
                                    </v-flex>
                                    <v-flex>
                                        <b>Situação</b>
                                        <p v-if="readequacao.stAtendimento === 'I'">
                                            Rejeitado
                                        </p>
                                        <p v-else>
                                            Recebido
                                        </p>
                                    </v-flex>
                                    <v-flex>
                                        <b>Data avaliação</b>
                                        <p v-if="readequacao.dtAvaliador">
                                            {{ readequacao.dtAvaliador | formatarData }}
                                        </p>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                    <v-flex>
                                        <b>Descrição da avaliação</b>
                                        <p v-html="readequacao.dsAvaliacao"/>
                                    </v-flex>
                                </v-layout>
                            </div>
                            <br>
                            <div v-if="readequacao.siEncaminhamento === 15">
                                <v-list
                                    v-for="(parecer, index) in readequacao.pareceres"
                                    :key="index">
                                    <v-flex
                                        lg12
                                        dark
                                        class="text-xs-left">
                                        <b><h4>PARECER TÉCNICO</h4></b>
                                        <v-divider class="pb-2"/>
                                    </v-flex>
                                    <v-layout
                                        row
                                        justify-space-between>
                                        <v-flex>
                                            <b>Parecer favorável?</b>
                                            <p v-if="parecer.ParecerFavoravel === '2'">
                                                SIM
                                            </p>
                                            <p v-else>
                                                NÂO
                                            </p>
                                        </v-flex>

                                        <v-flex>
                                            <b>Data parecer</b>
                                            <p> {{ parecer.DtParecer | formatarData }}</p>
                                        </v-flex>
                                    </v-layout>
                                    <v-layout
                                        row
                                        justify-space-between>
                                        <v-flex>
                                            <b>Descrição do parecer - Técnico / Parecerista</b>
                                            <p v-html="parecer.ResumoParecer"/>
                                        </v-flex>
                                    </v-layout>
                                </v-list>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-divider/>
                    <v-card-actions>
                        <v-spacer/>
                        <v-btn
                            color="red"
                            flat
                            @click="dialog = false"
                        >
                            Fechar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <ReadequacoesDevolvidas/>
        </v-flex>
        <v-layout v-else>
            <v-container
                grid-list-md
                text-xs-center>
                <v-layout
                    row
                    wrap>
                    <v-flex>
                        <v-card>
                            <v-card-text class="px-0">Nenhuma readequação encontrada</v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-layout>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import ReadequacoesDevolvidas from './components/ReadequacoesDevolvidas';
import { utils } from '@/mixins/utils';

export default {
    name: 'DadosReadequacoes',
    components: {
        Carregando,
        ReadequacoesDevolvidas,
    },
    filters: {
        filtrarIcone(tipo) {
            let icone = '';
            switch (tipo) {
            case 1:
            case 2:
            case 23:
            case 22:
                icone = 'attach_money';
                break;
            case 4:
                icone = 'account_balance';
                break;
            case 9:
                icone = 'place';
                break;
            case 10:
                icone = 'people';
                break;
            case 13:
                icone = 'timer';
                break;
            case 12:
                icone = 'short_text';
                break;
            case 14:
            case 11:
                icone = 'perm_media';
                break;
            case 20:
                icone = 'date_range';
                break;
            case 16:
                icone = 'playlist_add_check';
                break;
            default:
                icone = 'subject';
            }
            return icone;
        },
    },
    mixins: [utils],
    props: {
        idPronac: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            readequacao: {},
            dialog: false,
            loading: true,
            gruposReadequacao: {},
            headers: [
                {
                    text: 'PROTOCOLO',
                    align: 'left',
                    value: 'stAtendimento',
                },
                {
                    text: 'DT. SOLICITAÇÃO',
                    align: 'center',
                    value: 'dtSolicitacao',
                },
                {
                    text: 'SITUAÇÃO',
                    align: 'left',
                    value: 'dsEncaminhamento',
                },
                {
                    text: 'DT. AVALIAÇÃO',
                    align: 'center',
                    value: 'dtAvaliador',
                },
                {
                    text: 'VISUALIZAR',
                    align: 'center',
                    value: 'dsAvaliacao',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'execucao/dadosReadequacoes',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.loading = true;
            this.buscarDadosReadequacoes(value.idPronac);
        },
        dados() {
            this.loading = false;
            this.gruposReadequacao = this.obterGrupoReadequacoes();
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarDadosReadequacoes(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarDadosReadequacoes: 'execucao/buscarDadosReadequacoes',
        }),
        showItem(item) {
            this.readequacao = item;
            this.dialog = true;
        },
        validarAcessoSituacao(siEncaminhamento) {
            const permitidos = [2, 3, 4, 5, 6, 7, 9, 10, 15];
            return permitidos.indexOf(siEncaminhamento) !== -1;
        },
        obterGrupoReadequacoes() {
            const gruposReadequacao = {};
            const { dadosReadequacoes } = this.dados;

            dadosReadequacoes.forEach((readequacao) => {
                const { tipoReadequacao } = readequacao;
                if (gruposReadequacao[tipoReadequacao] == null
                        || gruposReadequacao[tipoReadequacao].length < 1) {
                    gruposReadequacao[tipoReadequacao] = [];
                }
                gruposReadequacao[tipoReadequacao].push(
                    readequacao,
                );
            });

            return gruposReadequacao;
        },
    },
};
</script>
