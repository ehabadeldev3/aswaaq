import indexEmployeeShift from "../../view/admin/employeeShift/index";
import createEmployeeShift from "../../view/admin/employeeShift/create";
import editEmployeeShift from "../../view/admin/employeeShift/edit";
import store from "../../store/admin";

export default [
    {
        path: 'employeeShift',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'indexEmployeeShift',
                component: indexEmployeeShift,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('employeeShifts read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'create',
                name: 'createEmployeeShift',
                component: createEmployeeShift,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('employeeShifts create')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            {
                path: 'edit/:id(\\d+)',
                name: 'editEmployeeShift',
                component: editEmployeeShift,
                props: true,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('employeeShifts edit')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
