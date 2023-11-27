import indexCar from "../../view/admin/car/index";
import createCar from "../../view/admin/car/create";
import editCar from "../../view/admin/car/edit";
import store from "../../store/admin";

export default [
    {
        path: 'car',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexCar',
                component: indexCar,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('car read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create',
                name: 'createCar',
                component: createCar,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('car create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'edit/:id(\\d+)',
                name: 'editCar',
                component: editCar,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('car edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
