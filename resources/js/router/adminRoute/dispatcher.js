import indexDispatcher from "../../view/admin/dispatcher/index";
import createDispatcher from "../../view/admin/dispatcher/create";
import editDispatcher from "../../view/admin/dispatcher/edit";
import changePasswordDispatcher from "../../view/admin/dispatcher/changePassword";
import store from "../../store/admin";

export default [
    {
        path: 'dispatcher',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexDispatcher',
                component: indexDispatcher,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('loading man read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create',
                name: 'createDispatcher',
                component: createDispatcher,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('loading man create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'edit/:id(\\d+)',
                name: 'editDispatcher',
                component: editDispatcher,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('loading man edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'changePassword/:id(\\d+)',
                name: 'changePasswordDispatcher',
                component: changePasswordDispatcher,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('loading man ChangePassword edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
