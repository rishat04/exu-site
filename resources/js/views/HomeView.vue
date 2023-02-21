<template>
    <h1 class="font-semibold text-3xl">Поиск</h1>
    <div class="flex flex-col items-center my-10">
        <form class="max-w-md w-full mx-auto mt-16" @submit.prevent="search">
            <div class="flex">
                <div class="flex flex-1">
                    <input
                        id="login"
                        type="text"
                        class="p-3 bg-gray-100 rounded-l-md outline-transparent w-full"
                        v-model.trim="query"
                        placeholder="Введите запрос..."
                    />
                </div>
                <div class="flex flex-col">
                    <button class="bg-green-800/50 px-5 py-3 rounded-r-md text-white">Поиск</button>
                </div>
            </div>
        </form>
        <div class="flex flex-col w-full space-y-5 w-full mt-16">
            <div
                class="flex space-x-4"
                v-for="video in fetchedVideos"
                :key="video.videoId"
            >
                <img
                    :src="video.thumbnails"
                    class="h-25 rounded-lg max-w-[350px]"
                >
                <div class="flex flex-col">
                    <h2 class="font-semibold text-lg">{{ video.title }}</h2>
                    <div class="flex flex-col space-y-2">
                        <a
                            :href="`https://www.youtube.com/channel/${video.channelId}`"
                            target="_blank"
                        >{{ video.channelTitle }}</a>
                        <span>{{ video.viewText }}</span>
                        <span>Duration: {{ convertTime(video.duration) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
            query: "",
            videos: [],
            nextPage: "",
        }
    },
    computed: {
        fetchedVideos() {
            return this.videos;
        },
    },
    methods: {
        search() {
            if (this.loading || !this.query) {
                return;
            }

            fetch(`/api/search?query=${this.query}&nextPage=${this.nextPage}`)
            .then((response) => response.json())
            .then((data) => {
                this.nextPage = data.nextPage;
                this.videos.push(...data.videos);
                this.loading = false;
            });

            this.loading = true;
        },
        convertTime(totalSeconds) {
            let time = "";

            let hours = ~~(totalSeconds / 3600);
            let minutes = ~~((totalSeconds - hours * 3600) / 60);
            let seconds = totalSeconds - hours * 3600 - minutes * 60;

            [hours, minutes, seconds].forEach((item) => {
                time += ((item < 10 ? "0" : "") + item + ":");
            })

            return time.substring(0, time.length - 1);
        }
    },
    mounted() {
        window.onscroll = () => {
            let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
            if (bottomOfWindow) {
                this.search();
            }
        };
    }
};
</script>
