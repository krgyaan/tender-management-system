## Tendeer Management System

Tender Management System is a web application for tender management. It is a web application for tender management. In Which one can manage their tender from creation to bidding to contract.

### Features

| Feature | Feature |
| ------- | ------- |
| Tender Creation | Tender Info |
| Tender Approval | RFQ+Response |
| EMD | Tender Fees |
| Pay on Portal, Bank Transfer, Cheque, DD, FDR, BG | Physical Docs |
| Pricing Sheets | Pricing Approval |
| Bid Submission | TQ Mgmt |
| RA Mgmt | Result |
| Courier Dashboard | Transport Dashboard |
| Employee Imprest | Followup Dashboard |
| Individual Calenders | Individual Performance |
| PQ Dashboard | Finance Docs Dashboard |
| Rent Agreement | PO/LOI Receipt |
| PO/LOI Amendment | PO/LOI Acceptance |
| Kickoff Meeting | PBG/SD request |
| Contract Agreement Request | Document Approval |
| Purchase Order | Payment Request |
| Bill Submission | Payment Receipt |
| Completion Certificate | Performance Certificate |
| Ongoing Project Dashboard | Loan & Advances |

### Installation

```bash
    git clone https://github.com/krgyaan/tender-management-system.git
    cd tender-management-system
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    php artisan db:seed
    php artisan storage:link
    php artisan serve
```

### Database

```bash
    mysql -u root -p
    create database tender_ms;
    use tender_ms;
    source database/tender_ms.sql;
```

### License

The Laravel framework is open-source software released under the [MIT license](https://opensource.org/licenses/MIT).

Copyright (c) Tendeer Management System
