# Scriptly Platform - Architectural Notes & Business Logic (`note.md`)

This file documents all business logic, marketplace rules, pricing validation standards, and architectural blueprints for the Scriptly Escrow Marketplace.

---

## 1. Service Hierarchy & Category Taxonomy

Services on Scriptly are structured hierarchically: Top-Level Service Category -> Sub-Service / Scope Branch -> Specific Deliverable Metrics.

### Category 1: Writing, Academic & Content Strategy
- **Sub-Services**:
  1. **Final Year Project Writing**: Undergraduate / Postgraduate thesis, Chapters 1–5, Literature Review, Methodology, Questionnaire Design, SPSS data analysis, Turnitin plagiarism check.
  2. **Serious Executive & Feasibility Report Writing**: Business plans, Market feasibility studies, Pitch deck financial models, Corporate whitepapers.
  3. **Academic & Research Papers**: Journal submissions, Conference papers, Theoretical frameworks, Bibliography & citation reviews.
  4. **Copywriting & Digital Content**: High-converting sales copy, SEO blog articles, Website copy, Technical manuals, Product descriptions.

- **Mandatory Writing Scope Metrics**:
  - Exact Page Count or Word Count (e.g. *1–5 pages*, *6–15 pages*, *16–30 pages*, *50+ pages / Chapters 1–5*).
  - Data Analysis & Modeling (*SPSS / Survey Analysis*, *Excel Financial Model*, *None*).
  - Citation & Formatting Standard (*APA 7th, Harvard, IEEE, MLA, Corporate*).
  - Plagiarism Report Requirement (*Turnitin Report < 10% Required*).

---

### Category 2: Web Development & Software Engineering
- **Sub-Services / Scope Branches**:
  1. **Full-Stack Web Application**: End-to-end system including Frontend UI, Backend API/business logic, Database architecture, and server deployment.
  2. **Frontend Development Only**: UI/UX implementation into responsive web interfaces (React, Vue, Tailwind CSS, HTML/JS) with API integration.
  3. **Backend & API Development Only**: REST/GraphQL APIs, Relational Database schemas (MySQL/PostgreSQL), Authentication (JWT/OAuth), and server-side processing.
  4. **CMS & Custom Websites**: WordPress, Webflow, Shopify, Custom PHP Portals (5–10 pages).

- **Mandatory Web Scope Metrics**:
  - Development Tier (*Full-Stack*, *Frontend Only*, *Backend Only*, *CMS Website*).
  - Post-Launch Support Tier (*Delivery Only*, *14-Day Bug Warranty*, *30–60 Days Maintenance*).
  - Target Tech Stack (*PHP MVC, Laravel, Node.js, React, Tailwind, MySQL, etc.*).

---

### Category 3: Mobile App Development
- **Sub-Services**:
  1. **Cross-Platform Mobile App** (Flutter, React Native).
  2. **Native iOS App** (Swift, SwiftUI).
  3. **Native Android App** (Kotlin, Jetpack Compose).
  4. **Mobile App UI Only / Mobile Backend API Only**.

---

### Category 4: UI/UX & Product Design
- **Sub-Services**:
  1. **Complete Web & Mobile UI/UX Design System** (Figma prototypes, user journeys, design tokens).
  2. **Brand Identity & Vector Design** (Logos, brand guidelines, stationery).
  3. **Pitch Deck & Presentation Design** (Investor decks, slide decks).

---

### Category 5: Data Analysis, AI & Analytics
- **Sub-Services**:
  1. **SPSS / Statistical Data Analysis** (Survey analysis, regression, hypothesis testing).
  2. **Business Intelligence & Dashboards** (PowerBI, Tableau, Excel automation).
  3. **Database Architecture & SQL Migration**.

---

### Category 6: Cloud, DevOps & Cyber Security
- **Sub-Services**:
  1. **CI/CD Deployment & Automation** (Docker, Kubernetes, GitHub Actions).
  2. **Cloud Server Infrastructure** (AWS, DigitalOcean, VPS setup, SSL/DNS).
  3. **Security Audit & Penetration Testing**.

---

## 2. Category Pricing Floors & Anti-Lowballing Standards

| Service Category | Sub-Service / Scope | Minimum Price Floor (₦) | Recommended Budget Range (₦) |
| :--- | :--- | :--- | :--- |
| **Writing & Academic** | Final Year Project (Chapters 1–5 + SPSS) | ₦50,000 | ₦60,000 – ₦180,000 |
| **Writing & Academic** | Executive Feasibility / Business Report | ₦30,000 | ₦45,000 – ₦150,000 |
| **Writing & Academic** | Copywriting / SEO Articles (Pack) | ₦15,000 | ₦20,000 – ₦60,000 |
| **Web Development** | Full-Stack Web System | ₦180,000 | ₦250,000 – ₦1,500,000+ |
| **Web Development** | Frontend Development Only | ₦70,000 | ₦100,000 – ₦350,000 |
| **Web Development** | Backend & API Development Only | ₦80,000 | ₦120,000 – ₦450,000 |
| **Web Development** | Standard Website (5–10 Pages) | ₦60,000 | ₦80,000 – ₦250,000 |
| **Web Development** | Single Landing Page | ₦25,000 | ₦35,000 – ₦70,000 |
| **Mobile App** | Cross-Platform / Native App | ₦200,000 | ₦300,000 – ₦2,000,000+ |
| **UI/UX Design** | Full App / Web UI Prototype (Figma) | ₦50,000 | ₦75,000 – ₦300,000 |
| **Data & Analytics** | Statistical / SPSS Data Analysis | ₦25,000 | ₦35,000 – ₦100,000 |
| **Cloud & DevOps** | CI/CD & Server Infrastructure | ₦60,000 | ₦90,000 – ₦300,000 |

---


### D. Revisions & Dispute Protection:
- If work is incomplete or violates agreed scope, the client clicks **"Request Revision"** (which resets/pauses the auto-approval timer).
- If the parties cannot agree, either party triggers **"Escrow Dispute & Mediation"**, where Scriptly admins review submitted files against mandatory scope metrics.

---

## 4. In-App Messaging, Custom Offer & Anti-Circumvention Security
- **Multimedia Capabilities**: Text, Voice Notes (waveform audio preview), Documents (PDF/DOCX/ZIP), and Image attachments.
- **In-Platform Scheduled Meetings**: In-chat "Schedule Platform Call" generator creating secure video meeting bridges (Google Meet) without exposing private personal phone numbers.
- **Fiverr-Style Custom Offer & Escrow Activation**:
  - Direct in-chat negotiation $\rightarrow$ Set final agreed price, milestone phases, and delivery deadline.
  - **Payment-First Rule**: The project order timer ONLY starts counting once escrow payment is deposited and locked.
  - **Auto-Delisting**: Once escrow deposit is confirmed, the project listing is immediately de-listed from the public marketplace.
- **Multi-Lingual Abuse & Contact Filter**:
  - Real-time regex and dictionary filter for abusive words (English, Pidgin, Igbo, Yoruba, Hausa).
  - Anti-circumvention detection for phone numbers, WhatsApp, Telegram handles, and external bank accounts. Violations trigger account warning and suspension flags.

---

## 5. UI & Design Language Standards
- **Border Radius**: Strictly `rounded-[3px]` across all components.
- **Indicators**: Status dots, profile avatars, and notification bell badges are `rounded-full`.
- **Colors**: Royal Blue (`#1952E1`), Deep Navy (`#0A2342`), Canvas (`#EFF2F7`), Card BG (`#FFFFFF`), Border (`border-slate-200/90`). **NO GRADIENTS**.
- **Mobile Navigation**: 5 items only on bottom dock (`index.php`, `projects.php`, `post-project.php` center CTA, `talent.php`, and `More` bottom sheet modal).
