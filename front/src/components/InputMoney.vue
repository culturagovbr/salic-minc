<template>
    <input
        ref="input"
        :disabled="false"
        :value="value"
        type="text"
        class="validate right-align"
        @input="updateMoney($event.target.value)"
        @blur="formatValue"
    >
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'InputMoney',
    mixins: [utils],
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
            val: 0,
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
        this.$refs.input.disabled = this.disabled;
        this.val = this.value;
        this.formatValue();
    },
    methods: {
        formatValue() {
            const num = this.formatFloat(this.$refs.input.value);
            this.$refs.input.value = this.converterParaReal(num);
            this.updateMoney(
                this.formatFloat(this.$refs.input.value),
            );
        },
        formatFloat(value) {
            let newValue = value;
            if (value.search(',') > 0) {
                newValue = newValue.replace('.', '');
                newValue = newValue.replace(',', '.');
            }
            return newValue;
        },
        updateMoney(value) {
            this.val = value;
            this.$emit('ev', this.val);
        },
    },
};
</script>
