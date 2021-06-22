const routes = [
  { path: '/index', component: Index, meta: {title: 'Inicio'} },
    { 
        name: 'analisar', 
        path: '/analisar/:id', 
        component: Analisar, 
        meta: {title: 'Analisar'} ,
        children: [
            {
              // UserProfile will be rendered inside User's <router-view>
              // when /user/:id/profile is matched
              path: 'completa',
              name: 'completa',
              component: AnaliseCompleta 
            },
            {
              // UserPosts will be rendered inside User's <router-view>
              // when /user/:id/posts is matched
              path: 'amostragem',
              name: 'amostragem',
              component: AnaliseAmostragem
            }
          ]
    },
  { path: '*', redirect: '/index' }
]

const router = new VueRouter({
    routes 
})
