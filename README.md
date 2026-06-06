# 🛒 Thrift — Marketplace Backend

A RESTful API backend for the **Thrift** second-hand fashion marketplace, built with **Laravel** and **Laravel Sanctum**.

👉 **Frontend Repository:** [https://github.com/ZakariaBarri/marketplace-frontend](https://github.com/ZakariaBarri/marketplace-frontend)

---

## ✨ Features

- 🔐 **Authentication** — Register, login, logout with Laravel Sanctum (Bearer tokens)
- 📦 **Products** — Full CRUD with image support, categories, sizes, and conditions
- 🛒 **Orders** — Complete order lifecycle: pending → accepted → shipped → delivered
- ⭐ **Reviews** — Buyer/seller review system with automatic rating updates
- 🔔 **Real-time Notifications** — Pusher-powered broadcasting via Laravel Echo
- 📍 **Addresses** — User address book management
- 👤 **Profile** — Profile update, avatar, password change, and stats
- 🛡️ **Admin Panel** — Manage users, products, and categories with a dedicated admin middleware
- ⏰ **Scheduled Jobs** — Auto-expire pending orders via a scheduled Artisan command

---

## 🗂️ Project Structure

```
app/
├── Console/Commands/       # Artisan commands (ExpireOrders)
├── Events/                 # Broadcasting events (OrderCreated, ReviewCreated)
├── Http/
│   ├── Controllers/Api/    # API controllers
│   │   └── Admin/          # Admin-only controllers
│   ├── Middleware/         # Auth, Admin guards
│   ├── Requests/           # Form request validation
│   └── Resources/          # API response transformers
├── Listeners/              # Event listeners (NotifySeller, UpdateUserRating)
├── Models/                 # Eloquent models
│   ├── User, Product, Order, Review
│   ├── Category, Condition, Size
│   ├── Addresse, Image
│   └── Notification
├── Notifications/          # Laravel notifications (OrderCreatedNotification)
└── Policies/               # Authorization policies

database/
└── migrations/             # All database migrations

routes/
├── api.php                 # All API routes
└── channels.php            # Broadcasting channels
```

---

## 🚀 Getting Started

### Prerequisites

- **PHP** >= 8.1
- **Composer**
- **MySQL**
- **Pusher** account (for real-time notifications)

### Installation

```bash
# Clone the repository
git clone https://github.com/ZakariaBarri/marketplace-backend.git
cd marketplace-backend

# Install dependencies
composer install

# Copy environment file and configure it
cp .env.example .env
php artisan key:generate

# Run database migrations
php artisan migrate

# Start the development server
php artisan serve
```

The API will be available at `http://127.0.0.1:8000/api`.

---

## ⚙️ Environment Configuration

Fill in the following keys in your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

---

## 📡 API Endpoints

### Auth
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | ❌ | Register a new user |
| POST | `/api/login` | ❌ | Login and get token |
| POST | `/api/logout` | ✅ | Logout current device |
| POST | `/api/logout-all` | ✅ | Logout all devices |
| GET | `/api/me` | ✅ | Get authenticated user |

### Products
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/products` | ❌ | List all products |
| GET | `/api/products/{id}` | ❌ | Get a product |
| POST | `/api/products` | ✅ | Create a product |
| PUT | `/api/products/{id}` | ✅ | Update a product |
| DELETE | `/api/products/{id}` | ✅ | Delete a product |
| GET | `/api/my-products` | ✅ | Get current user's products |

### Orders
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/orders` | ✅ | Place an order |
| GET | `/api/buyer/orders` | ✅ | Get purchases |
| GET | `/api/seller/orders` | ✅ | Get received orders |
| GET | `/api/orders/{id}` | ✅ | Get order details |
| POST | `/api/orders/{id}/accept` | ✅ | Accept an order |
| POST | `/api/orders/{id}/ship` | ✅ | Mark as shipped |
| POST | `/api/orders/{id}/deliver` | ✅ | Mark as delivered |
| POST | `/api/orders/{id}/cancel` | ✅ | Cancel an order |
| POST | `/api/orders/{id}/reject` | ✅ | Reject an order |

### Reviews
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/reviews` | ✅ | Submit a review |
| GET | `/api/users/{id}/reviews` | ✅ | All reviews for a user |
| GET | `/api/users/{id}/reviews/seller` | ✅ | Seller reviews |
| GET | `/api/users/{id}/reviews/buyer` | ✅ | Buyer reviews |

### Profile & Addresses
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/profile` | ✅ | Get profile |
| PUT | `/api/profile` | ✅ | Update profile |
| POST | `/api/profile/password` | ✅ | Change password |
| GET | `/api/profile/stats` | ✅ | Get profile stats |
| GET | `/api/me/addresses` | ✅ | List addresses |
| POST | `/api/me/addresses` | ✅ | Add an address |
| PUT | `/api/me/addresses/{id}` | ✅ | Update an address |
| DELETE | `/api/me/addresses/{id}` | ✅ | Delete an address |

### Notifications
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/notifications` | ✅ | List notifications |
| POST | `/api/notifications/{id}/read` | ✅ | Mark one as read |
| POST | `/api/notifications/read-all` | ✅ | Mark all as read |

### Admin (requires admin role)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/dashboard` | Stats overview |
| GET | `/api/admin/users` | List users |
| DELETE | `/api/admin/users/{id}` | Delete a user |
| GET | `/api/admin/products` | List products |
| DELETE | `/api/admin/products/{id}` | Delete a product |
| GET/POST/PUT/DELETE | `/api/admin/categories` | Manage categories |

---

## 🗄️ Database Models

| Model | Description |
|-------|-------------|
| `User` | Buyers, sellers, and admins |
| `Product` | Listings with images, category, size, condition |
| `Order` | Transactions between buyer and seller |
| `Review` | Post-order ratings |
| `Category` | Product categories |
| `Condition` | Product conditions (new, used...) |
| `Size` | Clothing sizes |
| `Addresse` | User delivery addresses |
| `Image` | Product images |
| `Notification` | In-app notifications |

---

## 🛠️ Tech Stack

| Technology | Role |
|---|---|
| [Laravel](https://laravel.com/) | PHP framework |
| [Laravel Sanctum](https://laravel.com/docs/sanctum) | API token authentication |
| [Laravel Echo](https://laravel.com/docs/broadcasting) + [Pusher](https://pusher.com/) | Real-time broadcasting |
| MySQL | Relational database |
| Laravel Policies | Authorization per resource |
| Laravel Events & Listeners | Decoupled business logic |

---

## ⏰ Scheduled Commands

The `ExpireOrders` command automatically cancels pending orders past their expiry time. It is registered in `app/Console/Kernel.php` and should be added to your server's cron:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
