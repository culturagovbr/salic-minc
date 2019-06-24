<template>
    <v-container fluid>
        <v-layout
            v-if="loading">
            <carregando
                :text="'Montando edição...'"
                class="mt-5"
            />
        </v-layout>
        <v-layout
            v-else
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
                            <v-text-field
                                slot="activator"
                                :rules="[rules.required, rules.dataExecucaoChars, rules.dataExecucao]"
                                v-model="dateFormatted"
                                label="Escolha a data"
                                prepend-icon="event"
                            />
                            <v-date-picker
                                v-model="date"
                                :allowed-dates="allowedDates"
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
import { mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';

export default {
    name: 'TemplateDate',
    components: {
        Carregando,
    },
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
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            date: '',
            dateFormatted: '',
            menu: false,
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            campoAtual: 'readequacao/getCampoAtual',
        }),
    },
    watch: {
        date() {
            this.setChangedDate(this.date);
            this.loading = false;
        },
        campo() {
            if (this.campo.valor !== '') {
                let date = this.dadosReadequacao.dsSolicitacao.trim();
                if (date === '') {
                    date = this.campo.valor.trim();
                }
                this.setChangedDate(date);
                this.loading = false;
            }
        },
        loading() {
            if (this.loading === false) {
                this.updateCampo(this.date);
            }
        },
    },
    created() {
        if (typeof this.dadosReadequacao.dsSolicitacao !== 'undefined') {
            this.setChangedDate(this.dadosReadequacao.dsSolicitacao);
        }
        this.loading = false;
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
        allowedDates(value) {
            let currentDate = this.campo.valor.trim();
            if (currentDate.includes('/')) {
                const [day, month, year] = currentDate.split('/');
                currentDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            return value !== currentDate;
        },
    },
};
</script>
