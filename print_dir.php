<?php

error_reporting(0);

$dir = "uploads";
$return_array = array();

// Finds extensions of files
function findexts($filename) {
	$filename = strtolower($filename);
	$exts = split("[/\\.]", $filename);
	$n = count($exts) - 1;
	$exts = $exts[$n];
	return $exts;
}

if (!is_dir($dir) || !($myDirectory = opendir($dir))) {
	echo json_encode(array('status' => '500'));
	return;
}

// Gets each entry
while ($entryName = readdir($myDirectory)) {
	$dirArray[] = $entryName;
}

// Closes directory
closedir($myDirectory);

// Counts elements in array
$indexCount = count($dirArray);

// Sorts files
sort($dirArray);

// Loops through the array of files
for ($index = 0; $index < $indexCount; $index++) {

	// Allows ./?hidden to show hidden files
	if ($_SERVER['QUERY_STRING'] == "hidden") {$hide = "";
		$ahref = "./";
		$atext = "Hide";
	} else {$hide = ".";
		$ahref = "./?hidden";
		$atext = "Show";
	}
	if (substr("$dirArray[$index]", 0, 1) != $hide) {

		// Gets File Names
		$name = $dirArray[$index];
		$namehref = $dirArray[$index];

		// Gets Extensions
		$extn = findexts($dirArray[$index]);

		// Gets file size
		$size = number_format(filesize($dirArray[$index]));

		// Gets Date Modified Data
		$modtime = date("M j Y g:i A", filemtime($dirArray[$index]));
		$timekey = date("YmdHis", filemtime($dirArray[$index]));

		// Prettifies File Types, add more to suit your needs.
		switch ($extn) {
			case "png" :
				$extn = "PNG Image";
				break;
			case "jpg" :
				$extn = "JPEG Image";
				break;
			case "svg" :
				$extn = "SVG Image";
				break;
			case "gif" :
				$extn = "GIF Image";
				break;
			case "ico" :
				$extn = "Windows Icon";
				break;

			case "txt" :
				$extn = "Text File";
				break;
			case "log" :
				$extn = "Log File";
				break;
			case "htm" :
				$extn = "HTML File";
				break;
			case "php" :
				$extn = "PHP Script";
				break;
			case "js" :
				$extn = "Javascript";
				break;
			case "css" :
				$extn = "Stylesheet";
				break;
			case "pdf" :
				$extn = "PDF Document";
				break;

			case "zip" :
				$extn = "ZIP Archive";
				break;
			case "bak" :
				$extn = "Backup File";
				break;

			default :
				$extn = strtoupper($extn) . " File";
				break;
		}

		// Separates directories
		if (is_dir($dirArray[$index])) {
			$extn = "&lt;Directory&gt;";
			$size = "";
			$class = "dir";
		} else {
			$class = "file";
		}

		$is_dir = FALSE;

		// Cleans up . and .. directories
		if ($name == ".") {
			$name = ". (Current Directory)";
			$extn = "&lt;System Dir&gt;";
			$is_dir = TRUE;
		}
		if ($name == "..") {
			$name = ".. (Parent Directory)";
			$extn = "&lt;System Dir&gt;";
			$is_dir = TRUE;
		}

		$return_array[] = array('name' => $name, 'type' => $extn, 'size' => $size, 'directory' => $is_dir, 'modified' => $modtime);

	}

}
echo json_encode(array('status' => '200', 'files' => $return_array));
//return;
?>
