<template>
    <div :class="[
        'page-wrapper',
        this.$i18n.locale == 'ar' ? 'page-wrapper-ar' : '',
    ]">
        <notifications :position="this.$i18n.locale == 'ar' ? 'top left' : 'top right'" />

        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t("global.CollectOrdersPerDay") }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard', params: { lang: 'ar' } }">
                                    {{ $t("dashboard.Dashboard") }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t("global.CollectOrdersPerDay") }}</li>

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
                                <div class="row justify-content-between">
                                    <div class="col-12 row justify-content-end">
                                        <div class="form-group row col-md-6">
                                            <label>{{ $t("global.Date") }}</label>
                                            <input type="date" class="form-control date-input" v-model="date"
                                                @change="collect_order_by_date" />
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-end">
                                            <button @click.prevent="printExpense"
                                                class="btn btn-success print-button  w-25 h-50 mt-4">
                                                {{ $t("global.Print") }}
                                                <i class="fa fa-print"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- <div class="col-12 row mt-3">
                                        <div class="col-6">
                                            {{ $t("global.Search") }}:
                                            <input type="search" v-model="search" class="custom"/>
                                        </div>

                                    </div> -->
                                </div>
                            </div>
                            <div class="table-responsive" id="printExpense">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ $t("global.Image") }}</th>
                                            <th>{{ $t("global.Product") }}</th>
                                            <th>{{ $t("global.Flavor") }}</th>
                                            <th>{{ $t("global.Size") }}</th>
                                            <th>{{ $t("global.Measurement Unit") }}</th>
                                            <th>{{ $t("global.Quantity") }}</th>
                                            <th>{{ $t("global.deficit in quantity") }}</th>
                                            <th>{{ $t("global.Date") }}</th>
                                            <th>{{ $t("global.Action") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr v-if="Object.keys(products.data ?? []).length"
                                            v-for="(item, index) in products.data" :key="index">

                                            <td>
                                                <img :src="item[0].image ? '/upload/' + item[0].image : '/admin/img/Logo Dashboard.png'"
                                                    :alt="item[0].name" class="custom-img"
                                                    style="width:auto;height: 50px;" />
                                            </td>
                                            <td style="white-space:normal">{{ this.$i18n.locale == 'ar' ?
                                                item[0].product_ar : item[0].product_en }}</td>
                                            <td style="white-space:normal">{{ this.$i18n.locale == 'ar' ?
                                                item[0].flavor_ar : item[0].flavor_en }}</td>
                                            <td style="white-space:normal">{{ this.$i18n.locale == 'ar' ?
                                                item[0].size_ar : item[0].size_en }}</td>
                                            <td style="white-space:normal">{{ this.$i18n.locale == 'ar' ?
                                                item[0].measurement_ar : item[0].measurement_en }}</td>
                                            <td>{{ item[0].total_quantity }}</td>
                                            <td>{{ item[0].deficit }}</td>
                                            <td>{{ item[0].date }}</td>
                                            <td>
                                                <a v-if="permission.includes('CollectOrdersPerDay edit')"
                                                    href="javascript:void(0);" class="btn btn-sm btn-success me-2"
                                                    data-bs-toggle="modal"
                                                    @click="editDeficit(item[0].log_id, this.$i18n.locale == 'ar' ? item[0].product_ar : item[0].product_en, item[0].deficit)"
                                                    data-bs-target="#editDeficit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-info me-2"
                                                    data-bs-toggle="modal"
                                                    @click="setProductDetaisl(item, this.$i18n.locale == 'ar' ? item[0].product_ar : item[0].product_en)"
                                                    data-bs-target="#showOrders">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>


                                        <tr v-else>
                                            <th class="text-center" colspan="10">
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
            <!-- start Pagination -->
            <Pagination :limit="2" :data="products" @pagination-change-page="collect_order_by_date">
                <template #prev-nav>
                    <span>&lt; {{ $t("global.Previous") }}</span>
                </template>
                <template #next-nav>
                    <span>{{ $t("global.Next") }} &gt;</span>
                </template>
            </Pagination>
            <!-- end Pagination -->
        </div>
        <!-- /Page Wrapper -->
        <!-- Edit Modal -->
        <div class="modal fade custom-modal" id="showOrders">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" id="print">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">
                            {{ product_name }}
                        </h4>
                        <button id="close-showOrders" type="button" class="close print-button" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body row">
                        <div class="card bg-white projects-card">
                            <div class="card-body pt-0">
                                <table class="table table-center table-hover mb-0 datatable">
                                    <thead>
                                        <tr>
                                            <th>{{ $t("global.Order Number") }}</th>
                                            <th>{{ $t("global.Order status") }}</th>
                                            <th>{{ $t("global.Supplier Name") }}</th>
                                            <!-- <th>{{ $t("global.State / Area") }}</th> -->
                                            <th>{{ $t("global.Quantity") }}</th>
                                            <th>{{ $t("global.Unit Price") }}</th>
                                            <th>{{ $t("global.Total Amount") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in showOrders" :key="item.order_id">
                                            <td>{{ item.order_id }}</td>
                                            <td>{{ $t('global.' + item.order_status) }}</td>
                                            <td>
                                                <span :class="[item.is_our_supplier ? 'badge badge-primary' : '']">
                                                     {{ item.supplier_name }}
                                                </span>
                                            </td>
                                            <!-- <td>{{ item.area_name }}</td> -->
                                            <td>{{ item.quantity }}</td>
                                            <td>{{ item.price }}</td>
                                            <td>{{ item.sub_total }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Edit Modal -->
        <!-- Edit Modal -->

        <div class="modal fade custom-modal" id="editDeficit">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" id="print">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">
                            {{ product_name }}
                        </h4>
                        <button id="close-editDeficit" type="button" class="close print-button" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body row">
                        <div class="card bg-white projects-card">
                            <div class="card-body pt-0">
                                <table class="table table-center table-hover mb-0 datatable">
                                    <thead>
                                        <tr>
                                            <th>{{ $t("global.deficit in quantity") }}</th>
                                            <th> <input type="text" class="form-control" v-model="edit_deficit"> </th>
                                            <th> <button @click="updateDeficitForProduct" class="btn btn-info">
                                                    {{ $t('global.Submit') }}
                                                </button> </th>
                                        </tr>
                                    </thead>

                                    <tbody v-if="errors">
                                        <tr>
                                            <td v-if="errors['log_id']" class="text-danger">{{ errors['log_id'][0] }}</td>
                                            <td v-if="errors['deficit']" class="text-danger">{{ errors['deficit'][0] }}</td>
                                        </tr>
                                    </tbody>

                                </table>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Edit Modal -->
    </div>
</template>

<script>
import { onMounted, inject, watch, ref, computed } from "vue";
import { useStore } from "vuex";
import adminApi from "../../../api/adminAxios";
import { useI18n } from "vue-i18n";

export default {
    name: "index",
    setup() {
        let store = useStore();
        let permission = computed(() => store.getters['authAdmin/permission']);

        // get packages
        let showOrders = ref({});
        let products = ref([]);
        let errors = ref({});
        let date = ref(new Date().toJSON().slice(0, 10));
        let loading = ref(false);
        let edit_log_id = ref("");
        let edit_deficit = ref("");
        let product_name = ref("");
        const search = ref("");

        let collect_order_by_date = (page = 1) => {
            loading.value = true;

            adminApi
                .get(
                    `/v1/dashboard/collect_order_by_date?page=${page}&search=${search.value}&date=${date.value}`
                )
                .then((res) => {
                    products.value = res.data.products;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                });
        };

        watch(search, (search, prevSearch) => {
            if (search.length >= 0) {
                collect_order_by_date();
            }
        });

        let printExpense = () => {
            $("#printExpense").printThis({});
        }

        onMounted(() => {
            collect_order_by_date();
        })

        // const sumOrders = (orders,commission_percentage) => {
        //     let sum = 0 ;
        //     orders.forEach(element => {
        //         sum += element.pivot.sub_total - (element.pivot.sub_total*element.commission_percentage/100)
        //     });
        //     return  Math.round(sum * 100) / 100;
        // }

        let dateFormat = (item) => {
            return new Date(item).toDateString();
        };

        let setProductDetaisl = (item, productName) => {
            product_name.value = productName
            showOrders.value = item;
        };

        let editDeficit = (log_id, productName, deficit) => {
            edit_log_id.value = log_id
            edit_deficit.value = deficit
            product_name.value = productName
        };

        let updateDeficitForProduct = () => {
            loading.value = true;
            errors.value = {}
            adminApi
                .post(`/v1/dashboard/updateDeficitForProduct`, { log_id: edit_log_id.value, deficit: edit_deficit.value })
                .then((res) => {
                    collect_order_by_date()
                    $('#close-editDeficit').click()
                })
                .catch((err) => {
                    console.log(err.response.data);
                    errors.value = err.response.data.errors
                })
                .finally(() => {
                    loading.value = false;
                });
        };

        return {
            products,
            printExpense,
            date,
            showOrders,
            loading,
            collect_order_by_date,
            setProductDetaisl,
            editDeficit,
            dateFormat,
            search,
            updateDeficitForProduct,
            edit_deficit,
            errors,
            product_name,
            permission
        };
    },
};
</script>

<style scoped>
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

.hover:hover {
    border: 2px solid;
    padding: 3px;
    border-radius: 7px;
}

.amount {
    background-color: #fcb00c;
    color: #000;
    padding: 10px;
}

.amount h5 {
    font-weight: 700;
    text-align: center;
}

.submit-margin {
    margin-top: 38px !important;
}

.date-input {
    width: 50% !important;
    display: inline-block !important;
    margin: 0px 8px 0 8px !important;
}

.select-input {
    width: 235px !important;
    display: inline-block !important;
    margin: 0px 8px 0 8px !important;
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

.custom-modal .close span {
    top: 0 !important;
}

.modal-dialog {
    max-width: 1000px !important;
}
</style>
