<template>
    <h1 class="font-semibold text-3xl">Поиск</h1>
    <!--   Search form     -->
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
        <!--    Search filters    -->
    <div class="flex mt-16">
        <div class="w-1/4 p-4">
            <Filters @changed="updateFilterParams" />
        </div>
        <div class="flex flex-col w-full p-4 space-y-5 w-full">
            <div
                class="flex space-x-4"
                v-for="video in fetchedVideos"
                :key="video.videoId"
            >
                <img
                    :src="video.thumbnail"
                    class="h-25 rounded-lg max-w-[350px]"
                >
                <div class="flex flex-col">
                    <h2 class="font-semibold text-lg">{{ video.title }}</h2>
                    <div class="flex flex-col space-y-2">
                        <a
                            :href="`https://www.youtube.com/channel/${video.channelId}`"
                            target="_blank"
                        >{{ video.channelTitle }}</a>
                        <span>{{ video.views }}</span>
                        <span>Duration: {{ convertTime(video.duration) }}</span>
                        <span>Published date: {{ video.date }}</span>
                        <div class="space-x-2">
                            <span
                                class="py-1 px-3 rounded-md cursor-default bg-gray-100"
                                v-for="keyword in video.keywords"
                            >{{ keyword }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!--    Search results    -->
    <div
        class="fixed w-20 h-20 rounded-full bg-gray-100 bottom-6 right-6 flex items-center justify-center drop-shadow-xl"
    >
        <span class="text-xl">{{ fetchedVideos.length }}</span>
    </div>
</template>

<script>
import Filters from "@/components/Filters.vue";

export default {
    components: { Filters },
    data() {
        return {
            loading: false,
            query: "",
            videos: [],
            nextPage: "",
            filterParams: "",
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

            fetch(`/api/search?query=${this.query}&nextPage=${this.nextPage}&${this.filterParams}`)
            .then((response) => response.json())
            .then((data) => {
                this.nextPage = data.nextPage;
                this.videos.push(...data.videos);
                this.loading = false;
            });

            this.loading = true;
        },
        updateFilterParams(filterParams) {
            console.log(filterParams)
            this.filterParams = filterParams;
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
