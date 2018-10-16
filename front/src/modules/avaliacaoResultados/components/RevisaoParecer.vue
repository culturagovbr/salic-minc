<template>

    <v-layout row justify-center>

        <v-dialog v-model="dialog"
                  scrollable
                  fullscreen
                  transition="dialog-bottom-transition"
                  hide-overlay
        >
            <v-btn
                slot="activator"
                color="red"
                dark
                small
                title="Comprovar Item"
            >
                <v-icon>gavel</v-icon>
            </v-btn>
            <v-card>
                <v-toolbar dark color="green darken-3">
                    <v-btn icon dark @click.native="dialog = false">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Analise de Resultados - Revisão do Parecer Técnico </v-toolbar-title>
                </v-toolbar>
                <v-container >
                    <v-card-text>

                        <v-card>
                            <v-card-title primary-title>
                                <v-container pa-0 ma-0>
                                <div>
                                    <div class="headline"><b>Projeto:</b> {{projeto.AnoProjeto}}{{projeto.Sequencial}} - {{projeto.NomeProjeto}}</div>
                                    <span class="black--text"><b>Proponente:</b> {{proponente.CgcCpf}} - {{proponente.Nome}}</span>
                                </div>
                                </v-container>
                            </v-card-title>

                            <v-card-text>

                                <v-container grid-list-xs text-xs-center ma-0 pa-0>

                                    <v-layout row wrap>
                                        <v-flex xs12 md6 mb-2>

                                            <v-data-table
                                                :items="[]"
                                                class="elevation-2"
                                                hide-headers
                                                hide-actions
                                            >
                                                <template slot="no-data">
                                                    <tr>
                                                        <th colspan="6">Quantidade de Comprovantes</th>
                                                    </tr>
                                                    <tr>
                                                        <td left><b>Total:</b></td>
                                                        <td >{{consolidacaoComprovantes.qtTotalComprovante}}</td>
                                                        <td left><b>Validados:</b></td>
                                                        <td><font color="#006400">{{consolidacaoComprovantes.qtComprovantesValidadosProjeto}} </font></td>
                                                    </tr>
                                                    <tr>
                                                        <td left><b>Não Avaliados:</b></td>
                                                        <td left>{{consolidacaoComprovantes.qtComprovantesNaoAvaliados}}</td>
                                                        <td left><b>Recusados:</b></td>
                                                        <td left><font color="red">{{consolidacaoComprovantes.qtComprovantesRecusadosProjeto}} </font></td>
                                                    </tr>
                                                </template>
                                            </v-data-table>

                                        </v-flex>




                                        <v-flex xs12 md6 mb-4>

                                            <v-data-table
                                                :items="[]"
                                                class="elevation-1"
                                                hide-headers
                                                hide-actions
                                            >
                                                <template slot="no-data">
                                                    <tr>
                                                        <th colspan="6">Valores Comprovados</th>
                                                    </tr>
                                                    <tr>
                                                        <td left><b>Total:</b></td>
                                                        <td >{{consolidacaoComprovantes.vlComprovadoProjeto}}</td>
                                                        <td left><b>Validados:</b></td>
                                                        <td><font color="#006400">{{consolidacaoComprovantes.vlComprovadoValidado}}</font></td>
                                                    </tr>
                                                    <tr>
                                                        <td left><b>Não Avaliados:</b></td>
                                                        <td left>{{consolidacaoComprovantes.vlNaoComprovado}}</td>
                                                        <td left><b>Recusados:</b></td>
                                                        <td left><font color="red">{{consolidacaoComprovantes.vlComprovadoRecusado}}</font></td>
                                                    </tr>
                                                </template>
                                            </v-data-table>

                                        </v-flex>
                                        <v-divider></v-divider>

                                        <v-flex md12 xs12>
                                            <v-data-table
                                                :items="[]"
                                                class="elevation-1"
                                                hide-headers
                                                hide-actions
                                                flat
                                            >
                                                <template slot="no-data">
                                                    <tr>
                                                        <th class="text-sm-left">Parecer Tecnico</th>
                                                    </tr>
                                                    <tr>
                                                        <td>{{parecer.dsParecer}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td :onchange="setStatus()"><b>Manifestação:</b> {{item}}</td>
                                                    </tr>
                                                </template>
                                            </v-data-table>
                                        </v-flex>

                                    </v-layout>
                                </v-container>
                            </v-card-text>
                        </v-card>

<v-divider></v-divider>
                        <v-flex xs12 md12>
                            <v-card>

                                <v-card-title primary-title>
                                    <div>
                                        <div class="black--text"><b>Histórico de Revisões</b></div>
                                    </div>
                                    <v-spacer></v-spacer>
                                    <v-btn icon @click="show = !show">
                                        <v-icon>{{ show ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}}</v-icon>
                                    </v-btn>
                                </v-card-title>
                                <v-slide-y-transition>
                                    <v-card-text v-show="show">




                                        <v-expansion-panel mb-2 focusable v-for="revisado in historico" :key="revisado.idAvaliacaoFinanceiraRevisao">
                                            <v-expansion-panel-content >
                                                <v-layout slot="header" class="blue--text">
                                                    <v-icon class="mr-3 blue--text" >insert_drive_file
                                                    </v-icon>
                                                    <span v-if="revisado.idGrupoAtivo === 125" >Revisão - Coordenador(a) - {{revisado.dtRevisao | date}}</span>
                                                    <span v-if="revisado.idGrupoAtivo === 126">Revisão - Coordenador(a) Geral - {{revisado.dtRevisao | date}}</span>
                                                    <v-spacer></v-spacer>
                                                    <template v-if="revisado.siStatus == 1" :onchange="revisado.siStatus">
                                                        <v-chip small color="green" text-color="white" >
                                                            <v-avatar>
                                                                <v-icon>check_circle</v-icon>
                                                            </v-avatar>
                                                            Aprovado
                                                        </v-chip>
                                                    </template>
                                                    <template v-if="revisado.siStatus == 0" :onchange="revisado.siStatus">
                                                        <v-chip small color="red" text-color="white">
                                                            <v-avatar>
                                                                <v-icon>close</v-icon>
                                                            </v-avatar>
                                                            Reprovado
                                                        </v-chip>
                                                    </template>
                                                    <template v-if="revisado.siStatus == 2" :onchange="revisado.siStatus">
                                                        <v-chip small color="grey" text-color="white">
                                                            <v-avatar>
                                                                <v-icon>report_problem</v-icon>
                                                            </v-avatar>
                                                            Não Avaliado
                                                        </v-chip>
                                                    </template>

                                                </v-layout>

                                                <v-card
                                                    :color="background[revisado.siStatus]"
                                                    flat
                                                    tile
                                                >
                                                    <v-flex >

                                                        <v-card-text>
                                                            <v-card>
                                                                    <v-data-table
                                                                        :items="[]"
                                                                        class="elevation-2"
                                                                        hide-headers
                                                                        hide-actions
                                                                    >
                                                                        <template slot="no-data">
                                                                            <tr>
                                                                                <th left><b>Revisão:</b></th>
                                                                                <td colspan="7">
                                                                                    <v-radio-group row v-model="revisado.siStatus" :disabled="true">
                                                                                        <v-radio label="Aprovado" :value="1" ></v-radio>
                                                                                        <v-radio label="Reprovado" :value="0" color="red"></v-radio>
                                                                                    </v-radio-group>
                                                                                </td>
                                                                            </tr>
                                                                        </template>
                                                                    </v-data-table>

                                                                    <v-textarea
                                                                        :disabled="true"
                                                                        solo
                                                                        no-resize
                                                                        :value="revisado.dsRevisao"
                                                                        hint="Digite sua avaliação"
                                                                        height="180px"
                                                                    ></v-textarea>
                                                            </v-card>
                                                        </v-card-text>
                                                    </v-flex>
                                                </v-card>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>



                                    </v-card-text>
                                </v-slide-y-transition>
                            </v-card>
                        </v-flex>



                        <v-expansion-panel mb-2 v-if="perfilAtivo.revisar">
                            <v-expansion-panel-content >
                                <v-layout slot="header" class="blue--text">
                                    <v-icon class="mr-3 blue--text" >insert_drive_file
                                    </v-icon>
                                    <span v-if="grupo.codGrupo == 125">Revisão - Coordenador(a)</span>
                                    <span v-if="grupo.codGrupo == 126">Revisão - Coordenador(a) Geral</span>
                                    <v-spacer></v-spacer>
                                    <template v-if="revisao.siStatus == 1" :onchange="revisao.siStatus">
                                    <v-chip small color="green" text-color="white" >
                                        <v-avatar>
                                            <v-icon>check_circle</v-icon>
                                        </v-avatar>
                                        Aprovado
                                    </v-chip>
                                    </template>
                                    <template v-if="revisao.siStatus == 0" :onchange="revisao.siStatus">
                                    <v-chip small color="red" text-color="white">
                                        <v-avatar>
                                            <v-icon>close</v-icon>
                                        </v-avatar>
                                        Reprovado
                                    </v-chip>
                                    </template>
                                    <template v-if="revisao.siStatus == 2" :onchange="revisao.siStatus">
                                        <v-chip small color="grey" text-color="white">
                                            <v-avatar>
                                                <v-icon>report_problem</v-icon>
                                            </v-avatar>
                                            Não Avaliado
                                        </v-chip>
                                    </template>

                                </v-layout>

                                <v-card
                                    :color="background[revisao.siStatus]"
                                    flat
                                    tile
                                >
                                    <v-flex >

                                        <v-card-text>
                                            <v-card>

                                                <v-card-text class="elevation-2">
                                                    <v-data-table
                                                        :items="[]"
                                                        class="elevation-2"
                                                        hide-headers
                                                        hide-actions
                                                    >
                                                        <template slot="no-data">
                                                            <tr>
                                                                <th left><b>Revisão:</b></th>
                                                                <td colspan="7">
                                                                    <v-radio-group row v-model="revisao.siStatus" :disabled="!perfilAtivo.revisar">
                                                                        <v-radio label="Aprovado" :value="1" color="green"></v-radio>
                                                                        <v-radio label="Reprovado" :value="0" color="red"></v-radio>
                                                                    </v-radio-group>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </v-data-table>

                                                    <v-textarea
                                                        @input="inputRevisao($event)"
                                                        :disabled="!perfilAtivo.revisar"
                                                        solo
                                                        no-resize
                                                        :value="revisao.dsRevisao"
                                                        hint="Digite sua avaliação"
                                                        height="180px"
                                                    ></v-textarea>
                                                    <div>
                                                        <v-btn dark depressed small color="primary" @click.native="salvar()" v-if="perfilAtivo.revisar">
                                                            Salvar
                                                        </v-btn>
                                                    </div>
                                                </v-card-text>
                                            </v-card>
                                        </v-card-text>
                                    </v-flex>
                                </v-card>
                            </v-expansion-panel-content>
                        </v-expansion-panel>

                    </v-card-text>
                </v-container>

                <v-snackbar
                    v-model="snackbar"
                    :right="right"
                    :timeout="5000"
                    :top="top"
                >
                    Revisão efetuada!
                    <v-btn
                        color="pink"
                        flat
                        @click="snackbar = false"
                    >
                        Close
                    </v-btn>
                </v-snackbar >
            </v-card>
        </v-dialog>
    </v-layout>
</template>


<script>
    import { mapActions, mapGetters } from 'vuex';

    export default {
        name: 'RevisaoParecer',
        data() {
            return {
                snackbar: false,
                show: false,
                dialog: false,
                perfilAtivo: {
                    cordenador: false,
                    geral: false,
                    revisar: false,
                },
                revisao: {
                    siStatus: 2,
                    dsRevisao: '',
                    idAvaliacaoFinanceira: 0,
                    idGrupoAtivo: 21,
                    idAgente: 333,
                },
                revisaoGeral: {
                    siStatus: 2,
                    dsRevisao: '',
                    idAvaliacaoFinanceira: 0,
                    idGrupoAtivo: 21,
                    idAgente: 333,
                },
                background: [
                    'red lighten-4',
                    'green lighten-4',
                ],
                parecerData: { },
                items: [
                    {
                        id: 'R',
                        text: 'Reprovação',
                    },
                    {
                        id: 'A',
                        text: 'Aprovação',
                    },
                    {
                        id: 'P',
                        text: 'Aprovação com Ressalva',
                    },
                ],
                item: '',
            };
        },
        methods:
          {
              ...mapActions({
                  requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
                  listaRevisoes: 'avaliacaoResultados/obterHistoricoRevisao',
                  salvarRevisao: 'avaliacaoResultados/salvarRevisao',

              }),
              getConsolidacao(id) {
                  this.requestEmissaoParecer(id);
                  this.setStatus();
              },
              carregarHistorico(id) {
                  this.listaRevisoes(id);
              },
              setStatus() {
                  if (this.grupo.codGrupo == 125) {
                      /** corrdenador habilitado */
                      this.perfilAtivo.cordenador = false;
                      this.perfilAtivo.geral = true;
                      this.perfilAtivo.revisar = true;
                  } else if (this.grupo.codGrupo == 126) {
                      /**  cordenador Geral habilitado */
                      this.perfilAtivo.cordenador = true;
                      this.perfilAtivo.geral = false;
                      this.perfilAtivo.revisar = true;
                  } else { /** todos sem editar */
                      this.perfilAtivo.cordenador = true;
                      this.perfilAtivo.geral = true;
                      this.perfilAtivo.revisar = false;
                  }
                  this.items.forEach((i) => {
                      if (i.id === this.getParecer.siManifestacao) {
                          this.item = i.text;
                      }
                  });
              },
              inputRevisao(e) {
                  this.revisao.dsRevisao = e;
              },
              salvar() {
                  this.revisao.idAvaliacaoFinanceira = this.parecer.idAvaliacaoFinanceira;
                  this.revisao.idGrupoAtivo = this.grupo.codGrupo;
                  this.revisao.idAgente = this.agente[0].usu_codigo;
                  this.salvarRevisao(this.revisao);
                  this.snackbar = true;

              },
          },
        computed:
          {
              ...mapGetters({
                  modalVisible: 'modal/default',
                  consolidacaoComprovantes: 'avaliacaoResultados/consolidacaoComprovantes',
                  proponente: 'avaliacaoResultados/proponente',
                  parecer: 'avaliacaoResultados/parecer',
                  projeto: 'avaliacaoResultados/projeto',
                  grupo: 'menuSuperior/grupoAtivo',
                  agente: 'menuSuperior/usuarioAtivo',
                  historico: 'avaliacaoResultados/revisaoParecer',
              }),
          },
        mounted() {
            // this.getConsolidacao(this.$route.params.id);
            // this.carregarHistorico(this.parecer.idAvaliacaoFinanceira);
            this.getConsolidacao(195025);
            this.carregarHistorico(1);
        },
};
</script>

<style scoped>

</style>
