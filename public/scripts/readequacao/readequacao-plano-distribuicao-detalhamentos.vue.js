Vue.component('readequacao-plano-distribuicao-detalhamentos', {
    template: `
        <div class="readequacao-plano-distribuicao-detalhamentos">
            <plano-distribuicao-detalhamentos-listagem
                :disabled="disabled"
                :detalhamentos="detalhamentos"
                :canalaberto="produto.canalAberto"
                :local="local"
                 v-on:eventoListagem="tratarEvento"
                >
            </plano-distribuicao-detalhamentos-listagem>
            <plano-distribuicao-detalhamentos-formulario
                v-if="!disabled"
                :disabled="disabled"
                :idplanodistribuicao="produto.idPlanoDistribuicao"
                :local="local"
                :editarDetalhamento="detalhamento"
                v-on:eventoSalvarDetalhamento="salvarDetalhamento"
                >
            </plano-distribuicao-detalhamentos-formulario>
        </div>
    `,
    data: function () {
        return {
            detalhamentos: [],
            detalhamento: {}
        }
    },
    mixins: [utils],
    props: [
        'id',
        'produto',
        'local',
        'arrayDetalhamentos',
        'disabled'
    ],
    mounted: function () {
        this.obterDetalhamentos();
    },
    methods: {
        tratarEvento: function (evento, dados, index) {
            switch (evento) {
                case 'editarItem':
                    this.editarDetalhamento(dados, index);
                    break;
                case 'excluirItem':
                    this.excluirDetalhamento(dados, index);
                    break;
                case 'restaurarItem':
                    this.restaurarDetalhamento(dados, index);
                    break;
                default:
                    console.error("Evento n\u00E3o definido na listagem");
            }
        },
        editarDetalhamento(detalhamento, index) {
            this.detalhamento = detalhamento;
            $3("#" + detalhamento.idMunicipio + detalhamento.idPlanoDistribuicao + "_modal").modal('open');
        },
        salvarDetalhamento(detalhamento) {
            let self = this;
            $3.ajax({
                type: "POST",
                url: "/readequacao/plano-distribuicao/salvar-detalhamento-ajax/idPronac/" + self.id,
                data: detalhamento
            }).done(function (response) {
                if (response.success == 'true') {
                    let index = self.$data.detalhamentos.map(
                        item => item.idDetalhaPlanoDistribuicao
                    ).indexOf(response.data.idDetalhaPlanoDistribuicao);
                    if(index >= 0) {
                        Vue.set(self.detalhamentos, index, response.data);
                    } else {
                        self.detalhamentos.push(response.data);
                    }

                    self.mensagemSucesso(response.msg);
                    detalhamentoEventBus.$emit('callBackSalvarDetalhamento', true);
                    $3("#" + detalhamento.idMunicipio + detalhamento.idPlanoDistribuicao + "_modal").modal('close');
                }
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg);
            });
        },
        excluirDetalhamento(detalhamento, index) {
            let self = this;
            $3.ajax({
                type: "POST",
                url: "/readequacao/plano-distribuicao/excluir-detalhamento-ajax/idPronac/" + self.id,
                data: detalhamento
            }).done(function (response) {
                if (response.success == 'true' && response.data == 1) { // exclusao fisica
                    Vue.delete(self.detalhamentos, index);
                    self.mensagemSucesso(response.msg);
                } else { // exclusao logica
                    self.atualizarItemNaListagem(response);
                }
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg);
            });

        },
        restaurarDetalhamento(detalhamento, index) {
            let self = this;
            $3.ajax({
                type: "POST",
                url: "/readequacao/plano-distribuicao/restaurar-detalhamento-ajax/idPronac/" + self.id,
                data: detalhamento
            }).done(function (response) {
                self.atualizarItemNaListagem(response);
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg);
            });
        },
        atualizarItemNaListagem(response) {
            let self = this;
            if (response.success == 'true') {
                let index = self.$data.detalhamentos.map(
                    item => item.idDetalhaPlanoDistribuicao
                ).indexOf(response.data.idDetalhaPlanoDistribuicao);

                Vue.set(self.detalhamentos, index, response.data);
                self.mensagemSucesso(response.msg);
                return true;
            }
        },
        obterDetalhamentos: function () {
            var self = this;
            self.$data.detalhamentos = this.arrayDetalhamentos;
        }
    }
});

var detalhamentoEventBus = new Vue();