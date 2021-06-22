Vue.component(
    'salic-table-easy',
    {
        template:`
            <table :class="dados.class">
                <template v-if="(dados.lines && dados.lines.length > 0)">
                    <thead v-show="thead">
                        <template v-for="cab in dados.cols">
                            <template v-if="(typeof cab == 'object')">
                                <th :class="cab.class">
                                    {{cab.name}}
                                </th>
                            </template>
                            <template v-else>
                                <th>
                                    {{cab}}
                                </th>
                            </template>
                        </template>
                    </thead>
                    <tbody>
                        <tr v-for="(dado, index) in dados.lines">
                            <template v-for="(d, i) in dado">
                                <template v-if="(typeof dados.cols[i] == 'object')">
                                    <td v-html="d" :class="dados.cols[i].class"></td>
                                </template>
                                <template v-else>
                                    <td v-html="d"></td>
                                </template>
                            </template>
                        </tr>
                    </tbody>
                    <tfoot v-if="tfoot && dados.tfoot">
                        <tr>
                            <td :class="dados.cols[cIndex].class" v-for="(dado, cIndex) in dados.cols">
                                <template v-for="(foot, index) in dados.tfoot" v-if="cIndex == index">
                                    {{foot}} 
                                </template>
                            </td>
                        </tr>
                    </tfoot>
                </template>
                <template v-else>
                    <tbody>
                        <tr>
                            <td> Sem dados</td>
                        </tr>
                    </tbody>
                </template>
            </table>
        `,
        props: {
            dados : null, 
            thead: { default: true}, 
            tfoot: { default: false}
        },
        mounted: function(){ }
    }
);
