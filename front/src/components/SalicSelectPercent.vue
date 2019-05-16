<template>
    <select
        ref="combo"
        v-model="selecionado"
        style="width: 75px; display: inline-block;"
        tabindex="-1"
        class="browser-default"
        @change="valorSelecionado(selecionado)">
        <option
            v-for="(item, index) in items"
            :key="index"
            :value="item">{{ item }}%</option>
    </select>
</template>

<script>
export default {
    name: 'SalicSelectPercent',
    props: {
        disabled: {
            type: Boolean,
            default: false,
        },
        maximoCombo: {
            type: Number,
            default: 0,
        },
        selected: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            retorno: 1,
            selecionado: this.maximoCombo,
        };
    },
    computed: {
        items() {
            const total = [];
            for (let i = this.maximoCombo; i >= 0; i -= 1) {
                total.push(parseInt(i, 10));
            }
            return total;
        },
    },
    watch: {
        selected(val) {
            this.selecionado = parseInt(val, 10);
        },
        disabled() {
            this.$refs.combo.disabled = this.disabled;
            if (this.disabled) {
                this.value = 0;
            }
        },
    },
    mounted() {
        this.$refs.combo.disabled = this.disabled;
    },
    methods: {
        valorSelecionado(value) {
            this.retorno = value;
            this.$emit('evento', parseInt(this.retorno, 10));
        },
    },
};
</script>
