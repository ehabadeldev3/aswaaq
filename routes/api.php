<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'middleware' => ['changeLang']], function () {

    // start Dashboard auth
    Route::group(['prefix' => 'auth', 'namespace' => 'Dashboard'], function () {

        // start login
        Route::post('login', 'AuthDashboardController@login');

        // check token
        Route::get('checkToken',  'AuthDashboardController@authorizeUser');
    });

    // api token_access

    Route::middleware(['auth:api','can:view-dashboard'])->group(function () {
        // start Dashboard
        Route::group(['prefix' => 'dashboard', 'namespace' => 'Dashboard'], function () {

            Route::put('profile/{user}', 'ProfileController@updateUser');
            Route::get('profile', 'ProfileController@getUser');
            //start logout
            Route::post('logout', 'AuthDashboardController@logout');


            // start Notification
            Route::get('getAllNot', 'NotificationController@getAllNot');
            Route::get('getNotNotRead', 'NotificationController@getNotNotRead');
            Route::post('clearItem/{id}', 'NotificationController@clearItem');
            Route::post('getNotNotRead', 'NotificationController@clearAll');



            Route::get('dashboard-statistics', 'DashboardStatisticsController@index');


            // order Online
            Route::resource('orders', 'OrderController');
            Route::post('updateOrderItem', 'OrderController@updateOrderItem');
            Route::post('deleteItemCard', 'OrderController@deleteItemCard');

            // update orders
            Route::post('saveReceiverDetails/{order}', 'OrderController@saveReceiverDetails');
            Route::post('saveNoteToOrder/{order}', 'OrderController@saveNoteToOrder');
            Route::post('assignRepresentativeToOrder', 'OrderController@assignRepresentativeToOrder');
            Route::post('take_action_to_orders', 'OrderController@take_action_to_orders');
            Route::post('confirm_order_status', 'OrderController@confirm_order_status');
            Route::post('assignCarToOrder', 'OrderController@assignCarToOrder');

            //run sheet
            Route::get('get_run_sheets', 'RunSheetController@index');
            Route::post('create_run_sheet', 'RunSheetController@create_run_sheet');
            Route::post('updateDeficitForSheetItem', 'RunSheetController@updateDeficitForSheetItem');

            //  collect_order_by_date
            Route::get('collect_order_by_date', 'CollectOrdersByDateController@collect_order_by_date');
            Route::get('collect_orders_per_day_for_each_client', 'CollectOrdersByDateController@collect_orders_per_day_for_each_client');
            Route::post('updateDeficitForProduct', 'CollectOrdersByDateController@updateDeficitForProduct');



            // client
            Route::get('activationClient/{user}', 'ClientController@activationClient');
            Route::get('get_client_doesnt_have_orders', 'ClientController@get_client_doesnt_have_orders');
            Route::get('clients', 'ClientController@index');
            Route::get('client_profile/{user}', 'ClientController@client_details');
            Route::get('client_orders', 'ClientController@client_orders');
            Route::post('sendNotificationToAllClients', 'ClientController@sendNotificationToAllClients');
            Route::post('sendNotificationToClient', 'ClientController@sendNotificationToClient');
            Route::resource('client_categories', 'ClientCategoriesController')->except(['show']);


            // category
            Route::resource('category', 'CategoryController');
            Route::get('activationCategory/{category}', 'CategoryController@activationCategory');
            Route::get('activeCategory', 'CategoryController@activeCategory');

            // sub category
            Route::resource('subCategory', 'SubCategoryController')->except(['show']);
            Route::get('get_subCategories_by_category_id/{id}', 'SubCategoryController@getSubCategories');

            // company
            Route::resource('company', 'CompanyController')->except(['show']);
            Route::get('activationCompany/{company}', 'CompanyController@activationCompany');

            // flavor
            Route::resource('flavor', 'FlavorsController')->except(['show']);
            Route::get('activationFlavor/{flavor}', 'FlavorsController@activationFlavor');

            // sizes
            Route::resource('size', 'SizesController')->except(['show']);
            Route::get('activationSize/{size}', 'SizesController@activationSize');

            Route::resource('measure', 'MeasurementUnitController')->except(['show']);
            Route::get('activationMeausre/{measurement_unit}', 'MeasurementUnitController@activationMeausre');

            // product
            Route::resource('product', 'ProductController')->except(['show']);
            Route::get('activationProduct/{product}', 'ProductController@activationProduct');
            Route::post('delete_image/{product}', 'ProductController@delete_image');
            Route::get('activeProduct', 'ProductController@activeProduct');



            // supplier
            Route::resource('supplier', 'SupplierController')->except(['show']);
            Route::get('activationSupplier/{supplier}', 'SupplierController@activationSupplier');
            Route::get('activeSupplier', 'SupplierController@activeSupplier'); // using this when create dispatcher or when filter orders by supplier
            Route::get('supplier_profile/{supplier}', 'SupplierController@supplier_details');
            Route::get('supplier_orders', 'SupplierController@supplier_orders');
            Route::get('supplier_products', 'SupplierController@supplier_products');

            Route::get('categories_by_supplier', 'VirtualStockController@categories_by_supplier');
            Route::get('get_all_prices', 'VirtualStockController@index');
            Route::get('get_all_prices_for_supplier/{supplier}', 'VirtualStockController@get_prices_for_supplier');
            Route::post('store_product_price', 'VirtualStockController@store_product_price');
            Route::post('virtualStockExcel', 'VirtualStockController@saveExcelVirtualStock');
            Route::post('download_supplier_prices/{supplier}', 'VirtualStockController@download_supplier_prices');
            Route::post("markProductAsBestPrice", "VirtualStockController@markProductAsBestPrice");

            //best offers
            Route::prefix("best_offers/settings")->group(function () {
                Route::post("", "VirtualStockController@insertLimitSettings");
                Route::get("", "VirtualStockController@getLimitSettings");
            });


            // start role
            Route::resource('role', 'RoleController');

            // department
            Route::resource('department', 'DepartmentController');
            Route::get('activeDepartment', 'DepartmentController@activeDepartment');
            Route::get('activationDepartment/{department}', 'DepartmentController@activationDepartment');

            // job
            Route::resource('job', 'JobController');
            Route::get('activeJob', 'JobController@activeJob');
            Route::get('activationJob/{job}', 'JobController@activationJob');

            // employee
            Route::resource('employee', 'EmployeeController');
            Route::get('activationEmployee/{employee}', 'EmployeeController@activationEmployee');
            Route::get('all_roles', 'EmployeeController@all_roles');
            Route::post('employee/changePassword/{employee}', 'EmployeeController@changePassword');

            // shift
            Route::resource('shift', 'ShiftController');
            Route::get('activeShift', 'ShiftController@activeShift');
            Route::get('activationShift/{shift}', 'ShiftController@activationShift');


            // employee Shift
            Route::resource('employeeShift', 'EmployeeShiftController');


            // Representative
            Route::resource('representative', 'RepresentativeController')->except(['show']);
            Route::get('activeRepresentative', 'RepresentativeController@activeRepresentative');
            Route::get('activationRepresentative/{representative}', 'RepresentativeController@activationRepresentative');
            Route::post('representative/changePassword/{representative}', 'RepresentativeController@changePassword');
            Route::get('get_representatives', 'RepresentativeController@get_representatives');

            // loading_man
            Route::resource('loading_man', 'LoadingManController')->except(['show']);
            Route::get('get_all_additional_loading_man_permissions', 'LoadingManController@get_all_additional_loading_man_permissions');
            Route::get('activationLoadingMan/{loading_man}', 'LoadingManController@activationLoadingMan');
            Route::post('loading_man/changePassword/{loading_man}', 'LoadingManController@changePassword');

            // dispatcher
            Route::resource('dispatcher', 'DispatcherController')->except(['show']);
            Route::get('get_all_additional_dispatcher_permissions', 'DispatcherController@get_all_additional_dispatcher_permissions');
            Route::get('activationDispatcher/{dispatcher}', 'DispatcherController@activationDispatcher');
            Route::post('dispatcher/changePassword/{dispatcher}', 'DispatcherController@changePassword');
            // cars
            Route::resource('car', 'CarController')->except(['show']);
            Route::get('activeCars', 'CarController@activeCars');
            Route::get('get_cars', 'CarController@cars'); //custom api


            // treasury management
            Route::resource('treasury', 'TreasuryController');
            Route::get('mainTreasury', 'TreasuryController@mainTreasury');
            Route::get('activeTreasury', 'TreasuryController@activeTreasury');
            Route::get('activationTreasury/{treasury}', 'TreasuryController@activationTreasury');
            Route::get('treasuriesIncome/{id}', 'TreasuryController@treasuriesIncome');
            Route::get('treasuriesExpense/{id}', 'TreasuryController@treasuriesExpense');


            // income
            Route::resource('income', 'IncomeController');
            Route::get('mainIncome', 'IncomeController@mainIncome');
            Route::get('activeIncome', 'IncomeController@activeIncome');
            Route::get('activationIncome/{income}', 'IncomeController@activationIncome');

            // expense
            Route::resource('expense', 'ExpenseController');
            Route::get('mainExpense', 'ExpenseController@mainExpense');
            Route::get('activeExpense', 'ExpenseController@activeExpense');
            Route::get('activationExpense/{expense}', 'ExpenseController@activationExpense');

            // income and expense
            Route::resource('incomeAndExpense', 'IncomeAndExpenseController');
            Route::get('calcIncome', 'IncomeAndExpenseController@calcIncome');
            Route::get('calcExpense', 'IncomeAndExpenseController@calcExpense');
            Route::get('editExpense/{income_and_expense}/edit', 'IncomeAndExpenseController@editExpense');

            // Transferring Treasury management
            Route::resource('transferringTreasury', 'TransferringTreasuryController');


            // Main Account
            Route::resource('mainAccount', 'MainAccountController');

            // Sub Account
            Route::get('subAccount/{main}/{sub_account}', 'SubAccountController@index');
            Route::get('getMainSub/{sub_account}', 'SubAccountController@getMainSub');
            Route::post('storeSubAccount/{main}/{sub_account}', 'SubAccountController@store');
            Route::put('updateSubAccount/{sub_account}', 'SubAccountController@update');
            Route::get('editSubAccount/{sub_account}', 'SubAccountController@edit');

            // Daily Restriction
            Route::resource('dailyRestriction', 'DailyRestrictionController');

            // trial Balance
            Route::get('trialBalance', 'TrialBalanceController@index');



            // Account Statement
            Route::get('accountStatement', 'AccountStatementController@index');
            Route::get('accountDaily', 'AccountStatementController@accountDaily');

            // Income List
            Route::resource('incomeList', 'IncomeListController');
            // Financial Center
            Route::resource('financialCenter', 'FinancialCenterController');


            // suggestion
            Route::resource('suggestion', 'SuggestionController')->except('show');
            Route::get('suggestionClient', 'SuggestionClientController@index');
            Route::get('suggestionClient/{id}', 'SuggestionClientController@show');


            // slider Ads
            Route::resource('ads', 'AdsController');

            //offers
            Route::get('offers/products', 'OfferController@getProducts');
            Route::resource('offers', 'OfferController');


            Route::resource('province', 'ProvinceController');

            // area
            Route::resource('area', 'AreaController')->except(['show']);
            Route::get('get_all_areas', 'AreaController@get_all_areas');



            // discount
            Route::resource('discount', 'DiscountController')->except(['show']);
            Route::get('activationDiscount/{discount}', 'DiscountController@activationDiscount');

            // tax
            Route::resource('tax', 'TaxController')->except(['show']);
            Route::get('activationTax/{tax}', 'TaxController@activationTax');

            // Wallet Target
            Route::resource('wallet_target', 'WalletTargetController');
            Route::get('get_all_client_categories', 'WalletTargetController@get_all_client_categories');
            Route::get('get_all_clients', 'WalletTargetController@get_all_clients'); //get all clients for wallet targets

            // setting Ads
            Route::resource('setting', 'SettingController');


            Route::post('terms_and_conditions', 'TermsAndConditionsController@store');
            Route::get('terms_and_conditions', 'TermsAndConditionsController@index');


        });
    });
    // start Api mobile

    Route::group(['prefix' => 'mobile', 'namespace' => 'MobileApp', 'middleware' => ['closeApp']], function () {

        // provinces with areas
        Route::get('provinces', 'ProvinceController@province');
        // setting
        Route::get('setting', 'SettingController@setting');

        //check phone exist
        Route::get('checkPhone/{phone}', 'AuthMobileController@checkPhone');
        Route::get('terms', 'SettingController@terms');

        Route::post('signUp', 'AuthMobileController@signUp');
        Route::post('signIn', 'AuthMobileController@signIn');
        Route::post('forgotPassword', 'ForgotPasswordController@forgotPassword');

        Route::middleware(['auth:api','can:client-api'])->group(function () {

            Route::get('me',  'AuthMobileController@me');
            Route::post('changePassword',  'AuthMobileController@changePassword');

            Route::get('get_all_notifications',  'ProfileController@get_all_notifications');
            Route::get('get_unread_notifications',  'ProfileController@get_un_read_notifications');
            Route::post('mark_as_read',  'ProfileController@mark_as_read');

            Route::put('updateProfile',  'ProfileController@updateProfile');
            Route::put('update_image',  'ProfileController@update_image');

            //ads
            Route::resource('shop', 'ShopController');
            Route::post('update_selected_shop/{id}', 'ShopController@update_selected_shop');
            Route::get('slidersAds', 'AdsController@slidersAds');

            // category
            Route::get('categoryHome', 'CategoryController@categoryHome');
            Route::get('category', 'CategoryController@category');
            Route::get('subCategory/{id}', 'CategoryController@subCategory');

            //company
            Route::get('company/{id}', 'CompanyController@companyBySubCategoryId');
            Route::get('company', 'CompanyController@company');
            Route::get('companyHome', 'CompanyController@companyHome');

            //products
            Route::get('productCompany/{id}', 'ProductController@productCompany');
            Route::get('productCompanySubCategory/{subCategory_id}/{company_id}', 'ProductController@productCompanySubCategory');
            Route::get('getProductByBarcode/{barcode}', 'ProductController@getProductByBarcode');
            Route::get('products', 'ProductController@products');
            Route::get('best_products', 'ProductController@best_products');
            Route::get('get_products_offers', 'ProductController@get_products_offers');
            Route::get('products_by_category', 'ProductController@products_by_category');
            Route::get('similarProducts', 'ProductController@similarProducts');
            Route::get('product_details/{product}', 'ProductController@product_details');

            // suggestion
            Route::get('getSuggestion',  'SuggestionClientController@getSuggestion');
            Route::post('suggestion',  'SuggestionClientController@suggestion');

            // get taxes
            Route::get('getTaxes',  'TaxController@getTaxes');

            // get shipping price
            Route::get('shippingPrice',  'ShippingController@shippingPrice');
            Route::get('get_notifications',  'ProfileController@get_notifications');

            // check coupon
            Route::post('checkCoupon',  'CouponController@checkCoupon');

            // order
            Route::post('order',  'OrderController@createOrder');
            Route::post('get_date_and_cart_validation',  'OrderController@get_date_and_cart_validation');
            Route::get('trackingOrder',  'OrderController@trackingOrder');
            Route::get('finishedOrders',  'OrderController@finishedOrders');
            Route::get('active_targets',  'OrderController@active_targets');
        });
    });

    //end Api mobile

    // start Api mobile

    Route::group(['prefix' => 'representative', 'namespace' => 'Representative'], function () {
        Route::post('signIn', 'AuthRepresentativeController@signIn');
        Route::middleware(['auth:api','can:represenntative-api'])->group(function () {
            // Route::get('me',  'AuthRepresentativeController@me');
            // Route::post('changePassword',  'AuthRepresentativeController@changePassword');
            Route::get('representativeOrder', 'OrderController@getRepresentativeOrder');
            Route::post('orderDelivered/{id}', 'OrderController@orderDelivered');
            Route::post('orderReturned/{id}', 'OrderC       ontroller@orderReturned');
            Route::post('orderPartReturned/{id}', 'OrderController@orderPartReturned');
        });
    });

    //end Api mobile
});
