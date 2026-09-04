# SavioTrack - System Architecture & Security Analysis

## Overview
SavioTrack is a personal savings tracking web application built on Laravel 11. It allows users to manage savings goals, track wants and needs, record deposits, and submit withdrawal requests with AI-powered viability scoring.

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 11.56.1 (PHP 8.3) |
| Database | Supabase PostgreSQL (via Supavisor connection pooler) |
| Cache | Redis via Upstash (serverless, TLS) |
| Sessions | Redis (encrypted, 120-minute lifetime) |
| Queues | Redis (for async job processing) |
| Frontend | CDN Tailwind CSS + inline styles (no Vite build pipeline at runtime) |
| Hosting | Railway (AMS region) |
| Containerization | Docker (PHP 8.3-FPM base image) |

---

## Database Schema

### `users`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Auto-incrementing primary key |
| name | varchar(255) | User's full name |
| email | varchar(255) | Unique email address |
| email_verified_at | timestamp (nullable) | Email verification timestamp |
| password | varchar(255) | Bcrypt hashed password |
| remember_token | varchar(100) | "Remember me" session token |
| timestamps | timestamp | Created/updated timestamps |

### `password_reset_tokens`
| Column | Type | Description |
|--------|------|-------------|
| email | varchar(255) (PK) | User's email |
| token | varchar(255) | Reset token |
| created_at | timestamp | Token creation time |

### `sessions`
| Column | Type | Description |
|--------|------|-------------|
| id | varchar(255) (PK) | Session ID |
| user_id | bigint (FK, nullable) | Linked user |
| ip_address | varchar(45) (nullable) | Client IP |
| user_agent | text (nullable) | Browser user agent |
| payload | longtext | Encrypted session data |
| last_activity | integer | Last activity timestamp |

### `goals`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK) | Owner user |
| name | varchar(255) | Goal name |
| description | text (nullable) | Goal description |
| target_amount | decimal(15,2) | Target savings amount |
| current_amount | decimal(15,2) | Current saved amount |
| deadline | date (nullable) | Target completion date |
| status | enum: active,paused,completed,archived | Goal status |
| metadata | json (nullable) | Additional metadata |
| timestamps | timestamp | Created/updated |

### `wants`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK) | Owner user |
| goal_id | bigint (FK) | Linked goal |
| name | varchar(255) | Want name |
| description | text (nullable) | Want description |
| cost | decimal(15,2) | Want cost |
| priority | enum: low,medium,high | Priority level |
| status | enum: pending,saved,purchased,cancelled | Current status |
| metadata | json (nullable) | Additional metadata |
| timestamps | timestamp | Created/updated |

### `needs`
- Same schema as `wants`

### `deposits`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK) | Owner user |
| goal_id | bigint (FK) | Linked goal |
| amount | decimal(15,2) | Deposit amount |
| frequency | enum: daily,weekly,monthly,one_time | Deposit frequency |
| deposited_at | timestamp | Deposit date |
| metadata | json (nullable) | Additional metadata |
| timestamps | timestamp | Created/updated |

### `withdrawals`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK) | Owner user |
| goal_id | bigint (FK) | Linked goal |
| amount | decimal(15,2) | Withdrawal amount |
| reason | text | Reason for withdrawal |
| viability_score | integer (nullable) | AI-computed score (1-10) |
| decision | enum: pending,approved,rejected | Current decision status |
| decision_quality | enum: safe,bad,neutral | AI assessment of decision quality |
| ai_summary | text (nullable) | AI-generated analysis summary |
| user_notes | text (nullable) | User's notes on decision |
| metadata | json (nullable) | Additional metadata |
| timestamps | timestamp | Created/updated |

### `ai_interactions`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK) | Owner user |
| related_type | varchar(255) | Polymorphic relation type (Goal, Withdrawal, Deposit) |
| related_id | bigint (nullable) | Polymorphic relation ID |
| type | varchar(255) | Interaction type |
| prompt | text | User/system prompt |
| response | text (nullable) | AI response |
| context | json (nullable) | Contextual data |
| timestamps | timestamp | Created/updated |

---

## Security Features

### 1. Authentication

| Feature | Implementation |
|---------|---------------|
| **Guard** | Laravel `session` guard via `web` (config/auth.php) |
| **User Provider** | Eloquent ORM (`App\Models\User`) |
| **Password Hashing** | Bcrypt with `BCRYPT_ROUNDS=12` (config/app.php) |
| **Password Confirmation** | 3-hour timeout (`AUTH_PASSWORD_TIMEOUT=10800`) |
| **Rate Limiting** | 5 failed login attempts per email+IP combo triggers lockout (LoginRequest) |
| **Session Regeneration** | Token regenerated on login to prevent session fixation |
| **CSRF Protection** | Automatic CSRF tokens on all POST/PUT/DELETE forms |

### 2. Authorization

| Feature | Implementation |
|---------|---------------|
| **Route Protection** | `auth` and `verified` middleware on all authenticated routes |
| **User Scoping** | Every query uses `Auth::id()` or `Auth::user()` to scope to the authenticated user |
| **Goal Ownership** | `Auth::user()->goals()` relation ensures users can only access their goals |
| **Want/Need Ownership** | `where('user_id', Auth::id())` on all Want/Need queries |
| **Profile Updates** | Uses `Rule::unique(User::class)->ignore($this->user()->id)` for email uniqueness |
| **Account Deletion** | Requires current password confirmation via `password` rule |

### 3. Session Security

| Feature | Implementation |
|---------|---------------|
| **Driver** | Redis (encrypted) |
| **Lifetime** | 120 minutes idle timeout |
| **Encryption** | `SESSION_ENCRYPT=true` encrypts all session data |
| **Cookie Security** | `http_only=true`, `same_site=lax` |
| **Session Invalidation** | On logout, session is invalidated and token regenerated |

### 4. Input Validation

| Feature | Implementation |
|---------|---------------|
| **Login** | Validates email format and required password (LoginRequest) |
| **Registration** | Validates name, unique email, confirmed password with `Rules\Password::defaults()` |
| **Profile Updates** | Validates name and email uniqueness (ProfileUpdateRequest) |
| **Goal Creation** | Validates name, description, target_amount (min 0.01), deadline |
| **Want/Need Creation** | Validates name, cost, priority (low/medium/high), status |
| **Deposit Recording** | Validates amount, frequency, deposited_at |
| **Withdrawal Requests** | Validates amount (min 0.01, max current_amount), reason |
| **Withdrawal Processing** | Validates decision (approved/rejected), optional user_notes |
| **Password Reset** | 60-minute token expiry, 60-second throttle |

### 5. Output Security

| Feature | Implementation |
|---------|---------------|
| **Mass Assignment** | Explicit `$fillable` arrays on all models (User, Goal, Want, Need, Deposit, Withdrawal, AiInteraction) |
| **Attribute Hiding** | `$hidden` property on User model hides password and remember_token |
| **Output Encoding** | Blade auto-escaping on all template variables |
| **CSRF Tokens** | All forms include `@csrf` directive |

### 6. Infrastructure Security

| Feature | Implementation |
|---------|---------------|
| **HTTPS** | Platform-level HTTP→HTTPS 301 redirect (Railway proxy) |
| **APP_DEBUG** | Set to `false` in production |
| **APP_KEY** | 32-byte AES-256-CBC encryption key |
| **Secret Management** | Database credentials and Redis tokens in environment variables (never committed) |
| **Docker** | `.dockerignore` excludes `.env`, `.git`, `vendor`, `node_modules` |
| **Cache** | Redis cache uses database index 0 (Upstash-compatible) |

---

## User Flow

### 1. Access Flow

```
[Visitor]
    ↓
Landing Page (welcome.blade.php)
    ↓
    ├── [Guest] → Login/Register
    └── [Authenticated] → Dashboard
```

### 2. Registration Flow

```
1. User visits /register
2. Fills registration form (name, email, password, confirm_password)
3. Form submitted → RegisteredUserController::store()
   a. Validate input (email uniqueness, password confirmation)
   b. Create User with Hash::make(password)
   c. Fire Registered event
   d. Auto-login user
   e. Redirect to /dashboard
```

### 3. Login Flow

```
1. User visits /login
2. Enters email and password
3. Form submitted → AuthenticatedSessionController::store()
   a. LoginRequest::authenticate() called
   b. Rate limiter check (5 attempts per email+IP)
   c. Auth::attempt() with remember option
   d. Session regeneration
   e. Redirect to intended URL or /dashboard
4. On failure: Rate limit incremented, "Invalid credentials" error
```

### 4. Dashboard Flow

```
1. User visits /dashboard (requires auth + verified middleware)
2. GoalController::dashboard() called
   a. Fetch user's goals (latest first)
   b. Calculate totalSaved (sum of current_amount)
   c. Calculate totalTarget (sum of target_amount)
   d. Fetch recent deposits (last 10)
   e. Fetch recent withdrawals (last 10)
   f. Check for AI dashboard view mode (?view=progress|savings|deadlines)
   g. If AI view: call AiService::arrangeDashboard()
   h. Render dashboard/index.blade.php or dashboard/ai.blade.php
```

### 5. Goal Management Flow

```
1. User visits /goals (GoalController::index)
   → Paginated list of user's goals (20 per page)

2. User clicks "New Goal" → /goals/create (GoalController::create)
   → Shows goal creation form

3. User submits form → /goals (GoalController::store)
   a. Validate: name, description, target_amount, deadline
   b. Create goal via Auth::user()->goals()->create()
   c. Redirect to goal details page with success message

4. User views goal details → /goals/{id} (GoalController::show)
   a. Fetch goal (scoped to user)
   b. Fetch associated wants, needs, recent deposits
   c. Calculate totals
   d. Display goal card with progress bar

5. User edits goal → /goals/{id}/edit (GoalController::edit)
   → Pre-filled form with current goal data

6. User submits edit → /goals/{id} (GoalController::update)
   a. Validate all fields including status
   b. Update goal record
   c. Redirect to goal details

7. User deletes goal → DELETE /goals/{id} (GoalController::destroy)
   a. Delete goal (cascades to wants, needs, deposits, withdrawals)
   b. Redirect to goals list

8. User creates wants/needs/deposits/withdrawals from goal page
```

### 6. Want/Need Management Flow

```
1. User creates want/need from goal page → /wants/create?goal_id={id}
2. Form submission → /wants (WantController::store)
   a. Goal ownership verified (where user_id = auth user)
   b. Validate: name, cost (min 0.01), priority, status
   c. Create want/need linked to user and goal
   d. Redirect to wants/needs list

3. Subsequent operations (edit, update, delete) all scope to user_id
4. Wants/Needs appear on goal details page
```

### 7. Deposit Flow

```
1. User visits /deposits/create?goal_id={id}
   → Shows deposit form

2. User submits deposit → /deposits (DepositController::store)
   a. Validate: amount (min 0.01), frequency, deposited_at
   b. Create deposit via DepositService::createDeposit()
   c. If frequency != 'one_time', dispatch ProcessDepositJob
   d. ProcessDepositJob increments goal.current_amount
   e. Log AiInteraction for deposit processing
   f. Redirect to deposits list

3. ProcessDepositJob (queued via Redis):
   a. Find associated goal
   b. Increment current_amount
   c. Create AiInteraction log entry
```

### 8. Withdrawal Flow

```
1. User visits /withdrawals/create?goal_id={id}
   → Shows withdrawal form

2. User submits withdrawal request → /withdrawals (WithdrawalController::store)
   a. Validate: amount (min 0.01, max goal.current_amount), reason
   b. Create withdrawal with status='pending'
   c. Dispatch AIAnalysisJob (queued via Redis)
   d. Redirect to withdrawal detail page

3. AIAnalysisJob (queued):
   a. Fetch goal's wants and needs (non-cancelled)
   b. Calculate viability score (1-10) based on:
      - Amount vs current savings
      - Amount vs total wants/needs costs
      - Progress percentage
      - Savings progress
   c. Generate AI summary with assessment
   d. Update withdrawal with viability_score and ai_summary
   e. Log AiInteraction for withdrawal analysis

4. User views withdrawal → /withdrawals/{id} (WithdrawalController::show)
   → Displays AI analysis, viability score, summary

5. User processes decision → POST /withdrawals/{id}/process
   a. Validate: decision (approved/rejected), optional user_notes
   b. WithdrawalService::processDecision() called:
      - Update decision status
      - Calculate decision_quality (safe/bad) based on AI score
      - If approved: deduct amount from goal.current_amount
      - Log AiInteraction for decision
   c. Redirect with success message
```

### 9. Profile & Account Management

```
1. User visits /profile (ProfileController::edit)
   → Shows profile edit form (name, email)

2. User updates profile → PATCH /profile (ProfileController::update)
   a. Validate: name, unique email (excluding current user)
   b. Update user record
   c. If email changed: reset email_verified_at
   d. Redirect with "profile-updated" status

3. User updates password → PUT /password (PasswordController::update)
   a. Validate: current_password, password, password_confirmation
   b. Update password
   c. Invalidate all sessions (force logout everywhere)
   d. Redirect with "password-updated" status

4. User deletes account → DELETE /profile (ProfileController::destroy)
   a. Validate: password (current password required)
   b. Logout user
   c. Delete user record (cascades to all user data)
   d. Invalidate session
   e. Redirect to homepage
```

### 10. Password Reset Flow

```
1. User visits /forgot-password
   → Enter email address

2. System sends password reset link (email logged via log driver in dev)
3. User clicks link → /reset-password/{token}
4. User enters new password + confirmation
5. System validates token (60-minute expiry)
6. Password updated, user redirected to login
```

### 11. Email Verification Flow

```
1. User registers → Register event fires
2. Laravel sends verification email
3. User visits any protected route without verified email
   → Redirected to /verify-email
4. User clicks verification link → /verify-email/{id}/{hash}
   a. Middleware: signed, throttle:6,1
   b. Email marked as verified
   c. Redirect to dashboard
5. User can request new verification email (throttled:6,1)
```

---

## AI Interaction Tracking

The system logs all AI-related interactions in the `ai_interactions` table:

| Type | Trigger | Description |
|------|---------|-------------|
| `dashboard_arrangement` | `?view=progress/savings/deadlines` on dashboard | AI arranges dashboard layout based on view mode |
| `withdrawal_analysis` | Withdrawal creation (AIAnalysisJob) | AI analyzes withdrawal viability and generates summary |
| `withdrawal_decision` | Withdrawal processing (processDecision) | AI quality assessment of user's decision |
| `deposit_processed` | Recurring deposit processing (ProcessDepositJob) | Logs deposit processing |

### AI Viability Score Algorithm

The `AiService::calculateViabilityScore()` computes a score from 1-10 based on:

| Factor | Weight |
|--------|--------|
| Amount > 50% of current savings | -2 points |
| Amount > total wants + needs cost | -2 points |
| Progress < 30% | -1 point |
| Progress >= 80% | +2 points |
| Base score | 5 points |

Decision quality is assessed as:
- `approved` + score >= 7 = "safe"
- `rejected` + score < 4 = "safe"
- All other combinations = "bad"

---

## Queue Architecture

All AI analysis and deposit processing run via Redis queues:

| Job | Trigger | Handler |
|-----|---------|---------|
| `AIAnalysisJob` | Withdrawal creation | `AiService::analyzeWithdrawal()` |
| `ProcessDepositJob` | Recurring deposit creation | Goal amount increment + interaction logging |

Queue connection: Redis (Upstash)
Queue driver: `redis`

---

## Route Summary

### Public Routes
| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/` | Closure | Welcome page |
| GET | `/login` | AuthenticatedSessionController | Login form |
| POST | `/login` | AuthenticatedSessionController | Login attempt |
| GET | `/register` | RegisteredUserController | Registration form |
| POST | `/register` | RegisteredUserController | Register user |
| POST | `/forgot-password` | PasswordResetLinkController | Send reset link |
| GET | `/reset-password/{token}` | NewPasswordController | Reset form |
| POST | `/reset-password` | NewPasswordController | Update password |

### Authenticated Routes (middleware: `auth`,`verified`)
| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/dashboard` | GoalController | User dashboard |
| GET | `/goals` | GoalController | List goals |
| GET | `/goals/create` | GoalController | Create form |
| POST | `/goals` | GoalController | Store goal |
| GET | `/goals/{id}` | GoalController | Show goal |
| GET | `/goals/{id}/edit` | GoalController | Edit form |
| PUT/PATCH | `/goals/{id}` | GoalController | Update goal |
| DELETE | `/goals/{id}` | GoalController | Delete goal |
| GET | `/wants` | WantController | List wants |
| GET | `/wants/create` | WantController | Create form |
| POST | `/wants` | WantController | Store want |
| GET | `/wants/{id}/edit` | WantController | Edit form |
| PUT/PATCH | `/wants/{id}` | WantController | Update want |
| DELETE | `/wants/{id}` | WantController | Delete want |
| GET | `/needs` | NeedController | List needs |
| GET | `/needs/create` | NeedController | Create form |
| POST | `/needs` | NeedController | Store need |
| GET | `/needs/{id}/edit` | NeedController | Edit form |
| PUT/PATCH | `/needs/{id}` | NeedController | Update need |
| DELETE | `/needs/{id}` | NeedController | Delete need |
| GET | `/deposits` | DepositController | List deposits |
| GET | `/deposits/create` | DepositController | Create form |
| POST | `/deposits` | DepositController | Store deposit |
| GET | `/withdrawals` | WithdrawalController | List withdrawals |
| GET | `/withdrawals/create` | WithdrawalController | Create form |
| POST | `/withdrawals` | WithdrawalController | Store withdrawal |
| GET | `/withdrawals/{id}` | WithdrawalController | Show withdrawal |
| POST | `/withdrawals/{id}/process` | WithdrawalController | Process decision |
| GET | `/profile` | ProfileController | Edit profile |
| PATCH | `/profile` | ProfileController | Update profile |
| DELETE | `/profile` | ProfileController | Delete account |
| PUT | `/password` | PasswordController | Update password |
| POST | `/logout` | AuthenticatedSessionController | Logout |
| GET | `/verify-email` | EmailVerificationPromptController | Verification notice |
| GET | `/verify-email/{id}/{hash}` | VerifyEmailController | Verify email |
| POST | `/email/verification-notification` | EmailVerificationNotificationController | Resend verification |
| GET | `/confirm-password` | ConfirmablePasswordController | Confirm password |
| POST | `/confirm-password` | ConfirmablePasswordController | Store password confirmation |

---

## Model Relationships

```
User (1) → (N) Goals
User (1) → (N) Wants
User (1) → (N) Needs
User (1) → (N) Deposits
User (1) → (N) Withdrawals
User (1) → (N) AiInteractions

Goal (1) → (N) Wants
Goal (1) → (N) Needs
Goal (1) → (N) Deposits
Goal (1) → (N) Withdrawals

Withdrawal (N) → (1) Goal
Deposit (N) → (1) Goal
Want (N) → (1) Goal
Need (N) → (1) Goal

AiInteraction ← [morphTo] → Goal/Withdrawal/Deposit
```

---

## Deployment Architecture

### Infrastructure
- **Platform**: Railway (AMS region - Amsterdam)
- **Web Server**: PHP built-in server (`php artisan serve`) on port 8000
- **Database**: Supabase PostgreSQL via Supavisor connection pooler
- **Cache/Sessions**: Upstash Redis (serverless, TLS)
- **CDN**: Tailwind CSS via `cdn.tailwindcss.com`
- **Assets**: All CSS/JS served from CDN (no local Vite build at runtime)

### Environment Variables (Production)
```
APP_NAME=savioTrack
APP_ENV=production
APP_DEBUG=false
APP_URL=https://saviotrack-production-51e0.up.railway.app
APP_KEY=base64:[ENCRYPTED]

DB_CONNECTION=pgsql
DB_HOST=aws-1-eu-west-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.[PROJECT_REF]
DB_PASSWORD=[REDACTED]

SESSION_DRIVER=redis
SESSION_ENCRYPT=true
SESSION_LIFETIME=120

CACHE_STORE=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=predis
REDIS_HOST=[upstash-endpoint].upstash.io
REDIS_PORT=6379
REDIS_USERNAME=default
REDIS_PASSWORD=[REDACTED]
REDIS_SCHEME=tls
```

### Docker Configuration
- Base image: `php:8.3-fpm`
- Extensions: `pdo_pgsql`, `pgsql`, `zip`
- Multi-stage build: Node 20 image builds Vite assets, copied to PHP image
- Port: 8000
- Composer: Installed with `--no-dev` in production
