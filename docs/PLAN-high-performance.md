# Plan: High Performance & Security Architecture (Pilar B + A)

## 1. Goal
Prepare the specific application for high demand (1000+ concurrent users) and robust security.
Focus: **Performance (Redis/Queues)** as Priority 1, followed by **Security (Rate Limiting)** as Priority 2.

## 2. Phase 1: High Performance Infrastructure (The Engine)
*Objective: Remove blocking operations and speed up data retrieval.*

### 2.1 Redis Integration
- [ ] **Install Predis**: `composer require predis/predis`
- [ ] **Configure Laravel**: Update `.env` to use `redis` for `CACHE_DRIVER`, `SESSION_DRIVER`, and `QUEUE_CONNECTION`.
- [ ] **Verify Connection**: Ensure Laravel can talk to the local Redis instance.

### 2.2 Asynchronous Processing (Queues)
- [ ] **Configure Queues**: Set up `config/queue.php` optimized for Redis.
- [ ] **Refactor PDF Processing**:
    - Create `ProcessPdfUpload` Job.
    - Move `PdfParser` logic from Controller to Job.
    - Implement "Optimistic UI" (Show "Processing..." status to user).
- [ ] **Horizon (Optional but Recommended)**: Install Laravel Horizon for monitoring queues.

## 3. Phase 2: Security Fortress (The Shield)
*Objective: Prevent abuse and ensure system stability.*

### 3.1 Rate Limiting (Throttling)
- [ ] **Global Rate Limit**: Limit API requests to 60/min per user.
- [ ] **Upload Rate Limit**: Strict limit on `/books` POST (e.g., 5/min) to prevent storage flooding.
- [ ] **Login Protection**: Ensure standard login throttling is active.

### 3.2 Enhanced Validation
- [ ] **Mimetype & Magic Bytes**: Ensure files are *truly* PDFs.
- [ ] **Sanitize Filenames**: Force random hashes for storage filenames (already standard in Laravel, verifying).

## 4. Phase 3: Optimizations (The Polish)
- [ ] **Cache Hot Data**: Cache the "Sidebar Counts" and "Main Categories" for 60 minutes.
- [ ] **Image Optimization**: Auto-convert covers to WebP on upload.

## 5. Verification Plan
- [ ] **Load Test**: Simulate multiple PDF uploads.
- [ ] **Security Test**: Try to spam the API and verify 429 Too Many Requests.
- [ ] **UI Test**: Verify "Processing" state handling in frontend.
