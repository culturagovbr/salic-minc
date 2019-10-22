<template>
    <v-dialog
        v-model="dialog"
        max-width="800"
    >
        <v-tooltip
            slot="activator"
            bottom
        >
            <v-btn
                slot="activator"
                flat
                icon
            >
                <v-icon>assignment_ind</v-icon>
            </v-btn>
            <span>Encaminhar Projeto</span>
        </v-tooltip>
        <v-card>
            <v-form
                ref="form"
                v-model="form"
            >
                <v-card-title
                    class="title primary"
                    primary-title
                >
                    <span class="white--text">
                        Encaminhamento do projeto
                    </span>
                </v-card-title>
                <v-card-text>
                    <v-list
                        three-line
                        subheader
                    >
                        <v-subheader>
                            <h4 class="headline mb-0 grey--text text--darken-3">
                                {{ pronac }} - {{ nomeProjeto }}
                            </h4>
                        </v-subheader>
                        <v-divider class="mb-3" />
                        <v-select
                            v-model="destinatarioEncaminhamento"
                            :items="dadosDestinatarios"
                            :rules="[rules.required]"
                            :loading="loadingDestinatarios"
                            :label="loadingDestinatarios ? 'Carregando técnicos' : '-- Escolha um técnico  --'"
                            height="10px"
                            single-line
                            outline
                            item-text="usu_nome"
                            item-value="usu_codigo"
                            prepend-icon="perm_identity"
                        />
                        <v-textarea
                            ref="justificativa"
                            v-model="justificativa"
                            :rules="[rules.required]"
                            label="Observação de encaminhamento para análise"
                            prepend-icon="create"
                            color="green"
                            autofocus
                            outline
                            height="150px"
                        />
                    </v-list>
                </v-card-text>
                <v-divider />
                <v-card-actions>
                    <v-spacer />
                    <v-btn
                        color="red"
                        flat
                        @click="dialog = false; $refs.form.reset()"
                    >
                        Fechar
                    </v-btn>
                    <v-btn
                        :disabled="!form || loading"
                        :loading="loading"
                        color="primary"
                        flat
                        @click="enviarEncaminhamento"
                    >
                        Encaminhar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'Encaminhar',
    props: {
        idPronac: {
            type: String,
            default: '',
            validator(value) {
                return value !== '';
            },
        },
        nomeProjeto: {
            type: String,
            default: '',
        },
        pronac: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            dialog: false,
            rules: {
                required: v => !!v,
            },
            destinatarioEncaminhamento: null,
            loadingDestinatarios: false,
            justificativa: null,
            form: null,
            loading: false,
        };
    },
    computed: {
        ...mapGetters({
            dadosDestinatarios: 'avaliacaoResultados/dadosDestinatarios',
            usuarioGetter: 'autenticacao/getUsuario',
        }),
    },
    watch: {
        dialog(val) {
            if (!val) {
                this.$refs.form.reset();
            } else {
                const orgaoSuperior = this.$store.getters['autenticacao/getUsuario'].usu_org_max_superior;
                this.loadingDestinatarios = true;
                this.obterDestinatarios(orgaoSuperior).finally(() => {
                    this.loadingDestinatarios = false;
                });
            }
        },
    },
    methods: {
        ...mapActions({
            obterDestinatarios: 'avaliacaoResultados/obterDestinatarios',
            encaminharParaTecnico: 'avaliacaoResultados/encaminharParaTecnico',
            obterDadosTabelaTecnico: 'avaliacaoResultados/obterDadosTabelaTecnico',
            obterProjetosParaDistribuicaoAction: 'avaliacaoResultados/projetosParaDistribuir',
            projetosFinalizados: 'avaliacaoResultados/projetosFinalizados',
            distribuir: 'avaliacaoResultados/projetosParaDistribuir',
            mensagemSucesso: 'noticias/mensagemSucesso',
        }),
        enviarEncaminhamento() {
            const idSecretaria = this.usuarioGetter.usu_org_max_superior;
            this.loading = true;
            this.encaminharParaTecnico({
                atual: 4,
                proximo: 5,
                idPronac: this.idPronac,
                idOrgaoDestino: idSecretaria,
                idAgenteDestino: this.destinatarioEncaminhamento,
                cdGruposDestino: 1,
                dtFimEncaminhamento: '2015-09-25 10:38:41', // @todo pegar data do back
                idSituacaoEncPrestContas: 1,
                idSituacao: 1,
                dsJustificativa: this.justificativa,
                idSecretaria,
            }).then(() => {
                this.mensagemSucesso('Projeto encaminhado com sucesso!');
                this.obterProjetosParaDistribuicaoAction();
                this.obterDadosTabelaTecnico({ estadoid: 5, idSecretaria });
                // this.projetosFinalizados({ estadoid: 6, idSecretaria });
                this.dialog = false;
                this.$refs.form.reset();
            }).finally(() => {
                this.loading = false;
            });
        },
    },
};
</script>
