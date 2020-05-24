**Method 1** 

My favourite way of deleting recursively in a folder.
* First trim any extra directory separator.
* Use the class RecursiveDirectoryIterator class to get all files in the directory, and it's sub directories.
* Skip all hidden folders if set to do so.
* In RecursiveCallbackFilterIterator callback to only look for files starting with $startsWith (in this case "0aH"). Note that "0aH" (0AH) is the hexadecimal constant for line feed. I am not going with regex (preg_match) because its quite expensive for something like a directory scan.
* Return true in callback if the file starts with the given string and is not a directory.
* Now we can pass what was filtered into the RecursiveIteratorIterator to build out an iterator that we can loop over.
* Loop and delete the files.
 
 
 
 **Method 2**
 
Good ol' scandir()
* Trim trim trim...
* Scan root dir and loop over what was found.
* Omit the ".." folder which points to the top layer.
* If hidden files are not allowed, omit that as well.
* If the file object is a not a directory, and meets the condition where it starts with 0aH, unlink!
* If not, check if the file object is a directory, If it is, call the function (recursively) deleteFiles on the directory. This starts a new delete action in the folder and so on.