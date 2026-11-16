```markdown
# 🌾 Grameen Setu — Farm to Business Marketplace

> **From the field to the market — in one click.**[cite: 15]

**Grameen Setu** (*"The Rural Bridge"*) is a farmer-first, farm-to-business (F2B) digital agricultural marketplace designed to connect smallholder farmers in Bangladesh directly with retailers, wholesalers, and institutional buyers[cite: 3, 38]. By eliminating exploitative intermediaries, Grameen Setu empowers farmers to capture fair market prices while giving buyers transparent sourcing, real-time bidding, and verified supply[cite: 3, 38].

---

## 🎯 The Problem & Our Solution

In traditional agricultural supply chains (`Farmer → Local Trader → Wholesaler → Retailer → Consumer`), smallholder farmers typically receive only **40–50%** of the final market price, while buyers pay **2–3x** more due to middlemen[cite: 38]. Limited price visibility and slow sales channels also contribute to **~20% post-harvest losses**[cite: 38].

**Grameen Setu** bridges this gap with an accessible, community-driven platform featuring[cite: 3, 38]:
* **Direct Produce Listings & Stock Management:** Farmers list harvests with transparent pricing and automatic inventory deduction upon order placement[cite: 3, 4].
* **Live Bidding Arena:** Time-bound auctions with countdown timers, minimum bid increments (৳50), and instant bid history[cite: 3, 29].
* **Emergency Bids:** Buyers post urgent purchase requests (crop, quantity, offered price, delivery location) so nearby farmers can fulfill demand immediately[cite: 3, 17, 29].
* **Two-Way Trust & Coin Rewards:** Buyers and farmers rate each other post-delivery[cite: 4, 18]. When farmers rate buyers (1–5 stars), buyers earn **Grameen Coins** (3–12 coins) redeemable for **5% to 35% discounts** on future orders[cite: 10, 20, 21].
* **Live Market Intelligence:** Interactive price trend charts (`Chart.js`), 7-day crop price movements, and regional demand insights[cite: 4, 25].
* **Secure Messaging Hub:** Direct buyer-seller negotiation threads with unread badges and message search[cite: 14, 25].
* **Role-Based Dashboards & Admin Moderation:** Dedicated interfaces for **Farmers (Sellers)**, **Buyers**, and **Administrators** (who verify and approve product listings and orders before they go live)[cite: 3, 4].

---

## 🛠️ Technology Stack

* **Frontend:** HTML5, CSS3, Bootstrap, Tailwind CSS, Vanilla JavaScript, Chart.js, Ionicons[cite: 7, 27, 34]
* **Backend:** PHP (RESTful JSON APIs, session management, input sanitization)[cite: 3, 21]
* **Database:** MySQL (`grameen_setu` relational schema with ACID transactions for bidding, ratings, and coin redemptions)[cite: 3, 8, 20, 21]
* **Authentication & Security:** Role-based access control, `bcrypt` password hashing, and 6-digit Email OTP verification (valid for 10 minutes) via SMTP[cite: 3, 15, 16, 30]
* **Offline & Static Fallback:** `localStorage` state persistence for seamless demo previews[cite: 4, 7]

---

## ✨ Key Modules & Features

| Module | Description |
| :--- | :--- |
| **Auth & OTP Verification** | Role selection (`Farmer` / `Buyer`), unique NID/Email/Phone validation, and 6-digit email OTP verification for signup and login[cite: 15, 29]. |
| **Seller Dashboard** | Track active products, orders received, total revenue, and pending deliveries; manage stock and update order statuses (`pending → shipped → delivered`)[cite: 4, 13, 25]. |
| **Buyer Dashboard** | Browse verified produce, search farmers by location, view coin wallet balance, and place orders via bKash, Nagad, Rocket, or Cash on Delivery[cite: 4, 11]. |
| **Live Bidding & Emergency Bids** | Dual-mode arena (`Bidder Mode` & `Seller Mode`) supporting live crop auctions and urgent buyer procurement requests[cite: 17, 29]. |
| **Trending & Market Intel** | Filterable crop index (`Grains`, `Vegetables`, `Spices`), weekly price comparison graphs, and export/shortage alerts[cite: 14, 25]. |
| **Trust, Ratings & Coin Wallet** | Star-based feedback system where verified buyer ratings unlock redeemable discount tiers (`20 coins = 5% OFF` up to `150 coins = 35% OFF`)[cite: 10, 21]. |
| **Transaction History** | Unified financial ledger filtering across `Bids`, `Orders`, and `Payments`[cite: 12, 18]. |
| **Admin Panel** | Moderation queue to confirm pending products, approve orders, and audit platform transactions[cite: 18, 19]. |

---

## 📂 Project Structure

```text
GrameenSetu/
├── api/                        # PHP REST API Endpoints
│   ├── admin.php               # Admin moderation (orders, products, transactions)
│   ├── auth.php                # Registration, login, and 6-digit OTP verification
│   ├── bidding.php             # Live auctions & emergency bid CRUD operations
│   ├── dashboard.php           # Role-specific KPI aggregation
│   ├── messages.php            # Buyer-seller chat threads & read receipts
│   ├── orders.php              # Order creation & status lifecycle
│   ├── products.php            # Produce catalog & inventory management
│   ├── profile.php             # User settings & profile updates
│   ├── ratings.php             # Two-way ratings & coin wallet redemption
│   ├── seller_stats.php        # Seller revenue & order metrics
│   └── transactions.php        # Payment & coin ledger logging
├── assets/
│   ├── css/style.css           # Custom green-and-cream theme & responsive rules
│   └── js/                     # Frontend logic, Chart.js configs & offline demo mode
├── config/
│   └── database.php            # PDO MySQL connection & JSON response helpers
├── index.html                  # Main Buyer / Seller Dashboard
├── login.html                  # Sign In, Create Account & OTP Verification Portal
├── bidding.html                # Live Bidding Arena & Emergency Bids
├── trending.html               # Live Market Trends & Price Index
├── messages.html               # Secure Messaging Hub
├── history.html                # Complete Transaction Ledger
├── rating.html                 # Trust, Ratings & Grameen Coin Wallet
├── admin.html                  # Admin Confirmation & Audit Panel
├── settings.html               # Account & Farm Profile Settings
├── database.sql                # MySQL schema & seed structure
└── composer.json               # PHP dependencies (PHPMailer)

```

---

## 🚀 Getting Started

### Option 1: Static Frontend Demo (GitHub Pages)

Because GitHub Pages hosts static files only, `GrameenSetu` includes a built-in `localStorage` fallback mode so you can explore the dashboards, bidding UI, market trends, and ratings directly in the browser without a PHP server:

* **Live Preview:** `https://rehnuma14.github.io/GrameenSetu/`

### Option 2: Full-Stack Local Setup (PHP + MySQL)

To run the complete backend with database persistence, live bidding APIs, and email OTP authentication:

1. **Prerequisites:** Install **XAMPP**, **Laragon**, **WAMP**, or a local LAMP stack with **PHP 8.0+**, **MySQL**, and **Composer**.
2. **Clone the Repository:**
```bash
git clone [https://github.com/rehnuma14/GrameenSetu.git](https://github.com/rehnuma14/GrameenSetu.git)
cd GrameenSetu

```


3. **Install PHP Dependencies:**
```bash
composer install

```


4. **Configure the Database:**
* Start Apache and MySQL.
* Create a database named `grameen_setu` in phpMyAdmin or MySQL CLI.


* Import `database.sql` into `grameen_setu`.


* Update database credentials (and SMTP settings for OTP emails) in `config/database.php`.




5. **Launch the App:**
* Place the project folder inside your web root (e.g., `htdocs/grameen_setu`).


* Open `http://localhost/grameen_setu/login.html` in your browser.





---

## 👥 Team & Contributors

Developed for **CSE2221: Database Management Systems** — Department of Computer Science & Engineering, **Premier University, Chattogram**.


| Student ID | Name | Role & Key Contributions |
| :--- | :--- | :--- |
| `0222410005101125` | **Jobaida Khanam Juli** | **Project Lead** — Admin Panel, Buyer Dashboard, Trust & Ratings + Coin Wallet, Transaction History, Settings |
| `0222410005101123` | **Rehnuma Ahmed** | Login & Signup Flow, Email OTP Integration, Live Bidding Arena & Emergency Bids, Responsive Sidebar Navigation |
| `0222410005101107` | **Fahmida Sultana Rifat** | Seller Dashboard, Live Market Trends & Price Charts, Secure Messaging Hub |

---

## 🔮 Future Roadmap

* **SMS Gateway Integration:** Rural-friendly SMS alerts for bid updates and OTPs alongside email.


* **Bengali Localization:** Full Bangla UI toggle for smallholder farmers.


* **Third-Party Logistics APIs:** Integrated farm-to-doorstep delivery tracking.



```

```
