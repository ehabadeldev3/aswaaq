<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications position="top left"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('global.Suppliers') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexSupplier'}">{{$t('global.Suppliers')}}</router-link></li>
                            <li class="breadcrumb-item active">{{$t('global.Add Supplier')}}</li>
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
                                    :to="{name: 'indexSupplier'}"
                                    class="btn btn-custom btn-dark"
                                >
                                   {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['name']">{{ errors['name'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['address']">{{ errors['address'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['phone']">{{ errors['phone'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['commerical_register']">{{ errors['commerical_register'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['tax_card']">{{ errors['tax_card'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['responsible_name']">{{ errors['responsible_name'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['responsible_phone']">{{ errors['responsible_phone'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['amount']">{{ errors['amount'][0] }}<br /> </div>

                                    <form @submit.prevent="storeSupplier" class="needs-validation">
                                        <div class="form-row row">

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom01">{{$t('global.Supplier Name')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.name.$model"
                                                       id="validationCustom01"
                                                       :placeholder="$t('global.Supplier Name')"
                                                       :class="{'is-invalid':v$.name.$error || errors['name'],'is-valid':!v$.name.$invalid && !errors['name']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.name.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                    <span v-if="v$.name.maxLength.$invalid"> {{$t('global.IsMustHaveAtLeast')}} {{ v$.name.minLength.$params.min }} {{$t('global.Char')}}  <br /></span>
                                                    <span v-if="v$.name.minLength.$invalid"> {{$t('global.IsMustHaveAtMost')}}  {{ v$.name.maxLength.$params.max }} {{$t('global.Char')}}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom02">{{ $t('global.Supplier Address') }}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.address.$model"
                                                       id="validationCustom02"
                                                       :placeholder="$t('global.Supplier Address')"
                                                       :class="{'is-invalid':v$.address.$error || errors['address'],'is-valid':!v$.address.$invalid && !errors['address']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.address.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                    <span v-if="v$.address.maxLength.$invalid"> {{$t('global.IsMustHaveAtLeast')}} {{ v$.address.minLength.$params.min }} {{$t('global.Char')}}  <br /></span>
                                                    <span v-if="v$.address.minLength.$invalid"> {{$t('global.IsMustHaveAtMost')}}  {{ v$.address.maxLength.$params.max }} {{$t('global.Char')}}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom03">{{ $t('global.Supplier Phone') }}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.phone.$model"
                                                       id="validationCustom03"
                                                       :placeholder="$t('global.Supplier Phone')"
                                                       :class="{'is-invalid':v$.phone.$error || errors['phone'],'is-valid':!v$.phone.$invalid && !errors['phone']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.phone.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                    <span v-if="v$.phone.integer.$invalid"> {{$t('global.ThisFieldIsNumeric')}} <br /> </span>
                                                    <span v-if="v$.phone.maxLength.$invalid"> {{$t('global.IsMustHaveAtLeast')}} {{ v$.phone.minLength.$params.min }} {{$t('global.Char')}}  <br /></span>
                                                    <span v-if="v$.phone.minLength.$invalid"> {{$t('global.IsMustHaveAtMost')}}  {{ v$.phone.maxLength.$params.max }} {{$t('global.Char')}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom042">{{ $t('global.Supplier Commission') }} (%)</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.commission_percentage.$model"
                                                       id="validationCustom042"
                                                       :placeholder="$t('global.Supplier Commission')"
                                                       :class="{'is-invalid':v$.commission_percentage.$error || errors['commission_percentage'],'is-valid':!v$.commission_percentage.$invalid && !errors['commission_percentage']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>

                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom04">{{ $t('global.Suppliers commercial registration number') }}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.commerical_register.$model"
                                                       id="validationCustom04"
                                                       :placeholder="$t('global.Suppliers commercial registration number')"
                                                       :class="{'is-invalid':v$.commerical_register.$error || errors['commerical_register'],'is-valid':!v$.commerical_register.$invalid && !errors['commerical_register']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.commerical_register.maxLength.$invalid"> {{$t('global.IsMustHaveAtLeast')}} {{ v$.commerical_register.maxLength.$params.min }} {{$t('global.Char')}}  <br /></span>
                                                </div>
                                            </div>


                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom06">{{$t('global.The suppliers tax card number')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.tax_card.$model"
                                                       id="validationCustom06"
                                                       :placeholder="$t('global.The suppliers tax card number')"
                                                       :class="{'is-invalid':v$.tax_card.$error || errors['tax_card'],'is-valid':!v$.tax_card.$invalid && !errors['tax_card']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.tax_card.maxLength.$invalid"> {{$t('global.IsMustHaveAtLeast')}} {{ v$.tax_card.maxLength.$params.min }} {{$t('global.Char')}}  <br /></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom07">{{$t('global.The name of the person responsible for the supplier')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.responsible_name.$model"
                                                       id="validationCustom07"
                                                       :placeholder="$t('global.The name of the person responsible for the supplier')"
                                                       :class="{'is-invalid':v$.responsible_name.$error || errors['responsible_name'],'is-valid':!v$.responsible_name.$invalid && !errors['responsible_name']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.responsible_name.maxLength.$invalid"> {{$t('global.IsMustHaveAtLeast')}} {{ v$.responsible_name.maxLength.$params.min }} {{$t('global.Char')}}  <br /></span>
                                                </div>
                                            </div>






                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom09">{{$t('global.The phone number of the person responsible for the supplier')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.responsible_phone.$model"
                                                       id="validationCustom09"
                                                       :placeholder="$t('global.The phone number of the person responsible for the supplier') "
                                                       :class="{'is-invalid':v$.responsible_phone.$error || errors['responsible_phone'],'is-valid':!v$.responsible_phone.$invalid && !errors['responsible_phone']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.responsible_phone.integer.$invalid"> {{$t('global.ThisFieldIsNumeric')}} <br /> </span>
                                                    <span v-if="v$.responsible_phone.maxLength.$invalid"> {{$t('global.IsMustHaveAtLeast')}} {{ v$.responsible_phone.maxLength.$params.min }} {{$t('global.Char')}}  <br /></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3 "  >
                                                <label class="typo__label">{{$t('global.Supplier areas')}}</label>
                                                <Multiselect
                                                v-model="v$.areas.$model"
                                                :options="areas_data"
                                                mode="tags"
                                                close-on-select="false"
                                                :label="'text'"
                                                :searchable="true"
                                                :closeOnSelect="false"
                                                :class="{'is-invalid':v$.areas.$error || errors['areas'],'is-valid':!v$.areas.$invalid && !errors['areas']}"
                                                valueProp="id"
                                                />

                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.areas.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3 ">
                                                <div class="sec-body">
                                                    <div class="col-md-12 mb-12 sec-head">
                                                        <h3>
                                                            {{ $t('global.Workdays') }}
                                                        </h3>
                                                    </div>

                                                    <div class="col-md-12 mb-12" >

                                                        <Multiselect
                                                            v-model="v$.days.$model"
                                                            :options="days_data"
                                                            mode="tags"
                                                            close-on-select="false"
                                                            :label="'text'"
                                                            :searchable="true"
                                                            :closeOnSelect="false"
                                                            :class="{'is-invalid':v$.days.$error || errors['days'],'is-valid':!v$.days.$invalid && !errors['days']}"
                                                            valueProp="id"
                                                        />


                                                        <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                        <div class="invalid-feedback">
                                                            <span v-if="v$.days.$invalid"> {{$t('global.IsRequired')}} <br /> </span>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-md-6 mb-3 ">
                                                <div class="form-check">
                                                    <input
                                                    v-model.trim="v$.is_our_supplier.$model"
                                                    class="form-check-input"
                                                    :class="{'is-invalid':v$.is_our_supplier.$error || errors['is_our_supplier'],'is-valid':!v$.is_our_supplier.$invalid && !errors['is_our_supplier']}"
                                                    type="checkbox"
                                                    id="defaultCheck1"
                                                    />
                                                    <label class="form-check-label" for="defaultCheck1">
                                                    {{ $t("global.IsAswaaqSupplier") }}
                                                    </label>
                                                </div>
                                                <div class="text-danger">
                                                    <div v-if="isOurSupplierExist">
                                                    {{ $t("global.AswaaqSupplierChoosedBefore") }}
                                                    </div>
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
import {required,minLength,bool,maxLength,alpha,integer,decimal} from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import { useI18n } from 'vue-i18n';
import Multiselect from '@vueform/multiselect'


export default {
    name: "createDepartment",
    components: {
      Multiselect,
    },
    data(){
        return {
            errors:{},
        }
    },
    setup(){
        let {t} = useI18n();
        let loading = ref(false);
        let isOurSupplierExist = ref(false);
        let areas_data = ref([]);
        let days_data = ref([
            {id:"6" , text:t('global.Saturday')},
            {id:"0" , text:t('global.Sunday')},
            {id:"1" , text:t('global.Monday')},
            {id:"2" , text:t('global.Tuesday')},
            {id:"3" , text:t('global.Wednesday')},
            {id:"4" , text:t('global.Thursday')},

        ]);

        //start design
        let addSupplier =  reactive({
            data:{
                name : '',
                address : '',
                phone : '',
                commerical_register : '',
                commission_percentage : '',
                tax_card : '',
                responsible_name : '',
                responsible_phone : '',
                areas : [],
                days : [],
                is_our_supplier: false,
                type: "STORE",
                amount:0
            }
        });

        const rules = computed(() => {
            return {
                name: {
                    minLength: minLength(3),
                    maxLength:maxLength(70),
                    required
                },
                address: {
                    minLength: minLength(3),
                    maxLength:maxLength(100),
                    required
                },
                phone: {
                    minLength: minLength(4),
                    maxLength:maxLength(15),
                    integer,
                    required
                },
                days: {
                    required
                },
                areas: {
                    required
                },
                commerical_register: {
                    maxLength:maxLength(100),
                },
                commission_percentage: {

                },
                tax_card: {
                    maxLength:maxLength(100),
                },
                responsible_name: {
                    maxLength:maxLength(100),
                },
                responsible_phone: {
                    maxLength:maxLength(15),
                    integer
                },
                is_our_supplier: {
                    required
                },
                amount:{
                    decimal
                }
            }
        });


        const v$ = useVuelidate(rules,addSupplier.data);


        onMounted(async () => {
            await get_areas()
        })

        const get_areas = async () => {
            await adminApi.get('/v1/dashboard/get_all_areas')
            .then((result) => {
                result.data.data.areas.forEach(element => {
                    areas_data.value.push({id:element.id,text:element.province.name + " / "+ element.name})
                })
            })

        }


        return {loading,...toRefs(addSupplier),v$,t,isOurSupplierExist,areas_data,days_data};
    },
    methods: {
        storeSupplier(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                adminApi.post(`/v1/dashboard/supplier`,this.data)
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
                        this.isOurSupplierExist = err.response.data.errors && err.response.data.errors.is_our_supplier ? true :false
                    })
                    .finally(() => {
                        this.loading = false;
                    });

            }
        },
        resetForm(){
            this.data.name = '';
            this.data.address = '';
            this.data.address = '';
            this.data.type = 'STORE';
            this.data.phone = '';
            this.data.commerical_register = '';
            this.data.commission_percentage = '';
            this.data.tax_card = '';
            this.data.responsible_name = '';
            this.data.responsible_phone = '';
            this.data.is_our_supplier = '';
            this.data.days = [];
            this.data.areas = [];
            this.data.amount = 0;
            this.isOurSupplierExist = false;
        }
    }
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<style scoped>
.coustom-select {
    height: 100px;
}
.card{
    position: relative;
}

.sec-body{
    border: 3px solid #fcb00c;
    border-radius: 20px;
    padding: 10px;
}
.sec-head{
    background-color: #fcb00c;
    color: #000;
    border-radius: 11px;
    padding: 5px;
    text-align: center;
    margin-bottom: 8px;
    margin-top: 10px;
}
.sec-body:hover .sec-head{
    border: 3px solid #fcb00c;
    padding: 2px;
    border-radius: 11px;
    background-color: #ffff;
}
.sec-head h3{
    font-weight: 700;
}
</style>
