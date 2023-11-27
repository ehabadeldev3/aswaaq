<template>
    <div :class="[
        'page-wrapper',
        this.$i18n.locale == 'ar' ? 'page-wrapper-ar' : '',
    ]">
        <div class="content container-fluid">
            <notifications :position="this.$i18n.locale == 'ar' ? 'top left' : 'top right'" />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('global.Order details') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard', params: { lang: locale || 'ar' } }">
                                    {{ $t('dashboard.Dashboard') }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'indexOrderOnline', params: { lang: locale || 'ar' } }">
                                    {{ $t('global.Orders') }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t('global.Order details') }}</li>
                        </ul>
                    </div>

                </div>
            </div>
            <!-- /Page Header -->
            <loader v-if="loading" />

            <div class="row" v-if="Object.keys(order ?? []).length">
                <div class="col-lg-8 col-sm-12">
                    <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner" style="height: 442px; width: 100%">
                            <div :class="['carousel-item', key == 0 ? 'active' : '']" :style="[
                                this.$i18n.locale == 'ar' ? 'float:left!important' : '',
                                'margin-left: unset!important;',
                            ]" v-for="(product, key) in products" :key="product.id">
                                <img class="d-block w-100" style="height: 442px; width: 100%"
                                    :src="product.product.image_path ?? '/admin/img/Logo Dashboard.png'"
                                    alt="First slide" />
                            </div>
                        </div>
                        <a :class="
                            this.$i18n.locale == 'ar'
                                ? 'carousel-control-next'
                                : 'carousel-control-prev'
                        " :style="this.$i18n.locale == 'ar' ? 'right:unset!important' : ''" href="#carouselExampleSlidesOnly"
                            role="button" data-slide="prev">
                            <span :class="
                                this.$i18n.locale == 'ar'
                                    ? 'carousel-control-next-icon'
                                    : 'carousel-control-prev-icon'
                            " aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a  :class="
                            this.$i18n.locale == 'ar'
                                ? 'carousel-control-prev'
                                : 'carousel-control-next'
                        " href="#carouselExampleSlidesOnly" role="button" data-slide="next">
                            <span :class="
                                this.$i18n.locale == 'ar'
                                    ? 'carousel-control-prev-icon'
                                    : 'carousel-control-next-icon'
                            " aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    <div id="printDiv">

                        <div class="card" v-if="order.representative_id">
                            <div class="card-header">
                                <h5 class="card-title px-2">
                                    {{ $t("global.Representative Information") }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <h5 class="w-auto">{{ $t("global.Representative Name") }}</h5>
                                    <span class="w-auto" style="color: #fcb00c">{{
                                        order.representative.name
                                    }}</span>
                                </div>
                                <div class="row justify-content-between">
                                    <h5 class="w-auto">{{ $t("global.Representative Phone") }}</h5>
                                    <span class="w-auto" style="color: #fcb00c">{{
                                        order.representative.phone
                                    }}</span>
                                </div>

                                <div class="row justify-content-between">
                                    <h5 class="w-auto">{{ $t("global.Representative Email") }}</h5>
                                    <span class="w-auto" style="color: #fcb00c">{{
                                        order.representative.email
                                    }}</span>
                                </div>

                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title px-2">
                                    {{ $t("global.Receiver Information") }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Receiver Name") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.user.name
                                    }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Receiver Phone") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.user.phone
                                    }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">
                                        {{ $t("global.State / Area") }}
                                    </h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.area_name
                                    }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Receiver Address") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.shop.address
                                    }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Date") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.created_at
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title px-2">
                                    {{ $t("global.Order Information") }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Order Number") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.id
                                    }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Run Sheet Number") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.run_sheet ? order.run_sheet.run_sheet_id : '' 
                                    }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Delivery Date") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.delivery_date
                                    }}</span>
                                </div>
                                <div class="w-100" v-if="order.group.invoice_id">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Invoice id") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.group.invoice_id
                                    }}</span>
                                </div>
                                <div class="w-100" v-if="order.coupon">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Coupon") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.coupon
                                    }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Order Status") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c" v-if="order.hold == 0">
                                        <i class="far fa-pause-circle" v-if="order.order_status == 'Pending'"></i>
                                        <i class="text-info fas fa-truck" v-if="order.order_status == 'Shipping'"></i>
                                        <i class="text-success fas fa-check-circle"
                                            v-if="order.order_status == 'Completed' || order.order_status == 'Partial Return'"></i>
                                        <i class="text-info fa fa-cogs" v-if="order.order_status == 'Processing'"></i>
                                        <i class="fas fa-reply text-danger" v-if="order.order_status == 'Full Return'"></i>
                                        <i class="fa fa-times-circle text-danger"
                                            v-if="order.order_status == 'Canceled'"></i>
                                        {{ order.order_status }}</span>
                                        <span v-else><i class="text-danger far fa-pause-circle"></i>
                                                        {{ $t("global.Hold") }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Payment Status") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">
                                        <i class="text-success fas fa-check-circle"
                                            v-if="order.payment_status == 'Paid'"></i>
                                        <i class="fa fa-times-circle text-danger"
                                            v-if="order.payment_status == 'Failed'"></i>
                                        <i class="text-dark far fa-pause-circle"
                                            v-if="order.payment_status == 'Unpaid'"></i>
                                        {{ $t("global." + order.payment_status) }}</span>
                                </div>
                                <div class="w-100">
                                    <h5 class="w-50 d-inline-block">{{ $t("global.Payment Way") }}</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        $t("global." + order.payment_method)
                                    }}</span>
                                </div>
                                <div class="w-100" v-if="order.payment_method == 'Online' && order.transaction_id != 0">
                                    <h5 class="w-50 d-inline-block">Transaction id</h5>
                                    <span class="float-right w-auto" style="color: #fcb00c">{{
                                        order.transaction_id
                                    }}</span>
                                </div>
                            </div>
                        </div>


                        <div class="d-flex">
                            <div class="card w-100">
                                <div class="card-body pt-0">
                                    <div class="card-header mb-4">
                                        <h5 class="card-title">{{ $t("global.Order details") }}</h5>
                                    </div>

                                    <!-- {{-- items details --}}                     style="max-height: 1000px; overflow-y: scroll" -->
                                    <div class="card-box">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-secondary mb-4">
                                                <tbody>
                                                    <tr>
                                                        <th>{{ $t("global.Product") }}</th>
                                                        <th>{{ $t("global.Flavor") }}</th>
                                                        <th>{{ $t("global.Size") }}</th>
                                                        <th>{{ $t("global.Quantity") }}</th>
                                                        <th>{{ $t("global.Returned Quantity") }}</th>
                                                        <th>{{ $t("global.Measurement Unit Price") }}</th>
                                                        <th>{{ $t("global.Measurement Unit") }}</th>
                                                        <th>{{ $t("global.Received Amount") }}</th>
                                                        <th>{{ $t("global.Refund Amount") }}</th>
                                                        <th v-if="order.order_status == 'Partial Return' && can_edit_order && !order.confirmed_received_amount">{{ $t("global.Action") }}</th>
                                                    </tr>
                                                    <tr v-for="price in products" :key="price.id">
                                                        <td>{{ price.product.name}}</td>
                                                        <td>{{ price.product.flavor_name }}</td>
                                                        <td>{{ price.product.size.name }}</td>
                                                        <td>{{ price.pivot.quantity }}</td>
                                                        <td>{{ price.pivot.returned_quantity }}</td>
                                                        <td>{{ price.pivot.price }}</td>
                                                        <td>{{ price.pivot.measurement_type == 'sub' ?  price.product.sub_measure_name :price.product.main_measure_name }}</td>
                                                        <td>{{ price.pivot.sub_total }}</td>
                                                        <td>{{ price.pivot.refund_amount }}</td>
                                                        <td v-if="order.order_status == 'Partial Return' && can_edit_order && !order.confirmed_received_amount" >


                                                            <a href="javascript:void(0);" class="btn btn-sm btn-success me-2"
                                                            data-bs-toggle="modal"
                                                                data-bs-target="#update_item" @click="setSelectedItemForUpdate(price)">
                                                                <i class="far fa-edit"></i>
                                                            </a>

                                                            <button v-if="price.pivot.quantity != 0" @click.prevent="deleteItemCard(price.id, order.id,price.pivot.measurement_id)"
                                                                class="btn btn-sm btn-danger me-2">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <hr style="" />
                                            <br />
                                            <table class="table table-bordered mb-4">
                                                <tbody class="table-secondary">
                                                    <tr>
                                                        <th v-if="order.coupon_discount">{{ $t("global.Coupon Discount") }}
                                                        </th>
                                                        <th v-if="order.used_points">{{ $t("global.Used Points") }}</th>
                                                        <th v-if="order.shipping_cost">{{ $t("global.Shipping cost") }}</th>
                                                        <th>{{ $t("global.Supplier") }}</th>
                                                        <th>{{ $t("global.TotalTax") }}</th>
                                                        <!-- <th>{{ $t("global.Supplier Commission") }}</th> -->
                                                        <th>{{ $t("global.Refund Amount") }}</th>
                                                        <th>{{ $t("global.Total Amount") }}</th>
                                                    </tr>
                                                    <tr>
                                                        <!-- <td>{{ order.taxes }} {{ $t("global.LE") }}</td> -->
                                                        <td v-if="order.coupon_discount">{{ order.coupon_discount }} {{ $t("global.LE") }}</td>
                                                        <td v-if="order.used_points">{{ order.used_points }} {{ $t("global.LE") }}</td>


                                                        <td v-if="order.shipping_cost">
                                                            {{ order.shipping_cost }} {{ $t("global.LE") }}
                                                        </td>

                                                        <td>
                                                            {{ order.supplier.name}}
                                                        </td>
                                                        <td>
                                                            {{ order.tax_amount}}
                                                        </td>
                                                        <td>
                                                            {{ order.refund_amount}}
                                                        </td>
                                                        <!-- <td>{{ order.subtotal }} {{ $t("global.LE") }}</td> -->
                                                        <td>
                                                            {{ order.total_amount }} {{ $t("global.LE") }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card" v-if="Object.keys(notes ?? []).length">
                            <div class="card-header">
                                <h5 class="card-title px-2">
                                    {{ $t("global.Notes") }}
                                </h5>
                            </div>
                            <div class="card-body">
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
                                        <tbody >
                                            <template v-if="Object.keys(order.notes ?? {}).length">
                                                <tr v-for="item in order.notes" :key="item.id">
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
                <div class="col-lg-4 col-sm-12">
                    <div class="text-center card-box">
                        <div class="text-left">
                            <h4 class="header-title mb-4">{{ $t("global.User") }}</h4>
                        </div>
                        <div class="member-card">
                            <div class="avatar-xl member-thumb mb-2 mx-auto d-block star-div">
                                <img :src="client.image_path ?? 'https://ui-avatars.com/api/?name=' +
                                    client.name +
                                    '&amp;color=7F9CF5&amp;background=EBF4FF'" :onerror="
                                    'https://ui-avatars.com/api/?name=' +
                                    client.name +
                                    '&amp;color=7F9CF5&amp;background=EBF4FF'
                                " class="rounded-circle img-thumbnail" alt="profile-image" />
                                <i class="fas fa-certificate text-primary small star-icon" title="Featured Agent"></i>
                            </div>

                            <div class="">
                                <h5 class="font-18 mb-1">{{ client.name }}</h5>
                            </div>

                            <div class="mt-20">
                                <ul class="list-inline row">
                                    <!-- <li class="list-inline-item col-12 mx-0">
                                        <h5>{{ $t("global.Email") }}</h5>
                                        <p>{{ client.email }}</p>
                                    </li> -->
                                    <li class="list-inline-item col-6 mx-0">
                                        <h5>{{ $t("global.Number of Orders") }}</h5>
                                        <p>{{ order_numbers }}</p>
                                    </li>

                                    <li class="list-inline-item col-6 mx-0">
                                        <h5>{{ $t("global.Phone") }}</h5>
                                        <p>{{ client.phone }}</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- end membar card -->
                    </div>
                    <div class="text-center card-box">
                        <div class="text-left">
                            <h4 class="header-title mb-4">{{ $t("global.Supplier") }}</h4>
                        </div>
                        <div class="member-card">
                            <div class="avatar-xl member-thumb mb-2 mx-auto d-block star-div">
                                <img :src="order.supplier.image_path ?? 'https://ui-avatars.com/api/?name=' +
                                    order.supplier.name +
                                    '&amp;color=7F9CF5&amp;background=EBF4FF'" :onerror="
                                    'https://ui-avatars.com/api/?name=' +
                                    order.supplier.name +
                                    '&amp;color=7F9CF5&amp;background=EBF4FF'
                                " class="rounded-circle img-thumbnail" alt="profile-image" />
                                <i class="fas fa-certificate text-primary small star-icon" title="Featured Agent"></i>
                            </div>

                            <div class="">
                                <h5 class="font-18 mb-1">{{ order.supplier.name }}</h5>
                            </div>

                            <div class="mt-20">
                                <ul class="list-inline row">
                                    <!-- <li class="list-inline-item col-12 mx-0">
                                        <h5>{{ $t("global.Email") }}</h5>
                                        <p>{{ order.supplier.email }}</p>
                                    </li> -->
                                    <li class="list-inline-item col-6 mx-0">
                                        <h5>{{ $t("global.Number of Orders") }}</h5>
                                        <p>{{ supplier_orders }}</p>
                                    </li>

                                    <li class="list-inline-item col-6 mx-0">
                                        <h5>{{ $t("global.Phone") }}</h5>
                                        <p>{{ order.supplier.phone }}</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- end membar card -->
                    </div>
                    <div class="text-center card-box">
                        <div class="text-left">
                            <h4 class="header-title mb-4 d-flex justify-content-center">
                                <span>{{ $t("global.Action") }}</span>

                            </h4>
                        </div>
                        <div class="member-card">

                            <span>
                                    <a class="btn btn-sm btn-secondary" @click.prevent="printPolica"><i
                                            class="fa fa-print"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-success me-2"
                                        v-if=" can_edit_order && !order.confirmed_received_amount" style="height:49px" @click.prevent="confirm_status(order.id)">
                                            <i class="fa fa-check"></i> {{ $t('global.Confirm Status') }}
                                        </a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-info me-2"
                                     data-bs-toggle="modal"
                                        data-bs-target="#take_action" v-if=" can_edit_order && (order.order_status != 'Completed' || order.order_status != 'Canceled' || order.order_status != 'Partial Return' || order.order_status != 'Full Return') && !order.confirmed_received_amount">
                                        <i class="fa fa-edit"></i> {{ $t('global.Take Action') }}
                                    </a>
                                    <div v-else>
                                        {{ $t("global.The actions finished") }}
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#AddNewNote">
                                        <i class="fa fa-book"></i> {{ $t('global.Add Note') }}
                                    </a>

                                    <a href="javascript:void(0);" class="btn btn-sm btn-warning me-2 text-white" data-bs-toggle="modal"
                                        data-bs-target="#ReadNotes">
                                        <i class="fa fa-book"></i> {{ $t('global.Read Notes') }}
                                    </a>
                                </span>

                        </div>
                        <!-- end membar card -->
                    </div>
                    <div class="text-center card-box" v-if="Object.keys(orders_group ?? {}).length">
                        <div class="text-left">
                            <h4 class="header-title mb-4 d-flex justify-content-center">
                                <span>{{ $t("global.Orders in same group") }}</span>

                            </h4>
                        </div>
                        <div class="member-card">
                            <div class="col-sm-12 py-4 d-flex flex-row justify-content-center">

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ $t("global.Order Number") }}</th>
                                            <th>{{ $t("global.Supplier Name") }}</th>
                                            <th>{{ $t("global.Delivery Date") }}</th>
                                            <th>{{ $t("global.Total Amount") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <tr v-for="(item, index) in orders_group" :key="item.id">
                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">{{ item.id }}</td>

                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">
                                                    <span
                                                        :class="[item.supplier.is_our_supplier ? 'badge badge-primary' : '']">{{
                                                            item.supplier.name
                                                        }}</span>

                                                </td>

                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">
                                                    {{
                                                        item.delivery_date
                                                    }}
                                                </td>

                                                <td class="cursor" @click.prevent="goToOrderDetails(item.id)">
                                                    {{
                                                        item.total_amount
                                                    }}
                                                </td>


                                            </tr>

                                    </tbody>
                                </table>
                            </div>

                            </div>

                        </div>
                        <!-- end membar card -->
                    </div>
                    <div class="text-center card-box" v-if="Object.keys(order.representative_refund_part_note ?? {}).length">
                        <div class="text-left">
                            <h4 class="header-title mb-4 d-flex justify-content-center">
                                <span>{{ $t("global.Representative Return") }}</span>

                            </h4>
                        </div>
                        <div class="member-card">
                            <div class="col-sm-12 py-4 d-flex flex-row justify-content-center">

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ $t("global.Product") }}</th>
                                            <th>{{ $t("global.Flavor") }}</th>
                                            <th>{{ $t("global.Size") }}</th>
                                            <th>{{ $t("global.Measurement Unit") }}</th>
                                            <th>{{ $t("global.Returned Quantity") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <tr v-for="(item, index) in order.representative_refund_part_note" :key="item.id">
                                                <td>{{this.$i18n.locale  == 'ar' ? item.product_ar : item.product_en}}</td>
                                                <td>{{this.$i18n.locale  == 'ar' ? item.flavor_ar : item.flavor_en}}</td>
                                                <td>{{this.$i18n.locale  == 'ar' ? item.size_ar : item.size_en}}</td>
                                                <td>{{this.$i18n.locale  == 'ar' ? item.measurement_ar : item.measurement_en}}</td>
                                                <td >{{ item.quantity }}</td>
                                            </tr>

                                    </tbody>
                                </table>
                            </div>

                            </div>

                        </div>
                        <!-- end membar card -->
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade custom-modal" id="take_action">
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
                            <form @submit.prevent="take_action(order.id)">

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group" v-if="representatives.length">
                                                <label>{{ $t("global.representative") }}</label>
                                                <!--Start representative-->
                                                <div class="w-100" v-if="order.order_status == 'Pending' || order.order_status == 'Shipping' || order.order_status == 'Processing'">
                                                    <select class="form-control" v-model="representative_id">
                                                        <option value="">{{ $t('global.Without Option') }}</option>
                                                        <option :value="rep.id" v-for="rep in representatives" :key="rep.id">{{ rep.name }}</option>

                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12" v-if="cars_data.length">
                                            <div class="form-group">
                                                <label>{{ $t("global.Car") }}</label>
                                                <div class="w-100" v-if="order.order_status == 'Pending' || order.order_status == 'Shipping' || order.order_status == 'Processing'">
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
    <div class="modal fade custom-modal" id="update_item" >
        <div class="modal-dialog modal-dialog-centered " style="max-width:600px!important">
            <div class="modal-content">
                <!-- Modal Header -->

                <div class="modal-header d-block text-center">
                    <h4 class="modal-title">
                        {{ $t("global.Update Order") }}
                    </h4>
                     <button id="close-update_item" type="button" class="close print-button" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="modal-body row">
                    <div class="card bg-white projects-card">
                        <div class="card-body pt-0">
                            <form @submit.prevent="updateOrderItem()" v-if="Object.keys(selected_item_for_update ??[] ).length && selected_item_for_update.product">

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group" >
                                                <label>{{ $t("global.Product") }}</label>
                                                <!--Start representative-->
                                                <div class="w-100" >
                                                    <input :value="selected_item_for_update.product" class="form-control" disabled/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6" >
                                            <div class="form-group">
                                                <label>{{ $t("global.Flavor") }}</label>
                                                <div class="w-100" >
                                                    <input :value="selected_item_for_update.flavor" class="form-control" disabled/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6" >
                                            <div class="form-group">
                                                <label>{{ $t("global.Size") }}</label>
                                                <div class="w-100" >
                                                    <input :value="selected_item_for_update.size" class="form-control" disabled/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6" >
                                            <div class="form-group">
                                                <label>{{ $t("global.Measurement Unit") }}</label>
                                                <div class="w-100" >
                                                    <input :value="selected_item_for_update.measurement" class="form-control" disabled/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ $t("global.Quantity") }}</label>
                                                <div class="w-100" >
                                                    <input class="form-control" v-model="selected_item_for_update.quantity"/>
                                                </div>
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
                            <form @submit.prevent="saveNoteToOrder(order.id)">

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
                                        <tr v-for="item in order.notes" :key="item.id">
                                            <td>{{ item.user_name }}</td>
                                            <td>{{ item.type == 'customer_service' ? $t('global.Employee'): $t('global.Represntative')}}</td>
                                            <td>{{ item.note }}</td>
                                            <td>{{ item.created_at }}</td>
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
</template>

<script>
import { onMounted, computed, ref, watch } from "vue";
import { ordersComposable } from "./composables/order";
import {useStore} from "vuex";
import { useRouter } from "vue-router";
import { useI18n } from 'vue-i18n';

export default {
    props: ["id"],
    setup(props) {
        let store = useStore();
        let router = useRouter();
        let {locale} = useI18n()

        let permission = computed(() => store.getters['authAdmin/permission']);
        let can_edit_order =ref(false);

        const {
            setSelectedItemForUpdate,
            getOrder,
            loading,
            selected_item_for_update,
            updateOrderItem,
            representatives_data,
            refund_allowed,
            products,
            supplier_orders,
            order_numbers,
            refund_date,
            order,
            client,
            rep_search,
            area,
            state,
            getRepresentatives,
            orders_group,
            representatives,
            representative_id,
            order_status,
            selected_orders,
            car_id,
            cars_data,
            confirm_status,
            order_area_id,
            setting,
            take_action,
            saveNoteToOrder,
            getAllCars,
            goToOrderDetails,
            notes,
            note,
            deleteItemCard,
        } = ordersComposable();

        watch(() => props.id, () => {
            getOrder(props.id)
        })

        onMounted(async () => {
            await getOrder(props.id)
            await getRepresentatives(order.value.area_id,false)
            await getAllCars()
            can_edit_order.value = permission.value.includes('orderOnline edit')
        });

        const printPolica = async () => {
            $("#printDiv").printThis({
                header:
                    `<div class='row'>
                    <div class='col-12 text-center'>
                        <img src='/admin/img/Logo Dashboard.png' onerror='logo' style='width:200px;height:150px;margin: 50px 0;' class='my-auto'> </div>
                </div>`,
                // footer: `<h3 class='text-center'>المرتجعات خلال اسبوع واصناف الثلاجة في نفس اليوم</h3>`
            });

            // <div class='col-6'>
            //             ${setting.value.email ? "<p class='w-100'> <span class='mx-5'>" + setting.value.email + "</span><b> :البريد الالكتروني</b>" : ''}</p>
            //             ${setting.value.phone ? "<p class='w-100'> <span class='mx-5'>" + setting.value.phone + "</span><b> :رقم الهاتف</b></p>" : ''}</p>
            //             ${setting.value.wats_app ? "<p class='w-100'> <span class='mx-5'>" + setting.value.wats_app + "</span><b> :واتساب</b></p>" : ''}</p>
            //             ${setting.value.address ? "<p class='w-100'> <span class='mx-5'>" + setting.value.address + "</span><b> :العنوان</b></p>" : ''}</p>
            //             ${setting.value.facebook ? "<p class='w-100'> <span class='mx-5'>" + setting.value.facebook + "</span><b> :فيسبوك</b></p>" : ''}</p>
            //             ${setting.value.linkedin ? "<p class='w-100'> <span class='mx-5'>" + setting.value.linkedin + "</span><b> :لينكدان</b></p>" : ''}</p>
            //             ${setting.value.youtube ? "<p class='w-100'> <span class='mx-5'>" + setting.value.youtube + "</span><b> :يوتيوب</b></p>" : ''}</p>
            //             ${setting.value.work_time ? "<p class='w-100'> <span class='mx-5'>" + setting.value.work_time + "</span><b> :مواعيد العمل</b></p>" : ''}</p>
            //         </div>
        }


        return {
            can_edit_order,
            products,
            order,
            loading,
            setSelectedItemForUpdate,
            selected_orders,
            client,
            representative_id,
            order_status,
            confirm_status,
            car_id,
            selected_item_for_update,
            updateOrderItem,
            rep_search,
            area,
            orders_group,
            getOrder,
            representatives_data,
            representatives,
            state,
            printPolica,
            goToOrderDetails,
            cars_data,
            supplier_orders,
            order_numbers,
            refund_date,
            refund_allowed,
            take_action,
            deleteItemCard,
            saveNoteToOrder,
            notes,
            note,
        };
    },
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<style scoped>
.card-box {
    background-color: #a1ccff;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 20px;
}.cursor {
    cursor: pointer;
}


.img-thumbnail {
    width: 80px;
    height: 80px;
}
.star-div {
    position: relative;
}

.star-icon {
    position: absolute;
    top: 4px;
    right: 2px;
    font-size: 16px;
    background-color: #f3f3f3;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    line-height: 20px;
    text-align: center;
}

.phase {
    display: inline-block;
    padding: 5px;
}
</style>
