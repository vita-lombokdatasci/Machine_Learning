<?php

$path = '';

if (isset($_GET['p'])) {
    $path = $_GET['p'];
}

# Download file
if (isset($_GET['d'])) {
    $file_path = $path . $_GET['d'];
    if (is_file($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit();
    }
    echo "<hr/><p>File not found: " . $_GET['d'] . "</p>";
    exit();
}

# Delete file
//if (isset($_GET['rm'])) {
  //  $file_path = $path . $_GET['rm'];
//    if (is_file($file_path) && unlink($file_path)) {
//        echo "<hr/><p>File deleted succesfully: " . $_GET['rm'] . "</p>";
 //   } else {
    //    echo "<hr/><p>Can not delete file: " . $_GET['rm'] . "</p>";
//    }
//}

# Upload file
//if (isset($_POST['upload_dir'])) {
  //  $path = $_POST['dir'];
//    $file_name = basename($_FILES['file']['name']);
 //   $target_file = './' . $file_name;
  //  if (!is_file($target_file)) {
   //     move_uploaded_file($_FILES['file']['tmp_name'], $target_file);
//        echo "<hr/><p>File $file_name uploaded succesfully!</p>";
 //   } else {
  //      echo "<hr/><p>Cannot upload file: $file_name, file already exists!</p>";
//    }
//}

# Remove directory
//if (isset($_GET['rmd'])) {
//    $file_path = $path . $_GET['rmd'];
 //   if (is_dir($file_path) && rmdir($file_path)) {
//        echo "<hr/><p>Directory removed succesfully: " . $_GET['rmd'] . "</p>";
//    } else {
//        echo "<hr/><p>Can not remove directory, may not be empty: " . $_GET['rmd'] . "</p>";
 //   }
//}

# Helper functions
function humanFileSize($size, $unit="") {
    if((!$unit && $size >= 1<<30) || $unit == " GB")
        return number_format($size/(1<<30),2)." GB";
    if((!$unit && $size >= 1<<20) || $unit == " MB")
        return number_format($size/(1<<20),2)." MB";
    if((!$unit && $size >= 1<<10) || $unit == " KB")
        return number_format($size/(1<<10),2)." KB";
    return number_format($size)." B";
}
function redirect($url, $statusCode = 303) {
   header('Location: ' . $url, true, $statusCode);
   die();
}


# Directory listing

if ($path != '' && realpath('./' . $path) == realpath('./')) {
    redirect("?p=");
}

$my_dir = opendir('./' . $path);

echo '<title>Directory listing</title>';
echo '<hr/><h2>Directory listing</h2>';

echo '<table style="font-family: monospace; min-width: 45%" border=1>
<tr><th>File name</th><th>Size</th><th>Last modified</th><th>Actions</th></tr>';

if ($my_dir) {
    # List subdirectories
    while(false !== ($entry = readdir($my_dir))) {
        if ($entry !== '..') continue;
        $full_path = $path . $entry;
        if (is_dir($full_path)) {
            if ($entry == '..') {
                echo "<tr>
                    <td><a href=\"?p=/\">-</a></td>
                    <td>-</td><td>-</td><td>-</td>
                </tr>";
            } else 
                {echo "<tr>
                    <td><a href=\"?p=$full_path/\">[$entry]</a></td>
                    <td>-</td>
                    <td>" . date("Y-m-d H:i:s", filemtime($full_path)) . "</td>
                    <td><a href=\"?rmd=$entry&p=$path\">Remove</a></td>
                </tr>";
            }
        }
    }
    
    $my_dir = opendir('./' . $path);

    # List files
    while(false !== ($entry = readdir($my_dir))) {
        if ($entry == '.') continue;
        $full_path = $path . $entry;
        if (!is_dir($full_path)) {
            echo "<tr>
                <td><a href=\"$full_path\">$entry</a></td>
                <td>" . humanFileSize(filesize($full_path)) . "</td>
                <td>" . date("Y-m-d H:i:s", filemtime($full_path)) . "</td>
                <td>
                    <a href=\"?d=$entry&p=$path\">Download</a>
                    <a href=\"?rm=$entry&p=$path\"> </a>
                </td>
            </tr>";
        }
    }
}

echo '</table><hr/>';
//echo '<p><b>File upload</b> to current directory</p>';

//echo "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">
//    <input type=\"hidden\" name=\"upload_dir\" value=\"$path/\" />
//    <input type=\"file\" name=\"file\" />
//    <button type=\"submit\">Upload</button>
//</form><hr/>";

?>