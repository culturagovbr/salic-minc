<template>
    <v-text-field
        ref="field"
        v-model="model"
        :error-messages="errorMessages"
        v-bind="$attrs"
        @focus="onFocus"
        @keyup="onKeyUp"
        @change="onChange"
        @blur="onBlur"
    />
</template>

<script>
function tryParseFloat(str, defaultValue) {
    let retValue = defaultValue;
    if (str !== null) {
        if (str.length > 0) {
            if (!Number.isNaN(str)) {
                retValue = parseFloat(str);
            }
        }
    }
    return retValue;
}
export default {
    props: {
        value: {
            type: [String, Number],
            default: null,
        },
        'error-messages': {
            type: String,
            default: null,
        },
        allowNegative: {
            type: Boolean,
            default: false,
        },
        thousandsSeparator: {
            type: String,
            default: '.',
        },
        decimalSeparator: {
            type: String,
            default: ',',
        },
        languageCode: {
            type: String,
            default: 'pt-br',
        },
        languageConfig: {
            type: Object,
            default: () => ({
                style: 'decimal',
                maximumFractionDigits: 2,
                minimumFractionDigits: 2,
            }),
        },
    },
    data() {
        return {
            numberValue: this.value,
            model: this.value,
            isMasked: true,
            thousandsSeparatorRegex: new RegExp(`\\${this.thousandsSeparator}`, 'g'),
            decimalSeparatorRegex: new RegExp(`\\${this.decimalSeparator}`, 'g'),
        };
    },
    watch: {
        numberValue(v) {
            this.$emit('input', v);
        },
        value(v) {
            this.numberValue = v;
            if (!this.$refs.field.isFocused) {
                this.format();
            }
        },
    },
    created() {
        this.format();
    },
    methods: {
        onFocus() {
            this.isMasked = false;
            this.updateModel();
        },
        onBlur() {
            if (this.$listeners.blur) this.$listeners.blur();
            this.isMasked = true;
            this.format();
        },
        onKeyUp() {
            this.updateNumberValue();
        },
        onChange() {
            if (this.$listeners.change) this.$listeners.change();
        },
        updateNumberValue() {
            let v = this.model;
            let parsed;
            v = v.replace(this.thousandsSeparatorRegex, '');
            if (this.decimalSeparator !== '.') v = v.replace(this.decimalSeparatorRegex, this.thousandsSeparator);
            const result = tryParseFloat(v);
            if (!result) parsed = 0;
            else parsed = result;
            if (!this.allowNegative && result < 0) parsed = 0;
            this.numberValue = Math.round(parsed * 100) / 100;
        },
        updateModel() {
            if (this.numberValue === null) return;
            let v = this.numberValue.toString();
            v = v.replace(this.thousandsSeparatorRegex, this.decimalSeparator);
            this.model = v;
        },
        format() {
            if (this.numberValue === null) return;
            let v = this.numberValue;
            v = v.toLocaleString(this.languageCode, this.languageConfig);
            if (v.length !== 1 && v.slice(v.indexOf(this.decimalSeparator) + 1).length === 1) v += '0';
            this.model = v;
        },
    },
};
</script>
