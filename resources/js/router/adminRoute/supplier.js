import indexSupplier from "../../view/admin/supplier/index";
import createSupplier from "../../view/admin/supplier/create";
import editSupplier from "../../view/admin/supplier/edit";
import supplier_profile from "../../view/admin/supplier/supplier_profile";

import store from "../../store/admin";

export default [
    {
        path: 'supplier',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexSupplier',
                component: indexSupplier,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('supplier read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'supplier_profile/:id(\\d+)',
                props: true,
                name: 'supplier_profile',
                component: supplier_profile,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('supplier read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create',
                name: 'createSupplier',
                component: createSupplier,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('supplier create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'edit/:id(\\d+)',
                name: 'editSupplier',
                component: editSupplier,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('supplier edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
