<template>
    <table :class="dados.class">
        <template v-if="(dados.lines && dados.lines.length > 0)">
            <thead v-show="thead">
                <template v-for="(cab, index) in dados.cols">
                    <template v-if="(typeof cab == 'object')">
                        <th
                            :class="cab.class"
                            :key="index">
                            {{ cab.name }}
                        </th>
                    </template>
                    <template v-else>
                        <th :key="index">
                            {{ cab }}
                        </th>
                    </template>
                </template>
            </thead>
            <tbody>
                <tr
                    v-for="(dado, index) in dados.lines"
                    :key="index"
                >
                    <template v-for="(d, i) in dado">
                        <template v-if="(typeof dados.cols[i] == 'object')">
                            <td
                                :key="i"
                                :class="dados.cols[i].class"
                                v-html="d"/>
                        </template>
                        <template v-else>
                            <td
                                :key="i"
                                v-html="d"
                            />
                        </template>
                    </template>
                </tr>
            </tbody>
            <tfoot v-if="tfoot && dados.tfoot">
                <tr>
                    <td
                        v-for="(dado, cIndex) in dados.cols"
                        :key="cIndex"
                        :class="dados.cols[cIndex].class">
                        <template
                            v-for="(foot, index) in dados.tfoot"
                            v-if="cIndex == index">
                            {{ foot }}
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
</template>
<script>
export default {
    name: 'SlTableEasy',
    props: {
        dados: {
            type: Object,
            default: () => {},
        },
        thead: {
            type: Boolean,
            default: true,
        },
        tfoot: {
            default: false,
            type: Boolean,
        },
    },
};
</script>
