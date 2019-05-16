<template>
    <v-container fluid>
        <v-layout
            row
            wrap
        >
            <v-flex
                xs10
                md5
            >
                <v-card height="140px">
                    <v-card-title class="grey lighten-2 title">Versão original</v-card-title>
                    <v-divider/>
                    <v-card-text>
                        {{ campo.valor }}
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex
                xs10
                md2
                class="text-xs-center"
            >
                <v-btn
                    flat
                    class="indigo darken-1 text-xs-center"
                    color="white"
                    @click="copiarOriginal()"
                >
                    igualar
                    <v-icon>sync</v-icon>
                </v-btn>
            </v-flex>
            <v-flex
                xs10
                md5
            >
                <v-card
                    height="140px"
                >
                    <v-card-title
                        class="green lighten-2 title"
                    >
                        Versão readequada
                    </v-card-title>
                    <v-card-actions>
                        <v-menu
                            ref="menu"
                            v-model="menu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            lazy
                            transition="scale-transition"
                            offset-y
                            full-width
                            max-width="290px"
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    :rules="rules"
                                    v-model="dateFormatted"
                                    label="Escolha a data"
                                    prepend-icon="event"
                                    v-on="on"
                                />
                            </template>
                            <v-date-picker
                                v-model="date"
                                no-title
                                class="calendario-vuetify"
                                locale="pt-br"
                                color="primary lighten-1"
                                @input="menu = false; updateCampo($event)"
                            />
                        </v-menu>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>

export default {
    name: 'TemplateDate',
    props: {
        campo: {
            type: Object,
            default: () => {},
        },
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        minChar: {
            type: Number,
            default: 0,
        },
        rules: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            date: '',
            dateFormatted: '',
            menu: false,
        };
    },
    watch: {
        date() {
            this.$emit('dados-update', this.date);
            this.setChangedDate(this.date);
        },
        campo() {
            if (this.campo.valor !== '') {
                let date = this.dadosReadequacao.dsSolicitacao.trim();
                if (date === '') {
                    date = this.campo.valor.trim();
                }
                this.setChangedDate(date);
            }
        },
    },
    created() {
        this.setChangedDate();
    },
    methods: {
        setChangedDate(newDate = '') {
            let date = newDate;
            if (newDate === '') {
                date = this.dadosReadequacao.dsSolicitacao;
            }
            this.date = this.parseDate(date);
            this.dateFormatted = this.formatDate(this.date);
            this.updateCampo(this.date);
        },
        prepareDate(date) {
            const [day, month, year] = date.substr(0, 10).split('-');
            return `${day}-${month}-${year}`;
        },
        formatDate(date) {
            if (!date) {
                return null;
            }
            if (date.includes('-')) {
                const [year, month, day] = date.split('-');
                return `${day}/${month}/${year}`;
            }
            return date;
        },
        parseDate(date) {
            if (!date) {
                return null;
            }
            if (date.includes('/')) {
                const [day, month, year] = date.split('/');
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            return date;
        },
        updateCampo(e) {
            this.$emit('dados-update', e);
            this.atualizarContador(e.length);
        },
        atualizarContador(valor) {
            this.$emit('editor-texto-counter', valor);
        },
        copiarOriginal() {
            this.setChangedDate(this.campo.valor);
        },
    },
};
</script>
