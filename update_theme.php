<?php

$dir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    
    // Skip CS directory entirely as requested by user
    if (strpos($path, 'views' . DIRECTORY_SEPARATOR . 'cs') !== false) {
        continue;
    }
    
    if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($path);
        
        $replacements = [
            'btn-primary' => 'btn-success',
            'btn-outline-primary' => 'btn-outline-success',
            'text-primary' => 'text-success',
            'bg-primary' => 'bg-success',
            'bg-primary-subtle' => 'bg-success-subtle',
            'border-primary' => 'border-success',
            'alert-primary' => 'alert-success',
            'ring-primary' => 'ring-success'
        ];
        
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated: " . $path . "\n";
        }
    }
}

echo "Done.\n";
