<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications position="top left"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('global.Products')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexProduct'}">{{$t('global.Products')}}</router-link></li>
                            <li class="breadcrumb-item active">{{$t('global.Add Product')}}</li>
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
                                    :to="{name: 'indexProduct'}"
                                    class="btn btn-custom btn-dark"
                                >
                                   {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <form @submit.prevent="storeProduct" class="needs-validation">
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

                                            <!-- <div class="col-md-6 mb-3">
                                                <label for="validationCustom01">{{$t('global.Barcode')}} </label>
                                                <input
                                                    type="number" class="form-control"
                                                    v-model.trim="v$.barcode.$model"
                                                    id="validationCustom056"
                                                    :placeholder="$t('global.Barcode')"
                                                    :class="{'is-invalid':v$.barcode.$error,'is-valid':!v$.barcode.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.barcode.integer.$invalid">>{{$t('global.ThisFieldIsNumeric')}}<br /></span>
                                                </div>
                                            </div> -->

                                            <div class="col-md-6 mb-3">
                                                <label >{{ $t('global.Main Category') }}</label>
                                                <Select2 :class="{'is-invalid':v$.category_id.$error,'is-valid':!v$.category_id.$invalid}"
                                                 v-model="v$.category_id.$model"
                                                 :options="categories"/>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.category_id.required.$invalid">{{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label >{{ $t('global.Sub Category') }}</label>
                                                <select
                                                    name="type"
                                                    class="form-control"
                                                    v-model="v$.sub_category_id.$model"
                                                    :class="{'is-invalid':v$.sub_category_id.$error,'is-valid':!v$.sub_category_id.$invalid}"
                                                >
                                                    <option value="">---</option>
                                                    <option v-for="category in subCategories" :value="category.id" >
                                                        {{ category.name }}
                                                    </option>
                                                </select>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.sub_category_id.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label >{{ $t('global.Companies') }}</label>
                                                <Select2 :class="{'is-invalid':v$.company_id.$error,'is-valid':!v$.company_id.$invalid}"
                                                 v-model="v$.company_id.$model"
                                                  :options="companies"/>

                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.company_id.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label >{{ $t('global.Flavor') }}</label>
                                                <Select2 :class="{'is-invalid':v$.flavor_id.$error,'is-valid':!v$.flavor_id.$invalid}"
                                                 v-model="v$.flavor_id.$model"
                                                  :options="flavors_data"/>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <!-- <span v-if="v$.flavor_id.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span> -->
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label >{{ $t('global.Size') }}</label>
                                                <Select2 :class="{'is-invalid':v$.size_id.$error,'is-valid':!v$.size_id.$invalid}"
                                                 v-model="v$.size_id.$model"
                                                  :options="sizes_data"/>

                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.size_id.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3 "  >
                                                <label class="typo__label">{{$t('global.Suppliers')}}</label>
                                                <Multiselect
                                                v-model="v$.suppliers.$model"
                                                :options="suppliers_data"
                                                mode="tags"
                                                close-on-select="false"
                                                :label="'text'"
                                                :searchable="true"
                                                :closeOnSelect="false"
                                                :class="{'is-invalid':v$.suppliers.$error || errors['suppliers'],'is-valid':!v$.suppliers.$invalid && !errors['suppliers']}"
                                                valueProp="id"
                                                />

                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.suppliers.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                                <div class="invalid-feedback" v-if="errors['suppliers']">
                                                    <span> {{ errors['suppliers'][0]}}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label >{{$t('global.Main Measurement Unit')}}</label>
                                                <Select2 :class="{'is-invalid':v$.main_measurement_unit_id.$error,'is-valid':!v$.main_measurement_unit_id.$invalid}"
                                                 v-model="v$.main_measurement_unit_id.$model"
                                                 :options="units_data"/>

                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.main_measurement_unit_id.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label >{{ $t('global.The number of units within the main measurement unit') }} </label>
                                                <input
                                                    type="number" class="form-control"
                                                    v-model="v$.count_unit.$model"
                                                    @input="subPrice"
                                                    :class="{'is-invalid':v$.count_unit.$error,'is-valid':!v$.count_unit.$invalid}"
                                                >
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.count_unit.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                    <span v-if="v$.count_unit.integer.$invalid"> {{$t('global.ThisFieldIsNumeric')}} <br /></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label >{{$t('global.subUnitMeasurement')}}</label>
                                                <Select2 :class="{'is-invalid':v$.sub_measurement_unit_id.$error || errors['sub_measurement_unit_id'],'is-valid':!v$.sub_measurement_unit_id.$invalid && !errors['sub_measurement_unit_id']}"
                                                 v-model="v$.sub_measurement_unit_id.$model"
                                                 :options="units_data"/>

                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.sub_measurement_unit_id.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                                <div class="invalid-feedback" v-if="errors['sub_measurement_unit_id']">
                                                    <span> {{ errors['sub_measurement_unit_id'][0]}}<br /> </span>
                                                </div>
                                            </div>



                                            <div class="col-md-12 mb-3">
                                                <label for="validationCustom034">{{$t('global.DescriptionAr')}}</label>
                                                <textarea type="text" class="form-control custom-textarea"
                                                          v-model.trim="v$.description_ar.$model"
                                                          id="validationCustom034"
                                                          :placeholder="$t('global.DescriptionAr')"
                                                          :class="{'is-invalid':v$.description_ar.$error,'is-valid':!v$.description_ar.$invalid}"
                                                ></textarea>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.description_ar.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label for="validationCustom034">{{$t('global.DescriptionEn')}}</label>
                                                <textarea type="text" class="form-control custom-textarea"
                                                          v-model.trim="v$.description_en.$model"
                                                          id="validationCustom034"
                                                          :placeholder="$t('global.DescriptionEn')"
                                                          :class="{'is-invalid':v$.description_en.$error,'is-valid':!v$.description_en.$invalid}"
                                                ></textarea>
                                                <div class="valid-feedback">{{$t('global.LooksGood')}}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.description_en.required.$invalid"> {{ $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>

                                            <div class="col-md-3 row flex-fill">
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
                                                <div class="container-images" id="container-images" v-show="data.image && numberOfImage"></div>
                                                <div class="container-images" v-show="!numberOfImage">
                                                    <figure>
                                                        <figcaption>
                                                            <img :src="`/admin/img/company/img-1.png`">
                                                        </figcaption>
                                                    </figure>
                                                </div>
                                            </div>

                                            <div class="col-md-9 row flex-fill">
                                                <div class="btn btn-outline-primary waves-effect">
                                                    <span>
                                                        {{ $t('global.Select Files') }}
                                                        <i class="fas fa-cloud-upload-alt ml-3" aria-hidden="true"></i>
                                                    </span>
                                                    <input
                                                        name="mediaPackage[]"
                                                        type="file"
                                                        multiple
                                                        @change="preview2"
                                                        id="mediaPackage1"
                                                        accept="image/png,jepg,jpg"
                                                    >
                                                </div>

                                                <p class="num-of-files">{{numberOfImage1 ? numberOfImage1 + ' Files Selected' : 'No Files Chosen' }}</p>
                                                <div class="container-images" id="container-images1" v-show="data.files && numberOfImage1"></div>
                                                <div class="container-images" v-show="!numberOfImage1">
                                                    <figure>
                                                        <figcaption>
                                                            <img :src="`/admin/img/company/img-1.png`">
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
import {computed, onMounted, reactive, toRefs, ref, watch} from "vue";
import useVuelidate from '@vuelidate/core';
import {required,minLength,maxLength,numeric,integer} from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import { useI18n } from 'vue-i18n';


export default {
    name: "create Product",
    data(){
        return {
            errors:{}
        }
    },
    setup(){
        let loading = ref(false);
        let companies = ref([]);
        let categories = ref([]);
        let subCategories = ref([]);
        let suppliers_data = ref([]);
        let units_data = ref([]);
        let sizes_data = ref([]);
        let flavors_data = ref([]);
        const {t} = useI18n()

        //start design
        let addProduct =  reactive({
            data:{
                name_ar : '',
                name_en : '',
                barcode : '',
                description_ar : '',
                description_en : '',
                image : {},
                files : [],
                suppliers : [],
                size_id: null,
                flavor_id: null,
                category_id: null,
                main_measurement_unit_id: null,
                sub_measurement_unit_id: null,
                count_unit: null,
                sub_category_id: null,
                company_id: null,
            }
        });

        let getProduct= () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/product/create`)
                .then((res) => {
                    let l = res.data.data;

                    l.companies.forEach(element => {
                        companies.value.push({id:element.id,text:element.name})
                    });
                    l.categories.forEach(element => {
                        categories.value.push({id:element.id,text:element.name})
                    });
                    l.flavors.forEach(element => {
                        flavors_data.value.push({id:element.id,text:element.name})
                    });
                    l.sizes.forEach(element => {
                        sizes_data.value.push({id:element.id,text:element.name})
                    });
                    l.units.forEach(element => {
                        units_data.value.push({id:element.id,text:element.name})
                    });
                    l.suppliers.forEach(element => {
                        suppliers_data.value.push({id:element.id,text:element.name})
                    });
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        };

        let getSubCategory= (id) => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/get_subCategories_by_category_id/${id}`)
                .then((res) => {
                    let l = res.data.data;
                    subCategories.value = l.subCategories;
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        };

        watch(() => addProduct.data.category_id , () => {
            getSubCategory(addProduct.data.category_id)
        })

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
                barcode: {
                    integer
                },
                description_ar: {required},
                description_en: {required},
                image: {
                    required
                },
                files: {
                    required
                },
                suppliers: {
                    required
                },

                category_id: {
                    required,
                    integer
                },
                flavor_id: {
                    integer
                },
                size_id: {
                    required,
                    integer
                },
                main_measurement_unit_id: {
                    required,
                    integer
                },
                sub_measurement_unit_id: {
                    required,
                    integer
                },
                count_unit: {
                    required,
                    integer
                },
                sub_category_id: {
                    required,
                    integer
                },
                company_id: {
                    required,
                    integer
                },
            }
        });

        const v$ = useVuelidate(rules,addProduct.data);

        let preview = (e) => {

            let containerImages = document.querySelector('#container-images');
            if(numberOfImage.value){
                containerImages.innerHTML = '';
            }
            addProduct.data.image = {};

            numberOfImage.value = e.target.files.length;

            addProduct.data.image = e.target.files[0];

            let reader = new FileReader();
            let figure = document.createElement('figure');
            let figcap = document.createElement('figcaption');

            figcap.innerText = addProduct.data.image.name;
            figure.appendChild(figcap);

            reader.onload = () => {
                let img = document.createElement('img');
                img.setAttribute('src',reader.result);
                figure.insertBefore(img,figcap);
            }

            containerImages.appendChild(figure);
            reader.readAsDataURL(addProduct.data.image);

        };

        let preview2 = (e) => {

            let containerImages = document.querySelector('#container-images1');
            if(numberOfImage.value){
                containerImages.innerHTML = '';
            }
            addProduct.data.files = [];

            numberOfImage1.value = e.target.files.length;

            for(let file of e.target.files){

                addProduct.data.files.push(file);
                let reader = new FileReader();
                let figure = document.createElement('figure');
                let figcap = document.createElement('figcaption');

                figcap.innerText = file.name;
                figure.appendChild(figcap);

                reader.onload = () => {
                    let img = document.createElement('img');
                    img.setAttribute('src',reader.result);
                    figure.insertBefore(img,figcap);
                }

                containerImages.appendChild(figure);
                reader.readAsDataURL(file);
            }

        };

        const numberOfImage = ref(0);
        const numberOfImage1 = ref(0);

        onMounted(() => {
            getProduct();
        });


        return {
            loading,
            ...toRefs(addProduct),
            v$,
            preview,
            preview2,
            numberOfImage,
            numberOfImage1,
            companies,
            categories,
            t,
            units_data,
            flavors_data,
            suppliers_data,
            sizes_data,
            subCategories,
            getSubCategory
        };
    },
    methods: {

        storeProduct(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};
                let formData = new FormData();
                formData.append('name_ar',this.data.name_ar);
                formData.append('name_en',this.data.name_en);
                formData.append('barcode',this.data.barcode);
                formData.append('description_ar',this.data.description_ar);
                formData.append('description_en',this.data.description_en);
                formData.append('size_id',this.data.size_id);
                formData.append('flavor_id',this.data.flavor_id ?? '');
                formData.append('category_id',this.data.category_id);
                formData.append('sub_category_id',this.data.sub_category_id);
                formData.append('company_id',this.data.company_id);
                formData.append('main_measurement_unit_id',this.data.main_measurement_unit_id);
                formData.append('sub_measurement_unit_id',this.data.sub_measurement_unit_id);
                formData.append('count_unit',this.data.count_unit);
                formData.append('image',this.data.image);
                for( var i = 0; i < this.numberOfImage1; i++ ){
                    let file = this.data.files[i];
                    formData.append('files[' + i + ']', file);
                }
                for( var i = 0; i < this.data.suppliers.length; i++ ){
                    let supplier = this.data.suppliers[i];
                    formData.append('suppliers[' + i + ']', supplier);
                }



                adminApi.post(`/v1/dashboard/product`,formData)
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
                        console.log(err.response);
                    })
                    .finally(() => {
                        this.loading = false;
                    });

            }
        },
        resetForm(){
            document.querySelector('#container-images').innerHTML = '';
            document.querySelector('#container-images1').innerHTML = '';
            document.querySelector('#mediaPackage1').value = '';
            document.querySelector('#mediaPackage').value = '';
            this.numberOfImage = 0;
            this.numberOfImage1 = 0;
            this.data.name_ar = '';
            this.data.name_en = '';
            this.data.barcode = '';
            this.data.description_ar = '';
            this.data.description_en = '';
            this.data.image= {};
            this.data.files = [];
            this.data.suppliers = [];
            this.data.size_id = null;
            this.data.flavor_id = null;
            this.data.category_id = null;
            this.data.sub_category_id = null;
            this.data.main_measurement_unit_id = null;
            this.data.sub_measurement_unit_id = null;
            this.data.count_unit = null;
            this.data.company_id = null;
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
.custom-textarea {
    height: 150px;
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
