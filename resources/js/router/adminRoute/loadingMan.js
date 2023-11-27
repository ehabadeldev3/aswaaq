import indexLoadingMan from "../../view/admin/loading_man/index";
import createLoadingMan from "../../view/admin/loading_man/create";
import editLoadingMan from "../../view/admin/loading_man/edit";
import changePasswordLoadingMan from "../../view/admin/loading_man/changePassword";
import store from "../../store/admin";

export default [
    {
        path: 'loading_man',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexLoadingMan',
                component: indexLoadingMan,
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
                name: 'createLoadingMan',
                component: createLoadingMan,
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
                name: 'editLoadingMan',
                component: editLoadingMan,
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
                name: 'changePasswordLoadingMan',
                component: changePasswordLoadingMan,
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
