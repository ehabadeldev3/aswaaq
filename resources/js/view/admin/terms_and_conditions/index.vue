<template>
    <div :class="['page-wrapper', this.$i18n.locale == 'ar' ? 'page-wrapper-ar' : '']">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('global.Terms And Conditions') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard' }">
                                    {{ $t('dashboard.Dashboard') }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t('global.Terms And Conditions') }}</li>
                        </ul>
                    </div>

                </div>
            </div>
            <!-- /Page Header -->
            <!-- Table -->

            <loader v-if="loading" />
            <notifications :position="this.$i18n.locale == 'ar' ? 'top left' : 'top right'" />
            <div class="container">
                <form @submit.prevent="saveTermsAndConditions">

                    <div class="form-group">
                        <label for="editor">{{ $t("global.TermsAndConditionsInArabic") }}</label>
                        <textarea id="editor" class="form-control" :class="{
                            'is-valid': terms_ar,
                        }"></textarea>

                    </div>

                    <div class="form-group">
                        <label for="editor1">{{ $t("global.TermsAndConditionsInEnglish") }}</label>

                        <textarea id="editor1" class="form-control" :class="{
                            'is-valid': terms_en,
                        }"></textarea>

                    </div>


                    <button type="submit" class="btn btn-primary">
                        {{ $t("global.Submit") }}
                    </button>
                </form>
            </div>

        </div>
        <!-- /Page Wrapper -->
    </div>
</template>

<script>
import { onMounted, watch, ref, computed } from "vue";
import { useStore } from "vuex";
import adminApi from "../../../api/adminAxios";
import { useI18n } from 'vue-i18n';
import { notify } from "@kyvg/vue3-notification";

export default {
    name: "index",
    setup() {
        const { t } = useI18n({});

        let terms_ar = ref("");
        let terms_en = ref("");
        let loading = ref(false);


        let getTermsAndConditions = (page = 1) => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/terms_and_conditions`)
                .then((res) => {
                    terms_ar.value = res.data.terms_ar
                    terms_en.value = res.data.terms_en
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    ClassicEditor.create(document.querySelector('#editor'), {
                        language: 'ar'
                    }).then(editor => {
                        if(terms_ar.value.length>0)
                        editor.setData(terms_ar.value)
                        editor.model.document.on('change:data', () => {
                            terms_ar.value = editor.getData();
                        })
                    }).catch(error => {
                        console.error(error);
                    });
                    ClassicEditor.create(document.querySelector('#editor1'), {
                        language: 'en'
                    }).then(editor1 => {
                        if(terms_en.value.length > 0)
                        editor1.setData(terms_en.value)
                        editor1.model.document.on('change:data', () => {
                            terms_en.value = editor1.getData();
                        })
                    }).catch(error => {
                        console.error(error);
                    });
                    loading.value = false;
                });
        }
        let saveTermsAndConditions = (page = 1) => {
            loading.value = true;

            adminApi.post(`/v1/dashboard/terms_and_conditions`, { 'terms_ar': terms_ar.value, 'terms_en': terms_en.value })
                .then((res) => {
                    notify({
                        title: `${t('global.EditSuccessfully')} <i class="fas fa-check-circle"></i>`,
                        type: "success",
                        duration: 5000,
                        speed: 2000
                    });
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {

                    loading.value = false;
                });
        }

        onMounted(() => {

            getTermsAndConditions();
        });

        return { loading, terms_ar, terms_en, saveTermsAndConditions };

    },
    data() {
        return {
            locale: localStorage.getItem("langAdmin")
        }
    }
}
</script>

<style scoped>
.card {
    position: relative;
}



.custom {
    border: 1px solid #D7D7D7;
    padding: 2px;
    border-radius: 5px;
    width: 35%;
}

.btn {
    color: #fff;
}

.hover:hover {
    border: 2px solid;
    padding: 3px;
    border-radius: 7px;
}
</style>


