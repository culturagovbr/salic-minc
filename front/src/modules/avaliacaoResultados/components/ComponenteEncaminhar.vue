<template>
        <div>
            <div class="text-xs-center">
                <v-dialog
                        v-model="dialog"
                        width="650"
                >
                    <v-tooltip slot="activator" bottom>
                        <v-btn 
                                slot="activator"
                                color="green lighten-2"
                                text="white"
                                flat
                                light
                        >
                            <v-icon class="material-icons">assignment_ind</v-icon>
                        </v-btn>
                        <span>Encaminhar Projeto</span>
                    </v-tooltip>
                    <v-card>
                        <v-form
                                v-model="form"
                                ref="form"
                        >
                            <v-card-title
                                    class="headline green"
                                    primary-title
                            >
                                <span class="white--text">
                                    Encaminhamento do projeto
                                </span>
                            </v-card-title>
                            <v-card-text>
                                <v-list three-line subheader>
                                    <v-subheader>
                                        <h4 class="headline mb-0 grey--text text--darken-3">
                                            {{pronac}} - {{nomeProjeto}}
                                        </h4>    
                                    </v-subheader>
                                    <v-divider></v-divider>

                                    <v-subheader>
                                        Informações do encaminhamento
                                    </v-subheader>
                                    <v-list-tile>
                                        <v-list-tile-action>
                                            <v-icon color="green">group</v-icon>
                                        </v-list-tile-action>
                                        SEFIC/DEIPC/CGARE
                                    </v-list-tile>
                                    <v-list-tile>
                                        <v-list-tile-action>
                                            <v-icon color="green">perm_identity</v-icon>
                                        </v-list-tile-action>
                                        <v-select
                                                v-model="destinatarioEncaminhamento"
                                                height="10px"
                                                solo
                                                single-line
                                                :items="dadosDestinatarios"
                                                label="-- Escolha um técnico  --"
                                                item-text="usu_nome"
                                                item-value="usu_codigo"
                                                :rules="[rules.required]"
                                        ></v-select>
                                    </v-list-tile>


                                    <v-textarea
                                            v-model="justificativa"
                                            ref="justificativa"
                                            label="Justificativa de encaminhamento para análise"
                                            prepend-icon="create"
                                            color="green"
                                            autofocus
                                            :rules="[rules.required]"
                                            height="150px"
                                    ></v-textarea>

                                </v-list>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                        color="red"
                                        flat
                                        @click="dialog = false, $refs.form.reset()"
                                >
                                    Fechar
                                </v-btn>
                                <v-btn
                                        color="primary"
                                        flat
                                        @click="enviarEncaminhamento"
                                        :disabled="!form"
                                >
                                    Encaminhar
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
            </div>
        </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import Modal from '@/components/modal';

    export default {
        name: 'ComponenteEncaminhar',
        props: [
            'idPronac',
            'nomeProjeto',
            'pronac',
        ],
        data() {
            return {
                dialog: false,
                rules: {
                    required: v => !!v,
                },
                destinatarioEncaminhamento: null,
                justificativa: null,
                form: null,
            };
        },
        watch: {
            dialog(val) {
                if (!val) {
                    this.$refs.form.reset();
                } else {
                    this.obterDestinatarios();
                }
            },
        },
        components: {
            Modal,
        },
        computed: {
            ...mapGetters({
                dadosDestinatarios: 'avaliacaoResultados/dadosDestinatarios',
            }),
        },
        methods: {
            ...mapActions({
                obterDestinatarios: 'avaliacaoResultados/obterDestinatarios',
                encaminharParaTecnico: 'avaliacaoResultados/encaminharParaTecnico',
                obterDadosTabelaTecnico: 'avaliacaoResultados/obterDadosTabelaTecnico',
                projetosFinalizados: 'avaliacaoResultados/projetosFinalizados',
                distribuir: 'avaliacaoResultados/projetosParaDistribuir',
            }),
            enviarEncaminhamento() {
                this.encaminharParaTecnico({
                    atual: 4,
                    proximo: 5,
                    idPronac: this.idPronac || 123456,
                    idOrgaoDestino: 1,
                    idAgenteDestino: this.destinatarioEncaminhamento,
                    cdGruposDestino: 1,
                    dtFimEncaminhamento: '2015-09-25 10:38:41',
                    idSituacaoEncPrestContas: 1,
                    idSituacao: 1,
                    dsJustificativa: this.justificativa,
                });
                this.dialog = false;
                this.$refs.form.reset();

                this.projetosFinalizados({ estadoid: 6 });
                this.obterDadosTabelaTecnico({ estadoid: 5 });
                this.distribuir({ estadoid: 6 });
            },
        },
    };
</script>
