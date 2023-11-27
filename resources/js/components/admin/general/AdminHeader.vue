<template>
    <loader v-if="loading" />
    <div class="header">

        <!-- Logo -->
        <div :class="[this.$i18n.locale == 'ar' ?  'header-left-ar' : '','header-left']">
            <router-link :to="{name:'dashboard'}" class="logo">
                <img src="/web/img/logo.png" class="big" alt="Logo" style="height:50px!important">
            </router-link>
            <router-link :to="{name:'dashboard'}" class="logo logo-small">
                <img src="/web/img/logo.png" :class="[this.$i18n.locale == 'ar' ? 'img-ar' :'img']" alt="Logo" width="30" height="30" style="height:50px!important">
            </router-link>
            <!-- Sidebar Toggle -->
            <a href="javascript:void(0);" id="toggle_btn">
                <i :class="[this.$i18n.locale == 'ar' ? 'feather-chevrons-left' : 'feather-chevrons-right']"></i>
            </a>
            <!-- /Sidebar Toggle -->

            <!-- Mobile Menu Toggle -->
            <a class="mobile_btn" id="mobile_btn">
                <i :class="[this.$i18n.locale == 'ar' ? 'feather-chevrons-left' : 'feather-chevrons-right']"></i>
            </a>
            <!-- /Mobile Menu Toggle -->
        </div>
        <!-- /Logo -->
        <!-- Header Menu -->
        <ul :class="['nav' , this.$i18n.locale == 'en' ? ' user-menu':'user-menu-ar']">

            <!-- Notifications -->
            <Notification />
            <!-- /Notifications -->

            <!-- User Menu -->

            <li class="nav-item dropdown has-arrow main-drop">
                <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                    <img style="width:25px" class="left-image" :src="this.$i18n.locale == 'ar'
                                ? '/images/ar-eg.png'
                                : '/images/en-us.png'
                            " alt="Language Images">
                        <span class="menu-item mx-2">{{ this.$i18n.locale == "ar" ? "العربية" : "English"
                        }}</span>
                </a>
                <div class="dropdown-menu">
                    <a @click.prevent="changeLocale('en')" class="dropdown-item">
                        <img class="left-image" style="width:25px" src="/images/en-us.png"
                            alt="Language Images">
                        <span class="menu-item mx-2">{{ $t('global.English') }}</span>
                    </a>
                    <a @click.prevent="changeLocale('ar')" class="dropdown-item">
                        <img class="left-image" style="width:25px" src="/images/ar-eg.png"
                            alt="Language Images">
                        <span class="menu-item mx-2">{{ $t('global.Arabic') }}</span>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown has-arrow main-drop">
                <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                    <span class="user-img">
                        <img src="/admin/img/profiles/avatar-07.jpg" alt="">
                        <span class="status online"></span>
                    </span>
                </a>
                <div class="dropdown-menu">
                    <router-link :to="{name:'indexProfile'}" class="dropdown-item"><i data-feather="user" class="me-1"></i> {{$t('global.Profile')}}</router-link>
                    <a class="dropdown-item" href="#" @click="logout"><i data-feather="log-out" class="me-1"></i> Logout</a>
                </div>
            </li>
            <!-- /User Menu -->

        </ul>
        <!-- /Header Menu -->

    </div>
</template>

<script>
    import Notification from './notification.vue';
    import {useStore} from "vuex";
    import {computed,ref,onMounted} from "vue";
    import Cookies from "js-cookie";

export default {
    data() {
        return {
            locale: localStorage.getItem("langAdmin") ?? "ar",
        };
    },
    mounted() {
        this.loc = this.locale == "ar" ? "en" : "ar";
    },
    methods: {
        changeLocale(lang) {
            this.loading = true
            this.locale = lang;
            window.axios.defaults.headers.common["Accept-Language"] = this.locale;
            localStorage.setItem("langAdmin",this.locale)
            if (this.locale.toLowerCase() == 'ar' || this.locale.toLowerCase() == 'en' ) {
                this.$i18n.locale = this.locale.toLowerCase();
            }
            this.loading = false
            this.$emit('changeLanguageEvent')
            // change dir to rtl
            this.changeLangClass(this.locale);
        },
    },
    setup(){
        const store = useStore();
        const userId = ref(0);

        let loading = computed(() => {
            return store.getters['authAdmin/loading'];
        });

        onMounted(() => {
            changeLangClass(localStorage.getItem("langAdmin"));
            if (Cookies.get("tokenAdmin")){
                userId.value = JSON.parse(localStorage.getItem("user")).id
            }
        });

        function logout(){
            store.dispatch('authAdmin/logout');
        }


        const changeLangClass = (lang) => {
            // change dir to rtl
            if (lang == "ar") {
                $("#admin").addClass("rtl");
                $("#admin").removeClass("ltr");
                $('#admin').attr('dir','rtl');
            } else if (lang == "en") {
                $("#admin").addClass("ltr");
                $("#admin").removeClass("rtl");
                $('#admin').attr('dir','ltr');
            }
        };

        return {logout,loading,userId,changeLangClass};
    },
    components: {
        Notification
    }
}
</script>

<style>
.big {
    max-height: 90px !important;
    width: auto;
    height: 50px!important;
}
.mini-sidebar .header-left .logo img.img-ar{
    margin-left: 3px;
    margin-right: -22px;
}

.header .header-left{
    background: #fff;
}

.download{
    border: 2px solid;
    padding: 15px 10px;
    font-size: 16px;
    font-weight: 900;
    border-radius: 10px;
}
.dropdown-item{
    cursor:pointer
}
</style>
