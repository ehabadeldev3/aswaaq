<template>
    <div :class="['page-wrapper', this.$i18n.locale == 'ar' ? 'page-wrapper-ar' : '']">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t("global.Supplier Stock") }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard' }">
                                    {{ $t("dashboard.Dashboard") }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ supplier.name}}</li>
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
                                    <div class="col-5">
                                        {{ $t("global.Search") }}:
                                        <input type="search" v-model="search" class="custom" />
                                    </div>
                                    <div class="col-5 row justify-content-end">
                                        <router-link
                                            :to="{ name: 'indexSupplier' }"
                                            class="btn btn-custom btn-dark"
                                        >
                                            {{ $t("global.back") }}
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">{{ $t("global.Product") }}</th>
                                            <th class="text-center">{{ $t("global.Flavor") }}</th>
                                            <th class="text-center">{{ $t("global.Size") }}</th>
                                            <th class="text-center">{{ $t("global.Main Measurement Unit") }}</th>
                                            <th class="text-center">{{ $t("global.Sub Measurement Unit") }}</th>
                                            <th class="text-center">{{ $t("global.Main Measurement Unit Quantity") }}</th>
                                            <th class="text-center">{{ $t("global.Sub Measurement Unit Quantity") }}</th>
                                            <th class="text-center">{{ $t("global.Main Measurement Unit Price") }}</th>
                                            <th class="text-center">{{ $t("global.Sub Measurement Unit Price") }}</th>
                                            <th class="text-center">{{ $t("global.Main Measurement Unit Price After Sale") }}</th>
                                            <th class="text-center">{{ $t("global.Sub Measurement Unit Price After Sale") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="virtualStocks.length">
                                        <tr v-for="(item, index) in virtualStocks" :key="item.id">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td class="text-center">{{ item.product.name }}</td>
                                        <td class="text-center">{{ item.product.flavor_name }}</td>
                                        <td class="text-center">{{ item.product.size.name }}</td>
                                        <td class="text-center">{{ item.product.main_measure_name }}</td>
                                        <td class="text-center">{{ item.product.sub_measure_name }}</td>
                                        <td class="text-center">{{ Math.ceil(item.quantity / item.product.count_unit) }}</td>
                                        <td class="text-center">{{ item.quantity }}</td>
                                        <td class="text-center">{{ item.main_measurement_price }}</td>
                                        <td class="text-center">{{ item.sub_measurement_price }}</td>
                                        <td class="text-center">{{ item.main_measurement_price_after_sale }}</td>
                                        <td class="text-center">{{ item.sub_measurement_price_after_sale }}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr class="text-center">
                                            <th class="text-center" colspan="15">
                                                {{ $t("global.NoDataFound") }}
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
            <Pagination :data="virtualStocksPaginate" @pagination-change-page="getVirtualStock">
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
    </div>
</template>

<script>
import { onMounted, inject, watch, ref, toRefs, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import adminApi from "../../../api/adminAxios";

export default {
    name: "index",
    props:["id"],
    setup(props) {
        const emitter = inject("emitter");
        const { t } = useI18n({});
        const { id } = toRefs(props);

        // get packages
        let virtualStocks = ref([]);
        let virtualStocksPaginate = ref({});
        let supplier = ref({});
        let loading = ref(false);
        const search = ref("");
        let store = useStore();

        let permission = computed(() => store.getters["authAdmin/permission"]);

        let getVirtualStock = (page = 1) => {
            loading.value = true;

            adminApi
            .get(`/v1/dashboard/get_all_prices_for_supplier/${id.value}?page=${page}&search=${search.value}`)
            .then((res) => {
                let l = res.data.data;
                virtualStocksPaginate.value = l.virtualStocks;
                virtualStocks.value = l.virtualStocks.data;
                supplier.value = l.supplier;
            })
            .catch((err) => {
                console.log(err.response.data);
            })
            .finally(() => {
                loading.value = false;
            });
        };

        onMounted(() => {
            getVirtualStock();
        });

        emitter.on("get_lang", () => {
            getVirtualStock(search.value);
        });

        watch(search, (search, prevSearch) => {
            if (search.length >= 0) {
                getVirtualStock();
            }
        });


        return {
            getVirtualStock,
            loading,
            permission,
            supplier,
            search,
            virtualStocksPaginate,
            virtualStocks,
        };
    },
    data() {
        return {
            locale: localStorage.getItem("langAdmin"),
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

.custom-img {
    width: 100px;
}
</style>
