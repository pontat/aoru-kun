<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="mx-auto py-6 px-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">今日のやる気リスト</h2>
            </div>
        </header>
        <!-- Page Content -->
        <main>
            <div class="mx-2 mt-6 bg-white shadow rounded overflow-hidden" v-for="task in setTasks" :key="task.id">
                <div class="p-4 flex items-center justify-between">
                    <template v-if="!task.is_edit">
                        <h3 class="text-lg font-bold text-gray-800 leading-tight">
                            {{ task.name }}
                        </h3>
                        <task-button color="green" class="ml-2 min-w-max" @click="toggleEditForm(task.id)">
                            編集
                        </task-button>
                    </template>
                    <template v-if="task.is_edit">
                        <input
                            type="text"
                            class="w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm border-gray-300 rounded"
                            v-model="task.name"
                        />
                        <task-button color="indigo" class="ml-2 min-w-max" @click="updateTask(task)">
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
                    <task-button color="red" @click="toggleNewForm(false)">キャンセル</task-button>
                    <task-button color="indigo" @click="createTask()">登録</task-button>
                </div>
            </div>
            <div class="flex my-6" v-show="!isNewFormShow">
                <task-button color="indigo" class="w-full mx-2" @click="toggleNewForm(true)">やる気追加</task-button>
            </div>
        </main>
    </div>
</template>

<script>
import TaskButton from '../components/task/Button.vue'
import TaskLinkButton from '../components/task/LinkButton.vue'

export default {
    components: { TaskButton, TaskLinkButton },

    props: { auth: Object, errors: Object, tasks: Array },

    mounted() {
        this.setTasks = this.tasks
    },

    data() {
        return {
            setTasks: [],
            isNewFormShow: false,
            name: '',
        }
    },

    methods: {
        toggleEditForm(taskId) {
            const task = this.setTasks.find((task) => task.id === taskId)
            task.is_edit = true
        },

        toggleNewForm(bool) {
            this.isNewFormShow = bool
        },

        async createTask() {
            const errors = this.validateTask(this.name)
            if (errors.length) return alert(errors.join(','))

            const task = await axios
                .post('tasks', { line_user_id: 14, name: this.name })
                .catch((error) => alert('すまん！なんか上手く登録できひんかった！また出直してくれると助かるわ！'))

            this.setTasks = [...this.setTasks, task.data]
            this.name = ''
            this.isNewFormShow = false
        },

        async updateTask(editTask) {
            const errors = this.validateTask(editTask.name)
            if (errors.length) return alert(errors.join(','))

            const task = await axios
                .post(`tasks/${editTask.id}`, { line_user_id: 14, name: editTask.name })
                .catch((error) => alert('すまん！なんか上手く更新できひんかった！また出直してくれると助かるわ！'))

            const setTask = this.setTasks.find((setTask) => setTask.id === task.data.id)
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
