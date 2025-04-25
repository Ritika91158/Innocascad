# InnoCascade Platform

A student innovation platform designed to facilitate collaboration and idea sharing among students in tertiary institutions.

## Features

- User Authentication and Authorization
- Idea Sharing and Management
- Collaboration System
- Comment System
- Explore Ideas with Advanced Filtering
- Profile Management
- Sector-based Categorization

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- Node.js and npm (for frontend assets)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/innocascade.git
cd innocascade
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy the environment file and configure your settings:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Set up the database:
- Create a new MySQL database
- Update the database credentials in `.env`
- Run migrations:
```bash
php artisan migrate
```

6. Install frontend dependencies:
```bash
npm install
npm run dev
```

7. Start the development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Directory Structure

```
innocascade/
├── app/            # Application core files
├── config/         # Configuration files
├── database/       # Database migrations and seeders
├── public/         # Publicly accessible files
├── resources/      # Views and frontend assets
├── routes/         # Route definitions
├── storage/        # Application storage
└── tests/          # Test files
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please email support@innocascade.com or open an issue in the repository. 