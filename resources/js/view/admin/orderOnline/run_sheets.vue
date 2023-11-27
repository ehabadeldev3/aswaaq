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
                        <h3 class="page-title">{{ $t("global.Run Sheets") }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard', params: { lang: 'ar' } }">
                                    {{ $t("dashboard.Dashboard") }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t("global.Run Sheets") }}</li>

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
                                        <div class="form-group row col-md-12">
                                            <label>{{ $t("global.Date") }}</label>
                                            <input type="date" class="form-control date-input" v-model="date"
                                                @change="get_run_sheets" />
                                        </div>
                                       
                                    </div>

                                </div>
                            </div>
                            

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ $t("global.Run Sheet Number") }}</th>
                                            <th>{{ $t("global.Number of Orders") }}</th>
                                            <th>{{ $t("global.Date") }}</th>
                                            <th>{{ $t("global.Action") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <template v-if="sheets && Object.keys(sheets.data ?? []).length">
                                        <tr v-for="(item, index) in sheets.data" :key="index">

                                        
                                            <td>{{ item.id }}</td>
                                            <td>{{ item.orders_count }}</td>
                                            <td>{{ item.created_at }}</td>
                                        
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-info me-2"
                                                    data-bs-toggle="modal"
                                                    @click="showSheetDetails = item"
                                                    data-bs-target="#showSheetDetails">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>

                                    </template>
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
            <Pagination :limit="5" :data="sheets" @pagination-change-page="get_run_sheets">
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
        <div class="modal fade custom-modal" id="showSheetDetails">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" id="print">
                    <!-- Modal Header -->
                    <div class="modal-header d-flex flex-row justify-content-center ">
                        
                        <button id="close-showSheetDetails" type="button" class="close print-button" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>

                        <button @click.prevent="printExpense"
                            class="btn btn-success print-button  w-25 h-50 mt-4">
                            {{ $t("global.Print") }}
                            <i class="fa fa-print"></i>
                        </button>
                       
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body row">
                        

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
                                        <th>{{ $t("global.Unit Price") }}</th>
                                        <th>{{ $t("global.deficit in quantity") }}</th>
                                        <th>{{ $t("global.Action") }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="Object.keys(showSheetDetails ?? []).length">
                                        <tr 
                                        v-for="(item, index) in showSheetDetails.products" :key="index">
                                        <td>
                                            <img :src="item.product.image_path"
                                                :alt="item.name" class="custom-img"
                                                style="width:auto;height: 50px;" />
                                        </td>
                                        <td style="white-space:normal">{{ item.product.name }}</td>
                                        <td style="white-space:normal">{{ item.product.flavor_name }}</td>
                                        <td style="white-space:normal">{{ item.product.size_name }}</td>
                                        <td style="white-space:normal">{{ item.measurement_unit.name }}</td>
                                        <td>{{ item.quantity }}</td>
                                        <td>{{ item.unit_price }}</td>
                                        <td>{{ item.deficit }}</td>
                                        <td>
                                            <a v-if="permission.includes('run sheet edit')"
                                                href="javascript:void(0);" class="btn btn-sm btn-success me-2"
                                                data-bs-toggle="modal"
                                                @click="editDeficitInSheetItem(item.id, item.product.name , item.deficit)"
                                                data-bs-target="#editDeficitInSheetItem">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            
                                        </td>
                                    </tr>

                                    </template>
                                    

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
        <!-- /Edit Modal -->
        <!-- Edit Modal -->

        <div class="modal fade custom-modal" id="editDeficitInSheetItem">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" id="print">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">
                            {{ product_name }}
                        </h4>
                        <button id="close-editDeficitInSheetItem" type="button" class="close print-button" data-bs-dismiss="modal">
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
                                            <th> <button @click="updateDeficitForSheetItem" class="btn btn-info">
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
        let showSheetDetails = ref({});
        let sheets = ref([]);
        let errors = ref({});
        let date = ref(new Date().toJSON().slice(0, 10));
        let loading = ref(false);
        let run_sheet_product_id = ref("");
        let edit_deficit = ref("");
        let product_name = ref("");

        let get_run_sheets = (page = 1) => {
            loading.value = true;

            adminApi
                .get(
                    `/v1/dashboard/get_run_sheets?page=${page}&date=${date.value}`
                )
                .then((res) => {
                    sheets.value = res.data.data.sheets;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                });
        };

        let printExpense = () => {
            $("#printExpense").printThis({});
        }

        onMounted(() => {
            get_run_sheets();
        })

        

        let dateFormat = (item) => {
            return new Date(item).toDateString();
        };

        let editDeficitInSheetItem = (log_id, productName, deficit) => {
            run_sheet_product_id.value = log_id
            edit_deficit.value = deficit
            product_name.value = productName
        };

        let updateDeficitForSheetItem = () => {
            loading.value = true;
            errors.value = {}
            adminApi
                .post(`/v1/dashboard/updateDeficitForSheetItem`, { run_sheet_product_id: run_sheet_product_id.value, deficit: edit_deficit.value })
                .then((res) => {
                    get_run_sheets()
                    $('#close-editDeficitInSheetItem').click()
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
            sheets,
            printExpense,
            date,
            showSheetDetails,
            loading,
            get_run_sheets,
            editDeficitInSheetItem,
            dateFormat,
            updateDeficitForSheetItem,
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
