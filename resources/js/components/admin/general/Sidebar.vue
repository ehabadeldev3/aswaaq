<template>
    <!-- Sidebar -->
        <div :class="['sidebar' , this.$i18n.locale == 'ar' ? 'sidebar-ar' :'']" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li :class="[$route.name == 'dashboard'? 'active': '']">
                            <router-link :to="{name:'dashboard'}" >
                                <i class="fa fa-home" aria-hidden="true"></i>
                                <span>{{ $t('global.Dashboard') }}</span>
                            </router-link>
                        </li>


                        <li class="submenu" v-if="permission.includes('Orders')">
                            <a href="#">
                                <i class="fas fa-truck"></i>
                                <span>{{ $t("global.orders management") }}</span>
                                <span :class="['menu-arrow', 'menu-arrow-ar']"></span>
                            </a>
                            <ul>
                                <li :class="[{'active': ['indexOrderOnline','editOrderOnline','showOrderOnline'].includes($route.name)}]"
                                    v-if="permission.includes('orderOnline read')" >
                                    <router-link :to="{ name: 'indexOrderOnline' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.orders') }}
                                    </router-link>
                                </li>
                                <li :class="[$route.name == 'run_sheets' ? 'active' : '']"
                                    v-if="permission.includes('run sheet read')">
                                    <router-link :to="{ name: 'run_sheets' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.Run Sheets') }}
                                    </router-link>
                                </li>
                                <li :class="[$route.name == 'CollectOrdersPerDay' ? 'active' : '']"
                                    v-if="permission.includes('CollectOrdersPerDay read')">
                                    <router-link :to="{ name: 'CollectOrdersPerDay' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.CollectOrdersPerDay') }}
                                    </router-link>
                                </li>
                                <li :class="[$route.name == 'collect_orders_per_day_for_each_client' ? 'active' : '']"
                                    v-if="permission.includes('CollectOrdersPerDay read')">
                                    <router-link :to="{ name: 'collect_orders_per_day_for_each_client' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.collect_orders_per_day_for_each_client') }}
                                    </router-link>
                                </li>
                            </ul>
                        </li>






                    <li class="submenu" v-if="permission.includes('clients')" :class="[{'active': ['client_doesnt_have_orders','clients','editClient',''].includes($route.name)}]">
                        <a href="#"><i class="fas fa-suitcase"></i><span>{{ $t("global.Clients") }}</span><span
                                :class="['menu-arrow', 'menu-arrow-ar']"></span></a>
                        <ul>

                            <li :class="[$route.name == 'clients' ? 'active' : '']">
                                <router-link :to="{ name: 'clients' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                    {{ $t("sidebar.client") }}
                                </router-link>
                            </li>

                            <li :class="[$route.name == 'client_doesnt_have_orders' ? 'active' : '']">
                                <router-link :to="{ name: 'client_doesnt_have_orders' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                    {{ $t("global.clientDoesntHaveOrders") }}
                                </router-link>
                            </li>

                            <li
                                    :class="[{'active': ['indexClientCategories','createClientCategories','editClientCategories'].includes($route.name)}]"
                                    v-if="permission.includes('size read')"
                                >
                                    <router-link :to="{name:'indexClientCategories'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.Client Categories') }}
                                    </router-link>
                                </li>

                        </ul>
                    </li>

                        <!-- <li
                            :class="[{'active': ['indexSupplier','createSupplier','editSupplier'].includes($route.name)}]"
                            v-if="permission.includes('supplier read')"
                        >
                            <router-link :to="{name:'indexSupplier'}" >
                                <i class="fas fa-parachute-box"></i>
                                <span>{{ $t('sidebar.supplier') }}</span>
                            </router-link>
                        </li> -->

                        <!--start buy-->

                        <!-- <li class="submenu" v-if="permission.includes('buy')">
                            <a href="#" ><i class="fas fa-box-open"></i> <span> {{ $t('global.purchaseManagement') }}</span>  <span :class="[this.$i18n.locale == 'ar' ? 'menu-arrow-ar' : '','menu-arrow']"></span></a>
                            <ul>

                                <li
                                    :class="[{'active': ['createPurchaseInvoice','editPurchaseInvoice','indexPurchaseInvoice'].includes($route.name)}]"
                                    v-if="permission.includes('PurchaseInvoice read')"
                                >
                                    <router-link :to="{name:'indexPurchaseInvoice'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.PurchaseInvoice') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexPurchaseReturn','createPurchaseReturn'].includes($route.name)}]"
                                    v-if="permission.includes('PurchaseReturn read')"
                                >
                                    <router-link :to="{name:'indexPurchaseReturn'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.PurchaseReturn') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexEarnedDiscount','createEarnedDiscount','editEarnedDiscount'].includes($route.name)}]"
                                    v-if="permission.includes('EarnedDiscount read')"
                                >
                                    <router-link :to="{name:'indexEarnedDiscount'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.EarnedDiscount') }}
                                    </router-link>
                                </li>

                            </ul>
                        </li> -->

                        <!--end buy-->

                        <!--start store-->

                        <li class="submenu" v-if="permission.includes('productManagement')">
                            <a href="#" ><i class="fab fa-product-hunt"></i> <span> {{ $t('sidebar.ProductManagement') }}</span>  <span :class="[this.$i18n.locale == 'ar' ? 'menu-arrow-ar' : '','menu-arrow']"></span></a>
                            <ul>

                                <li
                                    :class="[{'active': ['indexCompany','createCompany','editCompany'].includes($route.name)}]"
                                    v-if="permission.includes('company read')"
                                >
                                    <router-link :to="{name:'indexCompany'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.company') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexCategory','createCategory','editCategory'].includes($route.name)}]"
                                    v-if="permission.includes('category read')"
                                >
                                    <router-link :to="{name:'indexCategory'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.category') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexSubCategory','createSubCategory','editSubCategory'].includes($route.name)}]"
                                    v-if="permission.includes('category read')"
                                >
                                    <router-link :to="{name:'indexSubCategory'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.subCategory') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexFlavor','createFlavor','editFlavor'].includes($route.name)}]"
                                    v-if="permission.includes('flavor read')"
                                >
                                    <router-link :to="{name:'indexFlavor'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.Flavors') }}
                                    </router-link>
                                </li>
                                <li
                                    :class="[{'active': ['indexSize','createSize','editSize'].includes($route.name)}]"
                                    v-if="permission.includes('size read')"
                                >
                                    <router-link :to="{name:'indexSize'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('global.Sizes') }}
                                    </router-link>
                                </li>
                                <li
                                    :class="[{'active': ['indexMeasure','createMeasure','editMeasure'].includes($route.name)}]"
                                    v-if="permission.includes('measureUnit read')"
                                >
                                    <router-link :to="{name:'indexMeasure'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.Measurement') }}
                                    </router-link>
                                </li>

                                <!-- <li
                                    :class="[{'active': ['indexSellingMethod','createSellingMethod','editSellingMethod'].includes($route.name)}]"
                                    v-if="permission.includes('sellingMethod read')"
                                >
                                    <router-link :to="{name:'indexSellingMethod'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.Selling_methods') }}
                                    </router-link>
                                </li> -->

                                <li
                                    :class="[{'active': ['indexProduct','createProduct','editProduct'].includes($route.name)}]"
                                    v-if="permission.includes('product read')"
                                >
                                    <router-link :to="{name:'indexProduct'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.products') }}
                                    </router-link>
                                </li>

                            </ul>
                        </li>

                        <!--end Store-->

                        <li class="submenu" v-if="permission.includes('stock_management')">
                            <a href="#"><i class="fas fa-cubes"></i><span>{{ $t("sidebar.StoreManagement") }}</span><span
                                :class="[this.$i18n.locale == 'ar' ? 'menu-arrow-ar' : '','menu-arrow']"></span></a>
                            <ul>
                                <li v-if="permission.includes('supplier read')">
                                    <router-link :to="{ name: 'indexSupplier' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t("sidebar.supplier") }}
                                    </router-link>
                                </li>

                                <li v-if="permission.includes('virtualStock read')"
                                    :class="[{ active: ['indexPrice', 'createPrice', 'editPrice'].includes($route.name) }]">
                                    <router-link :to="{ name: 'indexPrice' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t("global.Supplier Products Prices") }}
                                    </router-link>
                                </li>

                            </ul>
                        </li>
                        <!--start store-->


                        <!--start employee-->

                        <li class="submenu" v-if="permission.includes('role-employee')">
                            <a href="#" ><i class="fas fa-user-tie"></i> <span> {{ $t('global.EmployeesManagement') }}</span>  <span :class="[this.$i18n.locale == 'ar' ? 'menu-arrow-ar' : '','menu-arrow']"></span></a>
                            <ul>

                                <li
                                    :class="[{'active': ['indexDepartment','createDepartment','editDepartment'].includes($route.name)}]"
                                    v-if="permission.includes('department read')"
                                >
                                    <router-link :to="{name:'indexDepartment'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.Department') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexJob','createJob','editJob'].includes($route.name)}]"
                                    v-if="permission.includes('job read')"
                                >
                                    <router-link :to="{name:'indexJob'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.Jobs') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexRole','createRole','editRole'].includes($route.name)}]"
                                    v-if="permission.includes('role read')"
                                >
                                    <router-link :to="{name:'indexRole'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.Roles') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexEmployee','createEmployee','editEmployee'].includes($route.name)}]"
                                    v-if="permission.includes('employee read')"
                                >
                                    <router-link :to="{name:'indexEmployee'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t('sidebar.Employees') }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexShift','createShift','editShift'].includes($route.name)}]"
                                    v-if="permission.includes('shift read')"
                                >
                                    <router-link :to="{name:'indexShift'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.workShifts')}}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexEmployeeShift','createEmployeeShift','editEmployeeShift'].includes($route.name)}]"
                                    v-if="permission.includes('employeeShifts read')"
                                >
                                    <router-link :to="{name:'indexEmployeeShift'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.EmployeeShift')}}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexRepresentative','createRepresentative','editRepresentative','changePasswordRepresentative'].includes($route.name)}]"
                                    v-if="permission.includes('representative read')"
                                >
                                    <router-link :to="{name:'indexRepresentative'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.representative')}}
                                    </router-link>
                                </li>
                                <li
                                    :class="[{'active': ['indexDispatcher','createDispatcher','editDispatcher','changePasswordDispatcher'].includes($route.name)}]"
                                    v-if="permission.includes('dispatcher read')"
                                >
                                    <router-link :to="{name:'indexDispatcher'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.dispatcher')}}
                                    </router-link>
                                </li>
                                <li
                                    :class="[{'active': ['indexLoadingMan','createLoadingMan','editLoadingMan','changePasswordLoadingMan'].includes($route.name)}]"
                                    v-if="permission.includes('loading man read')"
                                >
                                    <router-link :to="{name:'indexLoadingMan'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.Loading Man')}}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexCar','createCar','editCar'].includes($route.name)}]"
                                    v-if="permission.includes('car read')"
                                >
                                    <router-link :to="{name:'indexCar'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.Car')}}
                                    </router-link>
                                </li>

                            </ul>
                        </li>

                        <!--end employee-->

                    <li class="submenu" v-if="permission.includes('platform Accounts')">
                        <a href="#"><i class="fas fa-coins"></i> <span> {{ $t('sidebar.Accounts') }}</span> <span
                                :class="['menu-arrow', this.$i18n.locale == 'ar' ? 'menu-arrow-ar' : '']"></span></a>
                        <ul>
                            <li :class="[$route.name == 'indexTreasury' ? 'active' : '']"
                                v-if="permission.includes('treasury read')">
                                <router-link :to="{ name: 'indexTreasury', params: { lang: this.$i18n.locale } }"
                                    :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : '']">
                                    {{ $t('sidebar.TreasuryManagement') }}
                                </router-link>
                            </li>

                            <li :class="[$route.name == 'indexIncome' ? 'active' : '']"
                                v-if="permission.includes('income read')">
                                <router-link :to="{ name: 'indexIncome', params: { lang: this.$i18n.locale } }"
                                    :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : '']">
                                    {{ $t('sidebar.IncomeManagement') }}
                                </router-link>
                            </li>

                            <li :class="[$route.name == 'indexExpense' ? 'active' : '']"
                                v-if="permission.includes('expense read')">
                                <router-link :to="{ name: 'indexExpense', params: { lang: this.$i18n.locale } }"
                                    :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : '']">
                                    {{ $t('sidebar.Expense') }}
                                </router-link>
                            </li>

                            <li :class="[$route.name == 'indexIncomeAndExpense' ? 'active' : '']"
                                v-if="permission.includes('income&expense read')">
                                <router-link
                                    :to="{ name: 'indexIncomeAndExpense', params: { lang: this.$i18n.locale } }"
                                    :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : '']">
                                    {{ $t('sidebar.IncomeAndExpense') }}
                                </router-link>
                            </li>


                            <li :class="[$route.name == 'suppliersDues' ? 'active' : '']"
                                v-if="permission.includes('SupplierDues read')">
                                <router-link :to="{ name: 'suppliersDues', params: { lang: this.$i18n.locale } }"
                                    :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : '']">
                                    {{ $t('global.Suppliers Dues') }}
                                </router-link>
                            </li>


                            <li :class="[$route.name == 'treasuriesIncome' ? 'active' : '']"
                                v-if="permission.includes('treasuriesIncome read')">
                                <router-link :to="{ name: 'treasuriesIncome', params: { lang: this.$i18n.locale } }"
                                    :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : '']">
                                    {{ $t('sidebar.treasuriesIncome') }}
                                </router-link>
                            </li>
                            <li :class="[$route.name == 'treasuriesExpense' ? 'active' : '']"
                                v-if="permission.includes('treasuriesExpense read')">
                                <router-link :to="{ name: 'treasuriesExpense', params: { lang: this.$i18n.locale } }"
                                    :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : '']">
                                    {{ $t('global.treasuriesExpense') }}
                                </router-link>
                            </li>

                            <li :class="[$route.name == 'indexTransferringTreasury' ? 'active' : '']"
                                v-if="permission.includes('transferringTreasury read')">
                                <router-link
                                    :to="{ name: 'indexTransferringTreasury', params: { lang: this.$i18n.locale } }"
                                    :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : '']">
                                    {{ $t('global.TransferringTreasury') }}
                                </router-link>
                            </li>

                        </ul>
                    </li>

                        <!--start financial Accounts-->
                        <li class="submenu" v-if="permission.includes('financial Accounts')">
                            <a href="#" ><i class="fas fa-boxes"></i> <span> {{$t('sidebar.FinancialAccounts')}}</span>  <span :class="['menu-arrow',this.$i18n.locale == 'ar'?'menu-arrow-ar':'']"></span></a>
                            <ul>

                                <li v-if="permission.includes('AccountsTree read')">
                                    <a  href="#" :class="['drop-child',this.$i18n.locale == 'ar'?'menu-arrow-ar ml-4':'padding-en']">
                                        <i  :class="['fa fa-tree',this.$i18n.locale == 'ar'?'t-right':'']"></i>
                                        {{$t('global.AccountsTree')}}
                                        <span :class="['menu-arrow',this.$i18n.locale == 'ar'?'menu-arrow-ar':'']"></span>
                                    </a>
                                    <ul>

                                        <li :class="[$route.name == 'indexAssets'? 'active': '']" v-if="permission.includes('AccountsTree read')">
                                            <router-link :to="{name:'indexAssets',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                                {{$t('sidebar.Assets')}}
                                            </router-link>
                                        </li>

                                        <li :class="[$route.name == 'indexOpponents'? 'active': '']" v-if="permission.includes('AccountsTree read')">
                                            <router-link :to="{name:'indexOpponents',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                                {{$t('global.Opponents')}}
                                            </router-link>
                                        </li>

                                        <li :class="[$route.name == 'indexExpenseAccounts'? 'active': '']" v-if="permission.includes('AccountsTree read')">
                                            <router-link :to="{name:'indexExpenseAccounts',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                                {{$t('global.Expenses')}}
                                            </router-link>
                                        </li>

                                        <li :class="[$route.name == 'indexIncomeAccounts'? 'active': '']" v-if="permission.includes('AccountsTree read')">
                                            <router-link :to="{name:'indexIncomeAccounts',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                                {{$t('global.Incomes')}}
                                            </router-link>
                                        </li>

                                    </ul>
                                </li>

                                <li :class="[$route.name == 'indexDailyRestriction'? 'active': '']" v-if="permission.includes('DailyRestriction read')">
                                    <router-link :to="{name:'indexDailyRestriction',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                        {{$t('global.DailyRestriction')}}
                                    </router-link>
                                </li>

                                <li :class="[$route.name == 'indexTrialBalance'? 'active': '']" v-if="permission.includes('TrialBalance read')">
                                    <router-link :to="{name:'indexTrialBalance',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                        {{$t('global.TrialBalance')}}
                                    </router-link>
                                </li>

                                <li :class="[$route.name == 'indexFinancialCenter'? 'active': '']" v-if="permission.includes('FinancialCenter read')">
                                    <router-link :to="{name:'indexFinancialCenter',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                        {{$t('global.FinancialCenter')}}
                                    </router-link>
                                </li>

                                <li :class="[$route.name == 'indexIncomeList'? 'active': '']" v-if="permission.includes('IncomeList read')">
                                    <router-link :to="{name:'indexIncomeList',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                        {{$t('global.IncomeList')}}
                                    </router-link>
                                </li>

                                <li :class="[$route.name == 'indexAccountStatement'? 'active': '']" v-if="permission.includes('AccountStatement read')">
                                    <router-link :to="{name:'indexAccountStatement',params: {lang:this.$i18n.locale}}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl':'']">
                                        {{$t('global.AccountStatement')}}
                                    </router-link>
                                </li>

                            </ul>
                        </li>
                        <!--end financial Accounts-->

                        <!--start Suggestions-->
                        <li class="submenu" v-if="permission.includes('Suggestions')">
                            <a href="#" ><i class="fas fa-comment-dots"></i> <span> {{$t('global.Suggestions')}}</span>  <span :class="[this.$i18n.locale == 'ar' ? 'menu-arrow-ar' : '','menu-arrow']"></span></a>
                            <ul>
                                <li
                                    :class="[{'active': ['indexSuggestion','createSuggestion','editSuggestion'].includes($route.name)}]"
                                    v-if="permission.includes('Suggestions read')"
                                >
                                    <router-link :to="{name:'indexSuggestion'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.Suggestions')}}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexSuggestionClient','showSuggestionClient','editSuggestion'].includes($route.name)}]"
                                    v-if="permission.includes('SuggestionClients read')"
                                >
                                    <router-link :to="{name:'indexSuggestionClient'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.SuggestionClients')}}
                                    </router-link>
                                </li>


                            </ul>
                        </li>
                        <!--end Suggestions-->

                        <!--start ads-->

                        <li class="submenu" v-if="permission.includes('ads')">
                            <a href="#" ><i class="fas fa-ad"></i> <span> {{$t('global.adsManegment')}}</span>  <span :class="[this.$i18n.locale == 'ar' ? 'menu-arrow-ar' : '','menu-arrow']"></span></a>
                            <ul>

                                <li v-if="permission.includes('offer read')"
                                    :class="[$route.name == 'OfferIndex' ? 'active' : '']">
                                    <router-link :to="{ name: 'OfferIndex' }" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{ $t("sidebar.Products Offers") }}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexFirstSliderAds','createFirstSliderAds','editFirstSliderAds'].includes($route.name)}]"
                                    v-if="permission.includes('SliderAds read')"
                                >
                                    <router-link :to="{name:'indexFirstSliderAds'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.firstSliderAds')}}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexSecondSliderAds','createSecondSliderAds','editSecondSliderAds'].includes($route.name)}]"
                                    v-if="permission.includes('SliderAds read')"
                                >
                                    <router-link :to="{name:'indexSecondSliderAds'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : '']">
                                        {{$t('global.secondSliderAds')}}
                                    </router-link>
                                </li>

                            </ul>
                        </li>

                        <!--end ads-->

                        <!--start area-->

                        <li class="submenu" v-if="permission.includes('areaManagement')">
                            <a href="#" ><i class="fas fa-city"></i> <span> {{$t('global.AreasManagement')}}</span>  <span :class="[this.$i18n.locale == 'ar' ? 'menu-arrow-ar' : '','menu-arrow']"></span></a>
                            <ul>

                                <li
                                    :class="[{'active': ['indexProvince','createProvince','editProvince'].includes($route.name)}]"
                                    v-if="permission.includes('areas read')"
                                >
                                    <router-link :to="{name:'indexProvince'}" :class="[this.$i18n.locale == 'ar' ?  'sidebar-menu-rtl' : 'sidebar-menu']">
                                        {{$t('global.Provinces')}}
                                    </router-link>
                                </li>

                                <li
                                    :class="[{'active': ['indexArea','createArea','editArea'].includes($route.name)}]"
                                    v-if="permission.includes('areas read')"
                                >
                                    <router-link :to="{name:'indexArea'}" :class="[this.$i18n.locale == 'ar' ? 'sidebar-menu-rtl' : 'sidebar-menu']">
                                        {{$t('global.areas')}}
                                    </router-link>
                                </li>

                            </ul>
                        </li>

                        <!--end area-->

                        <li
                            :class="[{'active': ['indexDiscount','createDiscount','editDiscount'].includes($route.name)}]"
                            v-if="permission.includes('discount read')"
                        >
                            <router-link :to="{name:'indexDiscount'}" >
                                <i class="fas fa-percent"></i>
                                <span>{{ $t('sidebar.coupon') }}</span>
                            </router-link>
                        </li>

                        <li
                            :class="[{'active': ['indexTax','createTax','editTax'].includes($route.name)}]"
                            v-if="permission.includes('tax read')"
                        >
                            <router-link :to="{name:'indexTax'}" >
                                <i class="fas fa-table"></i>
                                <span>{{ $t('sidebar.types_of_taxes') }}</span>
                            </router-link>
                        </li>

                        <li
                            :class="[{'active': ['indexWalletTarget','editWalletTarget','createWalletTarget'].includes($route.name)}]"
                            v-if="permission.includes('Wallet target read')"
                        >
                            <router-link :to="{name:'indexWalletTarget'}" >
                                <i class="fas fa-credit-card" aria-hidden="true"></i>
                                <span>{{ $t('global.Wallet Targets') }}</span>
                            </router-link>
                        </li>

                        <!--start setting-->
                        <li
                            :class="[{'active': ['editSetting','indexSetting'].includes($route.name)}]"
                            v-if="permission.includes('setting read')"
                        >
                            <router-link :to="{name:'indexSetting'}" >
                                <i class="fas fa-cogs"></i>
                                <span>{{ $t('sidebar.setting') }}</span>
                            </router-link>
                        </li>
                        <li :class="[$route.name == 'terms_and_conditions'? 'active': '']" v-if="permission.includes('Terms And Conditions')">
                            <router-link :to="{name:'terms_and_conditions'}" >
                                <i class="fa fa-home" aria-hidden="true"></i>
                                <span>{{ $t('global.Terms And Conditions') }}</span>
                            </router-link>
                        </li>
                        <!--end setting-->

                        <!--start download Representative-->
                        <li>
                            <a download href="/web/mandob.apk" >
                                <i class="fas fa-motorcycle"></i>
                                <span>{{ $t('global.downloadRepresentative') }}</span>
                            </a>
                        </li>
                        <!--end download Representative-->

                    </ul>
                </div>
            </div>
        </div>
        <!-- /Sidebar -->
    </template>

    <script>
    import {ref,onMounted,reactive,computed} from 'vue';
    import {useStore} from 'vuex';

    export default {
       setup(){

            let store = useStore();

            let permission = computed(() => store.getters['authAdmin/permission']);

            onMounted(() => {
                var $slimScrolls = $('.slimscroll');

                // Sidebar Slimscroll
                if ($slimScrolls.length > 0) {
                    $slimScrolls.slimScroll({
                        height: 'auto',
                        width: '100%',
                        position: 'right',
                        size: '7px',
                        color: '#ccc',
                        allowPageScroll: false,
                        wheelStep: 10,
                        touchScrollStep: 100
                    });
                    var wHeight = $(window).height() - 60;
                    $slimScrolls.height(wHeight);
                    $('.sidebar .slimScrollDiv').height(wHeight);
                    $(window).resize(function () {
                        var rHeight = $(window).height() - 60;
                        $slimScrolls.height(rHeight);
                        $('.sidebar .slimScrollDiv').height(rHeight);
                    });
                }
            });


            return {permission};
       }
    }


    window.onload = (event) => {
        var Sidemenu = function () {
            this.$menuItem = $('#sidebar-menu a');
        };
        function init() {
        var $this = Sidemenu;
            $('#sidebar-menu a').on('click', function (e) {
                if ($(this).parent().hasClass('submenu')) {
                    e.preventDefault();
                }
                if (!$(this).hasClass('subdrop')) {
                    $('ul', $(this).parents('ul:first')).slideUp(350);
                    $('a', $(this).parents('ul:first')).removeClass('subdrop');
                    $(this).next('ul').slideDown(350);
                    $(this).addClass('subdrop');
                } else if ($(this).hasClass('subdrop')) {
                    $(this).removeClass('subdrop');
                    $(this).next('ul').slideUp(350);
                }
            });
            $('#sidebar-menu ul li.submenu a.active').parents('li:last').children('a:first').addClass('active').trigger('click');
        }

    // Sidebar Initiate
        init();

    };

    </script>

    <style>
    .sidebar-ar {
        left: unset;
        right: 0;
    }
    .sidebar .sidebar-menu > ul > li > a span {
        margin-right: 10px;
        text-wrap:wrap;
    }

    .sidebar-menu li a{
        color: #fff;
    }

    .sidebar-menu li a:hover{
        color: #cd1f21 !important;
    }

    .sidebar-menu li.active > a{
        color: #cd1f21 !important;
        background-color: #fff !important;
    }

    .menu-title {
        color: #fcb00c !important;
    }

    .show{
        display: block;
    }
    .sidebar{
        background-color: #3f4b7e;
    }

    .sidebar-menu .menu-arrow.menu-arrow-ar{
        left: 15px;
        right: unset;
    }
    .sidebar-menu-rtl{
        padding: 7px 45px 7px 10px!important;
    }
    .padding-en{
        padding: 7px 10px 7px 32px !important;
    }
    .drop-child{
        padding: none !important;
    }
    .drop-child span{
        float: none !important;
    }
    .t-right{
        text-align: right !important;
    }

    </style>
