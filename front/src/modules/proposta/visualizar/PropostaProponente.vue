<template>
    <div>
        <PropostaFluxo :id-pre-projeto="idPreProjeto"/>
        <Proposta :idpreprojeto="String(idPreProjeto)"/>
        <SalicMenuSuspenso v-if="Object.keys(dadosProposta).length > 0">
            <li v-if="dadosProposta.fase_proposta !== 'proposta_arquivada'">
                <a
                    :href="`/solicitacao/mensagem/index/idPreProjeto/${idPreProjeto}`"
                    class="btn-floating red tooltipped"
                    data-tooltip="Solicitações"
                ><i class="material-icons">message</i></a>
            </li>
            <li v-if="dadosProposta.fase_proposta === 'proposta_com_proponente'">
                <a
                    :href="`/proposta/manterpropostaincentivofiscal/
                    identificacaodaproposta/idPreProjeto/${idPreProjeto}`"
                    class="btn-floating green tooltipped"
                    data-tooltip="Editar proposta"
                ><i class="material-icons">edit</i></a>
            </li>
        </SalicMenuSuspenso>

        <div
            class="tap-target"
            data-activates="menu-suspenso">
            <div class="tap-target-content white-text">
                <h5>Botão flutuante</h5>
                <p>
                    Clique aqui para acessar a ferramenta Minhas Solicitações
                    e enviar seu questionamento ao MinC
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import CookieMixin from '@/mixins/cookie';
import SalicMenuSuspenso from '@/components/SalicMenuSuspenso';
import PropostaFluxo from './components/PropostaFluxo';
import Proposta from './Proposta';

export default {
    name: 'PropostaProponente',
    components: {
        SalicMenuSuspenso,
        PropostaFluxo,
        Proposta,
    },
    mixins: [CookieMixin],
    props: {
        idPreProjeto: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            dados: {
                type: Object,
                default() {
                    return {};
                },
            },
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            dadosProposta: 'proposta/proposta',
        }),
    },
    watch: {
        dadosProposta() {
            this.mensagemBotaoFlutuante();
        },
    },
    methods: {
        mensagemBotaoFlutuante() {
            const self = this;
            let quantidadeExibida = self.getCookie('qtd_msg_visualizar_proposta');
            quantidadeExibida = quantidadeExibida ? parseInt(quantidadeExibida, 10) : 0;

            /* eslint-disable */
            $3(document).ready(function() {
                if (quantidadeExibida < 5) {
                    quantidadeExibida++;
                    /* eslint-disable */
                    $3('.tap-target').tapTarget('open');
                    self.setCookie('qtd_msg_visualizar_proposta', quantidadeExibida, 40)
                }
            });
        }
    }
};
</script>
