# Drive Laravel

Drive Laravel is a cloud storage application that allows users to
upload, store, and manage files and folders online, similar to Google
Drive. It provides a web-based interface for managing documents, images,
and other files, with built-in features for file organization, activity
tracking, and localization support.

## Features

- **Cloud File Storage**: Securely upload and store files in the cloud.
- **Folder Structure**: Organize files in folders to keep content structured.
- **Activity Logging**: Track user actions using Laravel Activity Log.
- **Localization**: Support for multiple languages with automatic string export (via Laravel Translatable String Exporter).
- **File Compression**: Compress files into ZIP archives (via Laravel ZipStream).
- **Testing**: Comprehensive tests powered by Pest PHP.

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and pnpm (for front-end assets)
- Laravel Sail (for Docker-based development environment)

### 1. Clone the Repository

```bash
git clone https://github.com/prave-com/drive-laravel
cd Drive_Laravel
```

### 2. Install Backend Dependencies

Run the following Composer command to install the PHP dependencies:

```bash
composer install
```

### 3. Set Up Environment File

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

### 4. Generate Application Key

Generate the application key:

```bash
php artisan key:generate
```

### 5. Set Up the Database

Configure your database connection in the `.env` file, for example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=drive_laravel
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

and then run migrations:

```bash
php artisan migrate
```

### 6. Install Front-End Dependencies

Run the following pnpm command to install front-end dependencies:

```bash
pnpm install
```

### 7. Run Development Environment

You can use Laravel Sail (Docker) to run the application locally. To
start the Sail container, use:

```bash
./vendor/bin/sail up -d
```

Visit the following URLs:

- App: `http://localhost`
- phpMyAdmin: `http://localhost:8080`
- Mailpit: `http://localhost:8025`

Alternatively, without Sail, run the app using:

```bash
php artisan serve
```

And access it at: `http://localhost:8000`.

### 8. Run Front-End Development Server

To run the front-end development server (which uses Vite) with Sail:

```bash
./vendor/bin/sail pnpm dev
```

Alternatively, if you're not using Sail, you can run the front-end development server with:

```bash
pnpm dev
```

This will start the Vite development server at `http://localhost:5173`.

## Mailpit Environment Configuration

For email handling during development, we use Mailpit, a lightweight email testing tool. It's configured in the .env file, and you should ensure that the following environment variables are set:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Mailpit Settings:

- **MAIL_MAILER**: Set to smtp to use the SMTP protocol.
- **MAIL_HOST**: Set to mailpit, which corresponds to the Mailpit container in the Sail environment.
- **MAIL_PORT**: Set to 1025, the default SMTP port for Mailpit.
- **MAIL_USERNAME & MAIL_PASSWORD**: Set to null as Mailpit doesn't require authentication for local development.
- **MAIL_ENCRYPTION**: Set to null as encryption is not used in the local development environment.
- **MAIL_FROM_ADDRESS**: Specify the email address used as the sender's email in your app.
- **MAIL_FROM_NAME**: This should use the application's name (set via APP_NAME).

Once the environment is set up, you can access Mailpit's web interface at `http://localhost:8025` to view and test emails sent by the application.

## TOTAL_STORAGE Environment Variable

The `TOTAL_STORAGE` environment variable defines the **total amount of available global storage** for the Drive Laravel application. This value is used to track the total number of bytes allocated for file storage within the system.

To set the `TOTAL_STORAGE` variable, add the following line to your `.env` file:

```env
TOTAL_STORAGE=10737418240
```

This example sets the total storage to 10 GB (10737418240 bytes). You can adjust this value according to your storage needs.

### Example:

If you want to set 1 TB of storage:

```env
TOTAL_STORAGE=1099511627776
```

Make sure that this value is in **bytes**.

## Testing

We use Pest PHP for testing in this project. To run the tests with Sail:

```bash
./vendor/bin/sail pest
```

Alternatively, if you're not using Sail, you can run the test with:

```bash
php artisan test
```

## Code Formatting

This project uses Prettier for code formatting. To check the formatting with Sail:

```bash
./vendor/bin/sail pnpm format:check
```

To automatically format your code:

```bash
./vendor/bin/sail pnpm format
```

Alternatively, if you're not using Sail, you can check the formatting with:

```bash
pnpm format:check
```

To automatically format your code:

```bash
pnpm format
```

## Language Translations

To generate lang translation with Sail:

```bash
./vendor/bin/sail artisan translatable:export id
```

Without Sail, use:

```bash
php artisan translatable:export id
```

## Deployment

For deployment, ensure your server meets the following requirements:

- PHP 8.2+
- Composer
- Node.js for asset compilation
- Proper file system permissions for file uploads

Run migrations on the production environment with Sail:

```bash
./vendor/bin/sail artisan migrate
```

Then, compile front-end assets:

```bash
./vendor/bin/sail pnpm build
```

Cache the route:

```bash
./vendor/bin/sail artisan route:cache
```

Without Sail, use the following commands:

```bash
php artisan migrate
pnpm build
php artisan route:cache
```

## Set Up Cron Jobs (Ubuntu)

To run scheduled tasks, set up a cron job to run once a day at midnight:

1. Edit the cron file:

```bash
crontab -e
```

2. Add the following line:

```bash
0 0 * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```

3. Save and exit.

## Package Support

The following packages are used in this project:

- Laravel Activity Log: To log user activity.
- Laravel ZipStream: To create downloadable zip files.
- Laravel Sail: To run the application in a Docker-based environment.
- Pest PHP: For testing.
- Prettier and Laravel Pint: For code formatting.

## Contributing

We welcome contributions to the Drive Laravel project! To contribute,
follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature`).
3. Make your changes and commit them (`git commit -m 'Add new feature'`).
4. Push to your branch (`git push origin feature/your-feature`).
5. Open a pull request.

Please make sure to follow the coding standards and write tests for any
new functionality.

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/MSIBDiskominfo-pswrn/Drive_Laravel/blob/main/LICENSE) file
for details.
