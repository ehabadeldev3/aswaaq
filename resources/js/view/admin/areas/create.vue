<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications :position="'top left'"  />
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('global.areas')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexArea'}">{{$t('global.areas')}}</router-link></li>
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
                                    :to="{name: 'indexArea'}"
                                    class="btn btn-custom btn-dark"
                                >
                                    {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">

                                    <div class="alert alert-danger text-center" v-if="errors['name']">{{ errors['name'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['shipping_price']">{{ errors['shipping_price'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['province_id']">{{ errors['province_id'][0] }}<br /> </div>

                                    <form @submit.prevent="storeArea" class="needs-validation">
                                        <div class="form-row row">

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom01">{{$t('treasury.NameAr')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.name_ar.$model"
                                                       id="validationCustom01"
                                                       :placeholder="$t('treasury.NameAr')"
                                                       :class="{'is-invalid':v$.name_ar.$error,'is-valid':!v$.name_ar.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.name_ar.required.$invalid">{{$t('global.NameIsRequired')}}<br /> </span>
                                                    <span v-if="v$.name_ar.minLength.$invalid">{{$t('global.NameIsMustHaveAtLeast')}} {{ v$.name_ar.minLength.$params.min }} {{$t('global.Letters')}} <br /></span>
                                                    <span v-if="v$.name_ar.maxLength.$invalid">{{$t('global.NameIsMustHaveAtMost')}} {{ v$.name_ar.maxLength.$params.max }} {{$t('global.Letters')}} </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom02">{{$t('treasury.NameEn')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.name_en.$model"
                                                       id="validationCustom02"
                                                       :placeholder="$t('treasury.NameEn')"
                                                       :class="{'is-invalid':v$.name_en.$error,'is-valid':!v$.name_en.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.name_en.required.$invalid">{{$t('global.NameIsRequired')}}<br /> </span>
                                                    <span v-if="v$.name_en.minLength.$invalid">{{$t('global.NameIsMustHaveAtLeast')}} {{ v$.name_en.minLength.$params.min }} {{$t('global.Letters')}} <br /></span>
                                                    <span v-if="v$.name_en.maxLength.$invalid">{{$t('global.NameIsMustHaveAtMost')}} {{ v$.name_en.maxLength.$params.max }} {{$t('global.Letters')}} </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>{{ $t('global.ChooseProvinces') }}</label>

                                                <select v-model="data.province_id" :class="['form-select',{'is-invalid':v$.province_id.$error,'is-valid':!v$.province_id.$invalid}]">
                                                    <option v-for="province in provinces" :kay="province.id" :value="province.id">{{province.name}}</option>
                                                </select>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.province_id.required.$invalid">{{$t('global.ProvincesIsRequired')}}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>{{ $t('global.shippingPrice') }}</label>
                                                <input type="number"
                                                       class="form-control"
                                                       v-model.trim="v$.shipping_price.$model"
                                                       :class="{'is-invalid':v$.shipping_price.$error,'is-valid':!v$.shipping_price.$invalid}"
                                                       :placeholder="$t('global.shippingPrice')"
                                                >
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.shipping_price.required.$invalid">{{ $t('global.IsRequired') }} <br/></span>
                                                    <span v-if="v$.shipping_price.numeric.$invalid">{{$t('global.ThisFieldIsNumeric')}} <br /></span>
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
import {required, minLength, between, maxLength, alpha, integer, numeric} from '@vuelidate/validators';
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
        let provinces = ref([]);

        let getMainProvincesViews = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/area/create`)
                .then((res) => {
                    let l = res.data.data;
                    provinces.value= l.provinces;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                })
        }

        onMounted(() => {
            getMainProvincesViews();
        });

        let addJob =  reactive({
            data:{
                name_ar:'',
                name_en:'',
                shipping_price:0,
                province_id:''
            }
        });

        const rules = computed(() => {
            return {
                name_ar: {
                    minLength: minLength(3),
                    maxLength:maxLength(40),
                    required
                },
                name_en: {
                    minLength: minLength(3),
                    maxLength:maxLength(40),
                    required
                },
                shipping_price:{
                    required,
                    numeric
                },
                province_id: {required}
            }
        });


        const v$ = useVuelidate(rules,addJob.data);


        return {t,provinces,loading,...toRefs(addJob),v$};
    },
    methods: {
        storeArea(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                adminApi.post(`/v1/dashboard/area`,this.data)
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
            this.data.name_ar = '';
            this.data.name_en = '';
            this.data.shipping_price = 0;
            this.data.province_id = '';
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
