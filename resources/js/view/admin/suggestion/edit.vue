<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications :position="'top left'"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('global.Suggestions')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexSuggestion'}">{{$t('global.Suggestions')}}</router-link></li>
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
                                    :to="{name: 'indexSuggestion'}"
                                    class="btn btn-custom btn-dark"
                                >
                                    {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['name_ar']">{{ errors['name_ar'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['name_en']">{{ errors['name_en'][0] }}<br /> </div>                                    <form @submit.prevent="editJob" class="needs-validation">
                                        <div class="form-row row">
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom01">{{$t('treasury.NameAr')}}</label>
                                                <input type="text" class="form-control"
                                                       v-model.trim="v$.name_ar.$model"
                                                       id="validationCustom01"
                                                       :placeholder="$t('treasury.NameAr')"
                                                       :class="{'is-invalid':v$.name_ar.$error || errors['name_ar'],'is-valid':!v$.name_ar.$invalid && !errors['name_ar']}"
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
                                                       :class="{'is-invalid':v$.name_en.$error || errors['name_en'],'is-valid':!v$.name_en.$invalid && !errors['name_en']}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.name_en.required.$invalid">{{$t('global.NameIsRequired')}}<br /> </span>
                                                    <span v-if="v$.name_en.minLength.$invalid">{{$t('global.NameIsMustHaveAtLeast')}} {{ v$.name_en.minLength.$params.min }} {{$t('global.Letters')}} <br /></span>
                                                    <span v-if="v$.name_en.maxLength.$invalid">{{$t('global.NameIsMustHaveAtMost')}} {{ v$.name_en.maxLength.$params.max }} {{$t('global.Letters')}} </span>
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
import {required,minLength,maxLength} from '@vuelidate/validators';
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
        let job = ref({});
        let loading = ref(false);

        let getMainJobViews = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/suggestion/${id.value}/edit`)
                .then((res) => {
                    let l = res.data.data;
                    addJob.data.name_ar = l.suggestion.name_ar;
                    addJob.data.name_en = l.suggestion.name_en;
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

        let addJob =  reactive({
            data:{
                name_ar : '',
                name_en : '',
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
            }
        });


        const v$ = useVuelidate(rules,addJob.data);


        return {t,loading,...toRefs(addJob),v$};
    },
    methods: {
        editJob(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                adminApi.put(`/v1/dashboard/suggestion/${this.id}`,this.data)
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
