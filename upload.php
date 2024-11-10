<?php
$servername = '127.0.0.1:3308';
$usernameDB = 'root';
$passwordDB = '';
$dbname = 'db';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];

    if (!empty($fileName)) {
        $path = "files/" . $fileName;

        // Check if the file already exists in the database
        $checkQuery = "SELECT * FROM filedownload WHERE filename = '$fileName'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) === 0) {
            $query = "INSERT INTO filedownload(filename) VALUES ('$fileName')";
            $run = mysqli_query($conn, $query);

            if ($run) {
                if (move_uploaded_file($fileTmpName, $path)) {
                    echo "File uploaded successfully: " . htmlspecialchars($fileName) . "<br>";
                    // Redirect to clear the POST data and prevent resubmission
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    echo "Error moving file to destination folder.<br>";
                }
            } else {
                echo "Error inserting filename into database: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "File already exists in the database.<br>";
        }
    } else {
        echo "Please select a file to upload.<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload and Download</title>
</head>
<body>
   <table border="1px" align="center">
       <tr>
           <td>
               <form action="upload.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" required>
                    <button type="submit" name="submit">Upload</button>
                </form>
           </td>
       </tr>
       <tr>
           <td>
              <?php
              $query2 = "SELECT * FROM filedownload";
              $run2 = mysqli_query($conn, $query2);

              if ($run2) {
                  echo "<p>Files in database:</p>";
                  while ($rows = mysqli_fetch_assoc($run2)) {
                      if (!empty($rows['filename'])) {
                          $filePath = __DIR__ . "/files/" . $rows['filename'];
                          echo "Filename: " . htmlspecialchars($rows['filename']) . "<br>";
                          if (file_exists($filePath)) {
                              echo "<a href='download.php?file=" . urlencode($rows['filename']) . "'>Download</a><br>";
                          } else {
                              echo "<span style='color:red;'>File missing: " . htmlspecialchars($rows['filename']) . "</span><br>";
                          }
                      } else {
                          echo "<span style='color:red;'>Invalid filename in database.</span><br>";
                      }
                  }
              } else {
                  echo "Error fetching records: " . mysqli_error($conn);
              }
              ?>
           </td>
       </tr>
   </table>
</body>
</html>
