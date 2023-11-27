<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications position="top left"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('global.Sub Categories') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexSubCategory'}">{{$t('global.Sub Categories') }}</router-link></li>
                            <li class="breadcrumb-item active">{{ $t('global.Update Sub Category') }}</li>
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
                                    :to="{name: 'indexSubCategory'}"
                                    class="btn btn-custom btn-dark"
                                >
                                    {{ $t('global.back') }}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <form @submit.prevent="editSupplier" class="needs-validation">
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

                                            <div class="col-md-6 mb-3">
                                                <label >{{ $t('global.ChoseCategory') }}</label>
                                                <select
                                                    name="type"
                                                    class="form-control"
                                                    v-model="v$.category_id.$model"
                                                    :class="{'is-invalid':v$.category_id.$error,'is-valid':!v$.category_id.$invalid}"
                                                >
                                                    <option value="">---</option>
                                                    <option v-for="category in categories" :value="category.id" >
                                                        {{ category.name }}
                                                    </option>
                                                </select>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.category_id.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-12 row flex-fill">
                                                <div class="btn btn-outline-primary waves-effect">
                                                    <span>
                                                        {{ $t('global.Select Files') }}
                                                        <i class="fas fa-cloud-upload-alt ml-3" aria-hidden="true"></i>
                                                    </span>
                                                    <input
                                                        name="mediaPackage"
                                                        type="file"
                                                        @change="preview"
                                                        id="mediaPackage"
                                                        accept="image/png,jepg,jpg"
                                                    >
                                                </div>

                                                <p class="num-of-files">{{numberOfImage ? numberOfImage + ' Files Selected' : 'No Files Chosen' }}</p>
                                                <div class="container-images" id="container-images" v-show="data.file && numberOfImage"></div>
                                                <div class="container-images" v-show="!numberOfImage">
                                                    <figure>
                                                        <figcaption v-if="image">
                                                            <img :src="`/upload/${image}`">
                                                        </figcaption>
                                                    </figure>
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
import {computed, onMounted, reactive,toRefs,ref} from "vue";
import useVuelidate from '@vuelidate/core';
import {required, minLength, maxLength, integer} from '@vuelidate/validators';
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
        let {t} = useI18n();
        let loading = ref(false);
        let image = ref('');
        let categories = ref([]);

        let getCategory = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/subCategory/${id.value}/edit`)
                .then((res) => {
                    let l = res.data.data;
                    addCategory.data.name_ar = l.category.name_ar;
                    addCategory.data.name_en = l.category.name_en;
                    addCategory.data.category_id = l.category.category_id;
                    image.value = l.category.media.file_name;
                    categories.value = l.main_categories;
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        }

        onMounted(() => {
            getCategory();
        });

        //start design
        let addCategory =  reactive({
            data:{
                name_ar : '',
                name_en : '',
                file : {},
                category_id:''
            }
        });

        const rules = computed(() => {
            return {
                name_ar: {
                    minLength: minLength(3),
                    maxLength:maxLength(70),
                    required
                },
                name_en: {
                    minLength: minLength(3),
                    maxLength:maxLength(70),
                    required
                },
                category_id: {required}
            }
        });

        const v$ = useVuelidate(rules,addCategory.data);

        let preview = (e) => {

            let containerImages = document.querySelector('#container-images');
            if(numberOfImage.value){
                containerImages.innerHTML = '';
            }
            addCategory.data.file = {};

            numberOfImage.value = e.target.files.length;

            addCategory.data.file = e.target.files[0];

            let reader = new FileReader();
            let figure = document.createElement('figure');
            let figcap = document.createElement('figcaption');

            figcap.innerText = addCategory.data.file.name;
            figure.appendChild(figcap);

            reader.onload = () => {
                let img = document.createElement('img');
                img.setAttribute('src',reader.result);
                figure.insertBefore(img,figcap);
            }

            containerImages.appendChild(figure);
            reader.readAsDataURL(addCategory.data.file);

        };

        const numberOfImage = ref(0);

        return {id,categories,loading,...toRefs(addCategory),v$,preview,numberOfImage,image,t};
    },
    methods: {
        editSupplier(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};

                let formData = new FormData();
                formData.append('name_ar',this.data.name_ar);
                formData.append('name_en',this.data.name_en);
                formData.append('category_id',this.data.category_id);
                formData.append('file',this.data.file);
                formData.append('_method','PUT');

                adminApi.post(`/v1/dashboard/subCategory/${this.id}`,formData)
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

.waves-effect {
    position: relative;
    overflow: hidden;
    cursor: pointer;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    width: 200px;
    height: 50px;
    text-align: center;
    line-height: 34px;
    margin: auto;
}

input[type="file"] {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 0;
    margin: 0;
    cursor: pointer;
    filter: alpha(opacity=0);
    opacity: 0;
}

.num-of-files{
    text-align: center;
    margin: 20px 0 30px;
}

.container-images {
    width: 90%;
    position: relative;
    margin: auto;
    display: flex;
    justify-content: space-evenly;
    gap: 20px;
    flex-wrap: wrap;
    padding: 10px;
    border-radius: 20px;
    background-color: #f7f7f7;
}
</style>
