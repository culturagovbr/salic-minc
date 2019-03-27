<template>
    <div>
        <div v-if="Object.keys(dados).length > 0">
            <table>
                <thead>
                    <tr>
                        <th>Proposta/Projeto</th>
                        <th>Solicita&ccedil;&atilde;o</th>
                        <th>Estado</th>
                        <th>Dt. Solicita&ccedil;&atilde;o</th>
                        <th>Dt. Resposta</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody
                    v-for="(dado, index) in dados"
                    :key="index">
                    <tr>
                        <td>{{ dado.NomeProjeto }}</td>
                        <td>{{ dado.dsSolicitacao }}</td>
                        <td>{{ dado.dsEncaminhamento }}</td>
                        <td>{{ dado.dtSolicitacao }}</td>
                        <td>{{ dado.dtResposta }}</td>
                        <td>
                            <div
                                class="btn blue small white-text tooltipped"
                                data-tooltip="Visualizar"
                                @click="setActiveTab(index);">
                                <i class="material-icons">visibility</i>
                            </div>
                        </td>
                    </tr>
                    <tr
                        v-if="activeTab === index"
                        bgcolor="#ffffff">
                        <td colspan="7">
                            <div>
                                <div class="row">
                                    <div class="col s12">
                                        <div v-if="dado.idPronac">
                                            <b>Pronac: </b>{{ dado.Pronac }}
                                        </div>
                                        <div v-else>
                                            <b>N&ordm; da Proposta: </b>{{ dado.idProjeto }}
                                        </div>
                                    </div>
                                    <div class="col s12">
                                        <b>Proposta/Projeto: </b>{{ dado.NomeProjeto }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <h5>Solicita&ccedil;&atilde;o</h5>
                                    </div>
                                    <div class="col s12">
                                        {{ dado.dsSolicitacao }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <h5>Resposta</h5>
                                    </div>
                                    <div
                                        v-if="dado.dsResposta"
                                        class="col s12">
                                        {{ dado.dsResposta }}
                                    </div>
                                    <div
                                        v-else
                                        class="col s12">
                                        Sem resposta para esta Solicita&ccedil;&atilde;o.
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            Sem dados.
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'PropostaHistoricoSolicitacoes',
    props: {
        idpreprojeto: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            activeTab: -1,
        };
    },
    computed: {
        ...mapGetters({
            dados: 'proposta/historicoSolicitacoes',
        }),
    },
    watch: {
        idpreprojeto(value) {
            this.buscarHistoricoSolicitacoes(value);
        },
    },
    mounted() {
        if (this.idpreprojeto !== 0) {
            this.buscarHistoricoSolicitacoes(this.idpreprojeto);
        }
    },
    methods: {
        setActiveTab(index) {
            if (this.activeTab === index) {
                this.activeTab = -1;
            } else {
                this.activeTab = index;
            }
        },
        ...mapActions({
            buscarHistoricoSolicitacoes: 'proposta/buscarHistoricoSolicitacoes',
        }),
    },
};
</script>
