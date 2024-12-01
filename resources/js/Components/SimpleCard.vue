<template>
  <div class="flex justify-center mt-12">
    <div class="block p-6 rounded-lg shadow-md bg-white dark:bg-gray-800 dark:text-gray-100 max-w-md w-full">
      <slot name="title" />

      <div class="text-gray-700 text-base mb-4">
        <slot/>
      </div>
    </div>
  </div>
</template>