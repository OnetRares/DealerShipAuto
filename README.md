# 🚗 Dealership Auto

**Dealership Auto** is a full-stack web application for managing a car dealership, with a **PHP backend** and a **React frontend**.  

It allows managing available cars, user authentication, and a responsive UI built with **Bootstrap**.

---

## ✨ Features

- 🔑 **User Authentication & Registration**  
- 🚘 **Car Management** (Create, Read, Update, Delete)  
- 📱 **Responsive UI** with **Bootstrap 5**  
- 🌐 **RESTful API** with JSON communication between backend and frontend  
- 🛠️ **Error logging & debugging**  

---

## 🛠️ Technologies Used

| Layer      | Technology               |
|------------|-------------------------|
| Backend    | PHP 8.x, MySQL, REST API |
| Frontend   | React 18+, Bootstrap 5, Vite |
| Versioning | Git                     |
| Others     | CORS, Session Management |

---

## 📂 Project Structure
dealership-auto/
├─ backend/
│ ├─ config/ # Database configuration
│ ├─ controllers/ # CarControllers, AuthControllers
│ ├─ models/ # Car, User, Repository
│ ├─ auth/ # endpoints for auth
│ ├─cars/ #endpoints for cars
│ └─ error.log # Error log
├─ frontend/
│ ├─ src/
│ │ ├─ assets/ # React components
│ │ ├─ pages/ # Main pages
│ │ ├─ main.jsx # React entry point
│ │ └─ App.jsx
│ ├─ package.json
│ └─ vite.config.js
├─ .gitignore
└─ README.md

## 🚀 Installation & Running

### Backend (PHP)
1. Clone the repo:
git clone <repo-url>
cd dealership-auto/backend

2.Configure your database in config/Database.php.
3.Start the PHP server: php -S localhost:8000 -t public

Frontend (React)
Navigate to the frontend folder: cd ../frontend
Install dependencies:npm install
Start the development server: npm run dev

💡 Notes

Backend serves JSON, frontend displays it using React + Bootstrap
Ideal setup for a full-stack PHP + React project.

Screenshots:
<img width="932" height="832" alt="image" src="https://github.com/user-attachments/assets/06f0038b-7a45-4fce-98b4-f04749424fd8" />

<img width="890" height="757" alt="image" src="https://github.com/user-attachments/assets/7785b039-6b04-4e85-84c6-39f11f9dcae2" />


