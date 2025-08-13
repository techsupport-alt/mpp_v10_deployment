<?php
/**
 * Enhanced Email Service for MPP
 * Handles email queuing, templates, and bulk sending
 */

require_once 'database.php';

class EmailService {
    private $pdo;
    private $templates = [];
    
    public function __construct() {
        $this->pdo = getDatabaseConnection();
        $this->loadTemplates();
    }
    
    /**
     * Load email templates
     */
    private function loadTemplates() {
        $this->templates = [
            'volunteer_welcome' => [
                'subject' => 'Welcome to MPP - Your Volunteer Application Received',
                'template' => 'volunteer_welcome.html'
            ],
            'volunteer_approved' => [
                'subject' => 'Your MPP Volunteer Application has been Approved!',
                'template' => 'volunteer_approved.html'
            ],
            'volunteer_reminder' => [
                'subject' => 'MPP Service Reminder - Your Time is Coming!',
                'template' => 'volunteer_reminder.html'
            ],
            'prayer_welcome' => [
                'subject' => 'Welcome to MPP Prayer Movement',
                'template' => 'prayer_welcome.html'
            ],
            'prayer_reminder' => [
                'subject' => 'MPP Prayer Time Reminder',
                'template' => 'prayer_reminder.html'
            ],
            'daily_update' => [
                'subject' => 'MPP Daily Update - Day {day_number}',
                'template' => 'daily_update.html'
            ],
            'weekly_report' => [
                'subject' => 'MPP Weekly Report - Week {week_number}',
                'template' => 'weekly_report.html'
            ]
        ];
    }
    
    /**
     * Queue an email for sending
     */
    public function queueEmail($recipient_email, $recipient_name, $template_type, $variables = [], $priority = 'medium', $scheduled_at = null) {
        try {
            if (!isset($this->templates[$template_type])) {
                throw new Exception("Email template '$template_type' not found");
            }
            
            $template = $this->templates[$template_type];
            $subject = $this->processTemplate($template['subject'], $variables);
            $body = $this->loadAndProcessTemplate($template['template'], $variables);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO email_queue (recipient_email, recipient_name, subject, body, email_type, priority, scheduled_at, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $recipient_email,
                $recipient_name,
                $subject,
                $body,
                $template_type,
                $priority,
                $scheduled_at ?? date('Y-m-d H:i:s'),
                $_SESSION['admin_id'] ?? null
            ]);
            
            return $result ? $this->pdo->lastInsertId() : false;
            
        } catch (Exception $e) {
            error_log("Email queue error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send queued emails
     */
    public function processEmailQueue($limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM email_queue 
                WHERE status = 'pending' AND scheduled_at <= NOW() 
                ORDER BY priority DESC, created_at ASC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $emails = $stmt->fetchAll();
            
            $sent_count = 0;
            $failed_count = 0;
            
            foreach ($emails as $email) {
                if ($this->sendQueuedEmail($email)) {
                    $sent_count++;
                } else {
                    $failed_count++;
                }
            }
            
            return [
                'sent' => $sent_count,
                'failed' => $failed_count,
                'processed' => count($emails)
            ];
            
        } catch (Exception $e) {
            error_log("Email queue processing error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send a specific queued email
     */
    private function sendQueuedEmail($email) {
        try {
            $success = $this->sendEmailNow($email['recipient_email'], $email['recipient_name'], $email['subject'], $email['body']);
            
            if ($success) {
                $stmt = $this->pdo->prepare("UPDATE email_queue SET status = 'sent', sent_at = NOW() WHERE id = ?");
                $stmt->execute([$email['id']]);
                return true;
            } else {
                $stmt = $this->pdo->prepare("UPDATE email_queue SET status = 'failed', error_message = ? WHERE id = ?");
                $stmt->execute(['Failed to send email', $email['id']]);
                return false;
            }
            
        } catch (Exception $e) {
            $stmt = $this->pdo->prepare("UPDATE email_queue SET status = 'failed', error_message = ? WHERE id = ?");
            $stmt->execute([$e->getMessage(), $email['id']]);
            return false;
        }
    }
    
    /**
     * Send email immediately
     */
    private function sendEmailNow($to, $name, $subject, $body) {
        // Use the existing sendEmail function from email.php
        require_once 'email.php';
        return sendEmail($to, $name, $subject, $body, false);
    }
    
    /**
     * Load and process email template
     */
    private function loadAndProcessTemplate($template_file, $variables) {
        $template_path = __DIR__ . '/../templates/' . $template_file;
        
        if (!file_exists($template_path)) {
            // Return a basic template if file doesn't exist
            return $this->getDefaultTemplate($variables);
        }
        
        $template_content = file_get_contents($template_path);
        return $this->processTemplate($template_content, $variables);
    }
    
    /**
     * Process template variables
     */
    private function processTemplate($template, $variables) {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        // Add default variables
        $defaults = [
            'site_name' => '84 Days Marathon Praise & Prayer',
            'site_url' => 'https://marathonpraise.ng',
            'current_year' => date('Y'),
            'current_date' => date('F j, Y'),
            'unsubscribe_url' => 'https://marathonpraise.ng/unsubscribe'
        ];
        
        foreach ($defaults as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }
    
    /**
     * Get default email template
     */
    private function getDefaultTemplate($variables) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>{subject}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #FF6600, #8B4513); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { padding: 15px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>{site_name}</h1>
                    <p>84 Days of Transformation</p>
                </div>
                <div class="content">
                    <h2>Hello {name}!</h2>
                    <p>{message}</p>
                    <p>Thank you for being part of this historic prayer movement for Nigeria.</p>
                    <p>Blessings,<br>The MPP Team</p>
                </div>
                <div class="footer">
                    <p>&copy; {current_year} 84 Days Marathon Praise & Prayer. All rights reserved.</p>
                    <p><a href="{unsubscribe_url}">Unsubscribe</a></p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Send bulk emails to volunteers
     */
    public function sendBulkToVolunteers($template_type, $variables = [], $filters = []) {
        try {
            $where_conditions = ['1=1'];
            $params = [];
            
            // Apply filters
            if (!empty($filters['service_type'])) {
                $where_conditions[] = 'service_type = ?';
                $params[] = $filters['service_type'];
            }
            
            if (!empty($filters['state'])) {
                $where_conditions[] = 'state = ?';
                $params[] = $filters['state'];
            }
            
            if (!empty($filters['status'])) {
                $where_conditions[] = 'status = ?';
                $params[] = $filters['status'];
            }
            
            $sql = "SELECT email, full_name FROM volunteer_registrations WHERE " . implode(' AND ', $where_conditions);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $recipients = $stmt->fetchAll();
            
            $queued_count = 0;
            foreach ($recipients as $recipient) {
                $email_variables = array_merge($variables, ['name' => $recipient['full_name']]);
                if ($this->queueEmail($recipient['email'], $recipient['full_name'], $template_type, $email_variables)) {
                    $queued_count++;
                }
            }
            
            return [
                'total_recipients' => count($recipients),
                'queued' => $queued_count
            ];
            
        } catch (Exception $e) {
            error_log("Bulk email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get email queue statistics
     */
    public function getQueueStats() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    status,
                    COUNT(*) as count
                FROM email_queue 
                GROUP BY status
            ");
            
            $stats = [];
            while ($row = $stmt->fetch()) {
                $stats[$row['status']] = $row['count'];
            }
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Email stats error: " . $e->getMessage());
            return false;
        }
    }
}
?>
