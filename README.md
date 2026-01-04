# Galeriku 📸

**Galeriku** is a modern photo gallery application built with **Laravel 10** and **Tailwind CSS**. It allows users to upload photos, create albums, like/comment on posts, and follow other creators.

![Galeriku Banner](https://via.placeholder.com/1200x500.png?text=Galeriku+Preview)
_(Note: Replace with actual screenshots)_

## 🚀 Features

-   **User Authentication**: Sign up, Sign in, and Profile management.
-   **Photo Upload**: Upload photos managed via standard filesystem storage.
-   **Albums**: Organize photos into albums.
-   **Social Interactions**:
    -   Like & Comment on photos.
    -   Follow/Unfollow system.
    -   Notification system for interactions.
-   **Search & Discovery**: Explore new photos and users.
-   **Admin Dashboard**: Manage content and reports.
-   **Responsive Design**: Built with Tailwind CSS for mobile-friendly layouts.

## 🛠️ Tech Stack

-   **Backend**: Laravel 10.x (PHP 8.3^)
-   **Frontend**: Blade Templates, Tailwind CSS 4.x
-   **Asset Bundling**: Vite
-   **Database**: MySQL

## 📦 Installation (Localhost)

Follow these steps to set up the project locally:

1.  **Clone the repository**

    ```bash
    git clone https://github.com/Lonelyman86/Galeriku.git
    cd Galeriku
    ```

2.  **Install PHP Dependencies**

    ```bash
    composer install
    ```

3.  **Install Node Dependencies**

    ```bash
    npm install
    ```

4.  **Environment Setup**
    Copy `.env.example` to `.env` and configure your database credentials.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Database Migration**
    Run migrations to set up the database tables.

    ```bash
    php artisan migrate
    ```

6.  **Run Development Server**
    Start the Laravel server and Vite for frontend assets (run in separate terminals).

    ```bash
    # Terminal 1
    php artisan serve

    # Terminal 2
    npm run dev
    ```

7.  **Access the App**
    Open your browser and visit `http://localhost:8000`.

## 📂 Project Structure

-   **`app/Models`**: Eloquent models (User, Foto, Album, etc.).
-   **`app/Http/Controllers`**: Controllers handling business logic.
-   **`resources/views`**: Blade templates for UI.
-   **`routes/web.php`**: Application routes.

## 🤝 Contributing

Contributions are welcome! Please fork the repository and create a pull request.

## 📄 License

This project is licensed under the [MIT logic](https://opensource.org/licenses/MIT).
