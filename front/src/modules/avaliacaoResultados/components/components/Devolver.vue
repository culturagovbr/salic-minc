<template>
    <v-dialog
        v-if="Object.keys(usuarioGetter).length > 0"
        v-model="dialog"
        width="650"
    >
        <v-tooltip
            slot="activator"
            bottom
        >
            <v-btn
                slot="activator"
                color="green lighten-2"
                text="white"
                flat
                icon
                light
            >
                <v-icon
                    color="error"
                    class="material-icons"
                >
                    undo
                </v-icon>
            </v-btn>
            <span>Devolver Projeto</span>
        </v-tooltip>

        <v-card>
            <v-card-title
                class="headline primary"
                primary-title
            >
                <span class="white--text">
                    Devolver Projeto
                </span>
            </v-card-title>
            <v-container fluid>
                <v-card-text class="subheading">
                    <div
                        v-if="tecnico !== undefined
                            && tecnico !== null
                            && tecnico !== ''
                        && tecnico.nome !== 'sysLaudo'"
                    >
                        Confirma a devolução do projeto '{{ pronac }} - {{ nomeProjeto }}'
                        para análise do Tecnico: {{ tecnico.nome }}?
                    </div>
                    <div v-else>
                        Confirma a devolução do projeto
                        <b> {{ pronac }} - {{ nomeProjeto }}</b> para a etapa anterior?
                    </div>
                    <v-textarea
                        v-model="justificativa"
                        class="mt-4"
                        outline
                        name="input-7-4"
                        label="Justificativa"
                    />
                </v-card-text>
            </v-container>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="error"
                    flat
                    @click="dialog = false"
                >
                    Cancelar
                </v-btn>
                <v-btn
                    :loading="loading"
                    :disabled="loading"
                    color="success"
                    flat
                    @click="devolver()"
                >
                    Devolver
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'Devolver',
    props: {
        idPronac: { type: String, default: '' },
        usuario: { type: Object, default: () => {} },
        atual: { type: Number, default: 0 },
        proximo: { type: Number, default: 0 },
        nomeProjeto: { type: String, default: '' },
        pronac: { type: String, default: '' },
        idTipoDoAtoAdministrativo: {
            type: String,
            default: '',
            validator(value) {
                return ['622', '623'].includes(value);
            },
        },
        tecnico: { type: Object, default: () => {} },
    },
    data() {
        return {
            dialog: false,
            justificativa: '',
            loading: false,
        };
    },

    computed: {
        ...mapGetters({
            usuarioGetter: 'autenticacao/getUsuario',
        }),
    },

    methods: {
        ...mapActions({
            setDevolverProjetoAction: 'avaliacaoResultados/devolverProjeto',
            mensagemSucesso: 'noticias/mensagemSucesso',
        }),
        devolver() {
            const idSecretaria = this.usuarioGetter.usu_org_max_superior;

            const dados = {
                idPronac: this.idPronac,
                atual: this.atual,
                proximo: this.proximo,
                idTipoDoAtoAdministrativo: this.idTipoDoAtoAdministrativo,
                justificativa: this.justificativa,
                usuario: this.usuario,
                /* encaminhamento */
                dsJustificativa: this.justificativa,
                idOrgaoDestino: 1,
                /* agente */
                idAgenteDestino: this.tecnico.idAgente,
                cdGruposDestino: 1,
                dtFimEncaminhamento: '2015-09-25 10:38:41',
                idSituacaoEncPrestContas: 1,
                idSituacao: 1,
                idSecretaria,
            };

            this.loading = true;
            this.setDevolverProjetoAction(dados)
                .then(() => {
                    this.mensagemSucesso('Devolução realizada com sucesso');
                    this.dialog = false;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>
