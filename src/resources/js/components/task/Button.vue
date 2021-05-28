<template>
    <button :class="`px-4 py-2 ${color} ${hoverColor} text-lg font-bold text-white rounded shadow hover:shadow-md`">
        <slot />
    </button>
</template>

<script>
export default {
    props: {
        color: String,
        hoverColor: String,
    },
}
</script>
