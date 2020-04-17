<?php 
include 'dbo-connection.php';
include 'upload.php';

//PDO::query 
$all_animals = $pdo->query('SELECT * FROM animals');
$all_animals_compare = $pdo->query('SELECT * FROM animals');

//Global variables
$animal='';
$animals=[];
$no_valid_animal = false;
$no_valid_animals = false;

//Form response (with validation)
if(isset($_POST['search'])){
    if (!isset($_POST['animal']) || $_POST['animal'] === '' || ctype_space($_POST['animal'])){
        $no_valid_animal = true; 
    } else{
        $animal = $_POST['animal'];
    };
}

if (isset($_POST['choose'])){
    if (!isset($_POST['animals']) || !is_array($_POST['animals']) || count($_POST['animals']) === 0){
        $no_valid_animals = true;
    } else{
        $animals = $_POST['animals'];
    };
}

//Prepared Statement
$statement = $pdo->prepare("SELECT * FROM animals WHERE name = ?");

?>

<!--HTML Page starts here-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Zoo</title>
</head>
<body>
<main>
    <header class="main-header">
        <img src="animals.jpg" alt="djur">
        <h1>Välkommen till Parkens Zoo</h1>
    </header>

    <!-- Forms for searching for Animals -->
    <div class="main-container">
        <h2>Här kan du kika på våra djur</h2>
        <form action="index.php" method="post" class="form-container">
            Välj dina favoritdjur: 
            <select name="animals[]" multiple size="3">
                <?php            
                    foreach($all_animals as $row) {
                        printf(
                            '<option>%s</option>',
                            htmlspecialchars($row['name'], ENT_QUOTES)
                        );
                    }
                ?>
            </select>
            <input type="submit" name="choose" value="Välj">
        </form>

        <form action="index.php" method="post" class="form-container">
            Eller sök efter ditt favoritdjur:
            <input type="text" name="animal">
            <input type="submit" name="search" value="Sök">
        </form>

        <!-- Result -->
        <section>
        <h2>Valda djur:</h2>
        <div class="result-container">
            <div class="flex-container">
                <div class="flex-item-header">Namn</div>
                <div class="flex-item-header">Kategori</div>
                <div class="flex-item-header">Födelsedatum</div>
            </div>
            <?php 
            if (isset($_POST['search']) || isset($_POST['choose'])){

                //Render animals from select element 
                if(count($animals) > 0){
                    foreach ($animals as $row){
                        $statement->execute([$row]);
                        $result = $statement->fetchAll();
                        printf(
                            '<div class="flex-container">
                                <div class="flex-item">%s</div>
                                <div class="flex-item">%s</div>
                                <div class="flex-item">%s</div>
                            </div>',
                            htmlspecialchars($result[0]['name'], ENT_QUOTES),
                            htmlspecialchars($result[0]['category'], ENT_QUOTES),
                            htmlspecialchars($result[0]['birthday'], ENT_QUOTES)
                        );
                    }
                } else if ($no_valid_animals) {
                    echo '<div class="unvalid-result">Detta är inget giltigt sökvärde, välj/sök efter djur i sökfälten ovan </div>';
                } 

                //Render animal from input:search 
                if($animal){

                    //Search for animal in database
                    foreach($all_animals_compare as $row){
                        //if(gettype(stripos($row['name'], $animal)) == integer){} //för mer förlåtande värden t.ex jake eller gädd.
                        if(strtolower($row['name']) == strtolower(trim($animal))){
                            $statement->execute([$row['name']]);
                            $result = $statement->fetchAll();
                            printf(
                                '<div class="flex-container">
                                    <div class="flex-item">%s</div>
                                    <div class="flex-item">%s</div>
                                    <div class="flex-item">%s</div>
                                </div>',
                                htmlspecialchars($result[0]['name'], ENT_QUOTES),
                                htmlspecialchars($result[0]['category'], ENT_QUOTES),
                                htmlspecialchars($result[0]['birthday'], ENT_QUOTES)
                            );
                            $found_animal=true;
                        } 
                    }

                    if(!$found_animal){
                        echo '<div class="unvalid-result">Djuret som du sökte efter matchar inget djur som finns i djurparken, du kan alltid välja ett djur som finns i sökfältet ovan </div>';
                    }
                } else if ($no_valid_animal){
                    echo '<div class="unvalid-result">Detta är inget giltigt sökvärde, välj/sök efter djur i sökfälten ovan </div>';
                }
            } 
            //Default 
            else {
                echo '<div class="default-result"><p>Inga djur valda ännu, välj eller sök efter djur i sökfälten ovan</p></div>';
            }
            ?>
        </div>
        </section>

        <!-- Form for uploading image-file -->
        <h2>Du kan också ladda upp bilder på dina favoritdjur</h2>
        <form action="index.php" method="post" enctype="multipart/form-data" class="form-container">
            Välj en bild att ladda upp:
            <input type="file" name="file">
            <input type="submit" value="Ladda upp bild" name="upload">
        </form>

        <!--Upload result-->
        <p>
            <?php 
                //Messages
                if(isset($_POST["upload"])){
                    if($upload_ok){
                        echo $upload_success;
                    } else if ($upload_unknown_error) {
                        echo $upload_unknown_error;
                    } else if ($unvalid_image) {
                        echo $unvalid_image . $upload_error ;
                    } else if ($file_exist) {
                        echo $file_exist . $upload_error ;
                    } else if ($unvalid_file_extension) {
                        echo $unvalid_file_extension . $upload_error;
                    } 
                }
            ?>
        </p>

    </div>

    <footer class="main-footer">
        <p>Laboration PHP | Diana Nilsson | ITHS / Frontendutveckling</p>
    </footer>

</main>  
</body>
</html>
<?php 
    //Close PDO-connection
    $pdo = null;