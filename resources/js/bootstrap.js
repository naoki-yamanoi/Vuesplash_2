// Ajax 通信で用いる Axios ライブラリの設定

import { getCookieValue } from "./util";

window.axios = require("axios");

// Ajaxリクエストであることを示すヘッダーを付与する
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.axios.interceptors.request.use((config) => {
    // クッキーからトークンを取り出してヘッダーに添付する
    config.headers["X-XSRF-TOKEN"] = getCookieValue("XSRF-TOKEN");

    return config;
});

// response インターセプターはレスポンスを受けた後の処理を上書きする。
// API 呼び出しが増えると重複してしまうのでインターセプターにまとめる。
window.axios.interceptors.response.use(
    (response) => response, //成功時の処理
    (error) => error.response || error //失敗時の処理
);
