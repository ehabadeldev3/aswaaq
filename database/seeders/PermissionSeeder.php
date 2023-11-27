<?php

namespace Database\Seeders;

use App\Models\Notify;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [

            //Client
            ['name' => 'client read', 'role' => 'clients' ,'category' => 'General'],
            ['name' => 'client edit', 'role' => 'clients' ,'category' => 'General'],
            ['name' => 'dashboard statistics read', 'role' => 'general' ,'category' => 'General'],
            ['name' => 'client category read', 'role' => 'clients','category' => 'General'],
            ['name' => 'client category create', 'role' => 'clients','category' => 'General'],
            ['name' => 'client category edit', 'role' => 'clients','category' => 'General'],
            ['name' => 'client category delete', 'role' => 'clients','category' => 'General'],


            /////////////////////////////stock management
            //price
            //virtualStock
            ['name' => 'virtualStock read', 'role' => 'stock_management', 'category' => 'Stocks Management'],
            ['name' => 'virtualStock create', 'role' => 'stock_management', 'category' => 'Stocks Management'],
            ['name' => 'virtualStock edit', 'role' => 'stock_management', 'category' => 'Stocks Management'],
            ['name' => 'virtualStock delete', 'role' => 'stock_management', 'category' => 'Stocks Management'],

            //Supplier
            ['name' => 'supplier read', 'role' => 'stock_management','category' => 'Stocks Management'],
            ['name' => 'supplier create', 'role' => 'stock_management','category' => 'Stocks Management'],
            ['name' => 'supplier edit', 'role' => 'stock_management','category' => 'Stocks Management'],
            ['name' => 'supplier delete', 'role' => 'stock_management','category' => 'Stocks Management'],

            // orders management
            ['name' => 'orderOnline read', 'role' => 'Orders', 'category' => 'orders management'],
            ['name' => 'orderOnline edit', 'role' => 'Orders', 'category' => 'orders management'],
            ['name' => 'run sheet read', 'role' => 'Orders', 'category' => 'orders management'],
            ['name' => 'run sheet edit', 'role' => 'Orders', 'category' => 'orders management'],
            ['name' => 'CollectOrdersPerDay read', 'role' => 'Orders', 'category' => 'orders management'],
            ['name' => 'CollectOrdersPerDay edit', 'role' => 'Orders', 'category' => 'orders management'],


            // start product Management
            ['name' => 'category read','role' => 'productManagement','category' => 'category'],
            ['name' => 'category create','role' => 'productManagement','category' => 'category'],
            ['name' => 'category edit','role' => 'productManagement','category' => 'category'],
            ['name' => 'category delete','role' => 'productManagement','category' => 'category'],

            ['name' => 'subCategory read','role' => 'productManagement','category' => 'sub Category'],
            ['name' => 'subCategory create','role' => 'productManagement','category' => 'sub Category'],
            ['name' => 'subCategory edit','role' => 'productManagement','category' => 'sub Category'],
            ['name' => 'subCategory delete','role' => 'productManagement','category' => 'sub Category'],

            ['name' => 'company read','role' => 'productManagement','category' => 'Company'],
            ['name' => 'company create','role' => 'productManagement','category' => 'Company'],
            ['name' => 'company edit','role' => 'productManagement','category' => 'Company'],
            ['name' => 'company delete','role' => 'productManagement','category' => 'Company'],

            ['name' => 'measureUnit read','role' => 'productManagement','category' => 'MeasureUnit'],
            ['name' => 'measureUnit create','role' => 'productManagement','category' => 'MeasureUnit'],
            ['name' => 'measureUnit edit','role' => 'productManagement','category' => 'MeasureUnit'],
            ['name' => 'measureUnit delete','role' => 'productManagement','category' => 'MeasureUnit'],

            ['name' => 'size read','role' => 'productManagement','category' => 'Sizes'],
            ['name' => 'size create','role' => 'productManagement','category' => 'Sizes'],
            ['name' => 'size edit','role' => 'productManagement','category' => 'Sizes'],
            ['name' => 'size delete','role' => 'productManagement','category' => 'Sizes'],

            ['name' => 'flavor read','role' => 'productManagement','category' => 'Flavors'],
            ['name' => 'flavor create','role' => 'productManagement','category' => 'Flavors'],
            ['name' => 'flavor edit','role' => 'productManagement','category' => 'Flavors'],
            ['name' => 'flavor delete','role' => 'productManagement','category' => 'Flavors'],
            // end product Management


            ['name' => 'product read','role' => 'productManagement','category' => 'Product'],
            ['name' => 'product create','role' => 'productManagement','category' => 'Product'],
            ['name' => 'product edit','role' => 'productManagement','category' => 'Product'],
            ['name' => 'product delete','role' => 'productManagement','category' => 'Product'],
            // end product Management

            // start Area Management
            ['name' => 'provinces read','role' => 'areaManagement','category' => 'Province'],
            ['name' => 'provinces create','role' => 'areaManagement','category' => 'Province'],
            ['name' => 'provinces edit','role' => 'areaManagement','category' => 'Province'],
            ['name' => 'provinces delete','role' => 'areaManagement','category' => 'Province'],

            ['name' => 'areas read','role' => 'areaManagement','category' => 'Areas'],
            ['name' => 'areas create','role' => 'areaManagement','category' => 'Areas'],
            ['name' => 'areas edit','role' => 'areaManagement','category' => 'Areas'],
            ['name' => 'areas delete','role' => 'areaManagement','category' => 'Areas'],
            // end Area Management


            ['name' => 'discount read','role' => '','category' => 'Discount'],
            ['name' => 'discount create','role' => '','category' => 'Discount'],
            ['name' => 'discount edit','role' => '','category' => 'Discount'],
            ['name' => 'discount delete','role' => '','category' => 'Discount'],

            ['name' => 'tax read','role' => '','category' => 'Tax'],
            ['name' => 'tax create','role' => '','category' => 'Tax'],
            ['name' => 'tax edit','role' => '','category' => 'Tax'],
            ['name' => 'tax delete','role' => '','category' => 'Tax'],



            // end role-employee

            // start role-employee
            ['name' => 'department read','role' => 'role-employee','category' => 'Department'],
            ['name' => 'department create','role' => 'role-employee','category' => 'Department'],
            ['name' => 'department edit','role' => 'role-employee','category' => 'Department'],
            ['name' => 'department delete','role' => 'role-employee','category' => 'Department'],

            ['name' => 'job read','role' => 'role-employee','category' => 'jobs'],
            ['name' => 'job create','role' => 'role-employee','category' => 'jobs'],
            ['name' => 'job edit','role' => 'role-employee','category' => 'jobs'],
            ['name' => 'job delete','role' => 'role-employee','category' => 'jobs'],

            ['name' => 'role read','role' => 'role-employee','category' => 'role'],
            ['name' => 'role create','role' => 'role-employee','category' => 'role'],
            ['name' => 'role edit','role' => 'role-employee','category' => 'role'],
            ['name' => 'role delete','role' => 'role-employee','category' => 'role'],

            ['name' => 'employee read','role' => 'role-employee','category' => 'employee'],
            ['name' => 'employee create','role' => 'role-employee','category' => 'employee'],
            ['name' => 'employee edit','role' => 'role-employee','category' => 'employee'],
            ['name' => 'employee delete','role' => 'role-employee','category' => 'employee'],
            ['name' => 'employeeChangePassword edit','role' => 'role-employee','category' => 'employee'],

            ['name' => 'loading man read','role' => 'role-employee','category' => 'LoadingMan'],
            ['name' => 'loading man create','role' => 'role-employee','category' => 'LoadingMan'],
            ['name' => 'loading man edit','role' => 'role-employee','category' => 'LoadingMan'],
            ['name' => 'loading man delete','role' => 'role-employee','category' => 'LoadingMan'],
            ['name' => 'loading man ChangePassword edit','role' => 'role-employee','category' => 'LoadingMan'],

            ['name' => 'dispatcher read','role' => 'role-employee','category' => 'Dispatcher'],
            ['name' => 'dispatcher create','role' => 'role-employee','category' => 'Dispatcher'],
            ['name' => 'dispatcher edit','role' => 'role-employee','category' => 'Dispatcher'],
            ['name' => 'dispatcher delete','role' => 'role-employee','category' => 'Dispatcher'],
            ['name' => 'dispatcher ChangePassword edit','role' => 'role-employee','category' => 'Dispatcher'],

            ['name' => 'representative read','role' => 'role-employee','category' => 'Representative'],
            ['name' => 'representative create','role' => 'role-employee','category' => 'Representative'],
            ['name' => 'representative edit','role' => 'role-employee','category' => 'Representative'],
            ['name' => 'representative delete','role' => 'role-employee','category' => 'Representative'],
            ['name' => 'representativeChangePassword edit','role' => 'role-employee','category' => 'Representative'],

            ['name' => 'car read','role' => 'role-employee','category' => 'Car'],
            ['name' => 'car create','role' => 'role-employee','category' => 'Car'],
            ['name' => 'car edit','role' => 'role-employee','category' => 'Car'],
            ['name' => 'car delete','role' => 'role-employee','category' => 'Car'],

            ['name' => 'shift read','role' => 'role-employee','category' => 'Shift'],
            ['name' => 'shift create','role' => 'role-employee','category' => 'Shift'],
            ['name' => 'shift edit','role' => 'role-employee','category' => 'Shift'],
            ['name' => 'shift delete','role' => 'role-employee','category' => 'Shift'],

            ['name' => 'employeeShifts read','role' => 'role-employee','category' => 'Employee Shifts'],
            ['name' => 'employeeShifts create','role' => 'role-employee','category' => 'Employee Shifts'],
            ['name' => 'employeeShifts edit','role' => 'role-employee','category' => 'Employee Shifts'],
            ['name' => 'employeeShifts delete','role' => 'role-employee','category' => 'Employee Shifts'],
            // end role-employee

            ['name' => 'Wallet target read','role' => 'wallet','category' => 'Wallet'],
            ['name' => 'Wallet target create','role' => 'wallet','category' => 'Wallet'],
            ['name' => 'Wallet target edit','role' => 'wallet','category' => 'Wallet'],
            ['name' => 'Wallet target delete','role' => 'wallet','category' => 'Wallet'],

            // start financial Accounts
            ['name' => 'AccountsTree read','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'AccountsTree create','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'AccountsTree edit','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'DailyRestriction read','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'DailyRestriction create','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'DailyRestriction edit','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'TrialBalance read','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'FinancialCenter read','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'IncomeList read','role' => 'financial Accounts','category' => 'financial Accounts'],
            ['name' => 'AccountStatement read','role' => 'financial Accounts','category' => 'financial Accounts'],
            // end financial Accounts

            // start ads
            //Sliders
            ['name' => 'offer read', 'role' => 'advertise', 'category' => 'Ads'],
            ['name' => 'offer create', 'role' => 'advertise', 'category' => 'Ads'],
            ['name' => 'offer edit', 'role' => 'advertise', 'category' => 'Ads'],
            ['name' => 'offer delete', 'role' => 'advertise', 'category' => 'Ads'],

            ['name' => 'SliderAds read','role' => 'ads','category' => 'Ads'],
            ['name' => 'SliderAds create','role' => 'ads','category' => 'Ads'],
            ['name' => 'SliderAds edit','role' => 'ads','category' => 'Ads'],
            ['name' => 'SliderAds delete','role' => 'ads','category' => 'Ads'],
            // end ads

            // start sitting
            ['name' => 'setting read','role' => 'setting','category' => 'Setting'],
            ['name' => 'setting edit','role' => 'setting','category' => 'Setting'],
            ['name' => 'Terms And Conditions','role' => 'setting','category' => 'Setting'],
            // end setting


            // start platform Accounts
            ['name' => 'treasury read', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'treasury create', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'treasury edit', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'treasury delete', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'income read', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'income create', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'income edit', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'income delete', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'expense read', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'expense create', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'expense edit', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'expense delete', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'income&expense read', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'income&expense create', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'income&expense edit', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'income&expense delete', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'treasuriesIncome read', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'treasuriesExpense read', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'transferringTreasury read', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'transferringTreasury create', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'transferringTreasury edit', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],
            ['name' => 'transferringTreasury delete', 'role' => 'platform Accounts', 'category' => 'platform Accounts'],


            // end platform Accounts

            // start Suggestion
            ['name' => 'Suggestions read','role' => 'Suggestions','category' => 'Suggestions'],
            ['name' => 'Suggestions create','role' => 'Suggestions','category' => 'Suggestions'],
            ['name' => 'Suggestions edit','role' => 'Suggestions','category' => 'Suggestions'],
            ['name' => 'Suggestions delete','role' => 'Suggestions','category' => 'Suggestions'],
            ['name' => 'SuggestionClients read','role' => 'Suggestions','category' => 'Suggestions'],
            // end Suggestion

        ];


        $notifies = [
            'Add Suggestion',
            'Add Client',
            'Update Order',
            'Add Shop',
            'Online Payment',
            'Add OnlineOrder',
        ];

        foreach ($notifies as $notify) {
            Notify::create(['name' => $notify]);
        }

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'role' => $permission['role'],
                'category' => $permission['category']
            ]);
        }
    }
}
