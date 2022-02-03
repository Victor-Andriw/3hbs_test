
const routes = [

    {
        path: '/login',
        name : 'login',
        component: () => import('pages/Login/Login.vue'),
        meta: {
            auth: {
                access: false,
            },
        },
    },

    {
        path: '/',
        redirect: '/home',
        component: () => import('layouts/MainLayout.vue'),
        children: [
            { 
                path: 'home',  
                name : 'home',
                component: () => import('src/pages/Home/index.vue') 
            },
            { 
                path: 'airports',  
                name : 'airports',
                component: () => import('src/pages/Airports/index.vue') 
            },
            { 
                path: 'airlines',  
                name : 'airlines',
                component: () => import('src/pages/Airlines/index.vue') 
            },
            { 
                path: 'flights',  
                name : 'flights',
                component: () => import('src/pages/Flights/index.vue') 
            },
        ],
        meta: {
            auth: {
                access: true,
            },
        },
    },

    // Always leave this as last one,
    // but you can also remove it
    {
        path: '*',
        component: () => import('pages/Error404.vue')
    }
]

export default routes
