# AGENTS.md - Bryanz Logistics System

This file provides guidance to AI agents working on the Bryanz Logistics Laravel 12 application.

## Project Overview

Bryanz Logistics is a logistics management system for tracking shipments, managing clients, handling invoices/payments, and communicating via WhatsApp. The application uses AdminLTE for the admin panel and supports both Meta WhatsApp Cloud API and local WhatsApp providers.

## Development Commands

### Full Stack Development
```bash
composer dev        # Start Laravel server, queue worker, logs, and Vite in parallel
php artisan serve   # Start Laravel server only
npm run dev         # Start Vite dev server
npm run build       # Build assets for production
```

### Testing
```bash
composer test       # Run tests (clears config cache first)
php artisan test    # Run tests directly
php artisan test --filter TestClassName  # Run specific test class
php artisan test --filter test_method_name  # Run specific test method
```

### Database
```bash
php artisan migrate:fresh --seed  # Fresh migration with seeders
php artisan migrate               # Run pending migrations
php artisan db:seed               # Run seeders
```

### Queue & Jobs
```bash
php artisan queue:listen --tries=1  # Process queue jobs (used by composer dev)
php artisan queue:work              # Alternative queue worker
php artisan queue:flush             # Clear failed jobs
```

### Cache & Config
```bash
php artisan config:clear    # Clear config cache (required before running tests)
php artisan cache:clear     # Clear application cache
php artisan route:clear     # Clear route cache
php artisan view:clear      # Clear compiled views
```

### Code Quality
```bash
php artisan pint            # Laravel Pint code formatter
```

## Code Style Guidelines

### PHP/Laravel Conventions

**Imports & Namespaces:**
- Use fully qualified class names in imports
- Group imports: Laravel, Packages, Application
- Example:
```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Client;
use App\Models\Invoice;
```

**Model Conventions:**
- Use `HasFactory` trait in models
- Define `$fillable` array for mass assignment
- Use relationship methods with proper return types
- Example:
```php
class Shipment extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'tracking_number', 'origin', 'destination'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
```

**Controller Conventions:**
- Use resourceful controller methods (index, create, store, show, edit, update, destroy)
- Validate requests using form request classes or `$request->validate()`
- Return appropriate responses (view, redirect, JSON)
- Example:
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:clients',
    ]);

    $client = Client::create($validated);

    return redirect()->route('admin.clients.show', $client)
        ->with('success', 'Client created successfully.');
}
```

**Naming Conventions:**
- Models: Singular PascalCase (Shipment, Client, Invoice)
- Controllers: Plural PascalCase with Controller suffix (ShipmentsController)
- Database tables: snake_case plural (shipments, clients)
- Columns: snake_case (tracking_number, current_status)
- Relationships: camelCase (client(), invoices())

**Error Handling:**
- Use try-catch blocks for database operations
- Return appropriate error responses
- Use Laravel's validation system for input validation
- Example:
```php
try {
    $shipment = Shipment::create($validated);
} catch (\Exception $e) {
    return back()->withInput()->with('error', 'Failed to create shipment: ' . $e->getMessage());
}
```

### Blade Templates

**File Structure:**
- Views in `resources/views/` organized by feature
- Use `@extends('adminlte::page')` for admin pages
- Section structure: `@section('content')`, `@section('js')`, `@section('css')`

**Currency Display:**
- Always use `\App\Models\Setting::getCurrencySymbol()` for currency symbols
- For shipment-specific currency: `\App\Models\Setting::getCurrencySymbol($shipment->currency ?? null)`
- Format numbers with `number_format($amount, 0)` for whole numbers
- Example:
```blade
<td>{{ \App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null) }} {{ number_format($invoice->total, 0) }}</td>
```

**Blade Directives:**
- Use `@forelse` instead of `@foreach` with empty check
- Use `@can` for permission checks
- Use `@include` for reusable components
- Example:
```blade
@forelse($shipments as $shipment)
    <tr>
        <td>{{ $shipment->tracking_number }}</td>
    </tr>
@empty
    <tr><td colspan="5">No shipments found</td></tr>
@endforelse
```

### JavaScript/TypeScript

**Frontend Structure:**
- Use Alpine.js for interactive components
- Include scripts in `@section('js')` section
- Use Chart.js for data visualization in reports

**Build System:**
- Vite with Laravel Vite plugin
- Tailwind CSS for styling
- Bootstrap (via AdminLTE) for base components

### Testing Guidelines

**Test Structure:**
- Feature tests in `tests/Feature/`
- Unit tests in `tests/Unit/`
- Use `RefreshDatabase` trait for database tests
- Set up permissions in `setUp()` method

**Test Examples:**
```php
public function test_can_create_shipment()
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)
        ->post(route('admin.shipments.store'), [
            'client_id' => 1,
            'origin' => 'New York',
            'destination' => 'London',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('shipments', ['origin' => 'New York']);
}
```

**Test Database:**
- Uses SQLite in-memory database (`:memory:`)
- Clear config cache before running tests (`php artisan config:clear`)
- Use factories for test data generation

## Architecture Patterns

### WhatsApp Notification System
- Provider-based architecture with `WhatsAppProviderInterface`
- Two providers: `MetaWhatsAppProvider` and `LocalWhatsAppProvider`
- Configure provider in `.env`: `WHATSAPP_PROVIDER=meta` or `WHATSAPP_PROVIDER=local`

### Notification System
- Laravel notifications with custom `WhatsAppChannel`
- Rate-limited jobs to prevent WhatsApp bans
- Jobs: `SendBatchShipmentNotificationJob`, `SendBulkNotificationsJob`, `SendDelayedWhatsAppJob`

### Settings System
- Use `Setting::get('key', default)` for runtime configuration
- Currency settings via `system_currency` key (default: UGX)
- Currency symbols via `Setting::getCurrencySymbol()`

### Role-Based Access Control
- Uses `spatie/laravel-permission` package
- Default roles: admin, staff
- Middleware: `CheckRole` for route protection

## Important Notes

1. **Tracking Numbers**: Auto-generated on Shipment creation (format: `BRY-YYYYMMDD-NNNNNN`)
2. **Currency**: Default is UGX but configurable via admin settings
3. **Queue Jobs**: All implement `ShouldQueue` with retry logic (`$tries = 3`, `$backoff = 10`)
4. **Rate Limiting**: WhatsApp notifications use staggered delays (3-5 seconds)
5. **Expenses**: Always displayed in UGX (not configurable)
6. **Reports**: Use settings currency except expenses report

## Default Credentials
After seeding: `admin@admin.com` / `password`

## File Structure Reference
```
app/
├── Models/           # Eloquent models
├── Http/
│   ├── Controllers/ # Application controllers
│   └── Middleware/  # Custom middleware
├── Services/        # Business logic services
├── Notifications/   # Laravel notifications
└── Jobs/           # Queue jobs

resources/views/
├── adminlte/       # AdminLTE layout
├── shipments/      # Shipment views
├── clients/        # Client views
├── invoices/       # Invoice views
├── payments/       # Payment views
└── reports/        # Report views

config/
├── adminlte.php    # AdminLTE configuration
├── permission.php  # Spatie permissions
└── services.php    # Third-party services
```

## graphify

This project has a graphify knowledge graph at graphify-out/.

Rules:
- Before answering architecture or codebase questions, read graphify-out/GRAPH_REPORT.md for god nodes and community structure
- If graphify-out/wiki/index.md exists, navigate it instead of reading raw files
- For cross-module "how does X relate to Y" questions, prefer `graphify query "<question>"`, `graphify path "<A>" "<B>"`, or `graphify explain "<concept>"` over grep — these traverse the graph's EXTRACTED + INFERRED edges instead of scanning files
- After modifying code files in this session, run `graphify update .` to keep the graph current (AST-only, no API cost)
