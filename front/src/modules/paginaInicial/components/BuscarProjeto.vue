<template>
    <form @keyup.enter="irParaProjeto">
        <v-card
            color="blue-grey darken-2"
            class="white--text"
            dark>
            <v-card-title class="subheading">
                Buscar projeto
            </v-card-title>
            <v-card-text>
                <v-autocomplete
                    v-model="model"
                    :items="items"
                    :loading="isLoading"
                    :search-input.sync="campoDeBusca"
                    hide-selected
                    hide-no-data
                    cache-items
                    chips
                    deletable-chips
                    color="white"
                    item-text="Description"
                    item-value="API"
                    label="Projetos"
                    placeholder="Escreva o pronac ou nome do projeto"
                    prepend-icon="style"
                    return-object
                />
            </v-card-text>
            <v-divider/>
            <v-expand-transition>
                <div>
                    <v-list v-if="model">
                        <v-list-tile>
                            <v-btn
                                :href="`/projeto/#/${model.idPronac}`"
                                ripple
                                color="blue-grey"
                                class="white--text"
                            >
                                Ver projeto
                                <v-icon
                                    right
                                    dark>visibility</v-icon>
                            </v-btn>
                        </v-list-tile>
                    </v-list>
                    <v-list
                        v-if="model"
                        color="blue-grey darken-1"
                        class="white--text py-0">
                        <v-list-tile
                            v-for="(field, i) in fields"
                            :key="i"
                            :href="`/projeto/#/${model.idPronac}`"
                        >
                            <v-list-tile-content>
                                <v-list-tile-sub-title v-text="field.key"/>
                                <v-list-tile-title v-text="field.value"/>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list>
                </div>
            </v-expand-transition>
        </v-card>
    </form>
</template>

<script>
import axios from 'axios';
import _ from 'lodash';
import formatarCPNJ from '@/filters/cnpj';

export default {
    name: 'BuscarProjeto',
    data: () => ({
        descriptionLimit: 60,
        description: 60,
        entries: [],
        isLoading: false,
        model: null,
        campoDeBusca: null,
    }),
    computed: {
        fields() {
            if (!this.model) {
                return [];
            }

            return [
                { key: 'Pronac', value: this.model.Pronac },
                { key: 'Nome Projeto', value: this.model.NomeProjeto },
                { key: 'Situação', value: `${this.model.Situacao}: ${this.model.descricaoSituacao}` },
                { key: 'Proponente', value: this.model.NomeProponente },
                { key: 'Cpf/Cnpj', value: formatarCPNJ(this.model.CgcCPf) },
            ];
        },
        items() {
            return this.entries.map((entry) => {
                let Description = entry.NomeProjeto.length > this.descriptionLimit
                    ? `${entry.NomeProjeto.slice(0, this.descriptionLimit)} ...`
                    : entry.NomeProjeto;
                Description = `${entry.Pronac} - ${Description}`;
                return Object.assign({}, entry, { Description });
            });
        },
    },
    watch: {
        campoDeBusca() {
            this.debouncedObterProjetos();
        },
    },
    created() {
        this.debouncedObterProjetos = _.debounce(this.buscarProjetos, 500);
    },
    methods: {
        buscarProjetos() {
            // Items have already been loaded
            if (!this.campoDeBusca || this.campoDeBusca.length < 5) return;

            // Items have already been requested
            if (this.isLoading) return;

            this.isLoading = true;
            const self = this;

            axios.get(`/navegacao/projeto-rest/?pronac=${self.campoDeBusca}`)
                .then((response) => {
                    const { count, projetos } = response.data;
                    this.count = count;
                    this.entries = projetos;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        formatarCPNJ,
        irParaProjeto() {
            if (this.model === null) {
                return false;
            }

            if (this.model !== null && this.model.idPronac) {
                window.location = `/projeto/#/${this.model.idPronac}`;
            }
            return true;
        },
    },
};
</script>
