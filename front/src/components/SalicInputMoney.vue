<template>
    <input
        ref="input"
        :disabled="false"
        :value="value"
        v-bind="$attrs"
        type="text"
        class="validate right-align"
        @input="updateMoney($event.target.value)"
        @blur="formatValue"
    >
</template>

<script>

import numeral from 'numeral';

numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');
export default {
    name: 'SalicInputMoney',
    props: {
        value: {
            type: [Number, String],
            default: 0,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            val: 1,
        };
    },
    watch: {
        disabled() {
            this.$refs.input.disabled = this.disabled;
            if (this.disabled) {
                this.val = 0;
            }
        },
    },
    mounted() {
        this.formatValue();
        this.$refs.input.disabled = this.disabled;
    },
    methods: {
        formatValue() {
            this.$refs.input.value = numeral(this.$refs.input.value).format();
        },
        updateMoney(value) {
            this.val = value;
            this.$emit('ev', this.val);
        },
    },
};
</script>
