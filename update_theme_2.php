<?php

$dir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    
    // Skip CS directory
    if (strpos($path, 'views' . DIRECTORY_SEPARATOR . 'cs') !== false) {
        continue;
    }
    
    if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($path);
        
        $replacements = [
            "'warna' => 'primary'" => "'warna' => 'success'",
            "'warna' => 'info'" => "'warna' => 'success'",
            'btn-outline-info' => 'btn-outline-success',
            'btn-info' => 'btn-success',
            'text-info' => 'text-success',
            'bg-info' => 'bg-success',
            'border-info' => 'border-success',
            'btn-outline-warning' => 'btn-outline-success', // Standardize to green
            "'warna' => 'warning'" => "'warna' => 'success'", // Standardize to green
        ];
        
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated: " . $path . "\n";
        }
    }
}

echo "Done.\n";
