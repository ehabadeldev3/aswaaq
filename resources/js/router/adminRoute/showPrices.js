import indexShowPrices from "../../view/admin/showPrices/index";
import store from "../../store/admin";

export default [
    {
        path: 'showPrices',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexShowPrices',
                component: indexShowPrices,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('order read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            }
        ]
    },
];
