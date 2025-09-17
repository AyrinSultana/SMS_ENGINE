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
<img width="1429" height="769" alt="CreateSMSTemplate" src="https://github.com/user-attachments/assets/6c3e5a22-0072-49eb-94db-80811a9021f9" />
<img width="1475" height="762" alt="TemplateManagement" src="https://github.com/user-attachments/assets/d04b527d-3254-4174-9018-1e78aae91a7b" />
<img width="1482" height="763" alt="TemplateApprovalQueue" src="https://github.com/user-attachments/assets/1693f104-3780-46b7-b2bf-6cf75934427b" />
<img width="1492" height="740" alt="SelectSMSSendingMethod" src="https://github.com/user-attachments/assets/bacab321-8775-49e8-998e-9989087c40f6" />
<img width="1434" height="769" alt="SMSQueueStatus" src="https://github.com/user-attachments/assets/870bbaff-fdce-4827-a36e-610e73db1f31" />
<img width="1438" height="742" alt="SMSApprovalQueue" src="https://github.com/user-attachments/assets/f4ec6c7d-4b4c-4209-b9c0-a09d1da5f21b" />
<img width="1434" height="765" alt="SMSHistory" src="https://github.com/user-attachments/assets/d9d54df5-2d97-4df6-a292-a774026d927f" />


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



