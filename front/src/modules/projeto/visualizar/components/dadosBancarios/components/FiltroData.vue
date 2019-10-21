<template>
    <div>
        <span
            v-if="validaCampo().desc !== ''"
            color="alert">
            <div v-if="validaCampo().id === 1">
                <v-alert
                    :value="true"
                    type="warning"
                    class="black--text"
                >
                    {{ validaCampo().desc }}
                </v-alert>
            </div>
        </span>
        <v-layout
            row
            wrap>
            <v-flex xs12>
                <div>
                    <h3
                        class="subheading"
                        v-html="text"/>
                    <v-divider class="pb-2"/>
                </div>
            </v-flex>
            <v-flex
                xs12
                sm6
                md3>
                <v-menu
                    ref="menu"
                    :close-on-content-click="false"
                    v-model="menu"
                    :nudge-right="40"
                    lazy
                    transition="scale-transition"
                    offset-y
                    full-width
                    min-width="290px"
                >
                    <v-text-field
                        slot="activator"
                        v-model="datestring"
                        label="Escolha a data inicial da Pesquisa"
                        prepend-icon="event"
                        mask="##/##/####"
                        return-masked-value
                        @blur="date = parseDate(datestring)"
                    />
                    <v-date-picker
                        :max="new Date().toISOString().substr(0, 10)"
                        v-model="date"
                        class="calendario-vuetify"
                        scrollable
                        locale="pt-br"
                        color="primary lighten-1"
                        @input="menu = false"
                    />
                </v-menu>
            </v-flex>

            <v-flex
                xs12
                sm6
                md3>
                <v-menu
                    ref="menuFim"
                    :close-on-content-click="false"
                    v-model="menuFim"
                    :nudge-right="40"
                    lazy
                    transition="scale-transition"
                    offset-y
                    full-width
                    min-width="290px"
                    class="pl-4"
                >
                    <v-text-field
                        slot="activator"
                        v-model="datestringFim"
                        label="Escolha a data Final da Pesquisa"
                        prepend-icon="event"
                        mask="##/##/####"
                        return-masked-value
                        @blur="dateFim = parseDate(datestringFim)"
                    />
                    <v-date-picker
                        :max="new Date().toISOString().substr(0, 10)"
                        v-model="dateFim"
                        class="calendario-vuetify"
                        scrollable
                        locale="pt-br"
                        color="primary lighten-1"
                        @input="menuFim = false"
                    />
                </v-menu>
            </v-flex>
            <div class="pt-2 pl-2">
                <v-btn
                    :disabled="!validaCampo().validacao"
                    color="teal"
                    class="white--text"
                    @click="filtrarData()">Pesquisar
                </v-btn>
            </div>
        </v-layout>
    </div>
</template>
<script>

export default {
    name: 'FiltroData',
    props: {
        text: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            date: '',
            menu: false,
            datestring: '',
            dateFim: '',
            menuFim: false,
            datestringFim: '',
        };
    },
    watch: {
        date(val) {
            this.datestring = this.formatDate(val);
        },
        dateFim(val) {
            this.datestringFim = this.formatDate(val);
        },
    },
    methods: {
        formatDate(date) {
            if (!date) return null;

            const [year, month, day] = date.split('-');
            return `${day}/${month}/${year}`;
        },
        parseDate(date) {
            if (!date) return null;

            const [day, month, year] = date.split('/');
            return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
        },
        filtrarData() {
            const params = {
                dtInicio: this.date,
                dtFim: this.dateFim,
            };
            this.$emit('eventoFiltrarData', params);
        },
        validaCampo() {
            let status = {
                desc: '',
                validacao: false,
                id: 0,
            };
            if (this.date === '' && this.dateFim === '') {
                status = { desc: '', validacao: false, id: 2 };
                return status;
            }
            if (this.date === '' && this.dateFim !== '') {
                status = { desc: 'Escolha a data inicial', validacao: false, id: 1 };
                return status;
            }
            if (this.date !== '' && this.dateFim === '') {
                status = { desc: 'Escolha a data final', validacao: false, id: 1 };
                return status;
            }
            if (this.date === this.dateFim) {
                status = { desc: '', validacao: true, id: 3 };
                return status;
            }
            if (this.date !== '' && this.dateFim !== '') {
                if (Date.parse(this.date) > Date.parse(this.dateFim)) {
                    status = {
                        desc: 'Data Inicial não pode ser mais recente que a Data Final',
                        validacao: false,
                        id: 1,
                    };
                    return status;
                }
                if (!(Date.parse(this.date) < Date.parse(this.dateFim))) {
                    status = {
                        desc: 'Data Final não pode ser anterior da Data Inicial',
                        validacao: false,
                        id: 1,
                    };
                    return status;
                }
            }
            status = { desc: 'tudo certo', validacao: true, id: 3 };
            return status;
        },
    },
};
</script>
