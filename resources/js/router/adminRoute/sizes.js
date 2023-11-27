import indexSize from "../../view/admin/sizes/index";
import createSize from "../../view/admin/sizes/create";
import editSize from "../../view/admin/sizes/edit";
import store from "../../store/admin";

export default [
    {
        path: 'sizes',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexSize',
                component: indexSize,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('size read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create',
                name: 'createSize',
                component: createSize,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('size create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'edit/:id(\\d+)',
                name: 'editSize',
                component: editSize,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('size edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
