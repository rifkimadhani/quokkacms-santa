#!/bin/bash

# Output Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PACKAGE="com.madeiraresearch.hoteliptv3"
# Relative path inside the app's data folder (run-as starts at /data/data/pkg/)
TARGET_RELATIVE_PATH="files/mr"

echo -e "${YELLOW}=== AUTO INSTALL & CONFIG PHILIPS TV (NO-ROOT) ===${NC}"

# 1. Input Variables
read -p "Enter TV IP Address: " IP_TV
read -p "Enter APK file path: " APK_FILE
read -p "Enter config.json file path: " CONFIG_FILE

# File Validation
if [ ! -f "$APK_FILE" ]; then 
    echo -e "${RED}[ERROR] APK file not found at: $APK_FILE${NC}"
    exit 1
fi

if [ ! -f "$CONFIG_FILE" ]; then 
    echo -e "${RED}[ERROR] Config file not found at: $CONFIG_FILE${NC}"
    exit 1
fi

# 2. Connection
echo -e "\n${YELLOW}[1/6] Connecting to TV ($IP_TV)...${NC}"
adb disconnect "$IP_TV" > /dev/null 2>&1
adb connect "$IP_TV"
sleep 2

# Check connection status
if ! adb -s "$IP_TV:5555" get-state | grep -q "device"; then
    echo -e "${RED}[ERROR] Connection failed. Please check IP address & USB Debugging status.${NC}"
    exit 1
fi

# 3. Install APK
echo -e "\n${YELLOW}[2/6] Installing APK...${NC}"
# -r flag re-installs the app, keeping existing data (if any)
adb -s "$IP_TV:5555" install -r "$APK_FILE"

if [ $? -ne 0 ]; then
    echo -e "${RED}[ERROR] APK Installation failed.${NC}"
    exit 1
fi

# Run app
adb -s "$IP_TV:5555" shell am start -n com.madeiraresearch.hoteliptv3/.MainActivity

# 4. Push Config (The 'run-as' Logic)
echo -e "\n${YELLOW}[3/6] Injecting Config JSON...${NC}"

# Step A: Push to a public temporary folder (sdcard)
adb -s "$IP_TV:5555" push "$CONFIG_FILE" /sdcard/config_temp.json > /dev/null

# Step B: Move file to internal app storage using run-as
# mkdir -p: ensures the directory exists
adb -s "$IP_TV:5555" shell "run-as $PACKAGE mkdir -p $TARGET_RELATIVE_PATH"

# cp: copy file from sdcard to internal app folder
adb -s "$IP_TV:5555" shell "run-as $PACKAGE cp /sdcard/config_temp.json $TARGET_RELATIVE_PATH/config.json"

# Verify file existence
CHECK_SUCCESS=$(adb -s "$IP_TV:5555" shell "run-as $PACKAGE ls $TARGET_RELATIVE_PATH/config.json")

if [[ "$CHECK_SUCCESS" == *"$TARGET_RELATIVE_PATH/config.json"* ]]; then
    echo -e "${GREEN}[OK] Config successfully saved at: /data/data/$PACKAGE/$TARGET_RELATIVE_PATH/config.json${NC}"
    # Cleanup temp file
    adb -s "$IP_TV:5555" shell rm /sdcard/config_temp.json
else
    echo -e "${RED}[ERROR] Failed to inject config.${NC}"
    echo -e "${YELLOW}Possible cause: The APK is a 'Release' build (not debuggable). 'run-as' requires a Debuggable APK.${NC}"
    # Cleanup anyway
    adb -s "$IP_TV:5555" shell rm /sdcard/config_temp.json
    exit 1
fi

# 5. Disable Bloatware
echo -e "\n${YELLOW}[4/6] Disabling Bloatware & Setup Wizards...${NC}"
adb -s "$IP_TV:5555" shell pm disable-user --user 0 com.google.android.tvlauncher > /dev/null 2>&1
adb -s "$IP_TV:5555" shell pm disable-user --user 0 org.droidtv.welcome > /dev/null 2>&1
adb -s "$IP_TV:5555" shell pm disable-user --user 0 com.google.android.tungsten.setupwraith > /dev/null 2>&1

# 6. Set Accessibility & Launcher
echo -e "\n${YELLOW}[5/6] Finalizing System Settings...${NC}"

# Enable Accessibility Service
echo " - Enabling Accessibility Service..."
adb -s "$IP_TV:5555" shell settings put secure enabled_accessibility_services $PACKAGE/com.madeiraresearch.library.cdb.AppWatcherAccessibility
adb -s "$IP_TV:5555" shell settings put secure accessibility_enabled 1

# Set Default Launcher
echo " - Setting Default Home Activity..."
adb -s "$IP_TV:5555" shell cmd package set-home-activity $PACKAGE/.MainActivity

# Completion
# echo -e "\n${GREEN}=== SETUP COMPLETED ===${NC}"
# echo "Rebooting TV now..."
# adb -s "$IP_TV:5555" reboot

# Restart app
echo " - Restarting app..."
adb -s "$IP_TV:5555" shell am force-stop com.madeiraresearch.hoteliptv3
adb -s "$IP_TV:5555" shell am start -n com.madeiraresearch.hoteliptv3/.MainActivity