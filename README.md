# 📝 Laravel + Vue - Article Revision Feature

## Laravel RealWorld Example App (Upgraded to Laravel 12)

This project extends the **RealWorld Example App** by upgrading the backend from **Laravel 8** to **Laravel 12** and implementing an **Article Revision** feature.  

The feature ensures that every time an article is updated, its previous state is saved as a revision. Users can view revision history and revert articles to earlier versions.

---

## Setup & Installation

1. **Clone the repository:**
     ```sh
     git clone https://github.com/eman-m0hamed/laravel-realworld-example-app.git
     cd laravel-realworld-example-app
     ```

2. **Install dependencies:**
     - Open cmd inside the application then run this command to Install Dependencies:
     ```sh
     composer install
    
     ```

3. **Environment setup:**
     - Copy `.env.example` to `.env` and update database and other settings as needed.
     - Generate application key:
         ```sh
         php artisan key:generate
         ```

4. **Database setup:**
     - Create your database and update `.env` accordingly.
     - Run migrations and seeders:
         ```sh
         php artisan migrate --seed
         ```

5. **Run the application:**
    - To run the project write this command in project terminal
     ```sh
     php artisan serve
     ```
     The app will be available at `http://localhost:8000`.

## API Authentication
- Uses JWT (JSON Web Token) for API authentication.
- Register/login to receive a token, then use `Authorization: Bearer <token>` in API requests.

## Access ApI token
- user needs login to have the token.
- can use /api/login route.
- if the user does not have have account can register through /api/users route.

## How to send token with request
To make requests to the protected API endpoint, you need to include an access token in the request headers.
    
    Authorization: Bearer {access_token}



## Article Revision Feature

### Overview
- Each `Article` can have multiple `ArticleRevision` records, representing the history of changes.
- Revisions are linked to their parent article by `article_id`.
- Each revision can have its own set of tags.

### Endpoints
- **List revisions:** `GET /api/articles/{article}/revisions`  
    Returns all revisions for the given article (by slug), including tags if available.

- **Show revision:** `GET /api/articles/{article}/revisions/{revision}`  
    Returns a specific revision for the article, including tags.

- **Revert to revision:** `POST /api/articles/{article}/revisions/{revision}/revert`  
    Updates the article's content to match the selected revision.

### Assumptions & Design Decisions
- **Route Model Binding:**
    - Article revision endpoints use article slug for binding, not id, for clarity and performance.
    - The controller checks that the revision belongs to the article.
- **Authorization:**
    - Policy methods (`viewRevisions`, `revertRevisions`) are used to restrict access to revision actions.
- **Tags:**
    - If `ArticleRevision` has a `tags` relationship, tags are loaded and included in responses.
- **Error Handling:**
    - All API errors return a consistent JSON structure with `success`, `status`, and `message` fields.

## Notes
- This project uses the Laravel 12 slim skeleton structure: middleware and exception handling are registered in `bootstrap/app.php`.
- For further customization, see the `ApiExceptionTrait` and `ApiResponseTrait` in `app/Traits`.

- `ApiExceptionTrait` This trait is used inside Bootstrap/app.php to catch exceptions and return JSON instead of HTML error pages.

- `ApiResponseTrait` This trait helps you return consistent API responses for success and error cases.



For questions or contributions, please open an issue or pull request.



