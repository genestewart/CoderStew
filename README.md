# CoderStew - Professional Web Development Services

A modern, high-performance website built with Laravel 11 and Vue.js 3, featuring a comprehensive admin panel, newsletter management, and Microsoft Bookings integration.

## 🚀 Features

### Frontend (Vue.js 3 + Ionic)
- **Modern UI/UX**: Responsive design with Ionic components
- **Performance Optimized**: LCP < 2s, TTFB < 200ms, CLS < 0.1
- **SEO Ready**: Lighthouse score ≥ 90, structured data, meta tags
- **Accessibility**: WCAG 2.1 AA compliant
- **Progressive Web App**: Offline support, installable
- **Real-time Features**: Live chat, notifications
- **Portfolio Gallery**: Filterable project showcase
- **Contact Forms**: reCAPTCHA protected
- **Newsletter Signup**: Integrated with Listmonk
- **Microsoft Bookings**: Consultation scheduling

### Backend (Laravel 11)
- **RESTful API**: Comprehensive API endpoints
- **Admin Panel**: Backpack CRUD for content management
- **Authentication**: Laravel Sanctum for API security
- **Database**: MongoDB with Laravel MongoDB package
- **Queue Management**: Laravel Horizon for background jobs
- **Error Monitoring**: GlitchTip integration
- **Performance**: Redis caching, optimized queries
- **Email**: Newsletter and contact form handling

### Infrastructure
- **Containerized**: Docker Compose setup
- **Reverse Proxy**: Nginx with performance optimizations
- **Database**: MongoDB for flexible data storage
- **Cache**: Redis for session and application caching
- **Monitoring**: GlitchTip for error tracking
- **Newsletter**: Listmonk for email campaigns

## 🛠 Tech Stack

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Ionic 7** - Mobile-first UI components
- **TypeScript** - Type-safe development
- **Vite** - Fast build tool and dev server
- **Pinia** - State management
- **Vue Router** - Client-side routing
- **Axios** - HTTP client
- **Lucide Icons** - Beautiful icon library

### Backend
- **Laravel 11** - PHP web framework
- **PHP 8.3** - Latest PHP version
- **MongoDB** - NoSQL database
- **Redis** - In-memory data store
- **Backpack CRUD** - Admin panel
- **Laravel Sanctum** - API authentication
- **Laravel Horizon** - Queue monitoring

### DevOps & Infrastructure
- **Docker** - Containerization
- **Docker Compose** - Multi-container orchestration
- **Nginx** - Web server and reverse proxy
- **GlitchTip** - Error monitoring
- **Listmonk** - Newsletter management

## 📋 Prerequisites

- Docker and Docker Compose
- Node.js 18+ (for local development)
- PHP 8.3+ (for local development)
- Git

## 🚀 Quick Start

### 1. Clone the Repository
```bash
git clone https://github.com/genestewart/CoderStew.git
cd CoderStew
```

### 2. Environment Setup

#### Backend Environment
```bash
cp backend/.env.example backend/.env
```
The repository provides a sample configuration file at `backend/.env.example`.
Copy it to `.env` and update the values for your local setup.

Edit `backend/.env` with your configuration:
- Database credentials
- API keys (reCAPTCHA, etc.)
- Email settings
- External service credentials

#### Frontend Environment
```bash
cp frontend/.env.example frontend/.env
```

Edit `frontend/.env` with your configuration:
- API endpoints
- External service keys
- Feature flags

### 3. Start with Docker Compose
```bash
docker-compose up -d
```

This will start all services:
- **Frontend**: http://localhost (Vue.js app)
- **Backend API**: http://localhost/api (Laravel API)
- **Admin Panel**: http://localhost/admin (Backpack admin)
- **Newsletter**: http://newsletter.coderstew.local (Listmonk)
- **Error Monitoring**: http://errors.coderstew.local (GlitchTip)

### 4. Initialize the Application

#### Install Dependencies and Setup Database
```bash
# Backend setup
docker-compose exec laravel-api composer install
docker-compose exec laravel-api php artisan key:generate
docker-compose exec laravel-api php artisan migrate
docker-compose exec laravel-api php artisan db:seed

# Frontend setup
docker-compose exec vue-frontend npm install
```

### 5. Access the Application

- **Website**: http://localhost
- **Admin Panel**: http://localhost/admin
- **API Documentation**: http://localhost/api/documentation

## 🏗 Project Structure

```
CoderStew/
├── backend/                 # Laravel 11 API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── ...
│   ├── config/
│   ├── database/
│   ├── routes/
│   └── ...
├── frontend/               # Vue.js 3 + Ionic
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/
│   │   ├── router/
│   │   └── ...
│   ├── public/
│   └── ...
├── nginx/                  # Nginx configuration
├── docker-compose.yml      # Docker services
└── README.md
```

## 🔧 Development

### Local Development Setup

#### Backend Development
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

#### Frontend Development
```bash
cd frontend
npm install
npm run dev
```

### Available Scripts

#### Backend
```bash
# Run tests
php artisan test

# Generate API documentation
php artisan l5-swagger:generate

# Queue worker
php artisan horizon

# Clear caches
php artisan optimize:clear
```

#### Frontend
```bash
# Development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Lint code
npm run lint

# Type checking
npm run type-check
```

## 🚀 Deployment

### Production Deployment

1. **Environment Configuration**
   - Set production environment variables
   - Configure SSL certificates
   - Set up domain DNS

2. **Build and Deploy**
   ```bash
   # Build production images
   docker-compose build

   # Deploy
   docker-compose up -d
   ```

3. **Post-Deployment**
   ```bash
   # Run migrations
   docker-compose exec laravel-api php artisan migrate --force
   
   # Optimize Laravel
   docker-compose exec laravel-api php artisan optimize
   
   # Generate API documentation
  docker-compose exec laravel-api php artisan l5-swagger:generate
   ```

### Running on Unraid

The stack can be deployed on an Unraid server using the Docker Compose plugin
or Compose Manager UI.

1. **Copy the repository** to a share such as `/mnt/user/appdata/CoderStew`.
2. **Load the `docker-compose.yml`** in the plugin or UI and adjust volume
   paths if desired. Typical mappings on Unraid are:
   - `/mnt/user/appdata/CoderStew/backend` → `/var/www/html`
   - `/mnt/user/appdata/CoderStew/frontend` → `/app`
   - `/mnt/user/appdata/CoderStew/mongodb` → `/data/db`
   - `/mnt/user/appdata/CoderStew/redis` → `/data`
3. **Configure environment variables** for the `api` and `frontend` services
   (e.g., `APP_KEY` and `VITE_API_URL`) through the Unraid UI.
4. **Start the stack** from the plugin or by running:

   ```bash
   docker compose -f /mnt/user/appdata/CoderStew/docker-compose.yml up -d
   ```

   Access the site at `http://<unraid-ip>` once the containers are running.

## 📊 Performance Targets

- **Largest Contentful Paint (LCP)**: < 2.0s
- **Time to First Byte (TTFB)**: < 200ms
- **Cumulative Layout Shift (CLS)**: < 0.1
- **Lighthouse Performance Score**: ≥ 90
- **Lighthouse SEO Score**: ≥ 90
- **Lighthouse Accessibility Score**: ≥ 90

## 🔒 Security Features

- **API Authentication**: Laravel Sanctum
- **CSRF Protection**: Built-in Laravel protection
- **Rate Limiting**: API and contact form rate limits
- **Input Validation**: Comprehensive request validation
- **XSS Protection**: Content Security Policy headers
- **SQL Injection**: Eloquent ORM protection
- **reCAPTCHA**: Contact form protection

## 🧪 Testing

Before running the test suites install the dependencies for each project:

```bash
# Backend
cd backend && composer install

# Frontend
cd ../frontend && npm install
```

### Backend Testing
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Generate coverage report
php artisan test --coverage
```

### Frontend Testing
```bash
# Run unit tests
npm run test:unit

# Run e2e tests
npm run test:e2e

# Run tests with coverage
npm run test:coverage
```

## 📝 API Documentation

API documentation is automatically generated using L5 Swagger and available at:
- Development: http://localhost/api/documentation
- Production: https://yourdomain.com/api/documentation

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- **Email**: hello@coderstew.com
- **GitHub Issues**: [Create an issue](https://github.com/genestewart/CoderStew/issues)
- **Documentation**: [Project Wiki](https://github.com/genestewart/CoderStew/wiki)

## 🙏 Acknowledgments

- Laravel community for the amazing framework
- Vue.js team for the progressive framework
- Ionic team for the beautiful UI components
- All open-source contributors

---

**CoderStew** - Transforming ideas into powerful digital experiences.
