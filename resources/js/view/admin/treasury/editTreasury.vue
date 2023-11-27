<template>
    <div :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications :position="this.$i18n.locale == 'ar'? 'top left': 'top right'"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('treasury.TreasuryManagement')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexTreasury', params: {lang: locale || 'ar'}}">{{$t('treasury.TreasuryManagement')}}</router-link></li>
                            <li class="breadcrumb-item active">{{$t('treasury.EditTreasury')}}</li>
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
                                    :to="{name: 'indexTreasury', params: {lang: locale || 'ar'}}"
                                    class="btn btn-custom btn-dark"
                                >
                                    {{$t('treasury.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['name_ar']">{{ errors['name_ar'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['name_en']">{{ errors['name_en'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['treasury_id']">{{ errors['treasury_id'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['amount']">{{ errors['amount'][0] }}<br /> </div>
                                    <form @submit.prevent="editTreasury" class="needs-validation">
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
                                                <label>{{$t('treasury.ChooseTreasury')}}</label>
                                                <select v-model="data.treasury_id" class="form-select">
                                                    <option v-for="treasury in mainTreasury" :kay="treasury.id" :value="treasury.id">{{treasury.name}}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3 mt-5">
                                                <div class="sec-body">
                                                    <div class="col-md-12 mb-12 sec-head">
                                                        <h3>
                                                            {{ $t('global.TheBalanceOfTheFirstDuration') }}
                                                        </h3>
                                                    </div>

                                                    <div class="col-md-12 mb-12" >
                                                        <label >{{ $t('global.Amount') }}</label>
                                                        <input type="number" step="0.1"
                                                               class="form-control"
                                                               v-model.trim="v$.amount.$model"
                                                               id="validationCustom11"
                                                               :class="{'is-invalid':v$.amount.$error,'is-valid':!v$.amount.$invalid}"
                                                               :placeholder="$t('global.Amount')"
                                                        >
                                                        <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                        <div class="invalid-feedback">
                                                            <span v-if="v$.amount.decimal.$invalid"> {{$t('global.ThisFieldIsNumeric')}} <br /> </span>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <button class="btn btn-primary" type="submit">{{$t('treasury.Submit')}}</button>
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
import {required, minLength, between, maxLength, alpha, integer, decimal} from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import {useI18n} from "vue-i18n";


export default {
    name: "createPackage",
    data(){
        return {
            errors:{}
        }
    },
    props:["id"],
    setup(props){
        const emitter = inject('emitter');
        const {t} = useI18n({});
        const {id} = toRefs(props)
        // get edit treasury
        let mainTreasury = ref([]);
        let treasury = ref({});
        let loading = ref(false);


        let getMainTreasuryViews = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/treasury/${id.value}/edit`)
                .then((res) => {
                    let l = res.data.data;
                    mainTreasury.value= l.mainTreasury;
                    addTreasury.data.name_ar = l.treasury.name_ar;
                    addTreasury.data.name_en = l.treasury.name_en;
                    addTreasury.data.treasury_id = l.treasury.treasury_id;
                    addTreasury.data.amount = l.treasury.treasury_balance.amount;
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        }

        onMounted(() => {
            getMainTreasuryViews();
        });


        //start design
        let addTreasury =  reactive({
            data:{
                name_ar : '',
                name_en : '',
                treasury_id: '',
                amount: '',
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
                amount:{
                    decimal
                }

            }
        });


        const v$ = useVuelidate(rules,addTreasury.data);


        return {t,mainTreasury,loading,...toRefs(addTreasury),v$};
    },
    methods: {
        editTreasury(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                adminApi.put(`/v1/dashboard/treasury/${this.id}`,this.data)
                    .then((res) => {

                        notify({
                            title: `${this.t('global.EditSuccessfully')} <i class="fas fa-check-circle"></i>`,
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
