Hi, I hope this clears things up since I did not push myself to full frontend design, as I was more focused on functionality since i was a bit busy and had to submit .

# Ticket Classification System

A simple support ticket management system with automated ticket classification. Supports manual creation, classification (AI or dummy, since no OpenAI key is provided), filtering, pagination, and CSV export.

## Setup Instructions

1. Clone the repository: `git clone <your-repo-url>` and `cd <repo-folder>`  
2. Install PHP dependencies: `composer install`  
3. Install Node dependencies: `npm install`  
4. Generate application key: `php artisan key:generate`  
5. Create a copy of the environment file: `cp .env.example .env`  
6. Configure and copy the .env.example to  `.env` variables as needed (database, OpenAI, queue, etc.)  
7. Run migrations and seeders: `php artisan migrate --seed`  
8. Compile assets: `npm run dev`  
9. Run the development server: `php artisan serve`  
10. (Optional) Start the queue worker for classification jobs: `php artisan queue:work`  

## Assumptions & Trade-offs

OpenAI classification is optional; if disabled, dummy classification is used. Frontend design is minimal and functional. Rate limiting for bulk classification is not implemented in the UI. No authentication or user roles implemented.  

### Optional: What I’d do with more time

Add full and way better frontend design and responsive layout, implement user authentication and roles, add real-time updates for classification, add bulk ticket actions and better rate-limiting.  



