<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications :position="'top left'"  />
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('global.EmployeeShift')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexEmployeeShift'}">{{$t('global.EmployeeShift')}}</router-link></li>
                            <li class="breadcrumb-item active">{{$t('global.Add')}}</li>
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
                                <router-link
                                    :to="{name: 'indexEmployeeShift'}"
                                    class="btn btn-custom btn-dark"
                                >
                                    {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['shift_id']">{{ errors['shift_id'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['employee_id']">{{ errors['employee_id'][0] }}<br /> </div>

                                    <form @submit.prevent="storeShift" class="needs-validation">
                                        <div class="form-row row">

                                            <div class="col-md-6 mb-3">
                                                <label>{{ $t('global.workShifts') }}</label>

                                                <select v-model="data.shift_id" :class="['form-select',{'is-invalid':v$.shift_id.$error,'is-valid':!v$.shift_id.$invalid}]">
                                                    <option v-for="shift in shifts" :kay="shift.id" :value="shift.id">{{shift.name}}</option>
                                                </select>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.shift_id.required.$invalid">{{$t('global.ThisFieldIsRequired')}}<br /> </span>
                                                </div>

                                            </div>



                                            <div class="col-md-6 mb-3">
                                                <label>{{ $t('global.Employees') }}</label>

                                                <select v-model="data.employee_id" :class="['form-select',{'is-invalid':v$.employee_id.$error,'is-valid':!v$.employee_id.$invalid}]">
                                                    <option v-for="employee in employees" :kay="employee.id" :value="employee.id">{{employee.user.name}}</option>
                                                </select>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.employee_id.required.$invalid">{{$t('global.ThisFieldIsRequired')}}<br /> </span>
                                                </div>

                                            </div>



                                        </div>

                                        <button class="btn btn-primary" type="submit">{{$t('global.Submit')}}</button>
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
import {computed, onMounted, reactive,toRefs,inject,ref} from "vue";
import useVuelidate from '@vuelidate/core';
import {required,minLength,between,maxLength,alpha,integer} from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import {useI18n} from "vue-i18n";


export default {
    name: "create",
    data(){
        return {
            errors:{}
        }
    },
    setup(){
        const {t} = useI18n({});
        let loading = ref(false);
        let employees = ref([]);
        let stores = ref([]);
        let shifts = ref([]);

        let getData = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/employeeShift/create`)
                .then((res) => {
                    let l = res.data.data;
                    employees.value= l.employees;
                    stores.value= l.stores;
                    shifts.value= l.shifts;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                })
        }

        onMounted(() => {
            getData();
        });

        let addShift =  reactive({
            data:{
                employee_id : '',
                shift_id : ''
            }
        });

        const rules = computed(() => {
            return {
                employee_id: {required},
                shift_id: {required},
            }
        });


        const v$ = useVuelidate(rules,addShift.data);


        return {t,shifts,stores,employees,loading,...toRefs(addShift),v$};
    },
    methods: {
        storeShift(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                adminApi.post(`/v1/dashboard/employeeShift`,this.data)
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
                    })
                    .finally(() => {
                        this.loading = false;
                    });

            }
        },
        resetForm(){
            this.data.shift_id = '';
            this.data.employee_id = '';
        }
    }
}
</script>

<style scoped>
.coustom-select {
    height: 100px;
}
.card{
    position: relative;
}
</style>
