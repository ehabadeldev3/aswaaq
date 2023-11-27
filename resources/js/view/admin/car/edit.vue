<template>
    <div :class="['page-wrapper', this.$i18n.locale == 'ar' ? 'page-wrapper-ar' : '']">

        <div class="content container-fluid">

            <notifications position="top left" />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('global.Cars') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link
                                    :to="{ name: 'indexCar' }">{{ $t('global.Cars') }}</router-link></li>
                            <li class="breadcrumb-item active">{{ $t('global.Update Car') }}</li>
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
                                <router-link :to="{ name: 'indexCar' }" class="btn btn-custom btn-dark">
                                    {{ $t('global.back') }}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['name_ar']">{{ errors['name_ar'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['name_en']">{{ errors['name_en'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['plate_number']">{{ errors['plate_number'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['phone']">{{ errors['phone'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['description_en']">{{ errors['description_en'][0] }}<br /> </div>
                                    <div class="alert alert-danger text-center" v-if="errors['description_ar']">{{ errors['description_ar'][0] }}<br /> </div>

                                    <form @submit.prevent="editCar" class="needs-validation">
                                        <div class="form-row row">

                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom01">{{ $t('treasury.NameAr') }}</label>
                                                <input type="text" class="form-control" v-model.trim="v$.name_ar.$model"
                                                    id="validationCustom01" :placeholder="$t('treasury.NameAr')"
                                                    :class="{ 'is-invalid': v$.name_ar.$error, 'is-valid': !v$.name_ar.$invalid }">
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.name_ar.required.$invalid">{{ $t('global.NameIsRequired') }}<br />
                                                    </span>
                                                    <span
                                                        v-if="v$.name_ar.minLength.$invalid">{{ $t('global.NameIsMustHaveAtLeast') }}
                                                        {{ v$.name_ar.minLength.$params.min }} {{ $t('global.Letters') }}
                                                        <br /></span>
                                                    <span
                                                        v-if="v$.name_ar.maxLength.$invalid">{{ $t('global.NameIsMustHaveAtMost') }}
                                                        {{ v$.name_ar.maxLength.$params.max }} {{ $t('global.Letters') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom02">{{ $t('treasury.NameEn') }}</label>
                                                <input type="text" class="form-control" v-model.trim="v$.name_en.$model"
                                                    id="validationCustom02" :placeholder="$t('treasury.NameEn')"
                                                    :class="{ 'is-invalid': v$.name_en.$error, 'is-valid': !v$.name_en.$invalid }">
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.name_en.required.$invalid">{{ $t('global.NameIsRequired') }}<br />
                                                    </span>
                                                    <span
                                                        v-if="v$.name_en.minLength.$invalid">{{ $t('global.NameIsMustHaveAtLeast') }}
                                                        {{ v$.name_en.minLength.$params.min }} {{ $t('global.Letters') }}
                                                        <br /></span>
                                                    <span
                                                        v-if="v$.name_en.maxLength.$invalid">{{ $t('global.NameIsMustHaveAtMost') }}
                                                        {{ v$.name_en.maxLength.$params.max }} {{ $t('global.Letters') }}
                                                    </span>
                                                </div>
                                            </div>


                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom032">{{ $t('global.Phone Number') }} </label>
                                                <input type="text" class="form-control" v-model.trim="v$.phone.$model"
                                                    id="validationCustom032"
                                                    :class="{ 'is-invalid': v$.phone.$error, 'is-valid': !v$.phone.$invalid }">
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span
                                                        v-if="v$.phone.integer.$invalid">{{ $t('global.ThisFieldMustBeANumber') }}
                                                        <br /></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom0333">{{ $t('global.Plate Number') }} </label>
                                                <input type="text" class="form-control"
                                                    v-model.trim="v$.plate_number.$model" id="validationCustom0333"
                                                    :class="{ 'is-invalid': v$.plate_number.$error, 'is-valid': !v$.plate_number.$invalid }">
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>
                                                <div class="invalid-feedback">
                                                    <span v-if="v$.plate_number.required.$invalid"> {{
                                                        $t('global.IsRequired') }}<br /> </span>
                                                </div>
                                            </div>




                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom034">{{ $t('global.DescriptionAr') }}</label>
                                                <textarea type="text" class="form-control custom-textarea"
                                                    v-model.trim="v$.description_ar.$model" id="validationCustom034"
                                                    :placeholder="$t('global.DescriptionAr')"
                                                    :class="{ 'is-invalid': v$.description_ar.$error, 'is-valid': !v$.description_ar.$invalid }"></textarea>
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>

                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="validationCustom034">{{ $t('global.DescriptionEn') }}</label>
                                                <textarea type="text" class="form-control custom-textarea"
                                                    v-model.trim="v$.description_en.$model" id="validationCustom034"
                                                    :placeholder="$t('global.DescriptionEn')"
                                                    :class="{ 'is-invalid': v$.description_en.$error, 'is-valid': !v$.description_en.$invalid }"></textarea>
                                                <div class="valid-feedback">{{ $t('global.LooksGood') }}</div>

                                            </div>

                                            <div class="col-md-3 row flex-fill">
                                                <div class="btn btn-outline-primary waves-effect">
                                                    <span>
                                                        {{ $t('global.Car Image') }}
                                                        <i class="fas fa-cloud-upload-alt ml-3" aria-hidden="true"></i>
                                                    </span>
                                                    <input name="image" type="file" @change="preview" id="mediaPackage"
                                                        accept="image/png,jepg,jpg">
                                                </div>

                                                <p class="num-of-files">{{ numberOfImage ? numberOfImage + ' Files Selected'
                                                    : 'No Files Chosen' }}</p>
                                                <div class="container-images" id="container-images"
                                                    v-show="data.image && numberOfImage"></div>
                                                    <div class="container-images" v-show="!numberOfImage">
                                                    <figure>
                                                        <figcaption>
                                                            <img :src="`${image?? '/admin/img/company/img-1.png'}`">
                                                        </figcaption>
                                                    </figure>
                                                </div>
                                            </div>

                                            <div class="col-md-9 row flex-fill">
                                                <div class="btn btn-outline-primary waves-effect">
                                                    <span>
                                                        {{ $t('global.License Image') }}
                                                        <i class="fas fa-cloud-upload-alt ml-3" aria-hidden="true"></i>
                                                    </span>
                                                    <input name="license" type="file" @change="preview2" id="mediaPackage1"
                                                        accept="image/png,jepg,jpg">
                                                </div>

                                                <p class="num-of-files">{{ numberOfImage1 ? numberOfImage1 + ' Files Selected' : 'No Files Chosen' }}</p>
                                                <div class="container-images" id="container-images1"
                                                    v-show="data.license && numberOfImage1"></div>
                                                <div class="container-images" v-show="!numberOfImage1">
                                                    <figure>
                                                        <figcaption>
                                                            <img :src="`${license?? '/admin/img/company/img-1.png'}`">
                                                        </figcaption>
                                                    </figure>
                                                </div>
                                            </div>


                                        </div>


                                        <button class="btn btn-primary" type="submit">{{ $t('global.Update') }}</button>
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
import { computed, onMounted, reactive, toRefs, ref, watch } from "vue";
import useVuelidate from '@vuelidate/core';
import { required, minLength, maxLength, integer, numeric } from '@vuelidate/validators';
import adminApi from "../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import { useI18n } from 'vue-i18n';


export default {
    name: "editCar",
    data() {
        return {
            errors: {}
        }
    },
    props: ["id"],
    setup(props) {

        const { id } = toRefs(props)
        // get create Package
        let { t } = useI18n();
        let loading = ref(false);

        let images = ref([]);
        let image = ref('');
        let license = ref('');

        //start design
        let addCar = reactive({
            data: {
                name_ar : '',
                name_en : '',
                description_ar : '',
                description_en : '',
                image : {},
                license : {},
                phone: null,
                plate_number: null,
            }
        });

        let getCar = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/car/${id.value}/edit`)
                .then((res) => {
                    let l = res.data.data;
                    addCar.data.name_ar = l.car.name_ar;
                    addCar.data.name_en = l.car.name_en;
                    addCar.data.description_ar = l.car.description_ar;
                    addCar.data.description_en = l.car.description_en;
                    addCar.data.phone = l.car.phone;
                    addCar.data.plate_number = l.car.plate_number;
                    image.value = l.car.image_path;
                    license.value = l.car.license_path;
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        };

        onMounted(() => {
            getCar();
        });



        const rules = computed(() => {
            return {
                name_ar: {
                    minLength: minLength(3),
                    maxLength: maxLength(70),
                    required
                },
                name_en: {
                    minLength: minLength(3),
                    maxLength: maxLength(70),
                    required
                },
                description_ar: {},
                description_en: {},
                image: {

                },
                license: {},
                phone: {
                    integer
                },

                plate_number: {
                     required
                },

            }
        });

        const v$ = useVuelidate(rules, addCar.data);

        let preview = (e) => {

            let containerImages = document.querySelector('#container-images');
            if (numberOfImage.value) {
                containerImages.innerHTML = '';
            }
            addCar.data.image = {};

            numberOfImage.value = e.target.files.length;

            addCar.data.image = e.target.files[0];

            let reader = new FileReader();
            let figure = document.createElement('figure');
            let figcap = document.createElement('figcaption');

            figcap.innerText = addCar.data.image.name;
            figure.appendChild(figcap);

            reader.onload = () => {
                let img = document.createElement('img');
                img.setAttribute('src', reader.result);
                figure.insertBefore(img, figcap);
            }

            containerImages.appendChild(figure);
            reader.readAsDataURL(addCar.data.image);

        };

        let preview2 = (e) => {

            let containerImages = document.querySelector('#container-images1');
            if(numberOfImage.value){
                containerImages.innerHTML = '';
            }
            addCar.data.license = {};

            numberOfImage1.value = e.target.files.length;

            addCar.data.license = e.target.files[0];

            let reader = new FileReader();
            let figure = document.createElement('figure');
            let figcap = document.createElement('figcaption');

            figcap.innerText = addCar.data.license.name;
            figure.appendChild(figcap);

            reader.onload = () => {
                let img = document.createElement('img');
                img.setAttribute('src',reader.result);
                figure.insertBefore(img,figcap);
            }

            containerImages.appendChild(figure);
            reader.readAsDataURL(addCar.data.license);

        };


        const numberOfImage = ref(0);
        const numberOfImage1 = ref(0);

        let delete_image = (idMedia, index) => {
            loading.value = true;

            adminApi.post(`/v1/dashboard/delete_image/${id.value}`, { idMedia: idMedia })
                .then((res) => {
                    images.value.splice(index, 1);
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        }



        return {
            loading,
            ...toRefs(addCar),
            v$,
            t,
            preview,
            preview2,
            numberOfImage,
            numberOfImage1,
            license,
            image,
            images,
            delete_image,
            id
        };
    },
    methods: {
        editCar() {
            this.v$.$validate();

            if (!this.v$.$error) {

                this.loading = true;
                this.errors = {};

                let formData = new FormData();
                formData.append('name_ar',this.data.name_ar);
                formData.append('name_en',this.data.name_en);
                formData.append('description_ar',this.data.description_ar);
                formData.append('description_en',this.data.description_en);
                formData.append('phone',this.data.phone);
                formData.append('plate_number',this.data.plate_number);
                formData.append('image',Object.keys(this.data.image ?? []).length ? this.data.image: '');
                formData.append('license',this.data.license);
                formData.append('_method','put');

                adminApi.post(`/v1/dashboard/car/${this.id}`, formData)
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

.card {
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

.num-of-files {
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

.delete {
    position: absolute;
    top: 6px;
    left: 23px;
    font-size: 16px;
    padding: 0px 5px;
    text-align: center;
    border: 1px solid;
    border-radius: 50%;
}

.sec-body {
    border: 3px solid #fcb00c;
    border-radius: 20px;
    padding: 10px;
}

.sec-head {
    background-color: #fcb00c;
    color: #000;
    border-radius: 11px;
    padding: 5px;
    text-align: center;
    margin-bottom: 8px;
    margin-top: 10px;
}

.sec-body:hover .sec-head {
    border: 3px solid #fcb00c;
    padding: 2px;
    border-radius: 11px;
    background-color: #ffff;
}

.sec-head h3 {
    font-weight: 700;
}
</style>
