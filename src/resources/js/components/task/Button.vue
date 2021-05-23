<template>
    <button
        :class="`px-4 py-2 bg-${color}-600 hover:bg-${color}-500 text-lg font-bold text-white rounded shadow hover:shadow-md`"
    >
        <slot />
    </button>
</template>

<script>
export default {
    props: {
        color: String,
    },
}
</script>
