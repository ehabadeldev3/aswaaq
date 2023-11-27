import indexClientCategories from "../../view/admin/client_categories/index";
import createClientCategories from "../../view/admin/client_categories/create";
import editClientCategories from "../../view/admin/client_categories/edit";
import store from "../../store/admin";

export default [
    {
        path: 'client_categories',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexClientCategories',
                component: indexClientCategories,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('client category read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create',
                name: 'createClientCategories',
                component: createClientCategories,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('client category create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'edit/:id(\\d+)',
                name: 'editClientCategories',
                component: editClientCategories,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('client category edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
