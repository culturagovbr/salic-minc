<template>
    <v-dialog
        v-if="typeof usuario !== 'undefined' && Object.keys(usuario).length > 0 && usuario.grupo_ativo !== '124'"
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
            <v-container grid-list-md>
                <v-card-text class="subheading">
                    <div
                        v-if="tecnico !== undefined
                            && tecnico !== null
                            && tecnico !== ''
                            && tecnico.nome !== 'sysLaudo'"
                    >
                        Você deseja devolver o projeto '{{ pronac }} - {{ nomeProjeto }}'
                        para análise do Tecnico: {{ tecnico.nome }}?
                    </div>
                    <div v-else>
                        Você deseja devolver o projeto
                        <b> {{ pronac }} - {{ nomeProjeto }}</b> para a etapa anterior?
                    </div>
                    <v-textarea
                        v-model="justificativa"
                        outline
                        name="input-7-4"
                        label="Justificativa"
                    />
                </v-card-text>
            </v-container>
            <v-card-actions>
                <v-btn
                    color="success"
                    flat
                    @click="devolver()"
                >
                    Sim
                </v-btn>
                <v-btn
                    color="error"
                    flat
                    @click="dialog = false"
                >
                    Não
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>

import { mapActions } from 'vuex';

export default {
    name: 'Devolver',
    props: {
        idPronac: { type: String, default: '' },
        usuario: { type: Object, default: () => {} },
        atual: { type: String, default: '' },
        proximo: { type: String, default: '' },
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
        };
    },
    methods: {
        ...mapActions({
            setDevolverProjeto: 'avaliacaoResultados/devolverProjeto',
        }),
        devolver() {
            this.dialog = false;

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
            };

            this.setDevolverProjeto(dados);
        },
    },
};
</script>
