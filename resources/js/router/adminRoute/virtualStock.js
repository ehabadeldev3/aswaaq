import indexVirtualStock from "../../view/admin/virtualStock/index.vue";
import createVirtualStock from "../../view/admin/virtualStock/create.vue";
import showVirtualStock from "../../view/admin/virtualStock/show.vue";
import indexPrice from "../../view/admin/virtualStock/all_prices_index";

import store from "../../store/admin";

export default [
    {
        path: 'virtualStock',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexPrice',
                component: indexPrice,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('virtualStock read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                    return next();
                }
            },
            {
                path: '',
                name: 'indexVirtualStock',
                component: indexVirtualStock,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('virtualStock read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create/:id(\\d+)',
                name: 'createVirtualStock',
                component: createVirtualStock,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('virtualStock create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'show/:id(\\d+)',
                name: 'showVirtualStock',
                component: showVirtualStock,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('virtualStock read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
