# Admin Dashboard - Laravel Livewire

A comprehensive admin dashboard built with Laravel 10, Livewire 3, and Spatie Laravel Permission package.

## Features

- **Authentication System**: Complete user authentication with Laravel Breeze
- **Role & Permission Management**: Full CRUD operations for roles and permissions using Spatie Laravel Permission
- **User Management**: Create, edit, and manage users with role assignments
- **Admin Dashboard**: Beautiful dashboard with statistics and user overview
- **Responsive Design**: Mobile-friendly interface built with Tailwind CSS
- **Livewire Components**: Real-time interactions without page refreshes

## Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd syifa
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install Node.js dependencies**

   ```bash
   npm install
   ```

4. **Environment setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**

   - Create a MySQL database named `admin_dashboard`
   - Update your `.env` file with database credentials

   ```bash
   php artisan migrate --seed
   ```

6. **Build assets**

   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Users

The seeder creates two default users:

- **Super Admin**: `admin@admin.com` / `password`
- **Admin**: `admin@example.com` / `password`

## Roles & Permissions

### Roles

- **super-admin**: Full access to all features
- **admin**: Limited access to user management and viewing
- **moderator**: Basic user management
- **user**: Dashboard access only

### Permissions

- `view users`, `create users`, `edit users`, `delete users`
- `view roles`, `create roles`, `edit roles`, `delete roles`
- `view permissions`, `create permissions`, `edit permissions`, `delete permissions`
- `view dashboard`, `view settings`, `edit settings`

## Project Structure

```
app/
├── Livewire/
│   ├── Dashboard.php
│   ├── RoleManagement.php
│   ├── PermissionManagement.php
│   └── UserManagement.php
├── Models/
│   └── User.php (with HasRoles trait)
└── Http/
    └── Controllers/

resources/
├── views/
│   ├── livewire/
│   │   ├── dashboard.blade.php
│   │   ├── role-management.blade.php
│   │   ├── permission-management.blade.php
│   │   ├── user-management.blade.php
│   │   └── layout/
│   │       └── navigation.blade.php
│   └── components/
└── css/
    └── app.css

database/
├── migrations/
└── seeders/
    └── RolePermissionSeeder.php
```

## Usage

1. **Login**: Use the default credentials to access the dashboard
2. **Dashboard**: View statistics and recent users
3. **User Management**: Create, edit, and assign roles to users
4. **Role Management**: Create and manage roles with specific permissions
5. **Permission Management**: Create and manage individual permissions

## Technologies Used

- **Laravel 10**: PHP framework
- **Livewire 3**: Full-stack framework for Laravel
- **Spatie Laravel Permission**: Role and permission management
- **Laravel Breeze**: Authentication scaffolding
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework
- **MySQL**: Database

## Security Features

- Role-based access control
- Permission-based authorization
- CSRF protection
- Password hashing
- Input validation
- SQL injection prevention

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
