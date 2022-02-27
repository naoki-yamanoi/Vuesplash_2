<template>
    <nav class="navbar">
        <!-- RouterLinkはVue Router から提供されているコンポーネント -->
        <!-- <a>との違いは通常の画面遷移（＝サーバサイドへの GET リクエスト）が発生しない -->
        <RouterLink class="navbar__brand" to="/"> Vuesplash </RouterLink>
        <div class="navbar__menu">
            <div v-if="isLogin" class="navbar__item">
                <button class="button" @click="showForm = !showForm">
                    <i class="icon ion-md-add"></i>
                    Submit a photo
                </button>
            </div>
            <span v-if="isLogin" class="navbar__item"> username </span>
            <div v-else class="navbar__item">
                <RouterLink class="button button--link" to="/login">Login / Register</RouterLink>
            </div>
        </div>
        <PhotoForm v-model="showForm" />
    </nav>
</template>

<script>
    import PhotoForm from "./PhotoForm.vue";

    export default {
        components: {
            PhotoForm,
        },
        data() {
            return {
                showForm: false,
            };
        },
        computed: {
            isLogin() {
                return this.$store.getters["auth/check"];
            },
            username() {
                return this.$store.getters["auth/username"];
            },
        },
    };
</script>
