// CSRF 対策の実装

export const OK = 200;
export const CREATED = 201;
export const INTERNAL_SERVER_ERROR = 500;
export const UNPROCESSABLE_ENTITY = 422;
// 認証切れの場合のレスポンスコード
export const UNAUTHORIZED = 419;
export const NOT_FOUND = 404;

/**
 * クッキーの値を取得する
 * @param {String} searchKey 検索するキー
 * @returns {String} キーに対応する値
 */
export function getCookieValue(searchKey) {
    if (typeof searchKey === "undefined") {
        return "";
    }

    let val = "";

    // document.cookieで、クッキーは、name=12345;token=67890;key=abcdeの形式で取得。
    document.cookie.split(";").forEach((cookie) => {
        const [key, value] = cookie.split("=");
        if (key === searchKey) {
            return (val = value);
        }
    });

    return val;
}
