<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        <header class="bg-yellow-500 shadow">
            <div class="mx-auto py-5 px-4">
                <h2 class="font-semibold text-xl text-white leading-tight">達成履歴</h2>
            </div>
        </header>
        <!-- Page Content -->
        <main class="px-2 pb-6">
            <div class="mt-6 flex">
                <select
                    type="text"
                    class="mr-3 w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm border-gray-300 rounded"
                    v-model="targetYear"
                >
                    <option v-for="year in years" :key="year">{{ year }}</option>
                </select>
                <select
                    type="text"
                    class="w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm border-gray-300 rounded"
                    v-model="targetMonth"
                >
                    <option v-for="month in months" :key="month">{{ month }}</option>
                </select>
            </div>
            <template v-if="!loading">
                <div class="mt-6 flex justify-center">
                    <loader></loader>
                </div>
            </template>
            <template v-else>
                <div v-for="(tasks, key) in groupByTasks" :key="key">
                    <h3 class="mt-6 flex justify-center text-lg font-bold text-gray-800 leading-tight">
                        {{ formatDate(key) }}
                    </h3>
                    <div
                        class="mt-6 shadow rounded overflow-hidden"
                        :class="task.is_completed ? 'bg-gray-200' : 'bg-white'"
                        v-for="task in tasks"
                        :key="task.id"
                    >
                        <div class="p-4 flex items-center justify-between">
                            <svg
                                v-if="task.is_completed"
                                class="flex-none h-5 w-5 text-yellow-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                />
                            </svg>
                            <h3
                                class="flex-1 text-lg font-bold text-gray-800 leading-tight"
                                :class="task.is_completed ? '' : 'ml-5'"
                            >
                                {{ task.name }}
                            </h3>
                        </div>
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
import 'dayjs/locale/ja'

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
            this.groupByTasks = await axios.get(`/tasks/history/${thisMonth}`).then((response) => response.data)
        } catch (error) {
            alert('表示に失敗した！またやり直してみてもらえると助かるな！')
        } finally {
            this.loading = true
        }
    },

    data() {
        return {
            loading: false,
            targetYear: dayjs().year(),
            targetMonth: dayjs().month() + 1,
            months: [...Array(12).keys()].map((i) => ++i),
            years: [...Array(3).keys()].map((i) => dayjs().year() - i),
            lineUser: {},
            groupByTasks: [],
        }
    },

    watch: {
        targetYear: function () {
            this.fetchGroupByTasks()
        },
        targetMonth: function () {
            this.fetchGroupByTasks()
        },
    },

    methods: {
        formatDate(date) {
            dayjs.locale('ja')
            return dayjs(date).format('YYYY/MM/DD(ddd)')
        },
        async fetchGroupByTasks() {
            try {
                this.loading = false
                const thisMonth = dayjs(`${this.targetYear}-${this.targetMonth}`).format('YYYY-MM')
                this.groupByTasks = await axios.get(`/tasks/history/${thisMonth}`).then((response) => response.data)
            } catch (error) {
                alert('表示に失敗した！またやり直してみてもらえると助かるな！')
            } finally {
                this.loading = true
            }
        },
    },
}
</script>
