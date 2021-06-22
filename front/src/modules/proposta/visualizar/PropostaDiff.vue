<template>
    <v-app>
        <div v-if="Object.keys(dadosHistorico).length > 2">
            <PropostaAlteracoes
                :idpreprojeto="idpreprojeto"
                :dados-atuais="dadosAtuais"
                :dados-historico="dadosHistorico"/>
        </div>
        <div v-else-if="Object.keys(dadosAtuais).length > 2">
            <div class="card padding20">
                <div class="nao-existe-versao-proposta">
                    <h4><i class="material-icons small left">report</i>
                        N&atilde;o existe versionamento de
                        altera&ccedil;&otilde;es para o projeto informado.
                    </h4>
                    <p style="margin-left: 44px">
                        O proponente n&atilde;o fez altera&ccedil;&otilde;es
                        no projeto no prazo estabelecido.
                    </p>
                </div>
            </div>
            <Proposta
                :idpreprojeto="idpreprojeto"
                :proposta="dadosAtuais"/>
        </div>
        <div v-else>
            <div class="card padding20">
                <Carregando :text="'Carregando proposta'"/>
            </div>
        </div>
    </v-app>
</template>
<script>
import Carregando from '@/components/Carregando';
import PropostaAlteracoes from './components/PropostaAlteracoes';
import Proposta from './Proposta';

export default {
    name: 'PropostaDiff',
    components: {
        PropostaAlteracoes,
        Proposta,
        Carregando,
    },
    props: {
        idpreprojeto: {
            type: String,
            default: '',
        },
        tipo: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            dadosAtuais: {
                type: Object,
                default() {
                    return {};
                },
            },
            dadosHistorico: {
                type: Object,
                default() {
                    return {};
                },
            },
        };
    },
    mounted() {
        if (typeof this.idpreprojeto !== 'undefined') {
            this.buscar_dados();
        }
    },
    methods: {
        buscar_dados() {
            const self = this;
            /* eslint-disable */
                $3.ajax({
                    url: '/proposta/visualizar/obter-proposta-cultural-versionamento/idPreProjeto/' + self.idpreprojeto +
                        '/tipo/' + self.tipo
                }).done(function (response) {
                    self.dadosAtuais = response.data.atual;
                    self.dadosHistorico = response.data.historico;
                });
            },
        },
    };
</script>
