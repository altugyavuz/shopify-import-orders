<script setup>
import {computed} from 'vue';
import {usePage} from "@inertiajs/vue3";

const page = usePage();
console.log(page.props);
const errors = computed(() => page.props.errors);
const hasErrors = computed(() => Object.keys(errors.value).length > 0);
</script>

<template>
  <div v-if="hasErrors">
    <div class="bg-red-100 rounded-lg py-5 px-6 text-base text-red-700 mb-3" role="alert">
      <ul class="list-disc list-inside text-sm text-red-600">
        <li v-for="(error, key) in errors" :key="key">
          {{ error }}
        </li>
      </ul>
    </div>
  </div>
</template>
