import indexFlavor from "../../view/admin/flavors/index.vue";
import createFlavor from "../../view/admin/flavors/create.vue";
import editFlavor from "../../view/admin/flavors/edit.vue";
import store from "../../store/admin";

export default [
    {
        path: 'flavors',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexFlavor',
                component: indexFlavor,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('flavor read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create',
                name: 'createFlavor',
                component: createFlavor,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('flavor create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'edit/:id(\\d+)',
                name: 'editFlavor',
                component: editFlavor,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('flavor edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
