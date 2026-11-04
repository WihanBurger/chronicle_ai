<h1 align="center">Chronicle AI</h1>

<div align="center">
  <img alt="Chronicle AI Banner" src="./assets/Banner_ChronicleAI.png" width="100%" />
</div>

## Table of Contents

1. [About the Project](#1-about-the-project)
   - 1.1 [Project Description](#11-project-description)
   - 1.2 [Problem Statement](#12-problem-statement)
   - 1.3 [Built With](#13-built-with)
2. [Getting Started](#2-getting-started)
   - 2.1 [Prerequisites](#21-prerequisites)
   - 2.2 [How to Install](#22-how-to-install)
3. [Features and Usage](#3-features-and-usage)
4. [Architecture & Database Design](#4-architecture--database-design)
5. [Design Concept](#5-design-concept)
6. [Highlights and Challenges](#6-highlights-and-challenges)
7. [License](#7-license)
8. [Authors and Contact Info](#8-authors-and-contact-info)

---

## 1. About the Project

### 1.1 Project Description

**Chronicle AI** is a production-ready PHP and MySQL web application designed for tabletop RPG enthusiasts. It functions as an AI-driven narrative storytelling engine styled with parchment UI elements, allowing players and Game Masters to manage campaigns asynchronously. The platform features secure user authentication, role-based access control, and a permanent ledger for campaign tracking.

### 1.2 Problem Statement

Tabletop RPG campaigns often fall apart due to scheduling conflicts, and Game Masters (GMs) bear a heavy burden prepping sessions week after week. When groups miss a session, momentum dies, and tracking complex campaign lore across scattered notes or discordant group chats leads to confusion.

**Chronicle AI's solution** is an always-available, AI-driven narrative hub. It allows players to continue their journeys and explore the world asynchronously between live sessions. By managing character roles, maintaining a secure ledger of player choices, and generating dynamic AI responses, it keeps the campaign alive without putting additional prep work on the GM.

### 1.3 Built With

#### Backend & Server
![PHP](https://img.shields.io/badge/PHP-1A1515?style=for-the-badge&logo=php&logoColor=D3C5A3)
![MySQL](https://img.shields.io/badge/MySQL-1A1515?style=for-the-badge&logo=mysql&logoColor=D3C5A3)
![XAMPP](https://img.shields.io/badge/XAMPP-1A1515?style=for-the-badge&logo=xampp&logoColor=D3C5A3)

#### Frontend
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-1A1515?style=for-the-badge&logo=tailwind-css&logoColor=D3C5A3)
![HTML5](https://img.shields.io/badge/HTML5-1A1515?style=for-the-badge&logo=html5&logoColor=D3C5A3)
![CSS3](https://img.shields.io/badge/CSS3-1A1515?style=for-the-badge&logo=css3&logoColor=D3C5A3)

---

## 2. Getting Started

### 2.1 Prerequisites

- [XAMPP](https://www.apachefriends.org/) (for local Apache server and MySQL database)
- Git for version control
- A modern web browser

### 2.2 How to Install

1. **Clone the Repository**
Place the project folder inside your XAMPP `htdocs` directory (e.g., `C:/xampp/htdocs/chronicle_ai/`).

```bash
cd C:/xampp/htdocs/
git clone https://github.com/WihanBurger/chronicle_ai.git
```

2. **Start Local Server**
Open the XAMPP Control Panel and start both **Apache** and **MySQL**.

3. **Import the Database**
- Open phpMyAdmin at `http://localhost/phpmyadmin`
- Create a new database named `chronicle_db`.
- Click the **Import** tab, select the database export file provided in this repository (`database/database_export.sql`), and click **Go**.

4. **Launch the Application**
Open your browser and navigate to: `http://localhost/chronicle_ai/`

---

## 3. Features and Usage

- **AI-Driven Storytelling:** Generates dynamic narrative events and reacts to player choices using integrated AI logic.
- **Secure Authentication:** Robust user login and registration system with secure password hashes.
- **Role-Based Access Control:** Differentiates permissions between standard Adventurers and Guildmasters (Admins).
- **Asynchronous Campaign Tracking:** Players can interact with the story and log choices at their own pace.
- **Security-First Architecture:** All user inputs are sanitized and handled via MySQLi Prepared Statements to completely prevent SQL injection attacks.

---

## 4. Architecture & Database Design

The relational database (`chronicle_db`) is built in **Third Normal Form (3NF)** to eliminate data redundancy and ensure referential integrity. 

It includes the following core tables:
1. **`roles`**: Reference table defining user permissions (`adventurer`, `guildmaster`).
2. **`realms`**: Reference table storing available story worlds and their active/locked statuses.
3. **`members`**: Core table storing user credentials, secure password hashes, and foreign keys linking to roles.
4. **`journeys`**: Active campaign tracking table linked to members and realms via `INNER JOIN` queries.
5. **`story_logs`**: Permanent historical ledger logging all AI narration and player choices.

---

## 5. Design Concept

The UI is designed around a dark fantasy, tabletop aesthetic, heavily utilizing scrollytelling flows and character sheet layouts. 

**Color Palette**

| Midnight Ink (Primary) | Aged Parchment (Text/Accents) | Crimson Wax (Accent) |
|:---:|:---:|:---:|
| `#1A1515` | `#D3C5A3` | `#8B0000` |

**Typography**
- **Cormorant Garamond:** Used for headings and primary branding to evoke a classic fantasy novel feel.
- **Lora:** Used for highly readable, serif body text.

---

## 6. Highlights and Challenges

### Highlights
- **Database Architecture:** Successfully designing a 3NF relational database that seamlessly links users, roles, campaigns, and historical logs.
- **Security Implementation:** Ensuring the entire application is protected against SQL injection by exclusively using Object-Oriented MySQLi with Prepared Statements.
- **Thematic UI Integration:** Translating a Figma-designed scrollytelling layout into a fully responsive Tailwind CSS frontend that genuinely feels like a tabletop RPG.

### Challenges
- **AI Narrative Continuity:** Structuring the database logs to ensure the AI can reference past events seamlessly without overwhelming the context window.
- **Complex Joins:** Writing efficient `INNER JOIN` statements across multiple tables (`members`, `journeys`, `realms`, and `roles`) to populate the user dashboard dynamically based on their specific permissions.

---

## 7. License

Distributed under the MIT License.

---

## 8. Authors and Contact Info

- **Wihan Burger** - Developer