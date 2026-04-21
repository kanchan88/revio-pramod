Here is a comprehensive and detailed specification prompt you can use to instruct a development team or an AI coding assistant to build your review site. 

***

### **System Prompt: Build a Minimalist SaaS-Style Review Platform**

**Role:** You are an expert Full-Stack Developer and UI/UX Designer. Your task is to architect, design, and build a modern digital product review platform similar to GSMArena, but featuring a highly refined, clean, and minimalist SaaS-style interface. 

#### **1. Platform Overview**
* **Goal:** Create a comprehensive review and specification database for consumer products.
* **Design Language:** Clean, minimalist SaaS aesthetic. Think Stripe, Vercel, or Linear. Use abundant white space, subtle borders, soft shadows, sans-serif typography (e.g., Inter or Roboto), and a monochromatic base with a single primary accent color. Both Light and Dark modes are required.
* **Core Entities:** Categories, Subcategories, Products, Expert Reviews, and User Ratings.

#### **2. Taxonomy & Hierarchy**
The platform must support a strict hierarchical routing system:
* **Level 1: Categories** (Mobile Phones, Gadgets, Laptops, Cars)
* **Level 2: Subcategories** (e.g., *Mobile Phones* $\rightarrow$ Smartphones, Feature Phones; *Cars* $\rightarrow$ EVs, SUVs, Sedans; *Laptops* $\rightarrow$ Gaming, Ultrabooks)
* **Level 3: Detail Product View** (The individual product page with full specs and reviews)

#### **3. Product Data Structure (15-20 Parameters per Product)**
The database must be flexible enough to handle different schemas per category. Implement the following parameters for each category:

**Category A: Mobile Phones & Tablets**
1. Brand | 2. Model Name | 3. Release Date | 4. OS Version | 5. Dimensions & Weight | 6. Build Materials (Glass/Aluminum/Plastic) | 7. Display Type (OLED/LCD) & Size | 8. Display Resolution & Refresh Rate | 9. Processor/Chipset | 10. RAM Options | 11. Storage Options | 12. Main Camera Specs (MP, Aperture) | 13. Front Camera Specs | 14. Battery Capacity (mAh) | 15. Charging Speed (W) | 16. IP Rating (Water/Dust) | 17. Connectivity (5G, Wi-Fi standard) | 18. Price (MSRP)

**Category B: Laptops**
1. Brand | 2. Model Line | 3. Form Factor (Clamshell/2-in-1) | 4. Display Size & Aspect Ratio | 5. Display Resolution & Panel Type | 6. CPU Model | 7. GPU Model | 8. RAM Capacity & Type | 9. Storage Capacity & Type (SSD) | 10. Battery Size (Wh) | 11. I/O Ports | 12. Wireless Connectivity | 13. Keyboard Details | 14. Trackpad Type | 15. Webcam Resolution | 16. Dimensions | 17. Weight | 18. Base Price

**Category C: Cars**
1. Make | 2. Model | 3. Year | 4. Body Style | 5. Powertrain (ICE, Hybrid, EV) | 6. Engine/Motor Output (HP) | 7. Torque | 8. Transmission | 9. Drivetrain (AWD, FWD, RWD) | 10. 0-60 mph Time | 11. Range or MPG | 12. Charging Speed/Fuel Capacity | 13. Seating Capacity | 14. Cargo Space | 15. Infotainment Screen Size | 16. Advanced Safety Features (ADAS) | 17. Base MSRP

**Category D: Gadgets (Smartwatches, Drones, Audio)**
*(Requires a generic schema, but standard parameters include:)*
1. Brand | 2. Model | 3. Device Type | 4. Battery Life | 5. Connectivity | 6. Water Resistance | 7. Weight | 8. Dimensions | 9. Companion App OS | 10. Core Sensors | 11. Charging Method | 12. Launch Price (Add 3-5 specific variables depending on subcategory)

#### **4. Core Page Layouts & UI Components**

**A. Homepage**
* **Hero Section:** Minimalist search bar centered on the screen. 
* **Quick Navigation:** Icon-based grid for Categories (Phones, Laptops, Cars, Gadgets).
* **Trending/Latest:** A horizontal scrolling carousel of SaaS-style cards showing newly added products (thumbnail, name, rating badge).

**B. Category / Subcategory Pages**
* **Layout:** Sidebar for filtering (Brand, Price Range, Key Specs). Main area utilizes a responsive CSS grid displaying product cards.
* **Product Cards:** Clean borders. Must show: Image thumbnail, Brand, Model Name, 3 key specs, and aggregated review score (e.g., 4.8/5).

**C. Detailed Product View (The Core Feature)**
* **Header:** Sticky top bar with the product name, price, and a "Compare" button.
* **Hero Section:** High-quality image gallery on the left; Key summary specs and Review Score module on the right.
* **Specification Table:** Use a clean, alternating-row data table style. Group specs by categories (e.g., *Performance*, *Display*, *Design*). Do not use heavy grid lines; use subtle borders or background shades.
* **Review Section:** Tabbed interface separating "Expert Editor Review" (text-heavy, formatted with rich typography) and "User Reviews" (list of user ratings with pros/cons tags).

#### **5. Functional Requirements**
* **Search & Autocomplete:** Blazing fast global search. When typing, show instant dropdown results categorizing hits (e.g., showing a phone and a car if they share a model name).
* **Comparison Engine:** Users can select up to 3 items within the same category. Output a side-by-side sticky column table highlighting differences in green/red text.
* **Responsive Design:** The spec tables must elegantly collapse into an accordion or scrollable horizontal view on mobile devices.

#### **6. Recommended Tech Stack**
* **Frontend:** Next.js (React), TailwindCSS (for the minimalist styling), Framer Motion (for subtle interactions), Radix UI or Shadcn/ui (for accessible, unstyled core components).
* **Backend/Database:** PostgreSQL, Prisma ORM (to handle the complex relationships between categories and specs).
* **Search:** Algolia or Meilisearch for high-speed indexing.