# FurryHaven - SPCA Management System

FurryHaven is a comprehensive management system designed for the Makhanda SPCA, developed by team EzTeck. This project was created for the 2025 Information System III System Development Project.

> 🏆 **Project Achievement:** This system secured **2nd Place** out of 32 teams in the competition. 

## Overview

The FurryHaven system is tailored to support the operations and mission of the Society for the Prevention of Cruelty to Animals (SPCA). It is designed to enhance the efficiency of SPCA facilities by managing animal welfare, preventing cruelty, promoting adoption, and engaging with the community to foster responsible pet ownership.

The system addresses the full lifecycle of animal care, from intake and medical treatment to adoption and community engagement, while also supporting crucial administrative functions like fundraising and reporting.

## Key Features

-   **Animal Intake and Registration:** Register stray, rescued, or surrendered animals with vital details and allocate them to available kennel spaces.
-   **Medical Care Management:** Record and track vaccinations, spaying/neutering, treatments, and overall health monitoring.
-   **Cruelty Management:** Capture and manage data on reported cases of animal cruelty or neglect, including location, evidence, and reporter details.
-   **Adoption and Rehoming:** Maintain a centralized database of adoptable animals, manage adoption applications, and facilitate post-adoption follow-ups.
-   **Community Engagement:** Organize and track volunteer hours, activities, and foster programs.
-   **Fundraising and Donation Management:** Capture and manage donor information, track pledges, and generate receipts.
-   **Public Portal:** Allows the general public to view information about the SPCA, see adoptable animals, and apply for adoption without needing to log in.
-   **Reporting and Analytics:** Generate reports on animal intake, medical care, adoption rates, cruelty cases, and kennel occupancy.

## System Users

The system provides different levels of access for various stakeholders:

-   **Administrative Staff:** Full access to all system functionalities.
-   **Veterinary Staff:** Can access and manage animal medical records and view all animal particulars.
-   **Volunteer Staff:** Can view public animal profiles, apply to foster, and interact with animals (e.g., walking, feeding).
-   **General Public:** Can view public information, browse adoptable animals, and submit adoption applications.

## Technologies Used

-   **Backend:** PHP
-   **Frontend:** HTML, CSS, JavaScript
-   **Database:** MySQL/MariaDB (inferred from common PHP setups)

## Getting Started

To set up the project locally, follow these steps:

1.  **Prerequisites:**
    -   A local web server environment (e.g., XAMPP, MAMP, WAMP).
    -   PHP and Composer installed.
    -   A MySQL/MariaDB database server.

2.  **Installation:**
    -   Clone the repository to your local machine.
    -   Place the project folder into your web server's root directory (e.g., `htdocs` for XAMPP).
    -   Navigate to the project directory in your terminal and run `composer install` to install dependencies.
    -   Create a new database in your database server (e.g., phpMyAdmin).
    -   Update the database connection details in `config/databaseconnection.php` with your database host, username, password, and database name.

3.  **Running the Application:**
    -   Start your local web server.
    -   Open your web browser and navigate to the project's URL (e.g., `http://localhost/untitled%20folder/`).

## The Team (EzTeck)

This project was brought to life by the dedicated members of team EzTeck:

| Name              | Role                      |
| ----------------- | ------------------------- |
| Ziyanda Saola     | Project Manager           |
| Elami Nxumalo     | Front-End Manager         |
| Karabo Ntsie      | Database Manager          |
| Clinton Nzeru     | Quality Assurance Manager |
| Thato Baloyi      | Back-End Manager          |

