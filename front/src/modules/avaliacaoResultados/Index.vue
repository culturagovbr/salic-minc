<template>
    <div id="app">
        <v-app>
            <SlNav></SlNav>
            <v-content>
                <v-container fluid>
                  <v-layout>
                    <router-view></router-view>
                  </v-layout>
                </v-container>
            </v-content>
            <SlFoot></SlFoot>
      </v-app>
    </div>
</template>

<script>
    import SlNav from './components/SlNav';
    import SlFoot from './components/SlFoot';

    export default {
        name: 'Index',
        components: { SlNav, SlFoot },
    };
</script>
