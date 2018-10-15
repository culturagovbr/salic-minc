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


                        <v-expansion-panel mb-2>
                            <v-expansion-panel-content >
                                <v-layout slot="header" class="blue--text">
                                    <v-icon class="mr-3 blue--text" >insert_drive_file
                                    </v-icon>
                                    Revisão - Coordenador(a)
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
                                                                    <v-radio-group row v-model="revisao.siStatus" :disabled="perfilAtivo.cordenador">
                                                                        <v-radio label="Aprovado" :value="1" ></v-radio>
                                                                        <v-radio label="Reprovado" :value="0" color="red"></v-radio>
                                                                    </v-radio-group>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </v-data-table>

                                                    <v-textarea
                                                        :disabled="perfilAtivo.cordenador"
                                                        solo
                                                        no-resize
                                                        :value="revisao.dsRevisao"
                                                        hint="Digite sua avaliação"
                                                        height="180px"
                                                    ></v-textarea>
                                                    <div>
                                                        <v-btn dark depressed small color="primary" @click.native="salvar()" v-if="!perfilAtivo.cordenador">
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

                        <v-divider></v-divider>

                        <v-expansion-panel >
                            <v-expansion-panel-content >
                                <v-layout slot="header" class="blue--text">
                                    <v-icon class="mr-3 blue--text" >insert_drive_file
                                    </v-icon>
                                    Revisão - Coordenador(a) Geral
                                    <v-spacer></v-spacer>
                                    <template v-if="revisaoGeral.siStatus == 1" :onchange="revisaoGeral.siStatus">
                                        <v-chip small color="green" text-color="white" >
                                            <v-avatar>
                                                <v-icon>check_circle</v-icon>
                                            </v-avatar>
                                            Aprovado
                                        </v-chip>
                                    </template>
                                    <template v-if="revisaoGeral.siStatus == 0" :onchange="revisaoGeral.siStatus">
                                        <v-chip small color="red" text-color="white">
                                            <v-avatar>
                                                <v-icon>close</v-icon>
                                            </v-avatar>
                                            Reprovado
                                        </v-chip>
                                    </template>
                                    <template v-if="revisaoGeral.siStatus == 2" :onchange="revisaoGeral.siStatus">
                                        <v-chip small color="grey" text-color="white">
                                            <v-avatar>
                                                <v-icon>report_problem</v-icon>
                                            </v-avatar>
                                            Não Avaliado
                                        </v-chip>
                                    </template>
                                </v-layout>

                                <v-card
                                    :color="background[revisaoGeral.siStatus]"
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
                                                                    <v-radio-group row v-model="revisaoGeral.siStatus" :disabled="perfilAtivo.geral">
                                                                        <v-radio label="Aprovado" :value="1" ></v-radio>
                                                                        <v-radio label="Reprovado" :value="0" color="red"></v-radio>
                                                                    </v-radio-group>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </v-data-table>

                                                    <v-textarea
                                                        :disabled="perfilAtivo.geral"
                                                        solo
                                                        no-resize
                                                        :value="revisaoGeral.dsRevisao"
                                                        hint="Digite sua avaliação"
                                                        height="180px"
                                                    ></v-textarea>
                                                    <div>
                                                        <v-btn dark depressed small color="primary" @click.native="salvar()" v-if="!perfilAtivo.geral">
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
            </v-card>
        </v-dialog>
    </v-layout>
</template>


<script>
    import {mapActions, mapGetters} from 'vuex';

    export default {
      name: 'RevisaoParecer',
      data() {
          return {
              dialog: false,
              perfilAtivo: {
                  cordenador: false,
                  geral: false
              },
              revisao: {
                  siStatus: 2,
                  dsRevisao:'',
                  idAvaliacaoFinanceira: 0,
                  idGrupoAtivo: 21,
                  idAgente: 333
              },
              revisaoGeral:{
                  siStatus: 2,
                  dsRevisao:'',
                  idAvaliacaoFinanceira: 0,
                  idGrupoAtivo: 21,
                  idAgente: 333
              },
              background: [
                  'red lighten-4',
                  'green lighten-4'
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
              item:'',
          };
      },
      methods:
          {
              ...mapActions({
                  requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
              }),
              getConsolidacao(id) {
                  this.requestEmissaoParecer(id);
                  this.setStatus();
              },
              setStatus() {
                  this.items.forEach(i => {
                      if (i['id'] == this.getParecer.siManifestacao) {
                          this.item = i.text;
                      }
                  });

                  if(this.grupo.codGrupo == 125){
                      /** corrdenador habilitado */
                     this.perfilAtivo.cordenador = false;
                     this.perfilAtivo.geral = true;
                  }else if(this.grupo.codGrupo == 126){
                      /**  cordenador Geral habilitado */
                      this.perfilAtivo.cordenador = true;
                      this.perfilAtivo.geral = false;
                  }else{ /** todos sem editar */
                      this.perfilAtivo.cordenador = true;
                      this.perfilAtivo.geral = true;
                  }
              },
              salvar() {
                  if(this.grupo.codGrupo == 125){
                      this.revisao.idGrupoAtivo = this.grupoAtivo.codGrupo;
                      this.revisao.idAgente = this.usuarioAtivo[0].usu_codigo;
                      console.info(this.revisao);
                  }
                  if(this.grupo.codGrupo == 126){
                      this.revisaoGeral.idGrupoAtivo = this.grupoAtivo.codGrupo;
                      this.revisaoGeral.idAgente = this.usuarioAtivo[0].usu_codigo;
                      console.info(this.revisaoGeral);
                  }

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
                  getParecer: 'avaliacaoResultados/parecer',
                  grupo: 'menuSuperior/grupoAtivo',
                  agente: 'menuSuperior/usuarioAtivo',
              }),
          },
      mounted() {
          this.redirectLink = this.redirectLink + this.idPronac;
          this.getConsolidacao(195025);
      },
  };
</script>

<style scoped>

</style>
