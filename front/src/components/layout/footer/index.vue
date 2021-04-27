<template>
    <div>
        <v-footer
            height="auto"
            color="green darken-3"
            dark
        >
            <v-layout
                justify-center
                row
                wrap
            >
                <v-flex
                    xs3
                    text-xs-center>
                    <a
                        style="color: #f7cd6f"
                        class="orange-text text-lighten-3 tooltipped"
                        data-position="top"
                        data-delay="50"
                        data-tooltip="Ir para o portal de notícias, tutoriais e legislações da Lei de Incentivo &agrave; Cultura"
                        target="_blank"
                        href="http://rouanet.cultura.gov.br/"
                        data-tooltip-id="11c539d7-9913-16c0-3c16-ce4e6364817f">LEI DE INCENTIVO &Agrave; CULTURA</a>
                </v-flex>
                <v-flex
                    xs3
                    text-xs-center>
                    Desenvolvimento | SQL Server
                </v-flex>
                <v-flex
                    xs3
                    text-xs-center>
                    Versão:
                    <a
                        v-if="Object.keys(versao).length > 0"
                        style="color: #f7cd6f"
                        class="tooltipped"
                        data-position="top"
                        data-delay="50"
                        data-tooltip="Branch: avaliacao-resultados-fix-footer"
                        target="_blank"
                        href="https://github.com/culturagovbr/salic-minc/releases"
                    >
                        {{ versao }}
                    </a>
                    <a
                        v-else
                        href="#"
                        style="color: #f7cd6f"
                    >
                        carregando...
                    </a>
                </v-flex>
                <v-flex
                    xs3
                    text-xs-center>
                    <a
                        style="color: #f7cd6f"
                        class="orange-text text-lighten-3 tooltipped"
                        data-position="top"
                        data-delay="50"
                        data-tooltip="Ir para o Site: Desenvolvimento Salic MinC"
                        target="_blank"
                        href="http://culturagovbr.github.io/salic-minc"
                        data-tooltip-id="14f518c8-eb5f-3b9a-c172-cf75020f54f5">Desenvolvimento</a>
                </v-flex>
            </v-layout>
        </v-footer>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    name: 'SlFoot',
    components: {},
    data() {
        return {};
    },
    computed: {
        ...mapGetters({
            versao: 'layout/versao',
        }),
    },
};
</script>
