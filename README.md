# 📡 ZKTeco School Management System Integration

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![ZKTeco](https://img.shields.io/badge/Biometrics-ZKTeco-green?style=for-the-badge)
![Status](https://img.shields.io/badge/Auto--Sync-Active-blue?style=for-the-badge)

A robust integration layer for syncing biometric attendance data from ZKTeco devices to a School Management System database in real-time.

---

## ✨ Features

- **🔄 Auto-Sync**: Automatically fetches user and attendance data every 5 seconds.
- **⚡ Real-time Updates**: Live dashboard updates without page reloads.
- **🎣 Webhooks**: Instant notifications to external systems upon new attendance scans.
- **📅 Data Retention**: Stores raw logs and user metadata (Card Number, UID, Role).
- **🛡️ Resilience**: Automatic connection recovery and duplicate checks.

---

## 🛠️ Installation & Setup

### 1. Requirements

- Windows OS (for the ZKTeco SDK DLLs)
- PHP 8.1+
- Composer
- ZKTeco Network Device (e.g., K40, F18, UA-400)

### 2. Configuration

Copy `.env.example` to `.env` and configure your device settings:

```dotenv
ZK_DEVICE_IP=192.168.0.201
ZK_DEVICE_PORT=4370
ZK_WEBHOOK_URL=http://your-main-app.com/api/attendance/webhook
```

### 3. Dependencies

```bash
composer install
npm install && npm run build
```

---

## 🚀 Usage

### Manual Sync
To test the connection and fetch logs one-time:

```bash
php artisan zk:sync
```

### 🖥️ Dashboard
Start the development server:

```bash
php artisan serve --host=0.0.0.0
```

Access the dashboard at `http://localhost:8000` to view live logs and device status.

---

## 🤖 Auto-Start Guide (Windows)

To ensure the system runs automatically when your computer turns on, we have included a startup script.

### Step 1: Locate the Script
Find the file `start-scheduler.bat` in the project root:
`d:\Softweb system solution\School Management\zkteco\start-scheduler.bat`

### Step 2: Add to Startup Folder
1. Press `Win + R` and type `shell:startup`.
2. Right-click inside the folder > **New > Shortcut**.
3. Browse and select `start-scheduler.bat`.
4. Click **Finish**.

### Step 3: Verification
Restart your PC. A window will open automatically:
- **Project Server**: Starts on Port 8000.
- **Scheduler**: Runs in the background (showing "Running [zk:sync]" every 5s).

---

## 🔍 Troubleshooting

| Issue | Solution |
| :--- | :--- |
| **Connection Failed** | Check if the device IP is reachable (`ping 192.168.0.201`). Ensure Port 4370 is open. |
| **Scheduler Not Running** | Run `php artisan schedule:work` manually to see errors. |
| **Duplicate Logs** | The system automatically filters duplicates based on the timestamp and user ID. |

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
