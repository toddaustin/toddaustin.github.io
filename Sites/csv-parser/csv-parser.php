
<?php

//connect to the database
// dev server
//$conn = odbc_connect("KCdsn", "kcphpdev", "k3p#pd3v#3");
// production server
$conn = odbc_connect("dbName","dbUser","dbPassword");

if (!$conn) {
    echo "<p>Connection to Database failed.</p>\n";
}

if ($_FILES[csv][size] > 0) {
    $currentTable = $_POST['dataBase'];
    //clear rows from table
    ini_set("auto_detect_line_endings", true);
    // truncate table if it matches this array
    $truncateDB = array('first','second','third');
    if (!in_array($currentTable,$truncateDB)){
        odbc_exec($conn, 'TRUNCATE TABLE ' . $currentTable);
    }

    // //get the csv file
    $file = $_FILES[csv][tmp_name];
    $file2 = $_FILES[csv][tmp_name];

    $handle = fopen($file, "r");
    $handle2 = fopen($file2, "r");


    while (($result = fgetcsv($handle2)) !== false){$dataHead[] = $result;}
 //Now process the data and create table
    $data_rows=@count($dataHead[0]); //from the first row get the header name
    $headings ="";
    //Create the header part
    for($i=0; $i<$data_rows;$i++){
        $cell=$dataHead[0][$i]; //each header
        if($i<($data_rows -1)){
            $headings.="$cell,";
        } else {
            $headings.="$cell";
        }
    }
    // set Null to NULL or N/admin
    if ($currentTable == 'fourth' || $currentTable == 'fifth' || $currentTable == 'sixth'){
        $setNull = "NULL";
    } else {
        $setNull = "'N/A'";
    }

    $headings = str_replace(' ', '_', $headings);

    //loop through the csv file and insert into database

    do {
        if ($data[0]) {
            $insertNew =  "INSERT INTO $currentTable (" . $headings . ") VALUES (";
            for ($i = 0; $i < $data_rows; $i++) {
                if ($data[$i] == '' || $data[$i] == 'NULL'){
                   $insertNew .= $setNull;
                } else {
                    $insertNew .="'" . $data[$i] . "'";
                }
                if(($i+1) < $data_rows){
                    $insertNew .= ",";
                }
            }
            $insertNew .= ")";

            odbc_exec($conn, $insertNew);
        }

    } while ($data = fgetcsv($handle, 1000, ",", "'"));
    //redirect to success message
    echo $headings;
     header('Location: admin.php?success=1');die;

}

?>

<!DOCTYPE html>
 <html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Report Database Admin</title>
        <link rel="stylesheet" href="admin.css" />
    </head>

    <body>
        <header><h1>Report Database Admin</h1></header>
        <div>
        <form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
          <select name="dataBase">
                <option value="">Select Database To Update</option>
                <option value="first">First</option>
                <option value="second">Second</option>
                <option value="third">Third</option>
                <option value="fourth">Fourth</option>
                <option value="fifth">Fifth</option>
                <option value="sixth">Sixth</option>

            </select>
          <h3>Choose your file: </h3>
          <label for="csv">
                  <div class="button up">Upload CSV File</div>
                   <input name="csv" type="file" id="csv" accept=".csv"/>
          </label>
        <div>
          <input type="submit" name="submit" value="Load to DataBase" class="button" /> </div>
        </form>
        </div>
    <!-- Success Message -->
    <?php if (!empty($_GET[success])) {print_r('Database updated!'); /* echo "<b>Your file has been imported.</b><br><br>";*/} else if (!empty($_GET[fail])) {print_r('Borked!');}

?>

        <script>
        var input = document.querySelector('input');
        input.addEventListener('change', function(){
            alert ('file uploaded!');
        });
    </script>
    </body>
</html>