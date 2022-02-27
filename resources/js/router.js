import Vue from "vue";
import VueRouter from "vue-router";

// ページコンポーネントをインポートする
import PhotoList from "./pages/PhotoList.vue";
import Login from "./pages/Login.vue";
import SystemError from "./pages/errors/System.vue";
import PhotoDetail from "./pages/PhotoDetail.vue";
import store from "./store";
import NotFound from "./pages/errors/NotFound.vue";

// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter);

// パスとコンポーネントのマッピング
const routes = [
    // 写真一覧ページ
    {
        path: "/",
        component: PhotoList,
        // <PhotoList>にpage という props として渡されるようになる
        props: (route) => {
            const page = route.query.page;
            // 整数と解釈されない値は「1」と見なしている。
            return {
                page: /^[1-9][0-9]*$/.test(page) ? page * 1 : 1,
            };
        },
    },
    // 写真詳細ページ
    {
        path: "/photos/:id",
        component: PhotoDetail,
        // trueだと:idが<PhotoDetail>コンポーネントにpropsとして渡される
        props: true,
    },
    // ログイン/新規登録ページ
    {
        path: "/login",
        component: Login,
        // ログイン画面に切り替わる直前に呼び出す。
        beforeEnter(to, from, next) {
            // もしログインしていたらトップに、そうでなければそのままログイン画面に遷移。
            if (store.getters["auth/check"]) {
                next("/");
            } else {
                next();
            }
        },
    },
    // システムエラーページ
    {
        path: "/500",
        component: SystemError,
    },
    // 404ページ
    {
        // 定義されたルート以外のパス
        path: "*",
        component: NotFound,
    },
];

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: "history",
    // ページが変わるごとにスクロール位置が先頭に戻る。
    scrollBehavior() {
        return { x: 0, y: 0 };
    },
    routes,
});

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router;
