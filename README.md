
# 🎓 Learners’ Hot-desk – A Student Friendly Job Portal

**Team Name:** The FrontEnders  
**Course:** CSE479 (Web Programming) | Section 2  
**Institution:** East West University  

![Project Status](https://img.shields.io/badge/Status-Completed-success)
![Tech Stack](https://img.shields.io/badge/PHP-MySQL-blue)
![Frontend](https://img.shields.io/badge/HTML-CSS-orange)

## 📖 Introduction

**Learners’ Hot-desk** is a centralized, web-based recruitment platform designed to bridge the gap between students (job seekers) and employers. Unlike massive commercial platforms, this system is optimized for academic and small-organization use, providing a "Hot-desk" environment where learners can easily find opportunities.

The system digitizes the recruitment lifecycle—from job posting to application management—ensuring efficiency, transparency, and data integrity. It features a responsive interface built with HTML/CSS/JS and a robust backend powered by PHP and MySQL.

---

## ✨ Features

### 👨‍🎓 For Job Seekers (Students)
*   **User Registration & Login:** Secure account creation with CV/Resume upload capabilities.
*   **Job Search:** Browse available listings and search by keywords, location, or category.
*   **Apply for Jobs:** One-click application submission using the stored profile and CV.
*   **Dashboard:** specialized dashboard to view applied jobs and save jobs for later.
*   **Profile Management:** Update personal information and resume details.

### 🏢 For Employers
*   **Post Jobs:** Create detailed job listings (Title, Description, Location, Salary).
*   **Manage Listings:** Edit or delete existing job posts.
*   **Applicant Tracking:** View a list of candidates who applied for specific posts.
*   **CV Download:** Download applicant CVs directly for offline review.

### 🛡️ System & Security
*   **Authentication:** Secure PHP-based login/logout sessions.
*   **Session Management:** Protected routes ensuring only authorized users access specific pages.
*   **Role-Based Access:** Distinct interfaces for Admins, Employers, and Job Seekers.

---

## 🛠️ Tech Stack

*   **Frontend:** HTML5, CSS3, JavaScript (Slick Slider, Responsive Design).
*   **Backend:** PHP (Server-side scripting).
*   **Database:** MySQL (Relational Data Management).
*   **Server:** Apache (via XAMPP).

---

## 📂 Directory Structure

```text
Learners-Hot-desk/
├── Code/
│   ├── assets/            # CSS, JS, Fonts, and Images
│   ├── config.php         # Database connection settings
│   ├── project_jobportal.sql # Main Database file
│   ├── index.php          # Landing page
│   ├── login.php          # Authentication
│   ├── dashboard.php      # User dashboard
│   ├── ... (other php files)
├── Report/                # Project documentation and diagrams
└── README.md
