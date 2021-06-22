<template>
    <v-layout>
        <v-flex
            xs-10
            offset-xs1
            class="ma-3"
        >
            <span class="headline">R$</span>
            <input-money
                ref="readequacaoSaldo"
                :value="valorLocal"
                class="title"
                @ev="atualizarCampo($event)"
            />
            <span
                class="font-italic grey--text text--darken-2"
            >
                Valor total do saldo de aplicações disponível a ser utilizado
            </span>
        </v-flex>
    </v-layout>
</template>
<script>
import InputMoney from '@/components/InputMoney';

export default {
    name: 'ValorDisponivel',
    components: {
        InputMoney,
    },
    props: {
        valor: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
            valorLocal: 0,
        };
    },
    watch: {
        valor() {
            this.valorLocal = this.valor;
        },
    },
    created() {
        this.valorLocal = this.valor;
    },
    methods: {
        atualizarCampo(value) {
            if (parseInt(value, 10) === 0) {
                this.atualizarContador(0);
            } else {
                this.atualizarContador(value.length);
            }
            this.atualizarForm(value);
        },
        atualizarForm(value) {
            this.$emit('dados-update', value);
        },
        atualizarContador(valor) {
            this.$emit('editor-texto-counter', valor);
        },
    },
};
</script>
