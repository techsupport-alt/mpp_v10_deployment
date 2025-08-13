<?php
/**
 * Direct database initialization script
 * Run this to apply the enhanced schema
 */

require_once 'config/database.php';

echo "MPP Database Initialization\n";
echo "============================\n\n";

try {
    $pdo = getDatabaseConnection();
    echo "✅ Database connection successful\n";
    
    // Read and execute the base schema first
    $schema_file = __DIR__ . '/database/schema.sql';
    
    if (!file_exists($schema_file)) {
        throw new Exception("Schema file not found: $schema_file");
    }
    
    $sql = file_get_contents($schema_file);
    
    // Then append the simple schema
    $simple_schema_file = __DIR__ . '/database/simple_schema.sql';
    
    if (!file_exists($simple_schema_file)) {
        throw new Exception("Schema file not found: $simple_schema_file");
    }
    
    $sql .= "\n" . file_get_contents($simple_schema_file);
    echo "✅ Schema file loaded\n";
    
    // Split SQL statements by semicolon and execute them one by one
    $statements = explode(';', $sql);
    $executed = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        // Skip empty statements and comments
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $executed++;
            echo "✅ Executed statement " . ($executed) . "\n";
        } catch (PDOException $e) {
            $error_msg = "Error in statement " . ($executed + 1) . ": " . $e->getMessage();
            $errors[] = $error_msg;
            echo "⚠️  " . $error_msg . "\n";
            
            // Continue with other statements even if one fails
            continue;
        }
    }
    
    echo "\n=== DATABASE INITIALIZATION COMPLETE ===\n";
    echo "✅ Successfully executed $executed statements\n";
    
    if (!empty($errors)) {
        echo "⚠️  " . count($errors) . " errors encountered:\n";
        foreach ($errors as $error) {
            echo "   - $error\n";
        }
    }
    
    // Test if admin user was created
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    $admin_count = $stmt->fetchColumn();
    echo "👤 Admin users in database: $admin_count\n";
    
    // Test if enhanced columns were added
    $stmt = $pdo->query("SHOW COLUMNS FROM volunteer_registrations LIKE 'status'");
    $status_col = $stmt->fetch();
    echo $status_col ? "✅ volunteer_registrations.status column exists\n" : "❌ volunteer_registrations.status column missing\n";
    
    // Check if prayer_signups table exists and has status column
    $stmt = $pdo->query("SHOW TABLES LIKE 'prayer_signups'");
    $prayer_table_exists = $stmt->fetch();
    
    if ($prayer_table_exists) {
        $stmt = $pdo->query("SHOW COLUMNS FROM prayer_signups LIKE 'status'");
        $prayer_status_col = $stmt->fetch();
        echo $prayer_status_col ? "✅ prayer_signups.status column exists\n" : "❌ prayer_signups.status column missing\n";
    } else {
        echo "ℹ️  prayer_signups table does not exist (will be created when needed)\n";
    }
    
    echo "\n🎉 Database is ready for MPP backend operations!\n";
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
