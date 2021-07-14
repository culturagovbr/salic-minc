Vue.component('readequacao-saldo-planilha-orcamentaria', {
    template: `
<div v-if="planilha" class="planilha-orcamentaria card">
	<ul class="collapsible no-margin" data-collapsible="expandable">
    <li v-for="(fontes, fonte) of planilhaCompleta" v-if="isObject(fontes)">
    <li v-for="(fontes, fonte) of planilhaCompleta" v-if="isObject(fontes)">
      <div class="collapsible-header active red-text fonte" :class="converterStringParaClasseCss(fonte)">
        <i class="material-icons">beenhere</i>{{fonte}}<span class="badge">R$ {{fontes.total}}</span>
      </div>
      <div class="collapsible-body no-padding">
        <ul class="collapsible no-border no-margin" data-collapsible="expandable">
          <li v-for="(produtos, produto) of fontes" v-if="isObject(produtos)">
            <div class="collapsible-header active green-text" style="padding-left: 30px;" :class="converterStringParaClasseCss(produto)">
              <i class="material-icons">perm_media</i><span v-html="produto"></span><span class="badge">R$ {{produtos.total}}</span>
            </div>
            <div class="collapsible-body no-padding no-border">
              <ul class="collapsible no-border no-margin" data-collapsible="expandable">
                <li v-for="(etapas, etapa) of produtos" v-if="isObject(etapas)">
                  <div class="collapsible-header active orange-text" style="padding-left: 50px;" :class="converterStringParaClasseCss(etapa)">
                    <i class="material-icons">label</i>{{etapa}}<span class="badge">R$ {{etapas.total}}</span>
                  </div>
                  <div class="collapsible-body no-padding no-border">
                    <ul class="collapsible no-border no-margin" data-collapsible="expandable">
                      <li v-for="(locais, local) of etapas" v-if="isObject(locais)">
                        <div class="collapsible-header active blue-text" style="padding-left: 70px;" :class="converterStringParaClasseCss(local)">
                          <i class="material-icons">place</i>{{local}} <span class="badge">R$ {{locais.total}}</span>
                        </div>
                        <div class="collapsible-body no-padding margin20 scroll-x">
													<div class="center-align margin20">
														<template v-if="podeIncluirItem()">
															<a class="waves-effect waves-light btn white-text btn-incluir-novo-item"
																 v-on:click="incluirItem(locais, local, etapa, produto)">
																<i class="material-icons left">add</i>
																incluir item neste munic&iacute;pio
															</a>
														</template>
													</div>
                          <table class="bordered">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th class="left-align">Item</th>
                                <th class="left-align">Unidade</th>
                                <th class="right-align">Dias</th>
                                <th class="right-align">Qtde</th>
                                <th class="right-align">Ocor.</th>
                                <th class="right-align">Vl. Unit&aacute;rio</th>
                                <th class="right-align">Vl. Aprovado</th>
                                <th class="right-align">Vl. Comprovado</th>
                                <th class="left-align">Justificativa</th>
                                <th class="center-align" v-if="!disabled">A&ccedil;&atilde;o</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="row of locais" 
                                  :key="row.idPlanilhaProposta"
                                  v-if="isObject(row)"
                                  v-bind:class="defineCorLinha(row)"
                                  >
                                <td>{{row.Seq}}</td>
                                <td>
																	<template v-if="podeEditarItem(row)">
																		<a style="cursor: pointer"
																			 v-bind:class="defineCorLinha(row)"
																			 v-on:click="editarItem(row)"
																			 >{{row.Item}}</a>
																	</template>
																	
																	<template v-else>
																		{{row.Item}}
																	</template>
																</td>
                                <td>{{row.Unidade}}</td>
                                <td class="right-align">{{row.QtdeDias}}</td>
                                <td class="right-align">{{row.Quantidade}}</td>
                                <td class="right-align">{{row.Ocorrencia}}</td>
                                <td class="right-align">{{converterParaReal(row.vlUnitario)}}</td>
                                <td class="right-align">{{converterParaReal(row.vlAprovado)}}</td>
                                <td class="right-align">{{converterParaReal(row.vlComprovado)}}</td>
                                <td>{{row.dsJustificativa}}</td>
                                <td v-if="!disabled" class="center-align">
																	<template v-if="itemExcluido(row)">
																		<span class="grey-text lighten-3">restaurar</span><br/>
																		<a
																			v-on:click="restaurarItem(row)"
																			title="Restaurar item"
																			class=" small waves-effect waves-light grey-text lighten-3">
																			<i class="material-icons">restore</i>
																		</a>
																	</template>
																	<template v-if="exibirExcluirItem(row)">
																		<a
																			v-on:click="excluirItem(row)"
																			title="Excluir item"
																			class="small waves-effect waves-light red-text lighten-2">
																			<i class="material-icons">delete</i>
																		</a>
																	</template>
																</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </li>
                    </ul>
                  </div>
                </li> 
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </li>
  </ul>
	<div class="card-action">
		<span><b>Valor total do projeto:</b> R$ {{planilhaCompleta.total}}</span>			
	</div>
	<div id="modalEditar" class="modal" v-if="disponivelParaEdicaoReadequacaoPlanilha">
		<div class="modal-header margin20">
			<h4 class="center-align">Alterar item</h4>
		</div>
		<div class="modal-content center-align">
		    <planilha-orcamentaria-alterar-item
					:idPronac="idPronac"
					:unidades="unidades"
					v-on:fecharAlterar="fecharModalAlterar"
					v-on:atualizarItem="atualizarItens"
					v-bind:idPlanilhaAprovacao="idPlanilhaAprovacaoEdicao"
					v-on:atualizarSaldoEntrePlanilhas="atualizarSaldoEntrePlanilhas"
					v-bind:item="itemEdicao">
		    </planilha-orcamentaria-alterar-item>
		</div>
	</div>
	<div id="modalIncluir" class="modal" v-if="disponivelParaAdicaoItensReadequacaoPlanilha">
		<div class="modal-header margin20">
			<h4 class="center-align">Incluir item</h4>
		</div>
		<div class="modal-content center-align">
		    <planilha-orcamentaria-incluir-item
					:idPronac="idPronac"
					:idReadequacao="idReadequacao"
					:unidades="unidades"
					:dados="dadosIncluir"
					v-on:fecharIncluir="fecharModalIncluir"
					v-on:atualizarIncluir="atualizarItens"
					v-on:atualizarSaldoEntrePlanilhas="atualizarSaldoEntrePlanilhas"
					v-bind:idPlanilhaAprovacao="idPlanilhaAprovacaoEdicao">
		    </planilha-orcamentaria-incluir-item>
		</div>
	</div>
</div>
<div v-else>Nenhuma planilha encontrada</div>
    `,
    props: {
	idPronac: '',
	idReadequacao: '',
	objPlanilha: {},
	perfil: '',
	link: '',
	disabled: '',
	disponivelParaAdicaoItensReadequacaoPlanilha: '',
	disponivelParaEdicaoReadequacaoPlanilha: ''
    },
    mixins: [utils],
    data: function() {
	return {
	    planilha: {},
	    unidades: [],
	    idPlanilhaAprovacaoEdicao: '',
	    itemEdicao: {},
	    dadosIncluir: {
	    	recurso: '',
		uf: '',
		municipio: '',
		etapa: '',
		produto: '',
		listaItens: []
	    },
	    perfilProponente: 1111,
	    fonteRecursosFederais: 109
	};
    },
    created: function() {
	this.obterUnidades();
    },
    mounted: function () {
    },
    methods: {
	obterUnidades: function() {
	    let self = this;
	    $3.ajax({
		type: 'GET',
		url: '/readequacao/saldo-aplicacao/carregar-unidades',
		data: {
		    idPronac: self.idPronac
		}
	    }).done(function(response) {
		self.unidades = response.unidades;
	    });
	},
	iniciarCollapsible: function () {
            $3('.planilha-orcamentaria .collapsible').each(function() {
                $3(this).collapsible();
            });
        },
	converterStringParaClasseCss: function(text) {
            return text.toString().toLowerCase().trim()
                       .replace(/&/g, '-and-')
                       .replace(/[\s\W-]+/g, '-');
        },
	exibirExcluirItem: function(item) {
	    if (this.perfil != this.perfilProponente) {
		return false;
	    }
	    
	    if (!_.isNull(item.vlComprovado)
		&& item.vlComprovado > 0) {
		return false;
	    } else {
		if (!this.itemExcluido(item)) {
		    return true;
		}
	    }
	},
	defineCorLinha: function(item) {
	    let cor = '';
	    switch (item.tpAcao) {
		case 'E':
		    if (this.perfil == this.perfilProponente) {
                        cor = 'grey-text lighten-3';
                    } else {
			cor = 'red-text lighten-3';
		    }
		    break;
		case 'I':
		    cor = 'green-text lighten-3';
		    break;
		case 'A':
		    cor = 'blue-text lighten-3';
		    break;
		default:
		    cor = 'black-text';
		    break;
	    }
	    return cor;
	},
	editarItem: function(item) {
	    this.idPlanilhaAprovacaoEdicao = item.idPlanilhaAprovacao;
	    this.itemEdicao = item;
	    $3('#modalEditar').modal('open');
	},
	podeEditarItem: function(item) {
        const ETAPA_CUSTOS_VINCULADOS = 8;
        const ETAPA_REMUNERACAO_RECURSOS = 10;
        const etapasNaoEditar = [
            ETAPA_CUSTOS_VINCULADOS,
            ETAPA_REMUNERACAO_RECURSOS,
        ];
        if (this.perfil == this.perfilProponente
		    && this.link
		    && item.vlComprovado < item.vlAprovado
		    && this.disponivelParaEdicaoReadequacaoPlanilha
            && !etapasNaoEditar.includes(item.idEtapa)
            && (item.tpAcao != 'E'
		        && item.idFonte == this.fonteRecursosFederais)) {
		    return true;
	    }
	},
	podeIncluirItem: function() {
	    if (this.perfil == this.perfilProponente
		&& this.disponivelParaAdicaoItensReadequacaoPlanilha
		&& this.link) {
		return true;
	    }
	},
	incluirItem: function(info, local, etapa, produto) {
	    let firstKey = Object.keys(info)[0],
		instance = info[firstKey],
		uf = local.split(' - ')[0],
		municipio = local.split(' - ')[1];

	    etapa = etapa.split(' - ')[1];    
	    
	    this.dadosIncluir = {
	    	recurso: instance.FonteRecurso,
		etapa: etapa,
		uf: uf,
		municipio: municipio,
		produto: produto,
		idRecurso: instance.idFonte,
		idUnidade: instance.idUnidade,
		idMunicipio: instance.idMunicipio,
		idUF: instance.idUF,
		idProduto: instance.idProduto,
		idEtapa: instance.idEtapa,
		idReadequacao: self.idReadequacao,
		Ocorrencia: '',
		ValorUnitario: '',
		Dias: '',
		listaItens: []
	    };
	    this.obterItens(instance.idMunicipio, instance.idProduto, instance.idEtapa);
	    $3('#modalIncluir').modal('open');
	},
	obterItens: function(idMunicipio, idProduto, idEtapa) {
	    let self = this;
	    $3.ajax({
		type: 'POST',
		url: '/readequacao/readequacoes',
		data: {
		    idPronac: self.idPronac,
		    idMunicipio: idMunicipio,
		    idProduto: idProduto,
		    idEtapa: idEtapa
		}
	    }).done(function(data) {
		self.dadosIncluir.listaItens = data;
	    });
	},
	fecharModalAlterar: function() {
	    $3('#modalEditar').modal('close');
	},
	fecharModalIncluir: function() {
	    $3('#modalIncluir').modal('close');
	},
	atualizarItens: function() {
	    this.$emit('atualizarPlanilha');
	},
	itemExcluido: function(item) {
	    if (item.tpAcao == 'E') {
		return true;
	    }
	},
	excluirItem: function(item) {
	    if (confirm("Tem certeza que deseja exclir o item?")) {
		let self = this;
		
		$3.ajax({
                    type: 'POST',
                    url: '/readequacao/readequacoes/excluir-item-solicitacao',
		    data: {
			idPronac: self.idPronac,
			idPlanilha: item.idPlanilhaAprovacao
		    }
		}).done(function() {
		    item.tpAcao = 'E';
		    self.atualizarSaldoEntrePlanilhas();
		    self.mensagemSucesso("Item exclu&iacute;do com sucesso");
		});
	    }
	},
	restaurarItem: function(item) {
	    let self = this;
	    $3.ajax({
		type: 'POST',
		url: '/readequacao/readequacoes/alteracoes-tecnicas-no-item',
		data: {
		    idPlanilha: item.idPlanilhaAprovacao,
		    tpAcao: item.tpAcao
		},
	    }).done(function(){
		item.tpAcao = 'N';
		self.atualizarSaldoEntrePlanilhas();
		self.mensagemSucesso("Item restaurado com sucesso");
            });
	},
	atualizarSaldoEntrePlanilhas: function() {
	    this.$emit('atualizarSaldoEntrePlanilhas');
	}
    },
    watch: {
        objPlanilha: function (value) {
            this.planilha = value;
            this.iniciarCollapsible();
	    let self = this;
	    $3('#modalEditar').modal();
	    $3('#modalEditar').css('height', '85%');
	    $3('#modalEditar').css('max-height', '85%');

	    if (this.podeIncluirItem()) {
		$3('#modalIncluir').modal();
		$3('#modalIncluir').css('height', '85%');
		$3('#modalIncluir').css('max-height', '85%');
	    }
        }
    },
    computed: {
        planilhaCompleta: function () {
            if (!_.isEmpty(this.objPlanilha)) {
		this.planilha = this.objPlanilha;
            } else {
		return 0;
	    }
	    
            let novaPlanilha = {}, totalProjeto = 0, totalFonte = 0, totalProduto = 0, totalEtapa = 0, totalLocal = 0;

            novaPlanilha = this.planilha;
            Object.entries(this.planilha).forEach(([fonte, produtos]) => {
                totalFonte = 0;
                Object.entries(produtos).forEach(([produto, etapas]) => {
                    totalProduto = 0;
                    Object.entries(etapas).forEach(([etapa, locais]) => {
                        totalEtapa = 0;
                        Object.entries(locais).forEach(([local, itens]) => {
                            totalLocal = 0;
                            Object.entries(itens).forEach(([column, cell]) => {
                                totalLocal += cell.vlAprovado;
                            });
                            this.$set(this.planilha[fonte][produto][etapa][local], 'total',  numeral(totalLocal).format('0,0.00'));
                            totalEtapa += totalLocal;
                        });
                        this.$set(this.planilha[fonte][produto][etapa], 'total', numeral(totalEtapa).format('0,0.00'));
                        totalProduto += totalEtapa;
                    });
                    this.$set(this.planilha[fonte][produto], 'total', numeral(totalProduto).format('0,0.00'));
                    totalFonte += totalProduto;
                });
                this.$set(this.planilha[fonte], 'total', numeral(totalFonte).format('0,0.00'));
                totalProjeto += totalFonte;
            });
            this.$set(novaPlanilha, 'total', numeral(totalProjeto).format('0,0.00'));

            return novaPlanilha;
        }
    },
});
