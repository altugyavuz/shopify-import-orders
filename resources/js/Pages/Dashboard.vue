<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps(['orderCount']);
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="bg-gray-100 dark:bg-gray-900">
      <div
          class="relative min-h-screen flex flex-col items-center selection:bg-[#FF2D20] selection:text-white">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">

          <main class=" mt-12">
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
              <a
                  :href="route('orders.index')"
                  class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-white dark:ring-gray-800 dark:hover:text-black/50 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
              >
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="#FF2D20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 sm:size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                  </svg>
                </div>

                <div class="pt-3 sm:pt-5">
                  <h2 class="text-xl font-semibold text-black dark:text-gray-800">Orders ({{ orderCount }})</h2>

                  <p class="mt-4 text-lg/relaxed">
                    You can view all your orders here and import them from your shopify store.
                  </p>
                </div>

                <svg class="size-6 shrink-0 self-center stroke-[#FF2D20]" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"/>
                </svg>
              </a>
            </div>
          </main>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
