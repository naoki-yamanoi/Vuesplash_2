<template>
    <div class="photo">
        <figure class="photo__wrapper">
            <!-- 属性値を単なる文字列ではなく JavaScript の値として渡したい場合は属性名の頭にコロン : を付加 -->
            <img
                class="photo__image"
                :src="item.url"
                :alt="`Photo by ${item.owner.name}`"
            />
        </figure>
        <!-- マウスオーバー時の黒半透明な背景 -->
        <RouterLink
            class="photo__overlay"
            :to="`/photos/${item.id}`"
            :title="`View the photo by ${item.owner.name}`"
        >
            <div class="photo__controls">
                <button
                    class="photo__action photo__action--like"
                    :class="{ 'photo__action--liked': item.liked_by_user }"
                    title="Like photo"
                    @click.prevent="like"
                >
                    <i class="icon ion-md-heart"></i>{{ item.likes_count }}
                </button>
                <a
                    class="photo__action"
                    title="Download photo"
                    @click.stop
                    :href="`/photos/${item.id}/download`"
                >
                    <i class="icon ion-md-arrow-round-down"></i>
                </a>
            </div>
            <div class="photo__username">
                {{ item.owner.name }}
            </div>
        </RouterLink>
    </div>
</template>

<script>
    export default {
        // props は型など定義をできる限り詳細に書くことが公式で「必須」にカテゴライズされている
        props: {
            item: {
                type: Object,
                required: true,
            },
        },
        methods: {
            // クリックされた写真のIDといいね済みかどうかをデータとしてイベント発行先に渡す。
            like() {
                this.$emit("like", {
                    id: this.item.id,
                    liked: this.item.liked_by_user,
                });
            },
        },
    };
</script>
