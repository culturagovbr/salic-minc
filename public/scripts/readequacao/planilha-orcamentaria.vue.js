Vue.component('planilha-orcamentaria', {
    template: `
	<div id="planilhaOrcamentariaMontada">
	    <component
				v-bind:is="componentePlanilha"
				:idPronac="idPronac"
				:objPlanilha="planilhaOrcamentaria"
				:perfil="perfil"
				:link="link"
				:idReadequacao="idReadequacao"
				:disabled="disabled"
				:disponivelParaAdicaoItensReadequacaoPlanilha="disponivelParaAdicaoItensReadequacaoPlanilha"
				:disponivelParaEdicaoReadequacaoPlanilha="disponivelParaEdicaoReadequacaoPlanilha"
				v-on:atualizarSaldoEntrePlanilhas="atualizarSaldoEntrePlanilhas"
				v-on:atualizarPlanilha="montarPlanilhaOrcamentaria"
				>
	    </component>
	</div>
    `,
    props: {
	idPronac: '',
	idReadequacao: '',
	tipoPlanilha: '',
	link: false,
	componentePlanilha: '',
	perfil: '',
	disabled: false,
	disponivelParaAdicaoItensReadequacaoPlanilha: '',
	disponivelParaEdicaoReadequacaoPlanilha: ''
    },
    data: function() {
	return {
	    planilhaOrcamentaria: []
	}
    },
    created: function() {
	this.montarPlanilhaOrcamentaria();
    },
    methods: {
	montarPlanilhaOrcamentaria: function() {
	    let self = this;
	    $3.ajax({
		type: 'GET',
		url: '/readequacao/readequacoes/obter-planilha-orcamentaria',
		data: {
		    idPronac: self.idPronac,
		    tipoPlanilha: self.tipoPlanilha,
		    link: self.link
		}
	    }).done(function(response) {
		self.planilhaOrcamentaria = response.planilhaOrcamentaria;
	    });
	},
	atualizarSaldoEntrePlanilhas: function() {
	    this.$emit('atualizarSaldoEntrePlanilhas');
	}
    }
});
