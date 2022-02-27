<template>
    <div class="pagination">
        <RouterLink
            v-if="!isFirstPage"
            :to="`/?page=${currentPage - 1}`"
            class="button"
            >&laquo; prev</RouterLink
        >
        <RouterLink
            v-if="!isLastPage"
            :to="`/?page=${currentPage + 1}`"
            class="button"
            >next &raquo;</RouterLink
        >
    </div>
</template>

<script>
    export default {
        props: {
            // 現在ページ
            currentPage: {
                type: Number,
                required: true,
            },
            // 総ページ数
            lastPage: {
                type: Number,
                required: true,
            },
        },
        computed: {
            // 1ページ目かどうかをチェック
            isFirstPage() {
                return this.currentPage === 1;
            },
            // 最後のページかどうかをチェック
            isLastPage() {
                return this.currentPage === this.lastPage;
            },
        },
    };
</script>
