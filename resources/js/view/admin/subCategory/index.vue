<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('global.Sub Categories')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{name: 'dashboard'}">
                                    {{$t('global.Dashboard')}}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{$t('global.Sub Categories')}}</li>
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
                                        {{ $t('global.Search') }} :
                                        <input type="search" v-model="search" class="custom"/>
                                    </div>
                                    <div class="col-5 row justify-content-end">
                                        <router-link
                                            v-if="permission.includes('subCategory create')"
                                           :to="{name: 'createSubCategory'}"
                                            class="btn btn-custom btn-warning">
                                            {{$t('global.Add')}}
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ $t('global.Name') }}</th>
                                        <th>{{ $t('global.Image') }}</th>
                                        <th>{{ $t('global.Main Category') }}</th>
                                        <th>{{$t('global.Status')}}</th>
                                        <th>{{ $t('global.Actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="categories.length">
                                    <tr v-for="(item,index) in categories"  :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.name }}</td>
                                        <td>
                                            <img
                                                :src="'/upload/' + item.media.file_name"
                                                :alt="item.name"
                                                class="custom-img"
                                            />
                                        </td>
                                        <td>{{ item.category.name }}</td>
                                        <td>
                                            <a href="#" @click="activationCategory(item.id,item.status,index)">
                                                <span :class="[parseInt(item.status) ? 'text-success hover': 'text-danger hover']">{{
                                                        parseInt(item.status) ? $t('treasury.Active') : $t('treasury.Inactive') }}</span>
                                            </a>
                                        </td>
                                        <td>

                                            <router-link
                                                :to="{name: 'editSubCategory',params:{id:item.id}}"
                                               v-if="permission.includes('subCategory edit')"
                                               class="btn btn-sm btn-success me-2">
                                                <i class="far fa-edit"></i>
                                            </router-link>
                                            <a href="#" @click="deleteCategory(item.id,index)"
                                                v-if="permission.includes('subCategory delete')"
                                               data-bs-target="#staticBackdrop" class="btn btn-sm btn-danger me-2">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </td>

                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <th class="text-center" colspan="5">{{ $t('global.No Data Found') }}</th>
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
            <Pagination :limit="2" :data="categoriesPaginate" @pagination-change-page="getCategory">
                <template #prev-nav>
                    <span>&lt; {{ $t('global.Previous') }}</span>
                </template>
                <template #next-nav>
                    <span>{{ $t('global.Next') }} &gt;</span>
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
        let categories = ref([]);
        let {t} = useI18n();
        let categoriesPaginate = ref({});
        let loading = ref(false);
        const search = ref('');
        let store = useStore();

        let permission = computed(() => store.getters['authAdmin/permission']);

        let getCategory = (page = 1) => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/subCategory?page=${page}&search=${search.value}`)
                .then((res) => {
                    let l = res.data.data;
                    console.log(l);
                    categoriesPaginate.value = l.categories;
                    categories.value = l.categories.data;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                });
        }

        onMounted(() => {
            getCategory();
        });


        watch(search, (search, prevSearch) => {
            if (search.length >= 0) {
                getCategory();
            }
        });


        function deleteCategory(id, index) {
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

                    adminApi.delete(`/v1/dashboard/subCategory/${id}`)
                        .then((res) => {
                            categories.value.splice(index, 1);

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

        function activationCategory(id, active,index) {
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

                    adminApi.get(`/v1/dashboard/activationCategory/${id}`)
                        .then((res) => {
                            Swal.fire({
                                icon: 'success',
                                title: `${!active ? t('global.ActiveSuccessfully') :t('global.InactiveSuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            categories.value[index]['status'] =  active ? 0:1
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

        return {getCategory, loading,permission, search, deleteCategory, activationCategory, categoriesPaginate,categories};

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

.custom-img {
    width: 100px;
}
</style>
