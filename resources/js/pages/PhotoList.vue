<template>
    <div class="photo-list">
        <div class="grid">
            <Photo
                class="grid__item"
                v-for="photo in photos"
                :key="photo.id"
                :item="photo"
                @like="onLikeClick"
            />
        </div>
        <Pagination :current-page="currentPage" :last-page="lastPage" />
    </div>
</template>

<script>
    import { OK } from "../util";
    import Photo from "../components/Photo.vue";
    import Pagination from "../components/Pagination.vue";

    export default {
        components: {
            Photo,
            Pagination,
        },
        data() {
            return {
                // 写真一覧データ
                photos: [],
                // 現在ページ
                currentPage: 0,
                // 総ページ数
                lastPage: 0,
            };
        },
        methods: {
            // 写真一覧取得
            async fetchPhotos() {
                const response = await axios.get(
                    `/api/photos/?page=${this.$route.query.page}`
                );

                // 通信失敗の場合
                if (response.status !== OK) {
                    this.$store.commit("error/setCode", response.status);
                    return false;
                }

                // response.data でレスポンスの JSON を取得、その中のdataに写真一覧が入っている。
                this.photos = response.data.data;
                this.currentPage = response.data.current_page;
                this.lastPage = response.data.last_page;
            },
            // <Photo> から発行された like イベントを受け取る
            onLikeClick({ id, liked }) {
                if (!this.$store.getters["auth/check"]) {
                    alert("いいね機能を使うにはログインしてください。");
                    return false;
                }

                if (liked) {
                    this.unlike(id);
                } else {
                    this.like(id);
                }
            },
            async like(id) {
                // いいね付与 API への通信
                const response = await axios.put(`/api/photos/${id}/like`);

                // 通信エラーの場合
                if (response.status !== OK) {
                    this.$store.commit("error/setCode", response.status);
                    return false;
                }

                // ページ上の写真の見た目（いいね数とボタンの色）を変えるため、this.photo のデータを更新。
                this.photos = this.photos.map((photo) => {
                    if (photo.id === response.data.photo_id) {
                        photo.likes_count += 1;
                        photo.liked_by_user = true;
                    }
                    return photo;
                });
            },
            async unlike(id) {
                // いいね削除 API への通信
                const response = await axios.delete(`/api/photos/${id}/like`);

                // 通信エラーの場合
                if (response.status !== OK) {
                    this.$store.commit("error/setCode", response.status);
                    return false;
                }

                // this.photo のデータを更新し、見た目を変える。
                this.photos = this.photos.map((photo) => {
                    if (photo.id === response.data.photo_id) {
                        photo.likes_count -= 1;
                        photo.liked_by_user = false;
                    }
                    return photo;
                });
            },
        },
        watch: {
            // $route を監視
            $route: {
                // ページが切り替わったときに fetchPhotos が実行
                async handler() {
                    await this.fetchPhotos();
                },
                // immediateオプションをtrue→コンポーネントが生成されたタイミングでも実行
                // コンポーネントは同じだがページが異なる場合を考慮?
                immediate: true,
            },
        },
        props: {
            page: {
                type: Number,
                required: false,
                default: 1,
            },
        },
    };
</script>
