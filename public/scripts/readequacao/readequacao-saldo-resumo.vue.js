Vue.component('readequacao-saldo-resumo', {
template: `
<div>
	<div class="row center-align">
		<h4>Saldo</h4>
		<div class="col s4 center-align">
			<h6>Rendimento declarado</h6>
			<span style="font-weight:bold">R$ {{valorSaldoAplicacaoFormatado}}</span>
		</div>					
		<div class="col s4 center-align" v-bind:class="{ 'blue lighten-3': valorSaldoDisponivelParaUsoPositivo, 'red lighten-3': valorSaldoDisponivelParaUsoNegativo }">
			<h6>Dispon&iacute;vel</h6>
			<span style="font-weight:bold">R$ {{valorSaldoDisponivelParaUsoFormatado}}</span>
		</div>
		<div class="col s4 center-align" v-bind:class="{ 'blue lighten-3': valorSaldoUtilizadoPositivo, 'red lighten-3': valorSaldoUtilizadoNegativo }">
			<h6>Utilizado</h6>
			<span style="font-weight:bold">R$ {{valorSaldoUtilizadoFormatado}}</span>
		</div>
	</div>
	<div class="row center-align" v-show="valorSaldoDisponivelParaUsoNegativo">
		<div class="col s12 center-align">
			<i class="medium red-text material-icons" style="vertical-align: middle">warning</i>
			<span style="font-weight:bold" class="">Diminua os valores da planilha em R$ {{valorSaldoDisponivelParaUsoMensagem}} para poder finalizar a solicita&ccedil;&atilde;o.</span>
		</div>
	</div>
	<div class="row center-align" v-show="exibeMensagemFinalizar">
		<div class="col s12 center-align">
			<i class="medium green-text material-icons" style="vertical-align: middle">check_circle</i>
			<span style="font-weight:bold" class="">J&aacute; &eacute; poss&iacute;vel finalizar a solicita&ccedil;&atilde;o.</span>
		</div>
	</div>
	<div class="row center-align" v-show="valorSaldoUtilizadoNegativo">
		<div class="col s12 center-align">
			<i class="medium red-text material-icons" style="vertical-align: middle">warning</i>
			<span style="font-weight:bold" class="">O total da planilha &eacute; menor que o valor original; saldo de aplica&ccedil;&atilde;o n&atilde;o utilizado.</span>
		</div>
	</div>
	<div class="row center-align" v-show="readequacaoAlterada">
		<div class="col s12 center-align">
			<i class="medium red-text material-icons" style="vertical-align: middle">warning</i>
			<span style="font-weight:bold" class="">As informa&ccedil;&otilde;es da readequa&ccedil;&atilde;o foram alteradas. Salve para poder finalizar.</span>
		</div>
	</div>
</div>
`,
    props: {
	valorSaldoAplicacao: 0,
	valorEntrePlanilhasLimpo: 0,
	valorSaldoDisponivelParaUso: 0,
	valorSaldoUtilizado: 0,
	valorSaldoDisponivelParaUsoNegativo: false,
	valorSaldoDisponivelParaUsoNeutro: false,
	valorSaldoDisponivelParaUsoPositivo: false,
	valorSaldoUtilizadoPositivo: false,
	valorSaldoUtilizadoNeutro: false,
	valorSaldoUtilizadoNegativo: false,
	readequacaoAlterada: false
    },
    computed: {
	valorSaldoAplicacaoFormatado: function() {
	    return numeral(this.valorSaldoAplicacao).format();
	},
	valorSaldoDisponivelParaUsoMensagem: function() {
	    return numeral(this.valorSaldoDisponivelParaUso * -1).format();
	},
	valorSaldoDisponivelParaUsoFormatado: function() {
	    return numeral(this.valorSaldoDisponivelParaUso).format();
	},
	valorSaldoUtilizadoFormatado: function() {
	    return numeral(this.valorSaldoUtilizado).format();
	},
	exibeMensagemFinalizar: function() {
	    if (!this.valorSaldoUtilizadoNeutro
		&& !this.valorSaldoDisponivelParaUsoNegativo
		&& !this.readequacaoAlterada
	       ) {
		return true;
	    }
	}
    }
});
