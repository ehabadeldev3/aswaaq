
import store from "../../store/admin";
import client_doesnt_have_orders from "../../view/admin/client/client_doesnt_have_orders";
import client_profile from "../../view/admin/client/client_profile";
import clients from "../../view/admin/client/clients";

export default [
    {
        path: 'clients',
        component:  {
            template:'<router-view />',
        },
        children:[

            {
                path: 'client_doesnt_have_orders',
                name: 'client_doesnt_have_orders',
                component: client_doesnt_have_orders,

            },
            {
                path: 'client_profile/:id(\\d+)',
                props: true,
                name: 'client_profile',
                component: client_profile,
            },
            {
                path: '',
                name: 'clients',
                component: clients,
            },

        ],
        beforeEnter: (to, from,next) => {
            let permission = store.state.authAdmin.permission;

            if(permission.includes('client read')){
                return next();
            }else{
                return next({name:'Page404'});
            }
        }
    },
];
