# Farmer Payment

Laravel application for farmer lot intake, FRN capture, quality validation, pricing approval, and payment ledger posting.

## Setup

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Default seeded user:

```text
Email: test@example.com
Password: password
```

## Main Flow

1. Create organizer and farmer.
2. Create agreement with numeric rate, optional bonus, loss rules, and quality parameters.
3. Receive a lot against an active agreement and matching farmer-organizer mapping.
4. Capture FRN for accepted lots.
5. Submit quality validation.
6. Calculate pricing, approve pricing, then process payment.

## Security

Business routes are protected by session authentication. Guests are redirected to `/login`.

## Verification

```bash
php artisan migrate --force
php artisan test
```
