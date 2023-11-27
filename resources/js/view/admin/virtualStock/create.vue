<template>
    <div :class="['page-wrapper', 'page-wrapper-ar']">

        <div class="content container-fluid">

            <notifications :position="'top left'" />
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t("global.Supplier Stock") }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'indexSupplier' }">
                                    {{ $t("global.Suppliers Stocks") }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ supplier.name }}
                            </li>
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
                            <div class="card-header pt-0 mb-4">
                                <div class="row justify-content-between">
                                    <div class="col-2">
                                        <router-link :to="{ name: 'indexSupplier' }"
                                            class="btn btn-custom btn-dark">
                                            {{ $t("global.back") }}
                                        </router-link>

                                    </div>

                                    <div class="col-8 row justify-content-end">
                                        <form id="mainFormVirualStocks">

                                            <div class="form-group">
                                                <table class="table">
                                                    <vue-excel-xlsx v-if="Object.keys(all_prices_for_excel ?? []).length" :class="'btn btn-custom btn-warning mx-2 py-2 text-white'"
                                                            @click.prevent :data="all_prices_for_excel" :columns="columns"
                                                            :file-name="'pricing'" :file-type="'xlsx'"
                                                            :sheet-name="'sheetname'">
                                                            <i class="fa fa-download" aria-hidden="true"></i> {{
                                                                $t('global.Download Excel') }}
                                                        </vue-excel-xlsx>
                                                    <!-- <a @click.prevent="downloadExcelSheet" class="btn btn-info"><i
                                                            class="fa fa-download"></i></a> -->

                                                    <label class="form-group">{{ $t('global.UploadExcelFile') }}
                                                        <input class="form-control" type="file"
                                                            name="select_virtualStocks_file">

                                                    </label>
                                                    <button class="btn btn-success" style="margin:5px" type="submit"
                                                        name="upload"
                                                        @click.prevent="saveExcelVirtualStock">{{
                                                            $t('global.Add')
                                                        }}</button>
                                                </table>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="message.length > 0">{{
                                        message
                                    }}<br /></div>

                                    <form @submit.prevent="storeVirtualStock" class="needs-validation">
                                        <div class="form-row row">

                                            <!--Start Sale Products-->
                                            <div class="col-md-12 mb-12">
                                                <div class="row account">
                                                    <div class="col-md-12 mb-12 head-account">
                                                        <h3>{{ $t('global.Products') }}</h3>
                                                    </div>
                                                    <div v-for="(item, index) in data.product" :key="item.id"
                                                        class="col-md-12 mb-12 body-account row">

                                                        <!--Start Category-->
                                                        <div class="col-md-3 mb-3">
                                                            <label>{{ $t('global.mainCategory') }}</label>
                                                            <select @change="getSubCategory(item.category_id, index)"
                                                                v-model="item.category_id"
                                                                :class="['form-select', { 'is-invalid': v$.product[index].category_id.$error, 'is-valid': !v$.product[index].category_id.$invalid }]">
                                                                <option v-for="category in categories"
                                                                    :key="category.id" :value="category.id">
                                                                    {{ category.name }}</option>
                                                            </select>
                                                            <div class="valid-feedback">{{ $t('global.LooksGood') }}
                                                            </div>
                                                            <div class="invalid-feedback">
                                                                <span
                                                                    v-if="v$.product[index].category_id.required.$invalid">{{
                                                                        $t('global.ThisFieldIsRequired')
                                                                    }}<br />
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <!--End Category-->

                                                        <!--Start Sub Categoy-->
                                                        <div class="col-md-3 mb-3">
                                                            <label>{{ $t('global.subCategory') }}</label>
                                                            <select
                                                                @change="getProduct(item.category_id, item.sub_category_id, index)"
                                                                v-model="item.sub_category_id"
                                                                :class="['form-select', { 'is-invalid': v$.product[index].sub_category_id.$error, 'is-valid': !v$.product[index].sub_category_id.$invalid }]">
                                                                <option
                                                                    v-for="category in subCategory[index].subCategory"
                                                                    :key="category.id" :value="category.id">
                                                                    {{ category.name }}</option>
                                                            </select>
                                                            <div class="valid-feedback">{{ $t('global.LooksGood') }}
                                                            </div>
                                                            <div class="invalid-feedback">
                                                                <span
                                                                    v-if="v$.product[index].sub_category_id.required.$invalid">{{
                                                                        $t('global.ThisFieldIsRequired')
                                                                    }}<br /></span>
                                                            </div>
                                                        </div>
                                                        <!--End Sub Category-->

                                                        <!--Start Product @change="getMeasurementUnit(item.product_id,index)"-->
                                                        <div class="col-md-3 mb-3 ">
                                                            <label>{{ $t('global.Products') }}</label>


                                                            <div class="dropdown">
                                                                <button class="btn btn-secondary dropdown-toggle w-100"
                                                                    type="button" id="dropdownMenuButton"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false" style="overflow: auto;">
                                                                    <template v-if="item.product_id" >
                                                                        <img :src="item.image ? '/upload/' + item.image : '/admin/img/Logo Dashboard.png'"
                                                                            alt="product-image" style="
                                                                                width: 50px;
                                                                                height: 50px;
                                                                                border-radius: 50%;
                                                                            " />
                                                                        <span style="word-break: break-word;">{{ item.name }}</span>
                                                                    </template>
                                                                    <span v-else>{{
                                                                        $t("global.Product")
                                                                    }}</span>
                                                                </button>
                                                                <div :class="[
                                                                    'dropdown-menu bg-white',
                                                                    this.$i18n.locale == 'en' ? 'drop_ltr' : '',
                                                                ]" style="
                                                                height: 400px;
                                                                    overflow-y: scroll;
                                                                    width: 650px;
                                                                    z-index: 999999;
                                                                    left:544px!important;
                                                                    background:#e0e9e2
                                                                " aria-labelledby="dropdownMenuButton">
                                                                    <input type="text"
                                                                        :placeholder="$t('global.Search')"
                                                                        v-model="altr_search" class="form-control"
                                                                        onchange="event.stopPropagation()" />
                                                                    <loader v-if="loading2" />

                                                                    <div v-for="product in products[index].products"
                                                                        :key="product.id" :class="[
                                                                            'dropdown-item px-2 d-flex justify-content-between',
                                                                            product.id == item.product_id
                                                                                ? 'bg-secondary'
                                                                                : '',
                                                                        ]" @click="item.product_id = product.id;item.name = product.name + ' / ' + (product.flavor_name ? product.flavor_name + ' / ' : '' )+ product.size.name;
                                                                        item.image = product.image;
                                                                        item.main_measure_name = product.main_measure_name;
                                                                        item.count_unit = product.count_unit;
                                                                        item.sub_measure_name = product.sub_measure_name;
                                                                        ">
                                                                        <img :src="product.image ? '/upload/' + product.image : '/admin/img/Logo Dashboard.png'"
                                                                            alt="product-image"
                                                                            style="width: 50px; height: 50px" />
                                                                        <span style="
                                                                            overflow: hidden;
                                                                            height: 34px;
                                                                            font-size: 24px;
                                                                            word-break: break-word;
                                                                        ">{{ product.name + ' / ' +  (product.flavor_name ? product.flavor_name + ' / ' : '' ) + product.size.name }}</span>
                                                                    </div>

                                                                    <h5 v-if="
                                                                        Object.keys(products[index].products ?? []).length ==
                                                                        0
                                                                    " class="text-center">
                                                                        {{ $t("global.No Data Found") }}
                                                                    </h5>
                                                                </div>

                                                            </div>
                                                            <div class="valid-feedback">{{ $t('global.LooksGood') }}
                                                            </div>
                                                            <div class="invalid-feedback">
                                                                <span
                                                                    v-if="v$.product[index].product_id.required.$invalid">{{
                                                                        $t('global.ThisFieldIsRequired')
                                                                    }}<br />
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <!--End Product-->

                                                        <!--Start Quantity-->
                                                        <div class="col-md-3 mb-3" v-if="item.product_id">
                                                            <label>{{ $t('global.Quantity') }} ( {{ item.sub_measure_name }} ) </label>
                                                            <input type="number" class="form-control"
                                                                v-model.number="v$.product[index].quantity.$model"
                                                                :placeholder="$t('global.Quantity')"
                                                                :class="{ 'is-invalid': v$.product[index].quantity.$error, 'is-valid': !v$.product[index].quantity.$invalid }">
                                                            <div class="valid-feedback">{{ $t('global.LooksGood') }}
                                                            </div>
                                                            <div class="invalid-feedback">
                                                                <span
                                                                    v-if="v$.product[index].quantity.required.$invalid">{{
                                                                        $t('global.ThisFieldIsRequired')
                                                                    }}<br />
                                                                </span>
                                                                <span
                                                                    v-if="v$.product[index].quantity.numeric.$invalid">{{
                                                                        $t('global.ThisFieldIsNumeric')
                                                                    }}
                                                                    <br /></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 mb-3" v-if="item.product_id">
                                                            <label>{{ $t('global.Quantity') }} ( {{ item.main_measure_name }} ) </label>
                                                            <input type="number" class="form-control"
                                                                :value="v$.product[index].quantity.$model? v$.product[index].quantity.$model / item.count_unit :0"
                                                                disabled>
                                                        </div>
                                                        <!--End Quantity-->

                                                        <!--Start main_measurement_price-->
                                                        <div class="col-md-3 mb-3" v-if="item.product_id">
                                                            <label>{{ $t('global.main measurement unit price') }} ( {{ item.main_measure_name }} )</label>
                                                            <input type="number" class="form-control" step="0.01"
                                                                v-model="v$.product[index].main_measurement_price.$model"
                                                                :class="{ 'is-invalid': v$.product[index].main_measurement_price.$error, 'is-valid': !v$.product[index].main_measurement_price.$invalid }">
                                                            <div class="valid-feedback">{{ $t('global.LooksGood') }}
                                                            </div>
                                                            <div class="invalid-feedback">
                                                                <span
                                                                    v-if="v$.product[index].main_measurement_price.required.$invalid">{{
                                                                        $t('global.ThisFieldIsRequired')
                                                                    }}<br />
                                                                </span>
                                                                <span
                                                                    v-if="v$.product[index].main_measurement_price.numeric.$invalid">{{
                                                                        $t('global.ThisFieldIsNumeric')
                                                                    }}
                                                                    <br /></span>
                                                            </div>
                                                        </div>
                                                        <!--End main_measurement_price-->

                                                        <!--Start main_measurement_price-->
                                                        <div class="col-md-3 mb-3" v-if="item.product_id">
                                                            <label>{{ $t('global.Main Measurement Unit Price After Sale') }} ( {{ item.main_measure_name }} )</label>
                                                            <input type="number" class="form-control" step="0.01"
                                                                v-model="v$.product[index].main_measurement_price_after_sale.$model"
                                                                :class="{ 'is-invalid': v$.product[index].main_measurement_price_after_sale.$error, 'is-valid': !v$.product[index].main_measurement_price_after_sale.$invalid }">
                                                            <div class="valid-feedback">{{ $t('global.LooksGood') }}
                                                            </div>
                                                            <div class="invalid-feedback">
                                                                
                                                                <span
                                                                    v-if="v$.product[index].main_measurement_price_after_sale.numeric.$invalid">{{
                                                                        $t('global.ThisFieldIsNumeric')
                                                                    }}
                                                                    <br /></span>
                                                            </div>
                                                        </div>
                                                        <!--End main_measurement_price-->

                                                        <!--Start sub_measurement_price-->
                                                        <div class="col-md-3 mb-3" v-if="item.product_id">
                                                            <label>{{ $t('global.sub measurement unit price') }} ( {{ item.sub_measure_name }} )</label>
                                                            <input type="number" class="form-control" step="0.01"
                                                                v-model="v$.product[index].sub_measurement_price.$model"
                                                                :class="{ 'is-invalid': v$.product[index].sub_measurement_price.$error, 'is-valid': !v$.product[index].sub_measurement_price.$invalid }">
                                                            <div class="valid-feedback">{{ $t('global.LooksGood') }}
                                                            </div>
                                                            <div class="invalid-feedback">

                                                                <span
                                                                    v-if="v$.product[index].sub_measurement_price.numeric.$invalid">{{
                                                                        $t('global.ThisFieldIsNumeric')
                                                                    }}
                                                                    <br /></span>
                                                            </div>
                                                        </div>
                                                        <!--End sub_measurement_price-->
                                                        <!--Start sub_measurement_price-->
                                                        <div class="col-md-3 mb-3" v-if="item.product_id">
                                                            <label>{{ $t('global.Sub Measurement Unit Price After Sale') }} ( {{ item.sub_measure_name }} )</label>
                                                            <input type="number" class="form-control" step="0.01"
                                                                v-model="v$.product[index].sub_measurement_price_after_sale.$model"
                                                                :class="{ 'is-invalid': v$.product[index].sub_measurement_price_after_sale.$error, 'is-valid': !v$.product[index].sub_measurement_price_after_sale.$invalid }">
                                                            <div class="valid-feedback">{{ $t('global.LooksGood') }}
                                                            </div>
                                                            <div class="invalid-feedback">

                                                                <span
                                                                    v-if="v$.product[index].sub_measurement_price_after_sale.numeric.$invalid">{{
                                                                        $t('global.ThisFieldIsNumeric')
                                                                    }}
                                                                    <br /></span>
                                                            </div>
                                                        </div>
                                                        <!--End sub_measurement_price-->

                                                        <!--Start Public Price-->
                                                        <div class="col-md-3 mb-3" v-for="(unit,key) in item.units" :key="unit.id">
                                                            <label>{{ unit.name }}</label>
                                                            <input type="text" class="form-control"
                                                                v-model.number="data.product[index]['units'][key].price"
                                                                :placeholder="$t('global.Price')">
                                                        </div>
                                                        <!--End Public Price-->

                                                        <div class="col-md-3 mb-3">
                                                            <button @click.prevent="addDebit"
                                                                v-if="(data.product.length - 1) == index"
                                                                class="btn btn-sm btn-success me-2 mt-5">
                                                                <i class="fas fa-clipboard-list"></i>
                                                                {{ $t('global.AddANewLine') }}
                                                            </button>
                                                            <button v-if="index" @click.prevent="deleteDebit(index)"
                                                                data-bs-target="#staticBackdrop"
                                                                class="btn btn-sm btn-danger me-2 mt-5">
                                                                <i class="far fa-trash-alt"></i> {{
                                                                    $t('global.Delete')
                                                                }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <!--End Sale Product-->

                                        </div>

                                        <button class="btn btn-primary mt-2" type="submit">{{
                                            $t('global.Submit')
                                        }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table -->
        </div>
    </div>
</template>

<script>
import { computed, onMounted, reactive, toRefs, inject, ref, watch } from "vue";
import useVuelidate from '@vuelidate/core';
import { required, minLength, between, maxLength, alpha, integer, numeric } from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import { useI18n } from "vue-i18n";
import exportFromJSON from "export-from-json";


export default {
    name: "create",
    data() {
        return {
            errors: {},
        }
    },
    props: ["id"],
    setup(props) {
        const { t } = useI18n({});
        const { id } = toRefs(props);
        let loading = ref(false);
        let loading2 = ref(false);
        let message = ref('');
        let altr_search = ref('');
        let debounce = ref({});
        let all_prices_for_excel = ref({});
        let supplier = ref({});
        let selected_data = ref({
            category_id: '',
            sub_category_id: '',
            index: '',
        });

        let columns = ref([
            {
                label: "Product id",
                field: "Product id",
            },
            {
                label: "Product Name",
                field: "Product Name",
            },
            {
                label: "Size",
                field: "Size",
            },
            {
                label: "Flavor",
                field: "Flavor",
            },
            {
                label: "Main Measurement Unit Price",
                field: "Main Measurement Unit Price",
            },
            {
                label: "Sub Measurement Unit Price",
                field: "Sub Measurement Unit Price",
            },
            {
                label: "Sub Measurement Unit Quantity",
                field: "Sub Measurement Unit Quantity",
            },
            {
                label: "Main Measurement Unit Price After Sale",
                field: "Main Measurement Unit Price After Sale",
            },
            {
                label: "Sub Measurement Unit Price After Sale",
                field: "Sub Measurement Unit Price After Sale",
            },
        ]);
        let categories = ref([]);
        let productValidation = ref([{
            category_id: {
                required
            },
            sub_category_id: {
                required
            },
            product_id: {
                required
            },
            main_measurement_price: {
                required,
                numeric
            },
            sub_measurement_price: {
                numeric
            },
            main_measurement_price_after_sale: {
                numeric
            },
            sub_measurement_price_after_sale: {
                numeric
            },
            quantity: {
                required,
                numeric
            },

        }]);

        let getData = () => {
            loading.value = true;
            adminApi.get(`/v1/dashboard/categories_by_supplier?supplier_id=${id.value}`)
                .then((res) => {
                    categories.value = res.data.data.categories;
                    supplier.value = res.data.data.supplier;
                })
                .catch((err) => {
                    console.log(err.response.data);
                    this.errors = err.response.data.errors;
                    Swal.fire({
                        icon: 'error',
                        title: `${t('global.ThereIsAnErrorInTheSystem')}`,
                    });
                })
                .finally(() => {
                    loading.value = false;
                });
        }



        const downloadExcelSheet = async () => {
            loading.value = true
            await adminApi.post(`/v1/dashboard/download_supplier_prices/${parseInt(id.value)}`)
                .then((res) => {
                    all_prices_for_excel.value = res.data.data.products
                    // notify({
                    //     title: `${t('global.Downloaded Successfully')} <i class="fas fa-check-circle"></i>`,
                    //     type: "success",
                    //     duration: 5000,
                    //     speed: 2000
                    // });
                })
                .catch((err) => {
                    console.log(err.response.data);
                    this.errors = err.response.data.errors;
                    Swal.fire({
                        icon: 'error',
                        title: `${t('global.ThereIsAnErrorInTheSystem')}`,
                    });
                })
                .finally(() => {
                    loading.value = false;
                });
        }


        onMounted(() => {
            getData();
            downloadExcelSheet();
        });

        watch(altr_search, () => {
            clearTimeout(debounce.value);
            debounce.value = setTimeout(async () => {
                await getProduct(selected_data.value.category_id, selected_data.value.sub_category_id, selected_data.value.index, altr_search.value)
            }, 500)
        })

        let getSubCategory = (id, index) => {
            loading.value = true;
            adminApi.get(`/v1/dashboard/get_subCategories_by_category_id/${id}`)
                .then((res) => {
                    addJob.subCategory[index].subCategory =res.data.data.subCategories;
                })
                .catch((err) => {
                    console.log(err.response.data);
                    this.errors = err.response.data.errors;
                    Swal.fire({
                                icon: 'error',
                                title: `${t('global.ThereIsAnErrorInTheSystem')}`,
                            });
                })
                .finally(() => {
                    loading.value = false;
                });
        }

        let getProduct = (category_id, sub_category_id, index, search_key = '') => {
            selected_data.value = {
                category_id,
                sub_category_id,
                index,
            }
            loading.value = true;
            loading2.value = true;
            adminApi.get(`/v1/dashboard/product?category_id=${category_id}&sub_category_id=${sub_category_id}&supplier_id=${parseInt(id.value)}&search=${search_key}`)
                .then((res) => {
                    let l = res.data.data;
                    addJob.products[index].products = l.products.data;
                })
                .catch((err) => {
                    console.log(err.response.data);
                    this.errors = err.response.data.errors;
                    Swal.fire({
                                icon: 'error',
                                title: `${t('global.ThereIsAnErrorInTheSystem')}`,
                            });
                })
                .finally(() => {
                    loading.value = false;
                    loading2.value = false;
                });
        }

        let addJob = reactive({
            data: {
                product: [
                    {
                        supplier_id: parseInt(id.value),
                        category_id: null,
                        sub_category_id: null,
                        product_id: null,
                        main_measurement_price: null,
                        sub_measurement_price: 0,
                        main_measurement_price_after_sale: 0,
                        sub_measurement_price_after_sale: 0,
                        quantity: null,
                    }
                ],
            },
            subCategory: [
                { subCategory: [] }
            ],
            products: [
                { products: [], send: true }
            ],
        });

        const rules = computed(() => {
            return {
                product: [
                    ...productValidation.value
                ],
            }
        });

        const v$ = useVuelidate(rules, addJob.data);

        return { t, id, getProduct,downloadExcelSheet,columns,all_prices_for_excel, getSubCategory,altr_search, loading2,supplier, categories, loading, message, ...toRefs(addJob), v$, productValidation };
    },
    methods: {
        //
        saveExcelVirtualStock() {
            this.loading = true
            var data2 = new FormData(document.getElementById("mainFormVirualStocks"))
            data2.append('supplier_id', this.id)
            adminApi.post(`/v1/dashboard/virtualStockExcel`, data2)
                .then((res) => {
                    notify({
                        title: `${this.t('global.AddedSuccessfully')} <i class="fas fa-check-circle"></i>`,
                        type: "success",
                        duration: 5000,
                        speed: 2000
                    });
                    this.resetForm();
                    this.$nextTick(() => { this.v$.$reset() });
                })
                .catch((err) => {
                    this.errors = err.response.data.errors;
                    this.message = err.response.data.message;
                    console.log(err.response.data);
                    Swal.fire({
                        icon: 'error',
                        title: `${this.t('global.ThereIsAnErrorInTheSystem')}`,
                    });
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        //

        storeVirtualStock() {
            this.v$.$validate();

            if (!this.v$.$error) {
                this.loading = true;
                this.errors = {};
                this.message = '';

                adminApi.post(`/v1/dashboard/store_product_price`, this.data)
                    .then((res) => {
                        notify({
                            title: `${this.t('global.AddedSuccessfully')} <i class="fas fa-check-circle"></i>`,
                            type: "success",
                            duration: 5000,
                            speed: 2000
                        });
                        this.resetForm();
                        this.$nextTick(() => { this.v$.$reset() });
                    })
                    .catch((err) => {
                        this.errors = err.response.data.errors;
                        this.message = err.response.data.message;
                        console.log(err.response.data);
                        Swal.fire({
                                icon: 'error',
                                title: `${this.t('global.ThereIsAnErrorInTheSystem')}`,
                            });
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        },
        addDebit() {
            this.data.product.push({
                supplier_id: parseInt(this.id),
                category_id: null,
                sub_category_id: null,
                main_measurement_price: null,
                sub_measurement_price: 0,
                main_measurement_price_after_sale: 0,
                sub_measurement_price_after_sale: 0,
                product_id: null,
                quantity: null,

            });
            this.productValidation.push({
                category_id: {
                    required
                },
                sub_category_id: {
                    required
                },
                product_id: {
                    required
                },
                main_measurement_price: {
                    required,
                    numeric
                },
                sub_measurement_price: {
                    numeric
                },
                main_measurement_price_after_sale: {
                    numeric
                },
                sub_measurement_price_after_sale: {
                    numeric
                },
                quantity: {
                    required,
                    numeric
                },

            });

            this.subCategory.push({ subCategory: [] });
            this.products.push({ products: [], send: true });
            this.$nextTick(() => { this.v$.$reset() });
        },
        deleteDebit(index) {
            this.data.product.splice(index, 1);
            this.productValidation.splice(index, 1);
            this.subCategory.splice(index, 1);
            this.products.splice(index, 1);
            this.$nextTick(() => { this.v$.$reset() });
        },
        resetForm() {
            this.data.product = [{
                supplier_id: this.id,
                category_id: null,
                sub_category_id: null,
                main_measurement_price: null,
                sub_measurement_price: 0,
                main_measurement_price_after_sale: 0,
                sub_measurement_price_after_sale: 0,
                product_id: null,
                quantity: null,
            }];
        }
    }
}
</script>

<style scoped>
.coustom-select {
    height: 100px;
}

.card {
    position: relative;
}

.account {
    background-color: #979eb2;
    color: #000000 !important;
    border-radius: 5px;
}

.account2 {
    background-color: #00DD2F;
    color: #000000 !important;
    border-radius: 5px;
}

.head-account {
    display: flex;
    justify-content: center;
}

.head-account h3 {
    color: #000000 !important;
    font-weight: bold;
}

.body-account {
    border-top: 3px solid #000000;
    margin: 0 !important;
}

.text-height {
    height: 46px !important;
}

.error-amount {
    display: flex;
    justify-content: center;
    color: red;
    margin: 10px;
}
</style>
