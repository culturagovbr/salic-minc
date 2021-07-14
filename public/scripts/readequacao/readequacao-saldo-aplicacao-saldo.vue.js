Vue.component('readequacao-saldo-aplicacao-saldo', {
    template: `
<div class="col s3">
	<template v-if="!disabled">
		<label>Saldo dispon&iacute;vel *</label>
    <input-money
			ref="readequacaoSaldo"
			v-model="saldoDisponivel"
			v-on:ev="alterarSaldo">
		</input-money>
	</template>
	<template v-else>
		<span>Saldo dispon&iacute;vel</span>
		{{readequacao.saldo }}
	</template>
</div>
    `,
    props: [
	'dsSolicitacao',
	'disabled'
    ],
    data: function() {
	return {
	    saldoDisponivel: 0
	}
    },
    methods: {
	alterarSaldo: function(valor) {
	    this.$emit('eventoAtualizarDsSolicitacao', valor);
	}
    },
    watch: {
	dsSolicitacao: function(valor) {
	    this.saldoDisponivel = valor;
	}
    }
});
