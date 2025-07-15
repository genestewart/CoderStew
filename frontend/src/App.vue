<template>
  <ion-app>
    <ion-router-outlet />
  </ion-app>
</template>

<script setup lang="ts">
import { IonApp, IonRouterOutlet } from '@ionic/vue'
import { onMounted } from 'vue'
import { usePerformanceStore } from '@/stores/performance'

const performanceStore = usePerformanceStore()

onMounted(() => {
  // Initialize performance monitoring
  performanceStore.initializePerformanceMonitoring()
  
  // Track page load performance
  performanceStore.trackPageLoad()
})
</script>

<style lang="scss">
// Import custom fonts
@import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=PT+Mono&display=swap');

// Global styles
* {
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

body {
  font-family: 'Chakra Petch', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  line-height: 1.6;
  color: var(--ion-color-dark);
  background-color: var(--ion-color-light);
}

// Custom scrollbar
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: var(--ion-color-light-shade);
}

::-webkit-scrollbar-thumb {
  background: var(--ion-color-medium);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--ion-color-medium-shade);
}

// Focus styles for accessibility
*:focus {
  outline: 2px solid var(--ion-color-primary);
  outline-offset: 2px;
}

// Button focus styles
ion-button:focus {
  outline: none;
  box-shadow: 0 0 0 2px var(--ion-color-primary-tint);
}

// Skip link for accessibility
.skip-link {
  position: absolute;
  top: -40px;
  left: 6px;
  background: var(--ion-color-primary);
  color: var(--ion-color-primary-contrast);
  padding: 8px;
  text-decoration: none;
  border-radius: 4px;
  z-index: 1000;
  
  &:focus {
    top: 6px;
  }
}

// Performance optimization classes
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from {
  transform: translateX(100%);
}

.slide-leave-to {
  transform: translateX(-100%);
}

// Utility classes
.text-gradient {
  background: linear-gradient(135deg, var(--ion-color-primary), var(--ion-color-secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.glass-effect {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.shadow-soft {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.shadow-medium {
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.shadow-strong {
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}

// Responsive typography
@media (max-width: 768px) {
  body {
    font-size: 14px;
  }
}

@media (min-width: 1200px) {
  body {
    font-size: 16px;
  }
}

// Print styles
@media print {
  .no-print {
    display: none !important;
  }
  
  body {
    background: white !important;
    color: black !important;
  }
}
</style>
