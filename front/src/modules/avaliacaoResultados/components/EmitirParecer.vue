<template>
    <v-container grid-list-xl>
        <v-form v-model="valid">
            <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
                <v-btn slot="activator" color="green" dark>Emitir Parecer</v-btn>
                <v-card>
                    <v-toolbar dark color="green">
                        <v-btn icon dark :href="redirectLink">
                            <v-icon>close</v-icon>
                        </v-btn>

                        <v-toolbar-title>Avaliação Financeira - Emissão de Parecer</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-toolbar-items>
                            <v-btn dark flat @click.native="salvarParecer" :disabled="!valid">Salvar</v-btn>
                            <!--dialog = false -->
                        </v-toolbar-items>
                    </v-toolbar>

                    <v-container grid-list-sm>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12>
                                <p><b>Projeto:</b> {{projeto.IdPRONAC}} -  {{projeto.NomeProjeto}}</p>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
                                <p><b>Proponente:</b> {{proponente.CgcCpf}} - {{proponente.Nome}}</p>
                            </v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                    </v-container>

                    <h4 class="text-sm-center">Quantidade de Comprovantes</h4>
                    <v-container grid-list-sm>
                        <v-layout row wrap>
                            <v-flex xs10 sm3 md3 >
                                <div>
                                    <h4 class="label text-sm-right">Total</h4>
                                    <p class="text-sm-right">{{consolidacaoComprovantes.qtTotalComprovante}}</p>
                                </div>
                            </v-flex>
                            <v-flex xs10 sm3 md3>
                                <div>
                                    <h4 class="label text-sm-right">Validados</h4>
                                    <p class="text-sm-right">{{consolidacaoComprovantes.qtComprovantesValidadosProjeto}}</p>
                                </div>
                            </v-flex>
                            <v-flex xs10 sm3 md3>
                                <div>
                                    <h4 class="label text-sm-right">Recusados</h4>
                                    <p class="text-sm-right">{{consolidacaoComprovantes.qtComprovantesRecusadosProjeto}}</p>
                                </div>
                            </v-flex>
                            <v-flex xs10 sm3 md3>
                                <div>
                                    <h4 class="label text-sm-right">Não Avaliados</h4>
                                    <p class="text-sm-right">{{consolidacaoComprovantes.qtComprovantesNaoAvaliados}}</p>
                                </div>
                            </v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                    </v-container>

                    <h4 class="text-sm-center">Valores Comprovados</h4>
                    <v-container grid-list-sm>
                        <v-layout row wrap>
                            <v-flex xs10 sm3 md3 >
                                <div>
                                    <h4 class="label text-sm-right">Total</h4>
                                    <p class="text-sm-right">{{consolidacaoComprovantes.vlComprovadoProjeto}}</p>
                                </div>
                            </v-flex>
                            <v-flex xs10 sm3 md3>
                                <div>
                                    <h4 class="label text-sm-right">Validados</h4>
                                    <p class="text-sm-right">{{consolidacaoComprovantes.vlComprovadoValidado}}</p>
                                </div>
                            </v-flex>
                            <v-flex xs10 sm3 md3>
                                <div>
                                    <h4 class="label text-sm-right">Recusados</h4>
                                    <p class="text-sm-right">{{consolidacaoComprovantes.vlComprovadoRecusado}}</p>
                                </div>
                            </v-flex>
                            <v-flex xs10 sm3 md3>
                                <div>
                                    <h4 class="label text-sm-right">Não Avaliados</h4>
                                    <p class="text-sm-right">{{consolidacaoComprovantes.vlNaoComprovado}}</p>
                                </div>
                            </v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                    </v-container>

                    <v-container grid-list>
                        <v-layout wrap align-center>
                            <v-flex>
                                <v-select height="20px"
                                          v-model="parecer.siManifestacao"
                                          :rules="itemRules"
                                          :items="items"
                                          item-text="text"
                                          item-value="id"
                                          box
                                          label="Manifestação *"
                                          required="required"
                                ></v-select>
                            </v-flex>
                        </v-layout>
                        <v-flex>
                            <v-textarea
                                v-model="parecer.dsParecer"
                                :rules="parecerRules"
                                color="deep-purple"
                                label="Parecer *"
                                height="200px"
                                required="required"
                            ></v-textarea>
                        </v-flex>
                    </v-container>
                </v-card>
            </v-dialog>
        </v-form>
    </v-container>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import ModalTemplate from '@/components/modal';

    export default {
        name: 'UpdateBar',
        data() {
            return {
                tipo: true,
                idPronac: this.$route.params.id,
                redirectLink: '/prestacao-contas/realizar-prestacao-contas/index/idPronac/',
                valid: false,
                dialog: true,
                itemRules: [
                    v => !!v || 'Tipo de manifestação e obrigatório!',
                ],
                parecerRules: [
                    v => !!v || 'Parecer e obrigatório!',
                    v => v.length >= 10 || 'Parecer deve conter mais que 10 characters',
                ],
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
            };
        },
        components:
            {
                ModalTemplate,
            },
        methods:
            {
                ...mapActions({
                    modalOpen: 'modal/modalOpen',
                    modalClose: 'modal/modalClose',
                    requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
                    salvar: 'avaliacaoResultados/salvarParecer',

                }),
                fecharModal() {
                    this.modalClose();
                },
                getConsolidacao(id) {
                    this.requestEmissaoParecer(id);
                },
                salvarParecer() {
                    const data = {
                        idPronac: this.idPronac,
                        tpAvaliacaoFinanceira: this.tipo,
                        siManifestacao: this.parecer.siManifestacao,
                        dsParecer: this.parecer.dsParecer };
                    this.salvar(data);
                    this.dialog = false;
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
                }),
            },
        mounted() {
            this.redirectLink = this.redirectLink + this.idPronac;
            this.getConsolidacao(this.idPronac);
        },
    };
</script>
