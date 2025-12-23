#!/bin/bash

# --- Philips TV Restore Script (Ubuntu) ---
# Script untuk mengaktifkan kembali layanan standar dan menonaktifkan aksesibilitas kustom.

echo "Memulai proses pemulihan sistem..."

# 1. Enable Google TV Launcher
echo "Enabling Google TV Launcher..."
adb shell pm enable com.google.android.tvlauncher

# 2. Enable Philips Welcome Screen
echo "Enabling Philips Welcome Screen..."
adb shell pm enable org.droidtv.welcome

# 3. Enable Google Setup Wizard
echo "Enabling Google Setup Wizard..."
adb shell pm enable com.google.android.tungsten.setupwraith

# 4. Disable Accessibility Services
echo "Disabling Accessibility Services..."
adb shell settings put secure enabled_accessibility_services '""'

# 5. Clear Home Activity
echo "Clearing custom Home activity..."
adb shell cmd package clear-home-activity

# 6. Uninstall app
echo "Uninstalling app..."
adb shell pm uninstall com.madeiraresearch.hoteliptv3

echo "------------------------------------------------"
echo "Recovery Complete / Pemulihan Selesai."
echo "Silakan tekan tombol Home pada remote."
