import Vue from "vue";
// ルーティングの定義をインポートする
import router from "./router";
import store from "./store";
// ルートコンポーネントをインポートする
import App from "./App.vue";
import "./bootstrap";

const createApp = async () => {
    // Vue インスタンス生成前にログインチェック
    await store.dispatch("auth/currentUser");

    new Vue({
        el: "#app",
        router,
        store,
        components: { App },
        template: "<App />",
    });
};

createApp();
