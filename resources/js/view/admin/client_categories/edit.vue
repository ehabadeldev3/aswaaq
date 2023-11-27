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
                            <li class="breadcrumb-item active">{{$t('role.client category edit')}}</li>
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

                                    <form @submit.prevent="editClientCategories" class="needs-validation">
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

                                        <button class="btn btn-primary" type="submit">{{ $t('global.Submit') }}</button>
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
import {required, minLength, maxLength, numeric} from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import { useI18n } from 'vue-i18n';

export default {
    name: "editDepartment",
    data(){
        return {
            errors:{}
        }
    },
    props:["id"],
    setup(props){

        const {id} = toRefs(props)
        // get create Package
        let loading = ref(false);
        let {t} = useI18n();


        let getClientCategories = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/client_categories/${id.value}/edit`)
                .then((res) => {
                    let l = res.data.data;
                    addClientCategories.data.from_amount = l.client_category.from_amount;
                    addClientCategories.data.to_amount = l.client_category.to_amount;
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        }

        onMounted(() => {
            getClientCategories();
        });


        //start Supplier
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
        editClientCategories(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                adminApi.put(`/v1/dashboard/client_categories/${this.id}`,this.data)
                    .then((res) => {

                        notify({
                            title: `${this.t('global.EditSuccessfully')}<i class="fas fa-check-circle"></i>`,
                            type: "success",
                            duration: 5000,
                            speed: 2000
                        });

                    })
                    .catch((err) => {
                        this.errors = err.response.data.errors;
                    })
                    .finally(() => {
                        this.loading = false;
                    });

            }
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
