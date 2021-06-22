Vue.component('select-percent', {
    template: `
        <select 
            style="width: 75px; display: inline-block;" 
            @change="valorSelecionado(selecionado)" 
            v-model="selecionado"
            ref="combo" 
            tabindex="-1" 
            class="browser-default">
                <option v-for="item in items" v-bind:value="item">{{ item }}%</option>
        </select>
    `,
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        maximoCombo: {},
        selected: {
            default: 0
        }
    },
    data: function () {
        return {
            retorno: 1,
            selecionado: this.maximoCombo
        }
    },
    computed: {
        items: function () {
            var total = [];
            for (var i = this.maximoCombo; i >= 0; i--) {
                total.push(parseInt(i));
            }
            return total;
        }
    },
    watch: {
        selected: function(val) {
            this.selecionado = parseInt(val);
        },
        disabled: function () {
            this.$refs.combo.disabled = this.disabled;
            if (this.disabled) {
                this.value = 0;
            }
        }
    },
    methods: {
        valorSelecionado: function (value) {
            this.retorno = value;
            this.$emit('evento', parseInt(this.retorno))
        }
    },
    mounted: function () {
        this.$refs.combo.disabled = this.disabled;
    }
});
