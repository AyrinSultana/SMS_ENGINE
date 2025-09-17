# SMS Engine - Laravel MVC Architecture

This project has been refactored to follow Laravel's MVC pattern for better maintainability and extensibility.

## Architecture Overview

### Models
- `Template`: Manages SMS templates with approval workflow
- `PendingList`: Manages pending SMS messages waiting for approval
- `SmsQueue`: Manages the queue of SMS messages to be sent
- `SmsHistory`: Stores history of all SMS activities

### Controllers
- `SmsController`: Handles SMS sending operations
- `TemplateController`: Manages template CRUD operations
- `SmsHistoryController`: Manages SMS history display

### Services
- `SmsService`: Business logic for SMS operations
- `TemplateService`: Business logic for template operations
- `HistoryService`: Business logic for history operations

### Repositories
- `TemplateRepository`: Data access for templates
- `PendingListRepository`: Data access for pending SMS
- `SmsQueueRepository`: Data access for SMS queue
- `SmsHistoryRepository`: Data access for SMS history

### Resources
- `TemplateResource`: Presentation layer for templates
- `PendingListResource`: Presentation layer for pending SMS
- `SmsHistoryResource`: Presentation layer for SMS history

### Form Requests
- `SendSmsRequest`: Validation rules for sending SMS
- `TemplateRequest`: Validation rules for template operations
- `UpdateSmsStatusRequest`: Validation rules for updating SMS status

## Features

- **Template Management**: Create, edit, and delete SMS templates
- **Template Approval Workflow**: Templates require approval before use
- **Multiple SMS Sending Methods**:
  - Send to all users
  - Send to comma-separated numbers
  - Send from an Excel/CSV file
- **Personalization**: Support for variable substitution in templates
- **Queuing**: SMS are queued for sending to avoid performance issues
- **History Tracking**: Complete history of all SMS activities
- **Status Management**: Track status of SMS (pending, sent, failed)

## Installation and Setup

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure database settings
4. Run `php artisan migrate`
5. Run `php artisan key:generate`
6. Run `php artisan serve`

## Usage

### Sending SMS

1. Go to Template > SMS Form
2. Select a template
3. Choose the sending method (all users, comma-separated, Excel)
4. Submit the form

### Managing Templates

1. Go to Template
2. Create, edit, or delete templates
3. Approve or reject templates

### Viewing History

1. Go to SMS History
2. Filter by status, template, etc.
3. View detailed statistics



