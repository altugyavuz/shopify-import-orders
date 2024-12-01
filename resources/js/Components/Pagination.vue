<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  pagination: Object
})
</script>

<template>
  <nav class="relative flex flex-wrap justify-center mx-2 md:mx-0">
    <template v-for="link in pagination.links" :key="link.label">
      <Link
          preserve-scroll
          :href="link.url ?? ''"
          v-html="link.label"
          class="flex items-center justify-center w-full md:w-auto px-3 py-2 text-sm rounded-lg text-gray-600 mb-2 md:mb-0"
          :class="{ 'bg-gray-200': link.active, '!text-gray-300': !link.url }"
      />
    </template>
  </nav>
</template>