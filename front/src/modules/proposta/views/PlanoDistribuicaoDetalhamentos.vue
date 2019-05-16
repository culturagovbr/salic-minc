<template>
    <div class="plano-distribuicao-detalhamentos">
        <ul
            v-if="!loadingLocais"
            class="collapsible"
            data-collapsible="expandable">
            <li
                v-for="( local, index ) in locais"
                :key="index">
                <div class="collapsible-header"><i class="material-icons">place</i>
                    {{ local.uf ? local.uf : 'Exterior' }}
                    {{ local.cidade ? ` -  ${local.cidade}` : '' }}
                </div>
                <div
                    class="collapsible-body"
                    style="background-color: #fff">
                    <detalhamento-listagem
                        v-if="!loadingDetalhamentos"
                        :disabled="disabled"
                        :detalhamentos="obterDetalhamentosPorLocalizacao(local)"
                        :canal-aberto="canalAberto"
                        @eventoRemoverDetalhamento="removerDetalhamento"
                        @eventoEditarDetalhamento="editarDetalhamento"
                    />
                    <s-carregando
                        v-else
                        text="Carregando detalhamentos"/>
                    <detalhamento-formulario
                        v-if="!disabled"
                        ref="detalhamentoFormulario"
                        v-model="exibirFormulario"
                        :disabled="disabled"
                        :id-plano-distribuicao="idPlanoDistribuicao"
                        :id-pre-projeto="idPreProjeto"
                        :id-uf="local.idUF"
                        :id-municipio-ibge="local.idMunicipioIBGE"
                        :editar-detalhamento="detalhamento"
                        :id-normativo="idNormativo"
                        @eventoSalvarDetalhamento="salvarPlanoDetalhamento"
                    />
                </div>
            </li>
        </ul>
        <s-carregando
            v-else
            text="Carregando locais cadastrados..."/>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import MxUtilsProposta from '../mixins/utilsProposta';
import DetalhamentoFormulario from '../components/PlanoDistribuicaoDetalhamentos/DetalhamentoFormulario';
import DetalhamentoListagem from '../components/PlanoDistribuicaoDetalhamentos/DetalhamentoListagem';
import SCarregando from '@/components/Carregando';

export default {
    name: 'PlanoDistribuicaoDetalhamentos',
    components: {
        SCarregando,
        DetalhamentoFormulario,
        DetalhamentoListagem,
    },
    mixins: [utils, MxUtilsProposta],
    props: {
        idPreProjeto: {
            type: [String, Number],
            required: true,
        },
        idPlanoDistribuicao: {
            type: [String, Number],
            required: true,
        },
        disabled: {
            type: [String, Number],
            default: 1,
        },
        canalAberto: {
            type: [String, Number],
            default: 0,
        },
        idNormativo: {
            type: [String, Number],
            default: '',
        },
    },
    data() {
        return {
            detalhamento: {},
            exibirFormulario: false,
            loadingLocais: true,
            loadingDetalhamentos: true,
        };
    },
    computed: {
        ...mapGetters({
            locais: 'proposta/obterLocaisRealizacao',
            detalhamentos: 'proposta/obterPlanoDistribuicaoDetalhamentos',
        }),
    },
    watch: {
        locais() {
            this.loadingLocais = false;
            this.$nextTick(() => {
                this.iniciarCollapsible();
            });
        },
        detalhamentos() {
            this.loadingDetalhamentos = false;
        },
    },
    mounted() {
        this.obterDetalhamentos();
        this.buscarLocaisRealizacao(this.idPreProjeto);
    },
    methods: {
        ...mapActions({
            buscarLocaisRealizacao: 'proposta/buscarLocaisRealizacao',
            buscarDetalhamentos: 'proposta/buscarPlanoDistribuicaoDetalhamentos',
            salvarDetalhamento: 'proposta/salvarPlanoDistribuicaoDetalhamento',
            excluirDetalhamento: 'proposta/excluirPlanoDistribuicaoDetalhamento',
        }),
        removerDetalhamento(detalhamento) {
            // eslint-disable-next-line
            if (confirm('Tem certeza que deseja deletar o item?')) {
                this.mostrarModalCarregando();
                this.excluirDetalhamento(
                    {
                        idPreProjeto: this.idPreProjeto,
                        idDetalhaPlanoDistribuicao: detalhamento.idDetalhaPlanoDistribuicao,
                        idPlanoDistribuicao: detalhamento.idPlanoDistribuicao,
                    },
                ).then((response) => {
                    if (response.success === 'true') {
                        this.mensagemSucesso(response.msg);
                    }
                }).catch((e) => {
                    this.mensagemErro(e.responseJSON.msg);
                }).finally(() => {
                    this.ocultarModalCarregando();
                });
            }
        },
        editarDetalhamento(detalhamento) {
            this.exibirFormulario = true;
            this.detalhamento = Object.assign({}, detalhamento);
        },
        obterDetalhamentosPorLocalizacao(local) {
            return this.detalhamentos.filter(item => parseInt(item.idUF, 10) === parseInt(local.idUF, 10)
                && parseInt(item.idMunicipio, 10) === parseInt(local.idMunicipioIBGE, 10));
        },
        obterDetalhamentos() {
            const params = {
                idPlanoDistribuicao: this.idPlanoDistribuicao,
                idPreProjeto: this.idPreProjeto,
            };

            this.buscarDetalhamentos(params)
                .catch(() => {
                    this.mensagemErro('Erro ao buscar detalhamento');
                });
        },
        iniciarCollapsible() {
            const self = this;
            // eslint-disable-next-line
            $3(".collapsible").each(function () {
                // eslint-disable-next-line
                $3(this).collapsible({
                    accordion: true,
                    onOpen(el) {
                        el.find('.material-icons:first').html('remove');
                    },
                    onClose(el) {
                        el.find('.material-icons:first').html('place');
                        self.exibirFormulario = false;
                    },
                });
            });
        },
        salvarPlanoDetalhamento(detalhamento) {
            this.desabilitarBotaoSalvar(true);
            this.mostrarModalCarregando();
            this.salvarDetalhamento(
                {
                    idPreProjeto: this.idPreProjeto,
                    ...detalhamento,
                },
            ).then((response) => {
                if (response.success === 'true') {
                    this.mensagemSucesso(response.msg);
                    this.detalhamento = {};
                }
            }).catch((e) => {
                this.mensagemErro(e.responseJSON.msg);
            }).finally(() => {
                this.desabilitarBotaoSalvar(false);
                this.ocultarModalCarregando();
            });
        },
        desabilitarBotaoSalvar(val) {
            // eslint-disable-next-line
            this.$refs.detalhamentoFormulario.forEach((item) => { item.$refs.btnSalvar.disabled = val; });
        },
    },
};
</script>
