<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        <header class="bg-indigo-600 shadow">
            <div class="mx-auto py-6 px-4">
                <h2 class="font-semibold text-xl text-white leading-tight">今日のやる気リスト</h2>
            </div>
        </header>
        <!-- Page Content -->
        <main>
            <template v-if="!loading">
                <div class="mt-6 flex justify-center">
                    <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-32 w-32"></div>
                </div>
            </template>
            <template v-else>
                <div class="mx-2 mt-6 bg-white shadow rounded overflow-hidden" v-for="task in tasks" :key="task.id">
                    <div class="p-4 flex items-center justify-between">
                        <template v-if="!task.is_edit">
                            <h3 class="text-lg font-bold text-gray-800 leading-tight">
                                {{ task.name }}
                            </h3>
                            <task-button
                                color="bg-green-600"
                                hoverColor="hover:bg-green-500"
                                class="ml-2 min-w-max"
                                @click="toggleEditForm(task.id)"
                            >
                                編集
                            </task-button>
                        </template>
                        <template v-if="task.is_edit">
                            <input
                                type="text"
                                class="w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm border-gray-300 rounded"
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
                <div class="mx-2 mt-6 shadow rounded overflow-hidden" v-show="isNewFormShow">
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
                <div class="flex my-6" v-show="!isNewFormShow">
                    <task-button
                        color="bg-indigo-600"
                        hoverColor="hover:bg-indigo-500"
                        class="w-full mx-2"
                        @click="toggleNewForm(true)"
                    >
                        やる気追加
                    </task-button>
                </div>
            </template>
        </main>
    </div>
</template>

<script>
import TaskButton from '../components/task/Button'
import TaskLinkButton from '../components/task/LinkButton'

export default {
    components: { TaskButton, TaskLinkButton },

    props: { auth: Object, errors: Object, liffId: String },

    async mounted() {
        await liff.init({ liffId: this.liffId })
        if (!liff.isLoggedIn()) liff.login()
        const profile = await liff.getProfile()
        try {
            this.lineUser = await axios.get(`api/lineUsers/${profile.userId}`).then((response) => response.data)
            if (!this.lineUser) {
                this.lineUser = await axios.post(`api/lineUsers`, profile).then((response) => response.data)
            }

            this.tasks = await axios.get(`api/tasks/${this.lineUser.id}`).then((response) => response.data)
        } catch (error) {
            alert('すまん！なんか上手く開けんかった！また出直してくれると助かるわ！')
        } finally {
            this.loading = true
        }
    },

    data() {
        return {
            loading: false,
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
                .post('api/tasks', { line_user_id: this.lineUser.id, name: this.name })
                .catch((error) => alert('すまん！なんか上手く登録できひんかった！また出直してくれると助かるわ！'))

            this.tasks = [...this.tasks, task.data]
            this.name = ''
            this.isNewFormShow = false
        },

        async updateTask(editTask) {
            const errors = this.validateTask(editTask.name)
            if (errors.length) return alert(errors.join(','))

            const task = await axios
                .post(`api/tasks/${editTask.id}`, { line_user_id: this.lineUser.id, name: editTask.name })
                .catch((error) => alert('すまん！なんか上手く更新できひんかった！また出直してくれると助かるわ！'))

            const setTask = this.tasks.find((setTask) => setTask.id === task.data.id)
            setTask.name = task.data.name
            setTask.is_edit = false
        },

        validateTask(name) {
            let errors = []
            if (!name.length || !name.match(/\S/g)) errors = [...errors, 'フォームが空っぽやで！']
            if (name.length > 255) errors = [...errors, '内容は簡潔に！255文字以内で頼むわ！']
            return errors
        },
    },
}
</script>

<style>
.loader {
    border-top-color: #3498db;
    -webkit-animation: spinner 1.5s linear infinite;
    animation: spinner 1.5s linear infinite;
}

@-webkit-keyframes spinner {
    0% {
        -webkit-transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
    }
}

@keyframes spinner {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
