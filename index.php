<!doctype html>
<html>
	<head>
		<link rel="stylesheet" href="filesystem.css" />
		<script src="jquery-2.1.js"></script>
		<script src="listeners.js"></script>
	</head>
	<body>
		<div id="upload-pane" style="display: none;">
			<div id="upload-card" >
				<div id="upload-header">
					Upload Files
				</div>
				<form action="index.php" method="post"
				enctype="multipart/form-data" id="upload-form">

					<div id="drop-zone">
						<div id="drop-text-container">
							Drop files here
						</div>
					</div>
					Or
					<label for="file">Filename:</label>
					<input type="file" name="file" id="file">
					<br>
					<input type="submit" name="submit" value="Upload" id='upload-confirm'>
				</form>
			</div>
		</div>
		<?php
		error_reporting(0);

		$allowedExts = array("gif", "jpeg", "jpg", "png", "txt");
		$temp = explode(".", $_FILES["file"]["name"]);
		$extension = end($temp);

		$root = '/home/fbcrew/webapps/bryce_fileshare/';
		$upload_dir = 'uploads/';
		$dir = $root . $upload_dir;
		$file_path = $dir . $_FILES["file"]["name"];

		if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "text/plain")) && ($_FILES["file"]["size"] < 25000000) && in_array($extension, $allowedExts)) {

			if ($_FILES["file"]["error"] > 0) {
				echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
			} else {
				echo "Upload: " . $_FILES["file"]["name"] . "<br>";
				//echo "Type: " . $_FILES["file"]["type"] . "<br>";
				//echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
				//echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
				if (file_exists($file_path)) {
					echo $_FILES["file"]["name"] . " already exists. ";
				} else {
					move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
					//echo "Stored in: " . $file_path;
				}
			}

		} else {
			//echo "Invalid file, dir at" . __DIR__;
		}
		?>

		<div id="container">

			<h1>Directory Contents
			<button class='button' id='upload-button'>
				Upload
			</button></h1>

			<?php

			function listFolderFiles($list_dir) {
				print("<table class='sortable'>
				<thead>
					<tr>
						<th style='width: 20px;
						padding: 0;'></th>
						<th>Filename</th>
						<th>Type</th>
						<th>Size <small>(bytes)</small></th>
						<th>Date Modified</th>
					</tr>
				</thead>
				<tbody>");
				// $ffs = scandir($list_dir);
				// echo '<ul>';
				// foreach ($ffs as $ff) {
				// if ($ff != '.' && $ff != '..') {
				// echo '<li class="title">';
				// if (is_dir($list_dir . '/' . $ff)) {
				// echo $ff;
				// listFolderFiles($list_dir . '/' . $ff);
				// } else {
				// echo '<a href="' . $list_dir . '/' . $ff . '">' . $ff . '</a>';
				// }
				// echo '</li>';
				// }
				// }
				// echo '</ul>';
				//}

				// Opens directory
				$current_directory = '';
				$myDirectory = opendir($list_dir);

				// Gets each entry
				while ($entryName = readdir($myDirectory)) {
					$dirArray[] = $entryName;
				}

				// Finds extensions of files
				function findexts($filename) {
					$filename = strtolower($filename);
					$exts = split("[/\\.]", $filename);
					$n = count($exts) - 1;
					$exts = $exts[$n];
					return $exts;
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

						// Cleans up . and .. directories
						if ($name == ".") {$name = ". (Current Directory)";
							$extn = "&lt;System Dir&gt;";
						}
						if ($name == "..") {$name = ".. (Parent Directory)";
							$extn = "&lt;System Dir&gt;";
						}

						// Print 'em
						print("
          <tr class='$class menu-entry'>
          <td class='direct-download-container' ><a  class='direct-download' href='$list_dir$namehref' download='$list_dir$namehref' ></a></td>
            <td> <a href='$list_dir$namehref'>$name</a></td>
            <td><a href='$list_dir$namehref'>$extn</a></td>
            <td><a href='$list_dir$namehref'>$size</a></td>
            <td sorttable_customkey='$timekey'><a href='$list_dir$namehref'>$modtime</a></td>
          </tr>");
					}
				}

				print('				</tbody>
			</table>');
				if ($indexCount <= 2) {
					print("<div id='no-contents'>No Contents</div>");
				}
			}

			listFolderFiles($upload_dir);
			?>
		</div>
	</body>
</html>

