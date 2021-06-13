<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        <header class="bg-yellow-500 shadow">
            <div class="mx-auto py-5 px-4">
                <h2 class="font-semibold text-xl text-white leading-tight">達成履歴</h2>
            </div>
        </header>
        <!-- Page Content -->
        <main>
            <template v-if="!loading">
                <div class="mt-6 flex justify-center">
                    <loader></loader>
                </div>
            </template>
            <template v-else>
                <div
                    class="mx-2 mt-6 shadow rounded overflow-hidden"
                    :class="task.is_completed ? 'bg-gray-200' : 'bg-white'"
                    v-for="task in tasks"
                    :key="task.id"
                >
                    <div class="p-4 flex items-center justify-between">
                        <template v-if="!task.is_edit">
                            <h3
                                class="flex text-lg font-bold text-gray-800 leading-tight"
                                :class="task.is_completed ? '' : 'ml-5'"
                            >
                                <svg
                                    v-if="task.is_completed"
                                    class="h-5 w-5 text-yellow-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                    /></svg
                                >{{ task.name }}
                            </h3>
                        </template>
                    </div>
                </div>
            </template>
        </main>
    </div>
</template>

<script>
import Loader from '../components/task/Loader'
import TaskButton from '../components/task/Button'
import TaskLinkButton from '../components/task/LinkButton'
import dayjs from 'dayjs'

export default {
    components: { TaskButton, TaskLinkButton, Loader },

    props: { auth: Object, errors: Object, liffId: String },

    async mounted() {
        await liff.init({ liffId: this.liffId })
        if (!liff.isLoggedIn()) liff.login()

        try {
            const accessToken = liff.getAccessToken()
            this.lineUser = await axios
                .get(`/line-login`, { headers: { Authorization: `Bearer ${accessToken}` } })
                .then((response) => response.data)

            const thisMonth = dayjs().format('YYYY-MM')
            this.tasks = await axios.get(`/tasks/history/${thisMonth}`).then((response) => response.data)
        } catch (error) {
            alert('表示に失敗した！またやり直してみてもらえると助かるな！')
        } finally {
            this.loading = true
        }
    },

    data() {
        return {
            loading: false,
            lineUser: {},
            tasks: [],
        }
    },

    methods: {},
}
</script>
