<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications position="top left"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('global.Client Categories')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexClientCategories'}">{{$t('global.Client Categories')}}</router-link></li>
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
                                    :to="{name: 'indexClientCategories'}"
                                    class="btn btn-custom btn-dark"
                                >
                                   {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['from_amount']">{{ errors['from_amount'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['to_amount']">{{ errors['to_amount'][0] }}<br /> </div>

                                    <form @submit.prevent="storeClientCategories" class="needs-validation">
                                        <div class="form-row row">

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom01">{{$t('global.From Amount')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.from_amount.$model"
                                                       id="validationCustom01"
                                                       :placeholder="$t('global.From Amount')"
                                                       :class="{'is-invalid':v$.from_amount.$error,'is-valid':!v$.from_amount.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.from_amount.required.$invalid">{{$t('global.ThisFieldIsRequired')}}<br /> </span>
                                                    <span
                                                        v-if="v$.from_amount.numeric.$invalid">{{ $t('global.AmountIsNumeric') }}
                                                        <br /></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom02">{{$t('global.To Amount')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.to_amount.$model"
                                                       id="validationCustom02"
                                                       :placeholder="$t('global.To Amount')"
                                                       :class="{'is-invalid':v$.to_amount.$error,'is-valid':!v$.to_amount.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.to_amount.required.$invalid">{{$t('global.ThisFieldIsRequired')}}<br /> </span>
                                                    <span
                                                        v-if="v$.to_amount.numeric.$invalid">{{ $t('global.AmountIsNumeric') }}
                                                        <br /></span>
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
import {required,minLength,between,maxLength,alpha,numeric} from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import { useI18n } from 'vue-i18n';

export default {
    name: "createDepartment",
    data(){
        return {
            errors:{}
        }
    },
    setup(){
        let {t} = useI18n();
        let loading = ref(false);

        //start design
        let addClientCategories =  reactive({
            data:{
                from_amount : '',
                to_amount : '',
            }
        });

        const rules = computed(() => {
            return {
                from_amount: {
                    required,numeric
                },
                to_amount: {
                    required,numeric
                }
            }
        });


        const v$ = useVuelidate(rules,addClientCategories.data);


        return {loading,...toRefs(addClientCategories),v$,t};
    },
    methods: {
        storeClientCategories(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                adminApi.post(`/v1/dashboard/client_categories`,this.data)
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
            this.data.from_amount = '';
            this.data.to_amount = '';
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
