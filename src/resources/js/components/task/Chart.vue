<template>
    <vue3-chart-js v-bind="showBarChart()" />
</template>

<script>
import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'

export default {
    components: { Vue3ChartJs },

    props: { groupByTasks: Object },

    created() {
        const mCounts = this.motivationCounts
        const aCounts = this.achievementCounts
        const groupTasks = this.groupByTasks
        Object.keys(groupTasks).forEach((key) => {
            let date = key.slice(-2)
            date <= 5
                ? (mCounts[0] = mCounts[0] + groupTasks[key].length) &&
                  (aCounts[0] = aCounts[0] + groupTasks[key].filter((task) => task.is_completed).length)
                : date <= 10
                ? (mCounts[1] = mCounts[1] + groupTasks[key].length) &&
                  (aCounts[1] = aCounts[1] + groupTasks[key].filter((task) => task.is_completed).length)
                : date <= 15
                ? (mCounts[2] = mCounts[2] + groupTasks[key].length) &&
                  (aCounts[2] = aCounts[2] + groupTasks[key].filter((task) => task.is_completed).length)
                : date <= 20
                ? (mCounts[3] = mCounts[3] + groupTasks[key].length) &&
                  (aCounts[3] = aCounts[3] + groupTasks[key].filter((task) => task.is_completed).length)
                : date <= 25
                ? (mCounts[4] = mCounts[4] + groupTasks[key].length) &&
                  (aCounts[4] = aCounts[4] + groupTasks[key].filter((task) => task.is_completed).length)
                : (mCounts[5] = mCounts[5] + groupTasks[key].length) &&
                  (aCounts[5] = aCounts[5] + groupTasks[key].filter((task) => task.is_completed).length)
        })
    },

    data() {
        return {
            motivationCounts: new Array(0, 0, 0, 0, 0, 0),
            achievementCounts: new Array(0, 0, 0, 0, 0, 0),
        }
    },

    methods: {
        showBarChart() {
            return {
                type: 'bar',
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { min: 0, max: Math.max(...this.motivationCounts) } },
                },
                data: {
                    labels: ['1~5日', '6~10日', '11~16日', '16~20日', '20~25日', '26~31日'],
                    datasets: [
                        {
                            label: 'やる気数',
                            backgroundColor: '#FBBF24',
                            data: this.motivationCounts,
                        },
                        {
                            label: '達成数',
                            backgroundColor: '#F59E0B',
                            data: this.achievementCounts,
                        },
                    ],
                },
            }
        },
    },
}
</script>
