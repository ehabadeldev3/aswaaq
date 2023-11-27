<template>
    <div :class="[
        'page-wrapper',
        this.$i18n.locale == 'ar' ? 'page-wrapper-ar' : '',
    ]">
        <notifications :position="this.$i18n.locale == 'ar' ? 'top left' : 'top right'" />

        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('global.Orders') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard', params: { lang: locale || 'ar' } }">
                                    {{ $t('dashboard.Dashboard') }}
                                </router-link>
                            </li>

                            <li class="breadcrumb-item active">{{ $t('global.Orders') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <!-- Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <loader v-if="loading" />
                        <div class="card-body">
                            <div class="card-header pt-0">
                                <form action="#" method="post">
                                    <div class="row filter-row">
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="search">{{ $t("global.Name Client") }}</label>
                                                <input id="search" type="text" v-model="search" class="form-control"
                                                    :placeholder="$t('global.Name Client')" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="search2">{{ $t("global.Order Number") }}</label>
                                                <input id="search2" type="text" v-model="search2" class="form-control"
                                                    :placeholder="$t('global.Order Number')" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="search3">{{ $t("global.representative") }}</label>

                                                <select class="form-control" v-model="search3" @change="getOrders">
                                                    <option value="">{{ $t("global.All Representatives") }}</option>
                                                    <option :value="representative_item.id"
                                                        v-for="representative_item in all_representatives"
                                                        :key="representative_item.id">{{ representative_item.name }}
                                                    </option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="search4">{{ $t("global.Suppliers") }}</label>

                                                <select class="form-control" v-model="search4" @change="getOrders">
                                                    <option value="">{{ $t("global.All Suppliers") }}</option>
                                                    <option :value="supplier_item.id" v-for="supplier_item in all_suppliers"
                                                        :key="supplier_item.id">{{ supplier_item.name }}</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="search5">{{ $t("global.Areas") }}</label>

                                                <select class="form-control" v-model="search5" @change="getOrders">
                                                    <option value="">{{ $t("global.All Areas") }}</option>
                                                    <option :value="area_item.id" v-for="area_item in all_areas"
                                                        :key="area_item.id">{{ area_item.name + " / " +
                                                            area_item.province.name }}</option>

                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="status">{{ $t("global.Order status") }}</label>
                                                <select class="form-control" v-model="filter_order_status"
                                                    @change="getOrders">
                                                    <option value="">{{ $t("global.All Orders") }}</option>
                                                    <option value="Pending">{{ $t("global.Pending") }}</option>
                                                    <option value="Processing">{{ $t("global.Processing") }}</option>
                                                    <option value="Shipping">{{ $t("global.ShippingNow") }}</option>
                                                    <option value="Completed">{{ $t("global.Completed") }}</option>
                                                    <option value="Canceled">{{ $t("global.Canceled") }}</option>
                                                    <option value="Full Return">{{ $t("global.Full Return") }}</option>
                                                    <option value="Partial Return">{{ $t("global.Partial Return") }}</option>
                                                    <option value="Hold">{{ $t("global.Hold") }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="status">{{
                                                    $t("global.Payment Status")
                                                }}</label>
                                                <select class="form-control" v-model="filter_payment_status"
                                                    @change="getOrders">
                                                    <option value="">
                                                        {{ $t("global.All Orders") }}
                                                    </option>
                                                    <option value="Paid">
                                                        {{ $t("global.Paid") }}
                                                    </option>
                                                    <option value="Unpaid">
                                                        {{ $t("global.Unpaid") }}
                                                    </option>
                                                    <option value="Failed">
                                                        {{ $t("global.Failed") }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="status">{{
                                                    $t("global.Payment Way")
                                                }}</label>
                                                <select class="form-control" v-model="filter_payment_method"
                                                    @change="getOrders">
                                                    <option value="">
                                                        {{ $t("global.All Orders") }}
                                                    </option>
                                                    <option value="Cash">
                                                        {{ $t("global.Cash on delivery") }}
                                                    </option>
                                                    <option value="Online">
                                                        {{ $t("global.Online payment") }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="fromDate">{{ $t("global.FromDate") }}</label>
                                                <input id="fromDate" type="date" v-model="fromDate" class="form-control"
                                                    @change="getOrders" :placeholder="$t('global.FromDate')" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="toDate">{{ $t("global.ToDate") }}</label>
                                                <input id="toDate" type="date" v-model="toDate" @change="getOrders"
                                                    class="form-control" :placeholder="$t('global.ToDate')" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="Delivery Date">{{ $t("global.Delivery Date") }}</label>
                                                <input id="Delivery Date" type="date" v-model="delivery_date"
                                                    @change="getOrders" class="form-control"
                                                    :placeholder="$t('global.Delivery Date')" />
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label>{{ $t('global.Filter Confirm Status') }}</label>
                                                <select class="form-control" v-model="filter_confirmed_status"
                                                    @change="getOrders">
                                                    <option value="">
                                                        {{ $t("global.All Orders") }}
                                                    </option>
                                                    <option value="confirmed">
                                                        {{ $t("global.Confirmed Orders") }}
                                                    </option>
                                                    <option value="non_confirmed">
                                                        {{ $t("global.Non Confirmed Orders") }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p><span>{{ $t('global.Total Amount') }} :</span>
                                            <span class="p-3 badge badge-info"
                                                >{{
                                                    total_amount + " " + $t('global.LE') }} </span>
                                        </p>
                                        <!-- <button @click.prevent="downloadExcelSheet" class="btn btn-custom btn-warning w-25">
                                        <i class="fa fa-download" aria-hidden="true"></i> {{ $t('global.Download Excel') }}
                                        </button> -->
                                        <p><span>{{ $t('global.Selected Orders') }} :</span>
                                            <span class="p-3 badge badge-purple"
                                                >{{
                                                    selected_orders.length  }} </span>
                                        </p>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info me-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#take_action" v-if="can_edit_order" style="height:49px">
                                            <i class="fa fa-edit"></i> {{ $t('global.Take Action') }}
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-success me-2"
                                             v-if="can_edit_order" style="height:49px" @click.prevent="confirm_status(0)">
                                            <i class="fa fa-check"></i> {{ $t('global.Confirm Status') }}
                                        </a>
                                        <a href="javascript:void(0);" style="max-width: 200px;height:49px" @click.prevent="downloadExcelSheet" class="btn btn-custom btn-warning me-2"
                                             v-if="can_edit_order">
                                             <i class="fa fa-download" aria-hidden="true"></i> {{ $t('global.Download run sheet as excel') }}
                                        </a>

                                        <select class="form-control" style="width:10%" v-model="paginate"
                                            @change="getOrders">
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="200">200</option>
                                            <option value="300">300</option>
                                        </select>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive" style="min-height: 450px;">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" style="height: 22px!important;width: 22px!important;" v-model="select_all_orders"
                                                    @change="select_all_orders_method">
                                            </th>
                                            <th>{{ $t("global.Order Number") }}</th>
                                            <th>{{ $t("global.Run Sheet Number") }}</th>
                                            <th>{{ $t("global.Name Client") }}</th>
                                            <th>{{ $t("global.Supplier Name") }}</th>
                                            <th>{{ $t("global.Address") }}</th>
                                            <th>{{ $t("global.Delivery Date") }}</th>
                                            <th>{{ $t("global.Order status") }}</th>
                                            <th>{{ $t("global.Payment Way") }}</th>
                                            <th>{{ $t("global.Payment Status") }}</th>
                                            <th>{{ $t("global.Total Amount") }}</th>
                                            <th v-if="can_edit_order">{{ $t("global.Car") }}</th>
                                            <th v-if="can_edit_order">{{ $t("global.representative") }}</th>
                                            <th>{{ $t("global.Notes") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="Object.keys(orders.data ?? {}).length">
                                            <tr v-for="(item, index) in orders.data" :key="item.id" :style="[item.confirmed_received_amount == 1 ? 'background-color:#eb232b;color:#fff!important' : '']">

                                                <td >
                                                    <input v-if="item.confirmed_received_amount != 1" type="checkbox" style="height: 22px!important;width: 22px!important;" :value="item.id" v-model="selected_orders"
                                                        :checked="selected_orders.includes(item.id)">
                                                        <span v-else>#</span>
                                                </td>

                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">{{ item.id }} </td>
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">{{ item.run_sheet ? item.run_sheet.run_sheet_id : ''  }}</td>
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">

                                                    {{
                                                        item.user.name
                                                    }}
                                                </td>
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">
                                                    <span :style="[item.confirmed_received_amount == 1 &&item.supplier.is_our_supplier? 'background-color:#3f4b7e!important':'']"
                                                        :class="[item.supplier.is_our_supplier ? 'badge badge-primary' : '']">{{
                                                            item.supplier.name
                                                        }}</span>

                                                </td>
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">

                                                    {{
                                                        item.area_name
                                                    }}
                                                </td>
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">

                                                    {{
                                                        item.delivery_date
                                                    }}
                                                </td>

                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">
                                                    <span v-if="item.hold == 0">
                                                        <i class="far fa-pause-circle"
                                                            v-if="item.order_status == 'Pending'"></i>
                                                        <i class="text-info fas fa-truck"
                                                            v-if="item.order_status == 'Shipping'"></i>
                                                        <i class="text-success fas fa-check-circle"
                                                            v-if="item.order_status == 'Completed'"></i>
                                                        <i class="text-info fa fa-cogs"
                                                            v-if="item.order_status == 'Processing'"></i>
                                                        <i class="fas fa-reply text-danger"
                                                            v-if="item.order_status == 'Refund'"></i>
                                                        <i class="fa fa-times-circle text-danger"
                                                            v-if="item.order_status == 'Canceled'"></i>
                                                        {{ $t("global." + item.order_status) }}</span>
                                                    <span v-else><i class="text-danger far fa-pause-circle"></i>
                                                        {{ $t("global.Hold") }}</span>
                                                </td>
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">{{
                                                    $t("global." + item.payment_method) }}</td>
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">
                                                    <i class="text-success fas fa-check-circle"
                                                        v-if="item.payment_status == 'Paid'"></i>
                                                    <i class="fa fa-times-circle text-danger"
                                                        v-if="item.payment_status == 'Failed'"></i>
                                                    <i class="text-dark far fa-pause-circle"
                                                        v-if="item.payment_status == 'Unpaid'"></i>
                                                    {{ $t("global." + item.payment_status) }}
                                                </td>
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">{{
                                                    item.total_amount }}</td>


                                                <td v-if="can_edit_order">

                                                <div class="col-md-12 alternativeDetail-option">
                                                    <div class="row account">

                                                        <div class="col-md-12 mb-12 body-account row">
                                                            <!--Start representative-->
                                                            <div class="">
                                                                <div class="dropdown"
                                                                    v-if="(item.order_status == 'Pending' || item.order_status == 'Shipping' || item.order_status == 'Processing') && !item.confirmed_received_amount">
                                                                    <button
                                                                        class="btn btn-secondary dropdown-toggle"
                                                                        type="button" id="dropdownMenuButton"
                                                                        data-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        <span v-if="item.car_id && item.car">
                                                                            <!-- <img :src="item.car ? item.car.image_path : '/web/img/logo.png'"
                                                                                alt="product-image" style="
                                                                            width: 50px;
                                                                            height: 50px;
                                                                            border-radius: 50%;
                                                                            " /> -->
                                                                            {{ item.car.name +" / "+ item.car.plate_number }}</span>
                                                                        <span v-else>{{
                                                                            $t("global.Car")
                                                                        }}</span>
                                                                    </button>
                                                                    <div :class="[
                                                                                'dropdown-menu',
                                                                                this.$i18n.locale == 'en' ? 'drop_ltr' : '',
                                                                            ]" style="
                                                                        height: 400px;
                                                                        overflow-y: scroll;
                                                                        width: 400px;
                                                                        z-index: 999999;
                                                                        " aria-labelledby="dropdownMenuButton">
                                                                        <input type="text"
                                                                            :placeholder="$t('global.Search')"
                                                                            v-model="car_search"
                                                                            :class="['form-control ', item.car_id ? 'w-75 d-inline' : 'w-100']"
                                                                            onchange="event.stopPropagation()" />
                                                                        <button class="btn btn-danger mx-2"
                                                                            v-if="item.car_id"
                                                                            @click="assignCarToOrder(item.id, 0, 'cancel')">{{ $t('global.Cancel') }}</button>
                                                                        <loader v-if="loading2" />

                                                                        <div v-for="car in cars"
                                                                            :key="car.id" :class="[
                                                                                'dropdown-item px-2 d-flex justify-content-between',
                                                                                car.id == item.car_id
                                                                                    ? 'bg-secondary'
                                                                                    : '',
                                                                            ]" @click="assignCarToOrder(item.id, car.id, 'assign')">
                                                                            <img :src="item.car ? car.image_path : '/web/img/logo.png'"
                                                                                alt="product-image"
                                                                                style="width: 50px; height: 50px" />
                                                                            <span style="
                                                overflow: hidden;
                                                height: 34px;
                                                font-size: 24px;
                                                word-break: break-word;
                                                ">{{ car.name +" / "+ car.plate_number }}</span>
                                                                        </div>

                                                                        <h5 v-if="Object.keys(cars ?? []).length ==
                                                                            0
                                                                            " class="text-center">
                                                                            {{ $t("global.No Data Found") }}
                                                                        </h5>
                                                                    </div>
                                                                </div>
                                                                <div v-else>
                                                                    <span v-if="item.car_id && item.car">
                                                                        <!-- <img :src="item.car ? item.car.image_path : ''"
                                                                            alt="product-image" style="
                                                                            width: 50px;
                                                                            height: 50px;
                                                                            border-radius: 50%;
                                                                        " /> -->
                                                                        {{ item.car.name +" / "+ item.car.plate_number }}</span>
                                                                    <span v-else>-</span>
                                                                </div>
                                                            </div>
                                                            <!--End representative-->

                                                        </div>
                                                    </div>
                                                </div>
                                                </td>

                                                <td v-if="can_edit_order">

                                                <div class="col-md-12 alternativeDetail-option" >
                                                    <div class="row account">

                                                        <div class="col-md-12 mb-12 body-account row">
                                                            <!--Start representative-->
                                                            <div class="">
                                                                <div class="dropdown"
                                                                    v-if="(item.order_status == 'Pending' || item.order_status == 'Shipping' || item.order_status == 'Processing') && !item.confirmed_received_amount">
                                                                    <button
                                                                        @click="order_area_id = item.area_id; getRepresentatives()"
                                                                        class="btn btn-secondary dropdown-toggle"
                                                                        type="button" id="dropdownMenuButton"
                                                                        data-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        <span v-if="item.representative_id">
                                                                            <img :src="item.representative.image_path"
                                                                                alt="product-image" style="
                                                width: 50px;
                                                height: 50px;
                                                border-radius: 50%;
                                                " />
                                                                            {{ item.representative.name }}</span>
                                                                        <span v-else>{{
                                                                            $t("global.representative")
                                                                        }}</span>
                                                                    </button>
                                                                    <div :class="[
                                                                                'dropdown-menu',
                                                                                this.$i18n.locale == 'en' ? 'drop_ltr' : '',
                                                                            ]" style="
                                                height: 400px;
                                                overflow-y: scroll;
                                                width: 400px;
                                                z-index: 999999;
                                                " aria-labelledby="dropdownMenuButton">
                                                                        <input type="text"
                                                                            :placeholder="$t('global.Search')"
                                                                            v-model="rep_search"
                                                                            :class="['form-control ', item.representative_id ? 'w-75 d-inline' : 'w-100']"
                                                                            onchange="event.stopPropagation()" />
                                                                        <button class="btn btn-danger mx-2"
                                                                            v-if="item.representative_id"
                                                                            @click="assignRepresentativeToOrder(item.id, 0, 'cancel')">{{ $t('global.Cancel') }}</button>
                                                                        <loader v-if="loading2" />

                                                                        <div v-for="rep in representatives"
                                                                            :key="rep.id" :class="[
                                                                                'dropdown-item px-2 d-flex justify-content-between',
                                                                                rep.id == item.representative_id
                                                                                    ? 'bg-secondary'
                                                                                    : '',
                                                                            ]" @click="assignRepresentativeToOrder(item.id, rep.id, 'assign')">
                                                                            <img :src="rep.image_path"
                                                                                alt="product-image"
                                                                                style="width: 50px; height: 50px" />
                                                                            <span style="
                                                overflow: hidden;
                                                height: 34px;
                                                font-size: 24px;
                                                word-break: break-word;
                                                ">{{ rep.name }}</span>
                                                                        </div>

                                                                        <h5 v-if="Object.keys(representatives ?? []).length ==
                                                                            0
                                                                            " class="text-center">
                                                                            {{ $t("global.No Data Found") }}
                                                                        </h5>
                                                                    </div>
                                                                </div>
                                                                <div v-else>
                                                                    <span v-if="item.representative_id">
                                                                        <img :src="item.representative.image_path"
                                                                            alt="product-image" style="
                                                                            width: 50px;
                                                                            height: 50px;
                                                                            border-radius: 50%;
                                                                        " />
                                                                        {{ item.representative.name }}</span>
                                                                    <span v-else>-</span>
                                                                </div>
                                                            </div>
                                                            <!--End representative-->

                                                        </div>
                                                    </div>
                                                </div>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-success me-2" @click="clicked_on_order = item" data-bs-toggle="modal"
                                                        data-bs-target="#AddNewNote">
                                                        <i class="fa fa-book"></i> {{ $t('global.Add Note') }}
                                                    </a>

                                                    <a href="javascript:void(0);" class="btn btn-sm btn-warning me-2 text-white" @click="clicked_on_order = item" data-bs-toggle="modal"
                                                        data-bs-target="#ReadNotes">
                                                        <i class="fa fa-book"></i> {{ $t('global.Read Notes') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-else>
                                            <th class="text-center" colspan="9">
                                                {{ $t("treasury.NoDataFound") }}
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table -->

            <!-- Edit Modal -->
            <div class="modal fade custom-modal" id="take_action" v-if="can_edit_order">
                <div class="modal-dialog modal-dialog-centered " style="max-width:600px!important">
                    <div class="modal-content">
                        <!-- Modal Header -->

                        <div class="modal-header d-block text-center">
                            <h4 class="modal-title">
                                {{ $t("global.Take Action") }}
                            </h4>
                            <br>
                            ( <span class="text-danger"> {{ $t('global.Note* : You cant take any action on the completed orders') }}</span> )

                            <button id="close-take_action" type="button" class="close print-button" data-bs-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body row">
                            <div class="card bg-white projects-card">
                                <div class="card-body pt-0">
                                    <form @submit.prevent="take_action(0)">

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" v-if="all_representatives.length">
                                                        <label>{{ $t("global.representative") }}</label>
                                                        <!--Start representative-->
                                                        <div class="w-100" >
                                                            <select class="form-control" v-model="representative_id">
                                                                <option value="">{{ $t('global.Without Option') }}</option>
                                                                <option :value="rep.id" v-for="rep in all_representatives" :key="rep.id">{{ rep.name }}</option>

                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12" v-if="cars_data.length">
                                                    <div class="form-group">
                                                        <label>{{ $t("global.Car") }}</label>
                                                        <div class="w-100" >
                                                            <select class="form-control" v-model="car_id">
                                                                <option value="">{{ $t('global.Without Option') }}</option>
                                                                <option :value="car.id" v-for="car in cars_data" :key="car.id">{{ car.name }}</option>

                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ $t("global.Order Status") }}</label>
                                                        <select class="form-control" v-model="order_status">
                                                            <option value="">{{ $t('global.Without Option') }}</option>
                                                            <option value="Pending">{{ $t("global.Pending") }}</option>
                                                            <option value="Processing">{{ $t("global.Processing") }}</option>
                                                            <option value="Shipping">{{ $t("global.ShippingNow") }}</option>
                                                            <option value="Completed">{{ $t("global.Completed") }}</option>
                                                            <option value="Canceled">{{ $t("global.Canceled") }}</option>
                                                            <option value="Full Return">{{ $t("global.Full Return") }}</option>
                                                            <option value="Partial Return">{{ $t("global.Partial Return") }}</option>
                                                            <option value="Hold">{{ $t("global.Hold") }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">
                                                {{ $t("global.Submit") }}
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                {{ $t("global.Close") }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Edit Modal -->


            <!-- Edit Modal -->
            <div class="modal fade custom-modal" id="AddNewNote">
                <div class="modal-dialog modal-dialog-centered " style="max-width:600px!important">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">
                                {{ $t("global.Add Note") }}
                            </h4>
                            <button id="close-AddNewNote" type="button" class="close print-button" data-bs-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body row">
                            <div class="card bg-white projects-card">
                                <div class="card-body pt-0">
                                    <form @submit.prevent="saveNoteToOrder(clicked_on_order.id,'index')">

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ $t("global.Note") }}</label>
                                                        <select v-model="note" class="form-control">
                                                            <option value=""></option>
                                                            <option value="The place is closed">{{ $t('global.The place is closed') }}</option>
                                                            <option value="The order is fake">{{ $t('global.The order is fake') }}</option>
                                                            <option value="There is not enough cash">{{ $t('global.There is not enough cash') }}</option>
                                                            <option value="The product is different from the description">{{ $t('global.The product is different from the description') }}</option>
                                                            <option value="The expiry date is invalid">{{ $t('global.The expiry date is invalid') }}</option>
                                                            <option value="The products were ordered by mistake from the representative">{{ $t('global.The products were ordered by mistake from the representative') }}</option>
                                                            <option value="The products were ordered by mistake from the customer">{{ $t('global.The products were ordered by mistake from the customer') }}</option>
                                                        </select>


                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">
                                                {{ $t("global.Submit") }}
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                {{ $t("global.Close") }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Edit Modal -->

            <!-- Edit Modal -->
            <div class="modal fade custom-modal" id="ReadNotes">
                <div class="modal-dialog modal-dialog-centered modal-xl" >
                    <div class="modal-content ">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">
                                {{ $t("global.Read Notes") }}
                            </h4>
                            <button id="close-ReadNotes" type="button" class="close print-button" data-bs-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body row">
                            <div class="card bg-white projects-card">
                                <div class="card-body pt-0">
                                    <div class="table-responsive" >
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>{{ $t("global.User Name") }}</th>
                                                    <th>{{ $t("global.User Type") }}</th>
                                                    <th>{{ $t("global.Note") }}</th>
                                                    <th>{{ $t("global.Date") }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template v-if="Object.keys(clicked_on_order.notes ?? {}).length">
                                                    <tr v-for="item in clicked_on_order.notes" :key="item.id" >
                                                        <td>{{ item.user_name }}</td>
                                                        <td>{{ item.type == 'customer_service' ? $t('global.Employee'): $t('global.Represntative')}}</td>
                                                        <td>{{ $t('global.'+item.note) }}</td>
                                                        <td>{{ item.created_at }}</td>
                                                    </tr>
                                                </template>
                                                <tr v-else>
                                                    <th class="text-center" colspan="4">
                                                        {{ $t("treasury.NoDataFound") }}
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Edit Modal -->
            
            <!-- start Pagination -->
            <Pagination :limit="2" :data="orders" @pagination-change-page="getOrders">
                <template #prev-nav>
                    <span>&lt; Previous</span>
                </template>
                <template #next-nav>
                    <span>Next &gt;</span>
                </template>
            </Pagination>
            <!-- end Pagination -->
        </div>
        <!-- /Page Wrapper -->
    </div>
</template>

<script>
import { onMounted, computed,ref } from "vue";
import { ordersComposable } from "./composables/order";
import {useStore} from "vuex";

export default {
    name: "index",
    setup() {
        let store = useStore();
        let permission = computed(() => store.getters['authAdmin/permission']);
        let can_edit_order = ref(false);

        const {
            goToOrderDetails,
            orders,
            search,
            paginate,
            total_amount,
            saveNoteToOrder,
            all_orders_for_excel,
            filter_confirmed_status,
            delivery_date,
            search2,
            fromDate,
            clicked_on_order,
            toDate,
            search3,
            search4,
            note,
            confirm_status,
            cars,
            search5,
            select_all_orders,
            select_all_orders_method,
            selected_orders,
            all_representatives,
            getAllAreasForFilterOrders,
            getAllSuppliersForFilterOrders,
            getAllRepresentativesForFilterOrders,
            all_areas,
            all_suppliers,
            car_id,
            representative_id,
            order_status,
            loading,
            getOrders,
            assignRepresentativeToOrder,
            getAllCars,
            assignCarToOrder,
            filter_order_status,
            filter_payment_method,
            car_search,
            filter_payment_status,
            order_area_id,
            getRepresentatives,
            take_action,
            getCars,
            representatives,
            downloadExcelSheet,
            cars_data,
            rep_search,
            loading2,
        } = ordersComposable();

        onMounted(() => {
            getOrders();
            getAllSuppliersForFilterOrders();
            getAllAreasForFilterOrders();
            getAllRepresentativesForFilterOrders();
            getCars();
            getAllCars();
            can_edit_order.value = permission.value.includes('orderOnline edit')
        });

        return {
            cars_data,
            can_edit_order,
            take_action,
            downloadExcelSheet,
            getOrders,
            loading,
            saveNoteToOrder,
            assignCarToOrder,
            cars,
            search,
            paginate,
            confirm_status,
            clicked_on_order,
            search2,
            fromDate,
            filter_confirmed_status,
            delivery_date,
            car_id,
            representative_id,
            order_status,
            toDate,
            search3,
            select_all_orders,
            select_all_orders_method,
            selected_orders,
            note,
            search4,
            search5,
            all_representatives,
            all_areas,
            all_suppliers,
            orders,
            assignRepresentativeToOrder,
            filter_order_status,
            filter_payment_method,
            filter_payment_status,
            order_area_id,
            car_search,
            getRepresentatives,
            representatives,
            total_amount,
            goToOrderDetails,
            loading2,
            rep_search
        };
    },
};
</script>

<style scoped>
.cursor {
    cursor: pointer;
}

.card {
    position: relative;
}



.custom {
    border: 1px solid #d7d7d7;
    padding: 2px;
    border-radius: 5px;
    width: 35%;
}


.btn {
    color: #fff;
}
</style>
