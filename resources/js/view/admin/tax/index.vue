<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('sidebar.types_of_taxes') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{name: 'dashboard'}">
                                    {{ $t('dashboard.Dashboard') }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{$t('sidebar.types_of_taxes')}}</li>
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
                                        {{$t('global.Search')}} :
                                        <input type="search" v-model="search" class="custom"/>
                                    </div>
                                    <div class="col-5 row justify-content-end">
                                        <router-link
                                            v-if="permission.includes('tax create')"
                                           :to="{name: 'createTax'}"
                                            class="btn btn-custom btn-warning">
                                            {{ $t('global.Add') }}
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{$t('global.Tax Name')}}</th>
                                        <th>{{$t('global.Percentage')}}</th>
                                        <th>{{ $t('global.Date') }}</th>
                                        <th>{{$t('global.Status')}}</th>
                                        <!-- <th>{{ $t('global.Add to the application') }}</th> -->
                                        <th>{{$t('global.Actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="taxs.length">
                                    <tr v-for="(item,index) in taxs"  :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.percentage }}</td>
                                        <td>{{ dateFormat(item.created_at) }}</td>
                                        <td>
                                            <a href="#" @click="activationTax(item.id,item.status,index)">
                                                <span :class="[parseInt(item.status) ? 'text-success hover': 'text-danger hover']">{{
                                                        parseInt(item.status) ? $t('treasury.Active') : $t('treasury.Inactive') }}</span>
                                            </a>
                                        </td>
                                        <!-- <td>
                                            <a href="#" @click="appTax(item.id,item.app,index)">
                                                <span :class="[parseInt(item.app) ? 'text-success hover': 'text-danger hover']">{{
                                                        parseInt(item.app) ? 'مضافة الى التطبيق' : 'لا توجد فى التطبيق' }}</span>
                                            </a>
                                        </td> -->
                                        <td>

                                            <router-link
                                                :to="{name: 'editTax',params:{id:item.id}}"
                                               v-if="permission.includes('tax edit')"
                                               class="btn btn-sm btn-success me-2">
                                                <i class="far fa-edit"></i>
                                            </router-link>
                                            <a href="#" @click="deletetax(item.id,index)"
                                                v-if="permission.includes('tax delete')"
                                               data-bs-target="#staticBackdrop" class="btn btn-sm btn-danger me-2">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </td>

                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <th class="text-center" colspan="7">{{$t('global.No Data Found')}}</th>
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
            <Pagination :limit="2" :data="taxsPaginate" @pagination-change-page="getTax">
                <template #prev-nav>
                    <span>&lt; {{$t('global.Previous')}}</span>
                </template>
                <template #next-nav>
                    <span>{{$t('global.Next')}} &gt;</span>
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
        let {t} = useI18n();
        let taxs = ref([]);
        let taxsPaginate = ref({});
        let loading = ref(false);
        const search = ref('');
        let store = useStore();

        let permission = computed(() => store.getters['authAdmin/permission']);

        let getTax = (page = 1) => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/tax?page=${page}&search=${search.value}`)
                .then((res) => {
                    let l = res.data.data;
                    taxsPaginate.value = l.taxs;
                    taxs.value = l.taxs.data;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                });
        }

        onMounted(() => {
            getTax();
        });

        watch(search, (search, prevSearch) => {
            if (search.length >= 0) {
                getTax();
            }
        });

        let dateFormat = (item) => {
            let now = new Date(item);
            let st = `
                 ${now.getUTCHours()}:${now.getUTCMinutes()}:${now.getUTCSeconds()}
                ${now.getUTCFullYear().toString()}
                 /${(now.getUTCMonth() + 1).toString()}
                 /${now.getUTCDate()}
            `;
            return st;
        };


        function deletetax(id, index) {
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

                    adminApi.delete(`/v1/dashboard/tax/${id}`)
                        .then((res) => {
                            taxs.value.splice(index,  1);

                            Swal.fire({
                                icon: 'success',
                                title: t('global.DeletedSuccessfully'),
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

        function activationTax(id, active,index) {
            Swal.fire({
                title: `${active ? t('global.AreYouSureInactive') : t('treasury.AreYouSureActive')} `,
                text: t('global.You Cant retrieve This'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: t('global.No'),
confirmButtonText: t('global.Yes')
            }).then((result) => {
                if (result.isConfirmed) {

                    adminApi.get(`/v1/dashboard/activationTax/${id}`)
                        .then((res) => {
                            Swal.fire({
                                icon: 'success',
                                title: `${active ? t('global.InactiveSuccessfully') :t('global.ActiveSuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            taxs.value[index]['status'] =  active ? 0:1
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

        function appTax(id, app,index) {
            Swal.fire({
                title: `${app ? t('global.Are you sure to delete the tax from the application?') : t('global.Are you sure to add the tax to the application?')} `,
                text: t('global.You Cant retrieve This'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: t('global.No'),
confirmButtonText: t('global.Yes')
            }).then((result) => {
                if (result.isConfirmed) {

                    adminApi.get(`/v1/dashboard/activationTaxApp/${id}`)
                        .then((res) => {
                            Swal.fire({
                                icon: 'success',
                                title: `${app ? t('global.DeletedSuccessfully') :t('global.AddedSuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            taxs.value[index]['app'] =  app ? 0:1
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

        return {dateFormat,taxs, loading,permission, deletetax, search,getTax, activationTax,appTax, taxsPaginate};

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
