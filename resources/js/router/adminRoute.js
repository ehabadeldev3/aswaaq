import {createRouter, createWebHistory} from 'vue-router';
import Index from '../view/admin/Dashboard.vue';
import department from './adminRoute/department';
import supplier from './adminRoute/supplier';
import category from './adminRoute/category';
import company from './adminRoute/company';
import job from './adminRoute/job';
import role from './adminRoute/role';
import employee from './adminRoute/employee';
import shift from './adminRoute/shift';
import employeeShift from './adminRoute/employeeShift';
import measure from './adminRoute/measure';
import discount from './adminRoute/discount';
import tax from './adminRoute/tax';
import virtualStock from './adminRoute/virtualStock';
import representative from './adminRoute/representative';
import province from './adminRoute/province';
import area from './adminRoute/area';
import product from './adminRoute/product';
import client from './adminRoute/client';
import Assets from "./adminRoute/Assets";
import wallet_target from './adminRoute/walletTarget';
import notification from '../view/admin/notifications';
import Page404 from '../view/admin/Page404.vue';
import middlewarePipeline from "./middlewarePipeline";
import store from "../store/admin";
import guest from "../middleware/admin/guest";
import checkToken from "../middleware/admin/checkToken";
import auth from "../middleware/admin/auth";
import login from "../view/admin/login";
import forgetPassword from "../view/admin/forgetPassword";
import resetPassword from "../view/admin/resetPassword";
import subCategory from "./adminRoute/subCategory";
import Opponents from "./adminRoute/Opponents";
import ExpenseAccounts from "./adminRoute/ExpenseAccounts";
import IncomeAccounts from "./adminRoute/IncomeAccounts";
import dailyRestriction from "./adminRoute/dailyRestriction";
import trialBalance from "./adminRoute/trialBalance";
import financialCenter from "./adminRoute/financialCenter";
import incomeList from "./adminRoute/incomeList";
import AccountStatement from "./adminRoute/AccountStatement";
import firstSliderAds from "./adminRoute/firstSliderAds";
import secondSliderAds from "./adminRoute/secondSliderAds";
import setting from "./adminRoute/setting";
import expense from "./adminRoute/expense";
import income from "./adminRoute/income";
import client_categories from "./adminRoute/client_categories";
import car from "./adminRoute/car";
import incomeAndExpense from "./adminRoute/incomeAndExpense";
import transferringTreasury from "./adminRoute/transferringTreasury";
import treasuriesExpense from "./adminRoute/treasuriesExpense";
import treasuriesIncome from "./adminRoute/treasuriesIncome";
import treasury from "./adminRoute/treasury";
import suggestion from "./adminRoute/suggestion";
import suggestionClient from "./adminRoute/suggestionClient";
import terms_and_conditions from "../view/admin/terms_and_conditions/Index.vue";
import orderOnline from "./adminRoute/orderOnline";
import showPrices from "./adminRoute/showPrices";
import offer from './adminRoute/offer';
import flavor from './adminRoute/flavor';
import sizes from './adminRoute/sizes';
import loadingMan from './adminRoute/loadingMan';
import dispatcher from './adminRoute/dispatcher';



const routes = [
    {
        path: '/',
        name: 'loginLang',
        component: login,
        meta: {
            middleware: [guest]
        }
    },
    {
        path: '/login',
        name: 'login',
        component: login,
        meta: {
            middleware: [guest]
        }
    },
    {
        path: '/forget-password',
        name: 'forgetPassword',
        component: forgetPassword,
        meta: {
            middleware: [guest]
        }
    },
    {
        path: '/reset-password',
        name: 'resetPassword',
        component: resetPassword,
        meta: {
            middleware: [guest]
        }
    },
    {
        path: '/dashboard',
        name: 'partner',
        component: {
            template: '<router-view />',
        },
        meta: {
            middleware: [auth, checkToken]
        },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: Index,
            },
            {
                path: 'notification',
                name: 'notification',
                component: notification,
            },
            {
                path: 'terms_and_conditions',
                name: 'terms_and_conditions',
                component: terms_and_conditions,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('Terms And Conditions')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
            ...department,
            ...job,
            ...role,
            ...employee,
            ...shift,
            ...employeeShift,
            ...flavor,
            ...sizes,
            ...supplier,
            ...category,
            ...company,
            ...measure,
            ...loadingMan,
            ...discount,
            ...tax,
            ...province,
            ...car,
            ...area,
            ...wallet_target,
            ...client_categories,
            ...representative,
            ...product,
            ...subCategory,
            ...client,
            ...Assets,
            ...dispatcher,
            ...Opponents,
            ...virtualStock,
            ...ExpenseAccounts,
            ...offer,
            ...IncomeAccounts,
            ...dailyRestriction,
            ...trialBalance,
            ...financialCenter,
            ...incomeList,
            ...AccountStatement,
            ...firstSliderAds,
            ...secondSliderAds,
            ...setting,
            ...expense,
            ...income,
            ...incomeAndExpense,
            ...transferringTreasury,
            ...treasuriesExpense,
            ...treasuriesIncome,
            ...treasury,
            ...suggestion,
            ...suggestionClient,
            ...orderOnline,
            ...showPrices,
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'Page404',
        component: Page404
    },
];

const router = createRouter({
    history: createWebHistory(),
    linkActiveClass: 'active',
    routes
});


router.beforeEach((to, from, next) => {

    if (!to.meta.middleware) return next();
    const middleware = to.meta.middleware;
    const context = {
        to,
        from,
        next,
        store
    };
    return middleware[0]({
        ...context,
        next: middlewarePipeline(context, middleware, 1)
    });
});

export default router;
