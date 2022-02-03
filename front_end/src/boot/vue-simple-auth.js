import initialize from 'vue-simple-auth';
import Cookies from 'universal-cookie';
 
export default async function({ router, store, ssrContext }) {
    const cookies = ssrContext ? new Cookies(ssrContext.req.headers.cookie) : new Cookies();
 
    const config = {
        httpClient: 'axios',
        watchLoggedIn: true,
        cookies: {
            path: '/'
        },
        token: {
            name: 'Authorization',
            type: 'Bearer'
        },
        vuex: {
            namespace: '$auth'
        },
        redirect: {
            routes: {
                auth: '/',
                guest: '/login',
            },
            queryFrom: 'from',
            guard: ({ $auth }) => $auth.loggedIn,
        },
        endpoints: {
            authenticate: {
                method: 'post',
                url: '/login',
                property: 'access_token'
            },
            user: {
                method: 'get',
                url: '/user'
            },
            logout:{
                url:'/logout'
            },
        },
    };
 
  await initialize({ router, store, cookies, config });
};