<?php
// Include config to set timezone
include 'config/config.php';

echo "<h2>Timezone Configuration Test</h2>";
echo "<p><strong>Current Date/Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Current Timezone:</strong> " . date_default_timezone_get() . "</p>";
echo "<p><strong>UTC Time:</strong> " . gmdate('Y-m-d H:i:s') . "</p>";

echo "<h3>Common US Timezones (current time):</h3>";
$timezones = [
    'America/New_York' => 'Eastern Time (EST/EDT)',
    'America/Chicago' => 'Central Time (CST/CDT)', 
    'America/Denver' => 'Mountain Time (MST/MDT)',
    'America/Los_Angeles' => 'Pacific Time (PST/PDT)',
    'America/Phoenix' => 'Arizona Time (MST - no DST)'
];

foreach ($timezones as $tz => $name) {
    $original_tz = date_default_timezone_get();
    date_default_timezone_set($tz);
    echo "<p><strong>$name:</strong> " . date('Y-m-d H:i:s') . " ($tz)</p>";
    date_default_timezone_set($original_tz);
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<p>1. Find the timezone above that matches your current local time</p>";
echo "<p>2. Update config/config.php with the correct timezone</p>";
echo "<p>3. Example: <code>date_default_timezone_set('America/Chicago');</code></p>";
?>