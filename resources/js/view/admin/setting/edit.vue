<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications :position="'top left'"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('sidebar.setting')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexSetting'}">{{$t('sidebar.setting')}}</router-link></li>
                            <li class="breadcrumb-item active">{{$t('global.Edit')}}</li>
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
                                    :to="{name: 'indexSetting'}"
                                    class="btn btn-custom btn-dark"
                                >
                                    {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['close']">{{ errors['close'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['wats_app']">{{ errors['wats_app'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['facebook']">{{ errors['facebook'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['online_payment']">{{ errors['online_payment'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['order_amount']">{{ errors['order_amount'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['best_prices_limits']">{{ errors['best_prices_limits'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['cut_off_time']">{{ errors['cut_off_time'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['phone']">{{ errors['phone'][0] }}<br /> </div>
                                    <form @submit.prevent="editJob" class="needs-validation">
                                        <div class="form-row row">

                                            <div class="col-md-4 mb-3">
                                                <label>{{ $t('global.Minimum purchase amount') }}</label>
                                                <input type="text"
                                                       class="form-control"
                                                       v-model.trim="v$.order_amount.$model"
                                                       :class="{'is-invalid':v$.order_amount.$error,'is-valid':!v$.order_amount.$invalid}"
                                                >
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.order_amount.required.$invalid">{{ $t('global.ThisFieldIsRequired') }} <br/></span>
                                                        <span v-if="v$.order_amount.numeric.$invalid">{{$t('global.ThisFieldIsNumeric')}} <br /></span>

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label>{{ $t('global.The maximum number of the best products') }}</label>
                                                <input type="text"
                                                       class="form-control"
                                                       v-model.trim="v$.best_prices_limits.$model"
                                                       :class="{'is-invalid':v$.best_prices_limits.$error,'is-valid':!v$.best_prices_limits.$invalid}"
                                                >
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.best_prices_limits.required.$invalid">{{ $t('global.ThisFieldIsRequired') }} <br/></span>
                                                        <span v-if="v$.best_prices_limits.numeric.$invalid">{{$t('global.ThisFieldIsNumeric')}} <br /></span>

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label>{{ $t('global.Phone') }}</label>
                                                <input type="text"
                                                       class="form-control"
                                                       v-model.trim="v$.phone.$model"
                                                       :class="{'is-invalid':v$.phone.$error,'is-valid':!v$.phone.$invalid}"
                                                       :placeholder="$t('global.Phone')"
                                                >
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.phone.required.$invalid">{{ $t('global.PhoneIsRequired') }} <br/></span>
                                                    <span
                                                        v-if="v$.phone.minLength.$invalid">{{ $t('global.PhoneIsMustHaveAtLeast') }} {{
                                                            v$.phone.minLength.$params.min
                                                        }} {{ $t('global.Letters') }} <br/></span>
                                                    <span
                                                        v-if="v$.phone.maxLength.$invalid">{{ $t('global.PhoneIsMustHaveAtMost') }} {{
                                                            v$.phone.maxLength.$params.max
                                                        }} {{ $t('global.Letters') }} </span>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label>{{ $t('global.watsApp') }}</label>
                                                <input type="text"
                                                       class="form-control"
                                                       v-model.trim="v$.wats_app.$model"
                                                       :class="{'is-invalid':v$.wats_app.$error,'is-valid':!v$.wats_app.$invalid}"
                                                       :placeholder="$t('global.watsApp')"
                                                >
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.wats_app.required.$invalid">{{ $t('global.PhoneIsRequired') }} <br/></span>
                                                    <span
                                                        v-if="v$.wats_app.minLength.$invalid">{{ $t('global.PhoneIsMustHaveAtLeast') }} {{
                                                            v$.wats_app.minLength.$params.min
                                                        }} {{ $t('global.Letters') }} <br/></span>
                                                    <span
                                                        v-if="v$.wats_app.maxLength.$invalid">{{ $t('global.PhoneIsMustHaveAtMost') }} {{
                                                            v$.wats_app.maxLength.$params.max
                                                        }} {{ $t('global.Letters') }} </span>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label>{{ $t('global.facebook') }}</label>
                                                <input type="text"
                                                       class="form-control"
                                                       v-model.trim="v$.facebook.$model"
                                                       :class="{'is-invalid':v$.facebook.$error,'is-valid':!v$.facebook.$invalid}"
                                                       :placeholder="$t('global.facebook')"
                                                >
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.facebook.required.$invalid">{{ $t('global.IsRequired') }} <br/></span>
                                                    <span
                                                        v-if="v$.facebook.minLength.$invalid">{{ $t('global.IsMustHaveAtLeast') }} {{
                                                            v$.facebook.minLength.$params.min
                                                        }} {{ $t('global.Letters') }} <br/></span>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label>{{ $t('global.cut_off_time') }}</label>
                                                <input type="time"
                                                       class="form-control"
                                                       v-model.trim="v$.cut_off_time.$model"
                                                       :class="{'is-invalid':v$.cut_off_time.$error,'is-valid':!v$.cut_off_time.$invalid}"
                                                >
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.cut_off_time.required.$invalid">{{ $t('global.IsRequired') }} <br/></span>

                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="validationCustom02">{{$t('global.closeApp')}}</label>
                                                <input type="checkbox" id="validationCustom02" v-model="data.close" class="m-5" :checked="data.close">
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="online_payment">{{$t('global.Online payment')}}</label>
                                                <input id="online_payment" type="checkbox"  v-model="data.online_payment" class="m-5" :checked="data.online_payment">
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
import {required, minLength, maxLength, boolean,numeric} from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import {useI18n} from "vue-i18n";


export default {
    name: "editJob",
    data(){
        return {
            errors:{}
        }
    },
    props:["id"],
    setup(props){
        const {id} = toRefs(props)
        const {t} = useI18n({});
        let loading = ref(false);

        let getMainJobViews = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/setting/${id.value}/edit`)
                .then((res) => {
                    let l = res.data.data;
                    setting.data.close = l.setting.close;
                    setting.data.online_payment = l.setting.online_payment;
                    setting.data.phone = l.setting.phone;
                    setting.data.wats_app = l.setting.wats_app;
                    setting.data.facebook = l.setting.facebook;
                    setting.data.order_amount = l.setting.order_amount;
                    setting.data.cut_off_time = l.setting.cut_off_time;
                    setting.data.best_prices_limits = l.setting.best_prices_limits;
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        }

        onMounted(() => {
            getMainJobViews();
        });

        let setting =  reactive({
            data:{
                close:0,
                best_prices_limits:0,
                order_amount:0,
                online_payment:false,
                cut_off_time:'',
                phone: '',
                wats_app: '',
                facebook: '',
            }
        });

        const rules = computed(() => {
            return {
                phone: {
                    required,
                    minLength: minLength(8),
                    maxLength: maxLength(20)
                },
                wats_app: {
                    required,
                    minLength: minLength(8),
                    maxLength: maxLength(20)
                },
                facebook: {
                    required,
                    minLength: minLength(1),
                },
                order_amount: {
                    required,numeric

                },
                best_prices_limits: {
                    required,numeric
                },
                cut_off_time: {
                    required
                },

            }
        });


        const v$ = useVuelidate(rules,setting.data);


        return {t,loading,...toRefs(setting),v$};
    },
    methods: {
        editJob(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                adminApi.put(`/v1/dashboard/setting/${this.id}`,this.data)
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
</style>
