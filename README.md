# Chronicle AI — Tabletop RPG Storytelling Platform

## Overview
Chronicle AI is a production-ready PHP and MySQL web application designed for tabletop RPG enthusiasts. It features secure user authentication, role-based access control, and an AI-driven narrative storytelling engine.

---

## Tech Stack
- **Server Environment:** XAMPP (Apache & MySQL)
- **Backend:** PHP (Native, Object-Oriented MySQLi with Prepared Statements)
- **Frontend:** Tailwind CSS, Custom CSS, Google Fonts (Cormorant Garamond & Lora)
- **Version Control:** Git & GitHub

---

## Visual Mockups & UI Preview
Explore the core interface designs and scrollytelling layouts mapped during the project's development phase:

<p align="center">
  <img src="assets/Mockup 1.jpg" width="45%" alt="Chronicle AI Mockup 1" />
  <img src="assets/Mockup 2.jpg" width="45%" alt="Chronicle AI Mockup 2" />
</p>
<p align="center">
  <img src="assets/Mockup 3.jpg" width="45%" alt="Chronicle AI Mockup 3" />
  <img src="assets/Mockup 4.jpg" width="45%" alt="Chronicle AI Mockup 4" />
</p>

---

## Database Schema & Architecture
The relational database (`chronicle_db`) is built in Third Normal Form and includes the following tables:
1. **roles:** Reference table defining user permissions (adventurer, guildmaster).
2. **realms:** Reference table storing available story worlds and their active/locked statuses.
3. **members:** Core table storing user credentials, secure password hashes, and foreign keys linking to roles.
4. **journeys:** Active campaign tracking table linked to members and realms via `INNER JOIN` queries.
5. **story_logs:** Permanent historical ledger logging all AI narration and player choices.

---

## Setup & Installation Instructions
To run this project locally on your machine:

1. **Clone or Download Repository:**
   Place the project folder inside your XAMPP `htdocs` directory (e.g., `C:/xampp/htdocs/chronicle-ai/`).

2. **Start Local Server:**
   Open the XAMPP Control Panel and start both Apache and MySQL.

3. **Import the Database:**
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Create a new database named `chronicle_db`.
   - Click the **Import** tab, select the database export file provided in this repository (`database/database_export.sql`), and click **Go**.

4. **Launch the Application:**
   Open your browser and navigate to:
   `http://localhost/chronicle-ai/`

---

## Design & Documentation References
- **Wireframes & Storyboards:** Designed using Figma, mapping out scrollytelling flows and character sheet layouts.
- **Security Implementation:** All user inputs are handled via MySQLi Prepared Statements to completely prevent SQL injection attacks.