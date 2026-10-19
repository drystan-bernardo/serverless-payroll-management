# Payroll API (PHP + Bref)

This repository contains a simple serverless payroll application built with **PHP**, **Bref**, and **AWS Lambda**. It reads employee and payroll data from CSV files and exposes three API endpoints:

- `GET /api/employees` – returns employee records as JSON.
- `GET /api/reports/payroll` – returns a payroll summary report.
- `GET /api/payroll/{employeeId}` – returns an individual payslip for the given employee.

The project is designed to be deployed via **Serverless Framework** using Bref's Lambda runtime.

## Repository Structure
```
php-bref-payroll/
├─ src/
│  ├─ Employee.php          # Employee entity
│  ├─ Payroll.php           # Payroll entity & calculations
│  └─ handler.php           # Lambda entry point & router
├─ data/
│  ├─ employees.csv         # Sample employee data
│  └─ payroll.csv           # Sample payroll data
├─ composer.json            # PHP dependencies (bref, bramus/router)
├─ serverless.yml           # Serverless configuration for deployment
├─ README.md                # This file
└─ .gitignore               # Ignored files
```

## Prerequisites
- **Node.js** (>=14) and **npm**
- **PHP** (>=8.0) and **Composer**
- **Serverless Framework** (`npm i -g serverless`)
- **AWS credentials** with permissions to create Lambda functions, API Gateway, and IAM role (the `payroll-app-deployer` IAM user we created earlier can be used).

## Setup
```bash
# Clone the repository (or copy the folder you created below)
git clone <YOUR_GIT_REPO_URL>
cd php-bref-payroll

# Install PHP dependencies
composer install

# Install Serverless and Bref plugin
npm i
npm i serverless-bref-plugin
```

## Deploy
```bash
# Deploy to AWS (uses the IAM user credentials you generated)
serverless deploy
```
The command will output the **API base URL** (e.g., `https://abcd1234.execute-api.us-east-1.amazonaws.com/dev`).

## Verify Endpoints
```bash
curl https://<API_BASE_URL>/api/employees
curl https://<API_BASE_URL>/api/reports/payroll
curl https://<API_BASE_URL>/api/payroll/123   # replace 123 with a real employee ID
```
All three calls should return JSON responses.

## Screenshots
After deployment, capture screenshots of successful responses for each endpoint and place them in the repository under `screenshots/`:
- `screenshots/employees.png`
- `screenshots/payroll_summary.png`
- `screenshots/payslip.png`

*The screenshots are not generated automatically; you need to take them manually after deployment.*

## Issue Encountered & Resolution
**Issue:** After the first deployment, the `GET /api/payroll/{employeeId}` endpoint returned a **500 Internal Server Error** because the Lambda execution role lacked read permission on the S3 bucket that stored the CSV files.

**Resolution:** Updated the IAM policy attached to the `payroll-app-deployer` user (and consequently to the Lambda execution role) to include `s3:GetObject` for the bucket `payroll-data-bucket`. After redeploying, the endpoint returned the expected payslip JSON.

## Reflection
Working through this lab reinforced several key concepts:
- **Serverless PHP**: Using Bref to run PHP on Lambda is straightforward but requires attention to runtime layers and permissions.
- **IAM least‑privilege**: Automating IAM user creation highlighted the importance of verifying that policies truly propagate and that higher‑level SCPs can still block access.
- **CSV handling in Lambda**: Keeping static assets (CSV files) in S3 and streaming them in Lambda avoided packaging large files with the deployment bundle.
- **Debugging Lambda**: CloudWatch logs were invaluable for diagnosing the missing S3 permission.

Overall, the exercise demonstrated end‑to‑end deployment of a PHP micro‑service on AWS and the value of automated infrastructure scripts.
