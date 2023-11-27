<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications :position="'top left'"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('global.Update Wallet Target')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexWalletTarget'}">{{$t('global.Update Wallet Target')}}</router-link></li>
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
                                    :to="{name: 'indexWalletTarget'}"
                                    class="btn btn-custom btn-dark"
                                >
                                    {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['start']">{{ errors['start'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['end']">{{ errors['end'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['points']">{{ errors['points'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['start_date']">{{ errors['start_date'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['end_date']">{{ errors['end_date'][0] }}<br /> </div>
                                    <form @submit.prevent="editWalletTarget" class="needs-validation">
                                        <div class="form-row row">
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom03">{{$t('global.StartsFrom')}}</label>
                                                <input type="number" class="form-control"
                                                       v-model="v$.amount.$model"
                                                       id="validationCustom03"
                                                       :placeholder="$t('global.StartsFrom')"
                                                       :class="{'is-invalid':v$.amount.$error,'is-valid':!v$.amount.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.amount.required.$invalid">{{$t('global.StartsFromIsRequired')}}<br /> </span>
                                                    <span v-if="v$.amount.numeric.$invalid">{{$t('global.AmountIsNumeric')}} <br /></span>
                                                </div>
                                            </div>


                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom01">{{$t('global.Points')}}</label>
                                                <input type="number" class="form-control"
                                                       v-model.trim="v$.points.$model"
                                                       id="validationCustom01"
                                                       :placeholder="$t('global.Points')"
                                                       :class="{'is-invalid':v$.points.$error,'is-valid':!v$.points.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.points.required.$invalid">{{$t('global.AmountIsRequired')}}<br /> </span>
                                                    <span v-if="v$.points.numeric.$invalid">{{$t('global.AmountIsNumeric')}} <br /></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom06">{{$t('global.Start Date')}}</label>
                                                <input type="date" class="form-control"
                                                       v-model.trim="v$.start_date.$model"
                                                       id="validationCustom06"
                                                       :class="{'is-invalid':v$.start_date.$error,'is-valid':!v$.start_date.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom07">{{$t('global.End Date')}}</label>
                                                <input type="date" class="form-control"
                                                       v-model.trim="v$.end_date.$model"
                                                       id="validationCustom07"
                                                       placeholder="اسم المسئول لدي المورد"
                                                       :class="{'is-invalid':v$.end_date.$error,'is-valid':!v$.end_date.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.end_date.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3 "  >
                                                <label class="typo__label">{{$t('global.Client Categories')}}</label>
                                                <Select2
                                                v-model="selected_client_category"
                                                :options="client_categories_data"
                                                mode="tags"
                                                close-on-select="false"
                                                :label="'text'"
                                                :searchable="true"
                                                :closeOnSelect="false"
                                                valueProp="id"
                                                />

                                            </div>
                                            <div class="col-md-6 mb-3 "  v-if="Object.keys(clients_data ?? []).length">
                                                <label class="typo__label">{{$t('global.Clients')}}</label>
                                                <Multiselect
                                                v-model="v$.clients.$model"
                                                :options="clients_data"
                                                mode="tags"
                                                close-on-select="false"
                                                :label="'text'"
                                                :searchable="true"
                                                :closeOnSelect="false"
                                                :class="{'is-invalid':v$.clients.$error || errors['clients'],'is-valid':!v$.clients.$invalid && !errors['clients']}"
                                                valueProp="id"
                                                />

                                                <div class="invalid-feedback">
                                                    <span v-if="v$.clients.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
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
import {computed, onMounted, reactive,toRefs,watch,ref} from "vue";
import useVuelidate from '@vuelidate/core';
import {required,numeric} from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import {useI18n} from "vue-i18n";


export default {
    name: "edit",
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
        let clients_data = ref([]);
        let selected_client_category = ref(0);
        let client_categories_data = ref([]);

        const get_clients = async () => {
            await adminApi.get('/v1/dashboard/get_all_clients?selected_client_category='+selected_client_category.value)
            .then((result) => {
                result.data.clients.forEach(element => {
                    clients_data.value.push({id:element.id,text:element.name + ' - ' + element.phone + ' - ( '+  0+ " " + t('global.LE')+ ' )'})
                })
            })
        }
        const get_all_client_categories = async () => {
            client_categories_data.value.push({id:0,text:t('global.All Clients')})
            await adminApi.get('/v1/dashboard/get_all_client_categories')
            .then((result) => {
                result.data.data.client_categories.forEach(element => {
                    client_categories_data.value.push({id:element.id,text:element.from_amount + ' - ' +element.to_amount})
                })
            })
        }

        let getWalletTargets = async () => {
            loading.value = true;

            await adminApi.get(`/v1/dashboard/wallet_target/${id.value}/edit`)
                .then((res) => {
                    let l = res.data.data;
                    wallet_target.data.amount = l.wallet_target.amount;
                    wallet_target.data.end_date = l.wallet_target.end_date;
                    wallet_target.data.start_date = l.wallet_target.start_date;
                    wallet_target.data.points = l.wallet_target.points;
                    wallet_target.data.clients = l.clients;
                    l.all_clients.forEach(element => {
                        clients_data.value.push({id:element.id,text:element.name + ' - ' + element.phone + ' - ( '+  element.total_amount + " " + t('global.LE')+ ' )'})
                    })
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        }

        onMounted(() => {
            get_all_client_categories()
            getWalletTargets();
        });

        watch(selected_client_category , () => {
            if(selected_client_category.value > 0)
                get_clients()
            clients_data.value = []
            wallet_target.data.clients = []
        })

        let wallet_target =  reactive({
            data:{
                amount:'',
                end_date:'',
                start_date:'',
                clients:[],
                points:'',
            }
        });

        const rules = computed(() => {
            return {
                points: {
                    required,
                    numeric
                },
                amount: {required,numeric},
                start_date: {required},
                end_date: {required},
                clients: { required(value){
                    return selected_client_category.value == 0 ? true : value
                } },
            }
        });


        const v$ = useVuelidate(rules,wallet_target.data);


        return {t,loading,...toRefs(wallet_target),v$,clients_data,selected_client_category,client_categories_data};
    },
    methods: {
        editWalletTarget(){
            this.v$.$validate();
            if(!this.v$.$error){
                this.loading = true;
                this.errors = {};
                let data = this.data
                data['selected_client_category'] = this.selected_client_category
                adminApi.put(`/v1/dashboard/wallet_target/${this.id}`,this.data)
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
