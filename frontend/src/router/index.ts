import { createRouter, createWebHistory } from '@ionic/vue-router'
import { RouteRecordRaw } from 'vue-router'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/HomePage.vue'),
    meta: {
      title: 'CoderStew - Professional Web Development Services',
      description: 'Expert web development, mobile apps, and digital solutions. Transform your ideas into powerful digital experiences.',
    },
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('@/views/AboutPage.vue'),
    meta: {
      title: 'About - CoderStew',
      description: 'Learn about our team, mission, and approach to creating exceptional digital solutions.',
    },
  },
  {
    path: '/services',
    name: 'Services',
    component: () => import('@/views/ServicesPage.vue'),
    meta: {
      title: 'Services - CoderStew',
      description: 'Comprehensive web development services including custom applications, e-commerce, and mobile solutions.',
    },
  },
  {
    path: '/portfolio',
    name: 'Portfolio',
    component: () => import('@/views/PortfolioPage.vue'),
    meta: {
      title: 'Portfolio - CoderStew',
      description: 'Explore our portfolio of successful projects and client success stories.',
    },
  },
  {
    path: '/portfolio/:slug',
    name: 'ProjectDetail',
    component: () => import('@/views/ProjectDetailPage.vue'),
    meta: {
      title: 'Project Details - CoderStew',
      description: 'Detailed view of our project work and implementation.',
    },
  },
  {
    path: '/blog',
    name: 'Blog',
    component: () => import('@/views/BlogPage.vue'),
    meta: {
      title: 'Blog - CoderStew',
      description: 'Latest insights, tutorials, and industry news from our development team.',
    },
  },
  {
    path: '/contact',
    name: 'Contact',
    component: () => import('@/views/ContactPage.vue'),
    meta: {
      title: 'Contact - CoderStew',
      description: 'Get in touch with our team to discuss your project requirements and get a quote.',
    },
  },
  {
    path: '/booking',
    name: 'Booking',
    component: () => import('@/views/BookingPage.vue'),
    meta: {
      title: 'Book Consultation - CoderStew',
      description: 'Schedule a consultation to discuss your project needs and requirements.',
    },
  },
  {
    path: '/privacy',
    name: 'Privacy',
    component: () => import('@/views/PrivacyPage.vue'),
    meta: {
      title: 'Privacy Policy - CoderStew',
      description: 'Our privacy policy and data protection practices.',
    },
  },
  {
    path: '/terms',
    name: 'Terms',
    component: () => import('@/views/TermsPage.vue'),
    meta: {
      title: 'Terms of Service - CoderStew',
      description: 'Terms and conditions for using our services.',
    },
  },
  {
    path: '/newsletter/unsubscribe',
    name: 'NewsletterUnsubscribe',
    component: () => import('@/views/NewsletterUnsubscribePage.vue'),
    meta: {
      title: 'Unsubscribe - CoderStew Newsletter',
      description: 'Unsubscribe from our newsletter.',
    },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFoundPage.vue'),
    meta: {
      title: '404 - Page Not Found - CoderStew',
      description: 'The page you are looking for could not be found.',
    },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      }
    } else {
      return { top: 0 }
    }
  },
})

// Navigation guards
router.beforeEach((to, from, next) => {
  // Update document title and meta tags
  if (to.meta.title) {
    document.title = to.meta.title as string
  }
  
  if (to.meta.description) {
    const metaDescription = document.querySelector('meta[name="description"]')
    if (metaDescription) {
      metaDescription.setAttribute('content', to.meta.description as string)
    }
  }

  // Track page view for analytics
  if (typeof gtag !== 'undefined') {
    gtag('config', 'GA_MEASUREMENT_ID', {
      page_title: to.meta.title,
      page_location: window.location.href,
    })
  }

  next()
})

router.afterEach((to, from) => {
  // Track performance metrics
  if (performance.mark) {
    performance.mark(`route-${to.name}-start`)
  }
})

export default router
