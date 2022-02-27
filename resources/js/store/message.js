const state = {
    content: "",
};

const mutations = {
    setContent(state, { content, timeout }) {
        state.content = content;

        if (typeof timeout === "undefined") {
            timeout = 3000;
        }

        // 一定時間経過後に自動的にクリア
        setTimeout(() => (state.content = ""), timeout);
    },
};

export default {
    namespaced: true,
    state,
    mutations,
};
