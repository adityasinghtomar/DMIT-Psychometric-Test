<?php
/**
 * Navigation Fix Script - DMIT Psychometric Test System
 * This script fixes all relative navigation links to use proper URLs
 */

// Define the files and their navigation patterns to fix
$filesToFix = [
    // Profile pages
    'profile/settings.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="../assessments/new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="../assessments/list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="settings.php"' => 'href="<?php echo url(\'profile/settings.php\'); ?>"',
        'href="../help/user_guide.php"' => 'href="<?php echo url(\'help/user_guide.php\'); ?>"'
    ],
    
    // Help pages
    'help/user_guide.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="../assessments/new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="../assessments/list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="../profile/settings.php"' => 'href="<?php echo url(\'profile/settings.php\'); ?>"',
        'href="user_guide.php"' => 'href="<?php echo url(\'help/user_guide.php\'); ?>"'
    ],
    
    // Assessment pages
    'assessments/new.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="fingerprint_collection.php' => 'href="<?php echo url(\'assessments/fingerprint_collection.php\'',
        'action=""' => 'action="<?php echo url(\'assessments/new.php\'); ?>"'
    ],
    
    'assessments/list.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="view.php' => 'href="<?php echo url(\'assessments/view.php\'',
        'href="edit.php' => 'href="<?php echo url(\'assessments/edit.php\'',
        'href="analysis.php' => 'href="<?php echo url(\'assessments/analysis.php\'',
        'href="report.php' => 'href="<?php echo url(\'assessments/report.php\''
    ],
    
    'assessments/fingerprint_collection.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="analysis.php' => 'href="<?php echo url(\'assessments/analysis.php\''
    ],
    
    'assessments/analysis.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="report.php' => 'href="<?php echo url(\'assessments/report.php\''
    ],
    
    'assessments/report.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="view.php' => 'href="<?php echo url(\'assessments/view.php\''
    ],
    
    'assessments/view.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="edit.php' => 'href="<?php echo url(\'assessments/edit.php\'',
        'href="analysis.php' => 'href="<?php echo url(\'assessments/analysis.php\'',
        'href="report.php' => 'href="<?php echo url(\'assessments/report.php\'',
        'href="fingerprint_collection.php' => 'href="<?php echo url(\'assessments/fingerprint_collection.php\''
    ],
    
    'assessments/edit.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="new.php"' => 'href="<?php echo url(\'assessments/new.php\'); ?>"',
        'href="list.php"' => 'href="<?php echo url(\'assessments/list.php\'); ?>"',
        'href="view.php' => 'href="<?php echo url(\'assessments/view.php\''
    ],
    
    // Admin pages
    'admin/dashboard.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="dashboard.php"' => 'href="<?php echo url(\'admin/dashboard.php\'); ?>"',
        'href="users.php"' => 'href="<?php echo url(\'admin/users.php\'); ?>"',
        'href="security.php"' => 'href="<?php echo url(\'admin/security.php\'); ?>"',
        'href="settings.php"' => 'href="<?php echo url(\'admin/settings.php\'); ?>"'
    ],
    
    'admin/users.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="dashboard.php"' => 'href="<?php echo url(\'admin/dashboard.php\'); ?>"',
        'href="users.php"' => 'href="<?php echo url(\'admin/users.php\'); ?>"',
        'href="security.php"' => 'href="<?php echo url(\'admin/security.php\'); ?>"',
        'href="settings.php"' => 'href="<?php echo url(\'admin/settings.php\'); ?>"',
        'action="../auth/register.php"' => 'action="<?php echo url(\'auth/register.php\'); ?>"'
    ],
    
    'admin/security.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="dashboard.php"' => 'href="<?php echo url(\'admin/dashboard.php\'); ?>"',
        'href="users.php"' => 'href="<?php echo url(\'admin/users.php\'); ?>"',
        'href="security.php"' => 'href="<?php echo url(\'admin/security.php\'); ?>"',
        'href="settings.php"' => 'href="<?php echo url(\'admin/settings.php\'); ?>"'
    ],
    
    'admin/settings.php' => [
        'href="../index.php"' => 'href="<?php echo url(\'index.php\'); ?>"',
        'href="dashboard.php"' => 'href="<?php echo url(\'admin/dashboard.php\'); ?>"',
        'href="users.php"' => 'href="<?php echo url(\'admin/users.php\'); ?>"',
        'href="security.php"' => 'href="<?php echo url(\'admin/security.php\'); ?>"',
        'href="settings.php"' => 'href="<?php echo url(\'admin/settings.php\'); ?>"'
    ]
];

echo "🔧 Navigation Fix Script\n";
echo "========================\n\n";

foreach ($filesToFix as $file => $replacements) {
    if (file_exists($file)) {
        echo "📝 Fixing: $file\n";
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "   ✅ Updated navigation links\n";
        } else {
            echo "   ℹ️  No changes needed\n";
        }
    } else {
        echo "❌ File not found: $file\n";
    }
}

echo "\n🎉 Navigation fix completed!\n";
echo "\nAll navigation links should now work correctly from any page.\n";
echo "The url() function will automatically calculate the correct relative path.\n";
?>
