<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('sidebar.coupon') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{name: 'dashboard'}">
                                    الرئيسية
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t('sidebar.coupon') }}</li>
                        </ul>
                    </div>

                </div>
            </div>
            <!-- /Page Header -->
            <!-- Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <loader v-if="loading"/>
                        <div class="card-body">
                            <div class="card-header pt-0">
                                <div class="row justify-content-between">
                                    <div class="col-5">
                                        بحث :
                                        <input type="search" v-model="search" class="custom"/>
                                    </div>
                                    <div class="col-5 row justify-content-end">
                                        <router-link
                                            v-if="permission.includes('discount create')"
                                           :to="{name: 'createDiscount'}"
                                            class="btn btn-custom btn-warning">
                                            اضافه
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>كود</th>
                                        <th>القيمه</th>
                                        <th> تاريخ البدايه و النهايه</th>
                                        <th>عدد المستخدمين</th>
                                        <th>اكثر من</th>
                                        <th>تاريخ الانشاء</th>
                                        <th>الحاله</th>
                                        <th>الاجراءات</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="discounts.length">
                                    <tr v-for="(item,index) in discounts"  :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.code }}</td>
                                        <td>{{ item.value }}</td>
                                        <td>{{ item.start_date != '' ?  dateFormat(item.start_date) + ' - ' + dateFormat(item.expire_date) : '-' }}</td>
                                        <td>{{ item.used_times + ' / ' + item.use_times }}</td>
                                        <td>{{ item.greater_than ?? '-' }}</td>
                                        <td>{{  dateFormat(item.created_at) }}</td>
                                        <td>
                                            <a href="#" @click="activationDiscount(item.id,item.status,index)">
                                                <span :class="[parseInt(item.status) ? 'text-success hover': 'text-danger hover']">{{
                                                        parseInt(item.status) ? $t('treasury.Active') : $t('treasury.Inactive') }}</span>
                                            </a>
                                        </td>
                                        <td>

                                            <router-link
                                                :to="{name: 'editDiscount',params:{id:item.id}}"
                                               v-if="permission.includes('discount edit')"
                                               class="btn btn-sm btn-success me-2">
                                                <i class="far fa-edit"></i>
                                            </router-link>
                                            <a href="#" @click="deleteSupplier(item.id,index)"
                                                v-if="permission.includes('discount delete')"
                                               data-bs-target="#staticBackdrop" class="btn btn-sm btn-danger me-2">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </td>

                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <th class="text-center" colspan="9">لا يوجد بيانات</th>
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
            <Pagination :limit="2" :data="discountsPaginate" @pagination-change-page="getDiscount">
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
import {onMounted, watch, ref,computed} from "vue";
import {useStore} from "vuex";
import adminApi from "../../../api/adminAxios";
import { useI18n } from "vue-i18n";
export default {
    name: "index",
    setup() {

        // get packages
        let discounts = ref([]);
        let discountsPaginate = ref({});
        let {t} = useI18n();
        let loading = ref(false);
        const search = ref('');
        let store = useStore();

        let permission = computed(() => store.getters['authAdmin/permission']);

        let getDiscount = (page = 1) => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/discount?page=${page}&search=${search.value}`)
                .then((res) => {
                    let l = res.data.data;
                    discountsPaginate.value = l.coupons;
                    discounts.value = l.coupons.data;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                });
        }

        onMounted(() => {
            getDiscount();
        });

        watch(search, (search, prevSearch) => {
            if (search.length >= 0) {
                getDiscount();
            }
        });

        let dateFormat = (item) => {
            return new Date(item).toDateString();
        };

        function deleteSupplier(id, index) {
            Swal.fire({
                title: t('global.AreYouSureDelete'),
                text: t('global.YouWontBeAbleToRevertThis'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: t('global.No'),
confirmButtonText: t('global.Yes')
            }).then((result) => {
                if (result.isConfirmed) {

                    adminApi.delete(`/v1/dashboard/discount/${id}`)
                        .then((res) => {
                            discounts.value.splice(index, 1);

                            Swal.fire({
                                icon: 'success',
                                title:t('global.DeletedSuccessfully'),
                                showConfirmButton: false,
                                timer: 1500
                            });
                        })
                        .catch((err) => {
                            Swal.fire({
                                icon: 'error',
                                title: t('global.There is an error'),
                                text: t('global.ThereIsAnErrorInTheSystem'),
                            });
                        });
                }
            });
        }

        function activationDiscount(id, active,index) {
            Swal.fire({
                title: `${parseInt(active) ? t('global.AreYouSureInactive') : t('treasury.AreYouSureActive')} `,
                text: t('global.You Cant retrieve This'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: t('global.No'),
confirmButtonText: t('global.Yes')
            }).then((result) => {
                if (result.isConfirmed) {

                    adminApi.get(`/v1/dashboard/activationDiscount/${id}`)
                        .then((res) => {
                            Swal.fire({
                                icon: 'success',
                                title: `${parseInt(active) ? t('global.ActiveSuccessfully') :t('global.InactiveSuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            discounts.value[index]['status'] =  parseInt(active) ? 0:1
                        })
                        .catch((err) => {
                            Swal.fire({
                                icon: 'error',
                                title: t('global.There is an error'),
                                text: t('global.ThereIsAnErrorInTheSystem'),
                            });
                        });
                }
            });
        }

        return {dateFormat,discounts, loading,permission, getDiscount, search, deleteSupplier, activationDiscount, discountsPaginate};

    },
    data() {
        return {
            locale: localStorage.getItem("langAdmin")
        }
    }
}
</script>

<style scoped>
.card {
    position: relative;
}



.custom {
    border: 1px solid #D7D7D7;
    padding: 2px;
    border-radius: 5px;
    width: 35%;
}

.btn {
    color: #fff;
}
.hover:hover{
    border: 2px solid;
    padding: 3px;
    border-radius: 7px;
}

</style>
