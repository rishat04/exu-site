<template>
    <div class="flex flex-col w-full rounded-md space-y-7">
        <div class="space-y-2">
            <div class="flex">
                <input
                    @input="updateFilterParams"
                    v-model.trim="viewsFrom"
                    type="text"
                    placeholder="Views from"
                    class="px-3 py-2 bg-gray-100 outline-transparent rounded-md"
                />
            </div>
            <div class="flex">
                <input
                    @input="updateFilterParams"
                    v-model.trim="viewsTo"
                    type="text"
                    placeholder="Views to"
                    class="px-3 py-2 bg-gray-100 outline-transparent rounded-md"
                />
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex">
                <input
                    @input="updateFilterParams"
                    v-model.trim="subsFrom"
                    type="text"
                    placeholder="Subs from"
                    class="px-3 py-2 bg-gray-100 outline-transparent rounded-md"
                />
            </div>
            <div class="flex">
                <input
                    @input="updateFilterParams"
                    v-model.trim="subsTo"
                    type="text"
                    placeholder="Subs to"
                    class="px-3 py-2 bg-gray-100 outline-transparent rounded-md"
                />
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex">
                <input
                    @input="updateFilterParams"
                    v-model.trim="durationFrom"
                    type="text"
                    placeholder="Duration from"
                    class="px-3 py-2 bg-gray-100 outline-transparent rounded-md"
                />
            </div>
            <div class="flex">
                <input
                    @input="updateFilterParams"
                    v-model.trim="durationTo"
                    type="text"
                    placeholder="Duration to"
                    class="px-3 py-2 bg-gray-100 outline-transparent rounded-md"
                />
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex">
                <input
                    @input="updateFilterParams"
                    v-model.trim="vsrFrom"
                    type="text"
                    placeholder="VSR from"
                    class="px-3 py-2 bg-gray-100 outline-transparent rounded-md"
                />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            viewsFrom: null,
            viewsTo: null,
            subsFrom: null,
            subsTo: null,
            durationFrom: null,
            durationTo: null,
            vsrFrom: null,
        }
    },
    mounted() {

    },
    methods: {
        handleValue(value) {
            return value?.replace(/[^0-9]/, "") || null;
        },
        updateFilterParams(event) {
            event.target.value = this.handleValue(event.target.value);
            const urlSearchParams = new URLSearchParams();
            for (let name in this.$data) {
                urlSearchParams.append(name, this.handleValue(this[name]));
            }
            this.$emit("changed", urlSearchParams.toString());
        }
    },
};
</script>
