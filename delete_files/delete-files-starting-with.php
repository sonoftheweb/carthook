<?php

# Method 1
/* *
 *    My favourite way of deleting recursively in a folder.
 * 1. First trim any extra directory separator
 * 2. Use the class RecursiveDirectoryIterator class to get all files in directory and it's sub directories
 * 3. Skip all hidden folders if set to do so
 * 4. In RecursiveCallbackFilterIterator callback to only look for files starting with $startsWith (in this case "0aH"). Note that "0aH" (0AH) is the hexadecimal constant for line feed.
 *      I ain't going with regex (preg_match) because its quite expensive for something like a directory scan.
 * 5. Return true in callback if the file starts with the given string and is not a directory.
 * 6. Now we can pass what was filtered into the RecursiveIteratorIterator to build out an iterator that we can loop over.
 * 7. Loop and delete the files
 * */
function deleteRecursiveIterator(string $path, string $startsWith, bool $omitHidden = true) {
    $path = rtrim($path, DIRECTORY_SEPARATOR);
    $directory = new RecursiveDirectoryIterator($path);
    $find = new RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) use ($startsWith, $omitHidden) {
        if ($current->getFilename()[0] === '.' && $omitHidden) {
            return false;
        }

        return (strpos($current->getFilename(), $startsWith) === 0 && !is_dir($current->getPathname()));
    });
    $iterator = new RecursiveIteratorIterator($find);
    foreach ($iterator as $i) {
        unlink($i->getPathname());
    }
}



# Method 2
/* *
 *  Good old scandir
 * 1. Trim trim trim...
 * 2. Scan root dir and loop over what was found.
 * 3. Omit the .. folder which points to the top layer
 * 4. If hidden files are not allowed, omit that as well
 * 5. If the file object is a not a directory, and meets the condition where it starts with 0aH, unlink!
 * 6. If not, check if the file object is a directory, If it is, call the function (recursively) deleteFiles on the directory.
 *       This starts a new delete action in the folder and so on.
 * */
function deleteFiles(string $path, string $startsWith, bool $omitHidden = true) {
    $path = rtrim($path, DIRECTORY_SEPARATOR);

    foreach (scandir($path) as $ff) {
        if ($ff === '..') continue;
        if ($ff === '.' && $omitHidden) continue;

        if (strpos($ff, $startsWith) === 0 && !is_dir($path . DIRECTORY_SEPARATOR . $ff)){
            unlink($path . DIRECTORY_SEPARATOR . $ff);
        }

        if (is_dir($path . DIRECTORY_SEPARATOR . $ff))
            deleteFiles($path . DIRECTORY_SEPARATOR . $ff, $startsWith, $omitHidden);
    }
}

