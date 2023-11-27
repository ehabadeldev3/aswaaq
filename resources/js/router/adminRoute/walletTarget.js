import indexWalletTarget from "../../view/admin/wallet_targets/index";
import editWalletTarget from "../../view/admin/wallet_targets/edit";
import createWalletTarget from "../../view/admin/wallet_targets/create";
import store from "../../store/admin";

export default [
    {
        path: 'wallet_target',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexWalletTarget',
                component: indexWalletTarget,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('Wallet target read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'edit/:id(\\d+)',
                name: 'editWalletTarget',
                component: editWalletTarget,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('Wallet target edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create',
                name: 'createWalletTarget',
                component: createWalletTarget,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('Wallet target create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
