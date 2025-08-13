<?php
/**
 * Add enhanced columns to existing tables
 */

require_once 'config/database.php';

echo "Adding Enhanced Columns to Existing Tables\n";
echo "==========================================\n\n";

try {
    $pdo = getDatabaseConnection();
    echo "✅ Database connection successful\n";
    
    // Add columns to volunteer_registrations if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'volunteer_registrations'");
    if ($stmt->fetch()) {
        echo "📋 Found volunteer_registrations table\n";
        
        $alterations = [
            "ADD COLUMN status ENUM('pending', 'approved', 'rejected', 'on_hold', 'deleted') DEFAULT 'pending'",
            "ADD COLUMN approval_date TIMESTAMP NULL",
            "ADD COLUMN approved_by INT NULL",
            "ADD COLUMN rejection_reason TEXT NULL",
            "ADD COLUMN notes TEXT NULL",
            "ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ];
        
        foreach ($alterations as $alteration) {
            try {
                $pdo->exec("ALTER TABLE volunteer_registrations $alteration");
                echo "  ✅ Added: $alteration\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo "  ℹ️  Column already exists: $alteration\n";
                } else {
                    echo "  ⚠️  Error: " . $e->getMessage() . "\n";
                }
            }
        }
    } else {
        echo "ℹ️  volunteer_registrations table does not exist\n";
    }
    
    // Add columns to prayer_signups if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'prayer_signups'");
    if ($stmt->fetch()) {
        echo "\n🙏 Found prayer_signups table\n";
        
        $alterations = [
            "ADD COLUMN status ENUM('pending', 'active', 'prayed', 'completed', 'deleted') DEFAULT 'pending'",
            "ADD COLUMN preferred_language ENUM('english', 'hausa', 'yoruba', 'igbo', 'pidgin') DEFAULT 'english'",
            "ADD COLUMN prayer_location VARCHAR(255) NULL",
            "ADD COLUMN admin_notes TEXT NULL",
            "ADD COLUMN prayer_response TEXT NULL",
            "ADD COLUMN prayed_at TIMESTAMP NULL",
            "ADD COLUMN prayed_by INT NULL",
            "ADD COLUMN assigned_time_slot INT NULL",
            "ADD COLUMN assigned_by INT NULL",
            "ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ];
        
        foreach ($alterations as $alteration) {
            try {
                $pdo->exec("ALTER TABLE prayer_signups $alteration");
                echo "  ✅ Added: $alteration\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo "  ℹ️  Column already exists: $alteration\n";
                } else {
                    echo "  ⚠️  Error: " . $e->getMessage() . "\n";
                }
            }
        }
    } else {
        echo "\nℹ️  prayer_signups table does not exist\n";
    }
    
    // Create indexes
    echo "\n📊 Creating indexes...\n";
    
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_volunteer_status ON volunteer_registrations(status)",
        "CREATE INDEX IF NOT EXISTS idx_volunteer_service ON volunteer_registrations(service_type)",
        "CREATE INDEX IF NOT EXISTS idx_volunteer_state ON volunteer_registrations(state)",
        "CREATE INDEX IF NOT EXISTS idx_volunteer_created ON volunteer_registrations(created_at)",
        "CREATE INDEX IF NOT EXISTS idx_volunteer_email ON volunteer_registrations(email)",
        "CREATE INDEX IF NOT EXISTS idx_prayer_status ON prayer_signups(status)",
        "CREATE INDEX IF NOT EXISTS idx_prayer_state ON prayer_signups(state)",
        "CREATE INDEX IF NOT EXISTS idx_prayer_created ON prayer_signups(created_at)",
        "CREATE INDEX IF NOT EXISTS idx_prayer_email ON prayer_signups(email)",
        "CREATE INDEX IF NOT EXISTS idx_admin_activity ON admin_activity_log(admin_id, created_at)",
        "CREATE INDEX IF NOT EXISTS idx_email_status ON email_queue(status)",
        "CREATE INDEX IF NOT EXISTS idx_email_scheduled ON email_queue(scheduled_at)"
    ];
    
    foreach ($indexes as $index) {
        try {
            $pdo->exec($index);
            echo "  ✅ Created index\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "Duplicate key name") !== false || 
                strpos($e->getMessage(), "doesn't exist") !== false) {
                echo "  ℹ️  Index already exists or table missing\n";
            } else {
                echo "  ⚠️  Error creating index: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n🎉 Column enhancement complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
