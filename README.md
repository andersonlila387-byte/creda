# Creda Platform

Welcome to the Creda repository. This project is built using PHP, MySQL, and Tailwind CSS.

## Getting Started for Collaborators

If you are a new collaborator setting up the project on your local machine, please follow the instructions below.

### Prerequisites

1. **XAMPP / WAMP / MAMP**: You need a local server environment with PHP 8+ and MySQL. [Download XAMPP here](https://www.apachefriends.org/index.html).
2. **Git**: Ensure you have Git installed on your machine.

### Installation Steps

#### 1. Clone the Repository
Open your terminal inside your `htdocs` (or `www`) folder and run:
```bash
git clone https://github.com/andersonlila387-byte/creda.git
cd creda
```

#### 2. Set Up the Database
We have included a database export in this repository (`database.sql`) so you can instantly get the current data structure and dummy data.

1. Open your XAMPP Control Panel and start **Apache** and **MySQL**.
2. Go to **phpMyAdmin** in your browser: `http://localhost/phpmyadmin`
3. Click on **New** in the left sidebar to create a new database.
4. Name the database **`creda_db`** and choose the Collation `utf8mb4_general_ci`, then click **Create**.
5. Select your newly created `creda_db` database.
6. Click the **Import** tab at the top.
7. Click **Choose File** and select the `database.sql` file located in the root of your cloned repository.
8. Scroll to the bottom and click **Import** (or "Go").

#### 3. Database Configuration
By default, the application connects to:
- **Host**: `localhost`
- **Database**: `creda_db`
- **Username**: `root`
- **Password**: `''` (Blank)

If your local MySQL setup has a different password, you will need to update the credentials in `app/config/database.php`.

#### 4. Run the Project
Open your web browser and navigate to:
```
http://localhost/creda/
```
*(Adjust the URL if you have a different folder structure or virtual host set up).*

---

### Contribution Workflow

To avoid overriding each other's code, follow this workflow:

1. Always pull the latest changes before starting:
   ```bash
   git pull origin main
   ```
2. When working on a large change, create a new branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Commit your changes with clear messages:
   ```bash
   git add .
   git commit -m "Added a new provider layout"
   ```
4. Push your branch and create a Pull Request on GitHub:
   ```bash
   git push -u origin feature/your-feature-name
   ```
