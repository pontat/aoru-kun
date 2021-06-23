<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        <header class="bg-yellow-500 shadow">
            <div class="mx-auto py-5 px-4">
                <h2 class="font-semibold text-xl text-white leading-tight">今日のやる気リスト</h2>
            </div>
        </header>
        <!-- Page Content -->
        <main class="px-2 pb-6">
            <template v-if="loading">
                <div class="mt-6 flex justify-center">
                    <loader></loader>
                </div>
            </template>
            <template v-else>
                <div
                    class="mt-6 shadow rounded overflow-hidden"
                    :class="task.is_completed ? 'bg-gray-200' : 'bg-white'"
                    v-for="task in tasks"
                    :key="task.id"
                >
                    <div class="p-4 flex items-center justify-between">
                        <template v-if="!task.is_edit">
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
                            <task-button
                                v-if="!task.is_completed"
                                color="bg-green-600"
                                hoverColor="hover:bg-green-500"
                                class="ml-2 min-w-max"
                                @click="toggleEditForm(task.id)"
                            >
                                編集
                            </task-button>
                            <task-button v-else color="bg-gray-600" class="ml-2 min-w-max">編集</task-button>
                        </template>
                        <template v-if="task.is_edit">
                            <input
                                type="text"
                                class="
                                    w-full
                                    focus:ring-blue-500
                                    focus:border-blue-500
                                    shadow-sm
                                    border-gray-300
                                    rounded
                                "
                                v-model="task.name"
                            />
                            <task-button
                                color="bg-indigo-600"
                                hoverColor="hover:bg-indigo-500"
                                class="ml-2 min-w-max"
                                @click="updateTask(task)"
                            >
                                保存
                            </task-button>
                        </template>
                    </div>
                </div>
                <div class="mt-6 shadow rounded overflow-hidden" v-show="isNewFormShow">
                    <div class="p-4 bg-white space-y-6">
                        <input
                            type="text"
                            class="w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm border-gray-300 rounded"
                            v-model="name"
                        />
                    </div>
                    <div class="p-4 bg-gray-50 flex justify-between">
                        <task-button color="bg-red-600" hoverColor="hover:bg-red-500" @click="toggleNewForm(false)">
                            キャンセル
                        </task-button>
                        <task-button color="bg-indigo-600" hoverColor="hover:bg-indigo-500" @click="createTask()">
                            登録
                        </task-button>
                    </div>
                </div>
                <div class="flex mt-6" v-show="!isNewFormShow">
                    <task-button
                        color="flex justify-center items-center bg-indigo-600"
                        hoverColor="hover:bg-indigo-500"
                        class="w-full mx-2"
                        @click="toggleNewForm(true)"
                    >
                        <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        やる気追加
                    </task-button>
                </div>
            </template>
        </main>
    </div>
</template>

<script>
import dayjs from 'dayjs'
import Loader from '../components/task/Loader'
import TaskButton from '../components/task/Button'
import TaskLinkButton from '../components/task/LinkButton'

export default {
    components: { Loader, TaskButton, TaskLinkButton },

    props: { auth: Object, errors: Object, liffId: String },

    async mounted() {
        await liff.init({ liffId: this.liffId })
        if (!liff.isLoggedIn()) liff.login()

        try {
            const accessToken = liff.getAccessToken()
            this.lineUser = await axios
                .get(`/line-login`, { headers: { Authorization: `Bearer ${accessToken}` } })
                .then((response) => response.data)

            const today = dayjs().format('YYYY-MM-DD')
            this.tasks = await axios.get(`/tasks/${today}`).then((response) => response.data)
        } catch (error) {
            alert('表示に失敗した！またやり直してみてもらえると助かるな！')
        } finally {
            this.loading = false
        }
    },

    data() {
        return {
            loading: true,
            lineUser: {},
            tasks: [],
            isNewFormShow: false,
            name: '',
        }
    },

    methods: {
        toggleEditForm(taskId) {
            const task = this.tasks.find((task) => task.id === taskId)
            task.is_edit = true
        },

        toggleNewForm(bool) {
            this.isNewFormShow = bool
        },

        async createTask() {
            const errors = this.validateTask(this.name)
            if (errors.length) return alert(errors.join(','))

            const task = await axios
                .post('tasks', { name: this.name })
                .catch((error) => alert('登録に失敗した！またやり直してみてもらえると助かるな！'))

            this.tasks = [...this.tasks, task.data]
            this.name = ''
            this.isNewFormShow = false
        },

        async updateTask(editTask) {
            const errors = this.validateTask(editTask.name)
            if (errors.length) return alert(errors.join(','))

            const task = await axios
                .put(`tasks/${editTask.id}`, { name: editTask.name })
                .catch((error) => alert('更新に失敗した！またやり直してみてもらえると助かるな！'))

            const setTask = this.tasks.find((setTask) => setTask.id === task.data.id)
            setTask.name = task.data.name
            setTask.is_edit = false
        },

        validateTask(name) {
            let errors = []
            if (!name.length || !name.match(/\S/g)) errors = [...errors, 'フォームが空っぽだよ！']
            if (name.length > 255) errors = [...errors, '内容は簡潔に！255文字以内にしてね！']
            return errors
        },
    },
}
</script>
