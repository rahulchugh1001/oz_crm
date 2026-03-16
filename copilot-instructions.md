# oz_crm Laravel Project Instructions

Project Type:
CRM system built with Laravel.

Architecture:
Follow Laravel MVC architecture.

Models:
Location: app/Models

Controllers:
Location: app/Http/Controllers

Views:
Use Blade templates in resources/views

Routes:
Define routes in routes/web.php using Route::resource for CRUD modules.

CRUD Pattern:
Every module should include:
- Model
- Migration
- Resource Controller
- Resource Route
- Blade Views (index, create, edit)

Validation:
Use Laravel validation in store and update methods.

Naming Conventions:
Model: Singular (Example: RejectReason)
Controller: RejectReasonController
Table: plural (reject_reasons)

Blade Structure:
resources/views/module_name/
    index.blade.php
    create.blade.php
    edit.blade.php


# Standard Database Fields

Every table in this project must include the following common fields:

status (boolean)
- 1 = Active
- 0 = Inactive

is_deleted (boolean)
- 0 = Not Deleted
- 1 = Deleted (Soft Delete Flag)

created_at
updated_at

Migration Example:

$table->boolean('status')->default(1)->comment('1=Active, 0=Inactive');
$table->boolean('is_deleted')->default(0)->comment('0=Not Deleted, 1=Deleted');
$table->timestamps();
